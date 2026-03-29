<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramRequest;
use App\Http\Resources\DiagramResource;
use App\Models\Diagram;
use App\Services\DiagramService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ValidateSQLRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class DiagramController extends Controller
{
    use AuthorizesRequests;

    protected DiagramService $diagramService;

    public function __construct(DiagramService $diagramService)
    {
        $this->diagramService = $diagramService;
    }

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
        $user = $request->user();
        $data = $request->validated();
        $data['user_id'] = $user->id;

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

        $script = $request->input("script");
        $diagram->schema = $this->diagramService->createSchema(json_decode($script));
        $diagram->save();
        return response()->json($diagram->schema);
    }

    /**
     * @throws AuthorizationException
     */
    public function export(Diagram $diagram): JsonResponse
    {
        $this->authorize('export', $diagram);

        $diagram->script = json_encode($this->diagramService->createScript($diagram->schema, $diagram->db_type ?? 'mysql'));
        $diagram->save();

        return response()->json($diagram->script);
    }

    /**
     * @throws AuthorizationException
     */
    public function share(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        if (!$diagram->share_access) {
            $diagram->share_access = 'read';
            $diagram->save();
        }

        return response()->json(['share_access' => $diagram->share_access]);
    }

    /**
     * @throws AuthorizationException
     */
    public function unshare(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        $diagram->share_access = null;
        $diagram->save();

        return response()->json(['status' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateShareAccess(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        $access = $request->input('access');
        if (!in_array($access, ['read', 'write'])) {
            return response()->json(['message' => 'Invalid access type'], 422);
        }

        $diagram->share_access = $access;
        $diagram->save();

        return response()->json(['share_access' => $diagram->share_access]);
    }

    public function saveByToken(string $token, Request $request): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)
            ->where('share_access', 'write')
            ->firstOrFail();

        $diagram->schema = $request->input('schema');
        $diagram->save();

        return response()->json(['status' => true]);
    }

    public function showByToken(string $token, Request $request): DiagramResource
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if ($request->user()->id !== $diagram->user_id && !$diagram->share_access) {
            abort(403, 'This diagram is not shared.');
        }

        return new DiagramResource($diagram);
    }

}
