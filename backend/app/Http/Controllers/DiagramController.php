<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CreateDiagramDTO;
use App\DTOs\ShareSettingsDTO;
use App\DTOs\UpdateDiagramDTO;
use App\Enums\DbType;
use App\Enums\DiagramAccess;
use App\Enums\ExportStatus;
use App\Enums\ImportStatus;
use App\Http\Requests\DiagramRequest;
use App\Http\Requests\ImportDiagramRequest;
use App\Http\Requests\SaveByTokenRequest;
use App\Http\Requests\UpdateShareAccessRequest;
use App\Http\Requests\UpdateVisitorAccessRequest;
use App\Http\Resources\DiagramResource;
use App\Http\Resources\DiagramVisitorResource;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\User;
use App\Services\DiagramCrudService;
use App\Services\DiagramSharingService;
use App\Services\DiagramSqlService;
use App\Services\PlanLimitService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;

#[Group('Diagrams')]
class DiagramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DiagramCrudService $crudService,
        private readonly DiagramSharingService $sharingService,
        private readonly DiagramSqlService $sqlService,
        private readonly PlanLimitService $planLimitService,
    ) {}

    #[Subgroup('CRUD')]
    public function index(Request $request): AnonymousResourceCollection
    {
        return DiagramResource::collection($this->crudService->getUserDiagrams($request->user()));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('CRUD')]
    public function show(Diagram $diagram): DiagramResource
    {
        $this->authorize('view', $diagram);

        return new DiagramResource($diagram);
    }

    #[Subgroup('CRUD')]
    public function store(DiagramRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($this->planLimitService->diagramLimitReached($user)) {
            abort(403, 'Free plan is limited to 1 diagram. Delete a diagram or upgrade to Pro to create more.');
        }

        $validated = $request->validated();

        $dto = new CreateDiagramDTO(
            name: $validated['name'],
            userId: $user->id,
            dbType: DbType::tryFrom((string) ($validated['db_type'] ?? '')) ?? DbType::MYSQL,
            shareAccess: isset($validated['share_access']) ? DiagramAccess::from($validated['share_access']) : null,
            library: (bool) ($validated['library'] ?? false),
            schema: $validated['schema'] ?? null,
        );

        $diagram = $this->crudService->createDiagram($dto);

        return $this->created(['status' => true, 'message' => 'Diagram created', 'data' => new DiagramResource($diagram)]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('CRUD')]
    public function update(Diagram $diagram, DiagramRequest $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        $validated = $request->validated();

        $dto = new UpdateDiagramDTO(
            name: $validated['name'] ?? null,
            dbType: isset($validated['db_type']) ? DbType::from($validated['db_type']) : null,
            shareAccess: isset($validated['share_access']) ? DiagramAccess::from($validated['share_access']) : null,
            library: isset($validated['library']) ? (bool) $validated['library'] : null,
            schema: $validated['schema'] ?? null,
        );

        return $this->crudService->updateDiagram($diagram, $dto)
            ? $this->success(['status' => true, 'message' => 'Diagram saved'])
            : $this->success(['status' => false, 'message' => 'Failed saving the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('CRUD')]
    public function destroy(Diagram $diagram): JsonResponse
    {
        $this->authorize('delete', $diagram);

        return $this->crudService->deleteDiagram($diagram)
            ? $this->noContent()
            : $this->success(['status' => false, 'message' => 'Failed deleting the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function import(Diagram $diagram, ImportDiagramRequest $request): JsonResponse
    {
        $this->authorize('import', $diagram);

        /** @var User $user */
        $user = $request->user();
        $this->sqlService->startImport($diagram, $request->validated()['script'], $user);

        return $this->success(['status' => 'pending'], 202);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function importStatus(Diagram $diagram): JsonResponse
    {
        $this->authorize('import', $diagram);

        return $this->success([
            'status' => $diagram->import_status,
            'schema' => $diagram->import_status === ImportStatus::DONE ? $diagram->schema : null,
            'error' => $diagram->import_error,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function export(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('export', $diagram);

        /** @var User $user */
        $user = $request->user();

        $this->sqlService->startExport($diagram, $user);

        return $this->success(['status' => 'pending'], 202);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function exportStatus(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        return $this->success([
            'status' => $diagram->export_status,
            'script' => $diagram->export_status === ExportStatus::DONE ? $diagram->script : null,
            'json' => $diagram->export_status === ExportStatus::DONE ? $diagram->export_json : null,
            'error' => $diagram->export_error,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function exportMigration(Diagram $diagram, Request $request): Response
    {
        $this->authorize('export', $diagram);

        /** @var User $user */
        $user = $request->user();

        $zipPath = $this->sqlService->createMigrationZip($diagram);
        try {
            $content = file_get_contents($zipPath);
            if ($content === false) {
                throw new \RuntimeException('Unable to prepare the migration export.');
            }
            $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $diagram->name).'_migrations.zip';
        } finally {
            if (is_file($zipPath)) {
                unlink($zipPath);
            }
        }

        if (! $this->planLimitService->consumeExportAllowance($user)) {
            abort(403, 'Free plan is limited to 3 exports per day. Try again after midnight (MSK) or upgrade to Pro.');
        }

        return response($content, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function exportJson(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('export', $diagram);

        /** @var User $user */
        $user = $request->user();

        $json = $this->sqlService->createJson(json_encode($diagram->schema));

        if (! $this->planLimitService->consumeExportAllowance($user)) {
            abort(403, 'Free plan is limited to 3 exports per day. Try again after midnight (MSK) or upgrade to Pro.');
        }

        return $this->success($json);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('SQL')]
    public function recordPngExport(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('export', $diagram);

        /** @var User $user */
        $user = $request->user();

        if (! $this->planLimitService->consumeExportAllowance($user)) {
            abort(403, 'Free plan is limited to 3 exports per day. Try again after midnight (MSK) or upgrade to Pro.');
        }

        return $this->success(['status' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function share(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return $this->success(['share_access' => $this->sharingService->ensureShared($diagram)]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function unshare(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        $this->sharingService->unshare($diagram);

        return $this->noContent();
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function updateShareAccess(Diagram $diagram, UpdateShareAccessRequest $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        $validated = $request->validated();

        $dto = new ShareSettingsDTO(
            access: isset($validated['access']) ? DiagramAccess::from($validated['access']) : null,
            requireApproval: isset($validated['require_approval']) ? (bool) $validated['require_approval'] : null,
            library: isset($validated['library']) ? (bool) $validated['library'] : null,
        );

        return $this->success($this->sharingService->updateShareSettings($diagram, $dto));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function getVisitors(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return DiagramVisitorResource::collection($this->sharingService->getVisitors($diagram))->response();
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function approveVisitor(Diagram $diagram, DiagramVisitor $visitor): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($visitor->diagram_id !== $diagram->id) {
            abort(404);
        }

        $visitor = $this->sharingService->approveVisitor($diagram, $visitor);

        return $this->success(['status' => true, 'access' => $visitor->access]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup('Sharing')]
    public function updateVisitorAccess(Diagram $diagram, DiagramVisitor $visitor, UpdateVisitorAccessRequest $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($visitor->diagram_id !== $diagram->id) {
            abort(404);
        }

        $visitor = $this->sharingService->setVisitorAccess($diagram, $visitor, DiagramAccess::from($request->validated()['access']));

        return $this->success(['status' => true, 'visitor_status' => $visitor->status, 'access' => $visitor->access]);
    }

    #[Subgroup('Sharing')]
    public function saveByToken(string $token, SaveByTokenRequest $request): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (! $this->sharingService->saveByToken($diagram, $request->user(), $request->validated()['schema'])) {
            abort(403);
        }

        return $this->success(['status' => true]);
    }

    #[Subgroup('Sharing')]
    public function showEmbed(string $token): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (! $diagram->share_access) {
            abort(403, 'This diagram is not shared.');
        }

        return $this->success($this->crudService->getEmbedData($diagram));
    }

    #[Subgroup('Sharing')]
    public function showByToken(string $token, Request $request): DiagramResource|JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();
        $result = $this->sharingService->resolveSharedAccess($diagram, $request->user());

        if ($result['status'] === 'not_shared') {
            abort(403, 'This diagram is not shared.');
        }
        if ($result['status'] === 'revoked') {
            return $this->success(['message' => 'Access revoked.'], 403);
        }
        if ($result['status'] === 'pending') {
            return $this->success(['pending_approval' => true], 403);
        }

        return new DiagramResource($result['diagram']);
    }
}
