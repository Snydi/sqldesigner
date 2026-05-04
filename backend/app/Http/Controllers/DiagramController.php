<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramRequest;
use App\Http\Resources\DiagramResource;
use App\Http\Resources\DiagramVisitorResource;
use App\Jobs\ExportDiagramJob;
use App\Jobs\ImportDiagramSchemaJob;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Services\DiagramCrudService;
use App\Services\DiagramSharingService;
use App\Services\DiagramSqlService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Subgroup;
use ZipArchive;

#[Group("Diagrams")]
class DiagramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly DiagramCrudService    $crudService,
        private readonly DiagramSharingService $sharingService,
        private readonly DiagramSqlService     $sqlService,
    )
    {
    }

    #[Subgroup("CRUD")]
    public function index(Request $request): AnonymousResourceCollection
    {
        return DiagramResource::collection($this->crudService->getUserDiagrams($request->user()));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("CRUD")]
    public function show(Diagram $diagram): DiagramResource
    {
        $this->authorize('view', $diagram);

        return new DiagramResource($diagram);
    }

    #[Subgroup("CRUD")]
    public function store(DiagramRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        return $this->crudService->createDiagram($data)
            ? response()->json(['status' => true, 'message' => 'Diagram created'])
            : response()->json(['status' => false, 'message' => 'Failed creating the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("CRUD")]
    public function update(Diagram $diagram, DiagramRequest $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        return $this->crudService->updateDiagram($diagram, $request->all())
            ? response()->json(['status' => true, 'message' => 'Diagram saved'])
            : response()->json(['status' => false, 'message' => 'Failed saving the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("CRUD")]
    public function destroy(Diagram $diagram): JsonResponse
    {
        $this->authorize('delete', $diagram);

        return $this->crudService->deleteDiagram($diagram)
            ? response()->json(['status' => true, 'message' => 'Diagram deleted'])
            : response()->json(['status' => false, 'message' => 'Failed deleting the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function import(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('import', $diagram);

        $diagram->script        = $request->input('script');
        $diagram->import_status = 'pending';
        $diagram->import_error  = null;
        $diagram->save();

        ImportDiagramSchemaJob::dispatch($diagram);

        return response()->json(['status' => 'pending'], 202);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function importStatus(Diagram $diagram): JsonResponse
    {
        $this->authorize('import', $diagram);

        return response()->json([
            'status' => $diagram->import_status,
            'schema' => $diagram->import_status === 'done' ? $diagram->schema : null,
            'error'  => $diagram->import_error,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function export(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        $diagram->export_status = 'pending';
        $diagram->export_error  = null;
        $diagram->save();

        ExportDiagramJob::dispatch($diagram);

        return response()->json(['status' => 'pending'], 202);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function exportStatus(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        return response()->json([
            'status' => $diagram->export_status,
            'script' => $diagram->export_status === 'done' ? $diagram->script : null,
            'json'   => $diagram->export_status === 'done' ? json_decode($diagram->export_json) : null,
            'error'  => $diagram->export_error,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function exportMigration(Diagram $diagram): Response
    {
        $this->authorize('export', $diagram);

        $files = $this->sqlService->createMigration($diagram->schema);
        $tmpFile = tempnam(sys_get_temp_dir(), 'migrations_');

        $zip = new ZipArchive();
        $zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            $zip->addFromString("migrations/{$file['filename']}", $file['content']);
        }
        $zip->close();

        $content = file_get_contents($tmpFile);
        unlink($tmpFile);
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $diagram->name) . '_migrations.zip';

        return response($content, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("SQL")]
    public function exportJson(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        return response()->json(json_decode($this->sqlService->createJson($diagram->schema)));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function share(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return response()->json(['share_access' => $this->sharingService->ensureShared($diagram)]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function unshare(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        $this->sharingService->unshare($diagram);

        return response()->json(['status' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function updateShareAccess(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        $access = $request->has('access') ? $request->input('access') : null;
        if ($access !== null && !in_array($access, ['read', 'write', 'per_user'])) {
            return response()->json(['message' => 'Invalid access type'], 422);
        }

        $requireApproval = $request->has('require_approval') ? (bool)$request->input('require_approval') : null;
        $library = $request->has('library') ? (bool)$request->input('library') : null;

        return response()->json($this->sharingService->updateShareSettings($diagram, $access, $requireApproval, $library));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function getVisitors(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return DiagramVisitorResource::collection($this->sharingService->getVisitors($diagram));
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function approveVisitor(Diagram $diagram, DiagramVisitor $visitor): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($visitor->diagram_id !== $diagram->id) {
            abort(404);
        }

        $visitor = $this->sharingService->approveVisitor($diagram, $visitor);

        return response()->json(['status' => true, 'access' => $visitor->access]);
    }

    /**
     * @throws AuthorizationException
     */
    #[Subgroup("Sharing")]
    public function updateVisitorAccess(Diagram $diagram, DiagramVisitor $visitor, Request $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($visitor->diagram_id !== $diagram->id) {
            abort(404);
        }

        $access = $request->input('access');
        if (!in_array($access, ['read', 'write', 'revoke'])) {
            return response()->json(['message' => 'Invalid access value'], 422);
        }

        $visitor = $this->sharingService->setVisitorAccess($diagram, $visitor, $access);

        return response()->json(['status' => true, 'visitor_status' => $visitor->status, 'access' => $visitor->access]);
    }

    #[Subgroup("Sharing")]
    public function saveByToken(string $token, Request $request): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (!$this->sharingService->saveByToken($diagram, $request->user(), $request->input('schema'))) {
            abort(403);
        }

        return response()->json(['status' => true]);
    }

    #[Subgroup("Sharing")]
    public function showEmbed(string $token): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (!$diagram->share_access) {
            abort(403, 'This diagram is not shared.');
        }

        return response()->json($this->crudService->getEmbedData($diagram));
    }

    #[Subgroup("Sharing")]
    public function showByToken(string $token, Request $request): DiagramResource|JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();
        $result = $this->sharingService->resolveSharedAccess($diagram, $request->user());

        if ($result['status'] === 'not_shared') abort(403, 'This diagram is not shared.');
        if ($result['status'] === 'revoked') return response()->json(['message' => 'Access revoked.'], 403);
        if ($result['status'] === 'pending') return response()->json(['pending_approval' => true], 403);

        return new DiagramResource($result['diagram']);
    }
}
