<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramRequest;
use App\Http\Requests\ValidateSQLRequest;
use App\Http\Resources\DiagramResource;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Services\DiagramService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class DiagramController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly DiagramService $diagramService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return DiagramResource::collection($this->diagramService->getUserDiagrams($request->user()));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Diagram $diagram): DiagramResource
    {
        $this->authorize('view', $diagram);

        return new DiagramResource($diagram);
    }

    public function store(DiagramRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        return $this->diagramService->createDiagram($data)
            ? response()->json(['status' => true, 'message' => 'Diagram created'])
            : response()->json(['status' => false, 'message' => 'Failed creating the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(Diagram $diagram, DiagramRequest $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        return $this->diagramService->updateDiagram($diagram, $request->all())
            ? response()->json(['status' => true, 'message' => 'Diagram saved'])
            : response()->json(['status' => false, 'message' => 'Failed saving the diagram']);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Diagram $diagram): JsonResponse
    {
        $this->authorize('delete', $diagram);

        return $this->diagramService->deleteDiagram($diagram)
            ? response()->json(['status' => true, 'message' => 'Diagram deleted'])
            : response()->json(['status' => false, 'message' => 'Failed deleting the diagram']);
    }

    public function validateSQL(ValidateSQLRequest $request): JsonResponse
    {
        $result = $this->diagramService->validateSQL(
            $request->input('sql'),
            $request->input('db_type', 'mysql')
        );

        return response()->json($result, $result['valid'] ? 200 : 422);
    }

    /**
     * @throws AuthorizationException
     */
    public function import(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('import', $diagram);

        return response()->json($this->diagramService->importSchema($diagram, $request->input('script')));
    }

    /**
     * @throws AuthorizationException
     */
    public function export(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        return response()->json($this->diagramService->exportScript($diagram));
    }

    /**
     * @throws AuthorizationException
     */
    public function exportMigration(Diagram $diagram): \Illuminate\Http\Response
    {
        $this->authorize('export', $diagram);

        $files   = $this->diagramService->createMigration($diagram->schema);
        $tmpFile = tempnam(sys_get_temp_dir(), 'migrations_');

        $zip = new \ZipArchive();
        $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            $zip->addFromString("migrations/{$file['filename']}", $file['content']);
        }
        $zip->close();

        $content  = file_get_contents($tmpFile);
        unlink($tmpFile);
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $diagram->name) . '_migrations.zip';

        return response($content, 200, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function exportJson(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        return response()->json(json_decode($this->diagramService->createJson($diagram->schema)));
    }

    /**
     * @throws AuthorizationException
     */
    public function share(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return response()->json(['share_access' => $this->diagramService->ensureShared($diagram)]);
    }

    /**
     * @throws AuthorizationException
     */
    public function unshare(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        $this->diagramService->unshare($diagram);

        return response()->json(['status' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateShareAccess(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        $access = $request->has('access') ? $request->input('access') : null;
        if ($access !== null && !in_array($access, ['read', 'write', 'per_user'])) {
            return response()->json(['message' => 'Invalid access type'], 422);
        }

        $requireApproval = $request->has('require_approval') ? (bool) $request->input('require_approval') : null;
        $library         = $request->has('library') ? (bool) $request->input('library') : null;

        return response()->json($this->diagramService->updateShareSettings($diagram, $access, $requireApproval, $library));
    }

    /**
     * @throws AuthorizationException
     */
    public function getVisitors(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return response()->json($this->diagramService->getVisitors($diagram));
    }

    /**
     * @throws AuthorizationException
     */
    public function approveVisitor(Diagram $diagram, DiagramVisitor $visitor): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($visitor->diagram_id !== $diagram->id) {
            abort(404);
        }

        $visitor = $this->diagramService->approveVisitor($diagram, $visitor);

        return response()->json(['status' => true, 'access' => $visitor->access]);
    }

    /**
     * @throws AuthorizationException
     */
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

        $visitor = $this->diagramService->setVisitorAccess($diagram, $visitor, $access);

        return response()->json(['status' => true, 'visitor_status' => $visitor->status, 'access' => $visitor->access]);
    }

    public function saveByToken(string $token, Request $request): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (!$this->diagramService->saveByToken($diagram, $request->user(), $request->input('schema'))) {
            abort(403);
        }

        return response()->json(['status' => true]);
    }

    public function showEmbed(string $token): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (!$diagram->share_access) {
            abort(403, 'This diagram is not shared.');
        }

        return response()->json($this->diagramService->getEmbedData($diagram));
    }

    public function showByToken(string $token, Request $request): DiagramResource|JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();
        $result  = $this->diagramService->resolveSharedAccess($diagram, $request->user());

        if ($result['status'] === 'not_shared') abort(403, 'This diagram is not shared.');
        if ($result['status'] === 'revoked')    return response()->json(['message' => 'Access revoked.'], 403);
        if ($result['status'] === 'pending')    return response()->json(['pending_approval' => true], 403);

        return new DiagramResource($result['diagram']);
    }
}
