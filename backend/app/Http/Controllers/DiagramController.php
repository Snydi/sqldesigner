<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramRequest;
use App\Http\Resources\DiagramResource;
use App\Events\VisitorRequested;
use App\Events\VisitorAccessChanged;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
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
        $diagram->library = false;
        $diagram->save();

        return response()->json(['status' => true]);
    }

    /**
     * @throws AuthorizationException
     */
    public function updateShareAccess(Diagram $diagram, Request $request): JsonResponse
    {
        $this->authorize('update', $diagram);

        if ($request->has('access')) {
            $access = $request->input('access');
            if (!in_array($access, ['read', 'write', 'per_user'])) {
                return response()->json(['message' => 'Invalid access type'], 422);
            }
            $diagram->share_access = $access;
        }

        if ($request->has('require_approval')) {
            $diagram->require_approval = (bool) $request->input('require_approval');
        }

        if ($request->has('library')) {
            $diagram->library = (bool) $request->input('library');
            if ($diagram->library && $diagram->share_access !== 'per_user') {
                $diagram->share_access = 'per_user';
            }
        }

        $diagram->save();

        return response()->json([
            'share_access'     => $diagram->share_access,
            'require_approval' => (bool) $diagram->require_approval,
            'library'          => (bool) $diagram->library,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function getVisitors(Diagram $diagram): JsonResponse
    {
        $this->authorize('update', $diagram);

        return response()->json(
            $diagram->visitors()->with('user')->orderByDesc('created_at')->get()->map(fn($v) => [
                'id' => $v->id,
                'user_id' => $v->user_id,
                'name' => $v->user->name ?: $v->user->email,
                'email' => $v->user->email,
                'status' => $v->status,
                'access' => $v->access,
            ])
        );
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

        $visitor->status = 'approved';
        if ($diagram->share_access === 'per_user') {
            $visitor->access = $visitor->access ?? 'read';
        }
        $visitor->save();

        try {
            $access = $diagram->share_access === 'per_user' ? ($visitor->access ?? 'read') : $diagram->share_access;
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, $access));
        } catch (\Exception $e) {}

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

        if ($access === 'revoke') {
            $visitor->status = 'revoked';
            $visitor->access = null;
        } else {
            $visitor->status = 'approved';
            $visitor->access = $access;
        }

        $visitor->save();

        try {
            $broadcastAccess = $visitor->status === 'revoked' ? 'revoked' : ($visitor->access ?? $diagram->share_access ?? 'read');
            broadcast(new VisitorAccessChanged($visitor->user_id, $diagram->share_token, $broadcastAccess));
        } catch (\Exception $e) {}

        return response()->json(['status' => true, 'visitor_status' => $visitor->status, 'access' => $visitor->access]);
    }

    public function saveByToken(string $token, Request $request): JsonResponse
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();
        $user = $request->user();

        if ($diagram->share_access === 'per_user') {
            $hasWriteAccess = DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('access', 'write')
                ->exists();
        } elseif ($diagram->share_access === 'write') {
            $visitor = DiagramVisitor::where('diagram_id', $diagram->id)
                ->where('user_id', $user->id)
                ->first();
            if ($visitor?->status === 'revoked') {
                $hasWriteAccess = false;
            } elseif ($diagram->require_approval) {
                $hasWriteAccess = $visitor?->status === 'approved';
            } else {
                $hasWriteAccess = true;
            }
        } else {
            $hasWriteAccess = false;
        }

        if (!$hasWriteAccess) {
            abort(403);
        }

        $diagram->schema = $request->input('schema');
        $diagram->save();

        return response()->json(['status' => true]);
    }

    public function showEmbed(string $token)
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();

        if (!$diagram->share_access) {
            abort(403, 'This diagram is not shared.');
        }

        return response()->json([
            'name'    => $diagram->name,
            'db_type' => $diagram->db_type,
            'schema'  => $diagram->schema,
        ]);
    }

    public function showByToken(string $token, Request $request)
    {
        $diagram = Diagram::where('share_token', $token)->firstOrFail();
        $user = $request->user();

        if ($user->id !== $diagram->user_id) {
            if (!$diagram->share_access) {
                abort(403, 'This diagram is not shared.');
            }


            if ($diagram->share_access === 'per_user') {
                $defaultStatus = $diagram->require_approval ? 'pending' : 'approved';
                $visitor = DiagramVisitor::firstOrCreate(
                    ['diagram_id' => $diagram->id, 'user_id' => $user->id],
                    ['status' => $defaultStatus, 'access' => 'read']
                );

                if ($visitor->wasRecentlyCreated && $diagram->require_approval) {
                    try {
                        broadcast(new VisitorRequested($diagram->share_token));
                    } catch (\Exception $e) {
                        // Broadcasting failure must not block the guest's request
                    }
                }

                if ($visitor->status === 'revoked') {
                    return response()->json(['message' => 'Access revoked.'], 403);
                }

                if ($visitor->status !== 'approved') {
                    return response()->json(['pending_approval' => true], 403);
                }

                $diagram->share_access = $visitor->access ?? 'read';

            } else {
                $defaultStatus = $diagram->require_approval ? 'pending' : 'approved';
                $visitor = DiagramVisitor::firstOrCreate(
                    ['diagram_id' => $diagram->id, 'user_id' => $user->id],
                    ['status' => $defaultStatus]
                );

                if ($visitor->wasRecentlyCreated && $diagram->require_approval) {
                    try {
                        broadcast(new VisitorRequested($diagram->share_token));
                    } catch (\Exception $e) {
                        // Broadcasting failure must not block the guest's request
                    }
                }

                if ($visitor->status === 'revoked') {
                    return response()->json(['message' => 'Access revoked.'], 403);
                }

                if ($diagram->require_approval && $visitor->status !== 'approved') {
                    return response()->json(['pending_approval' => true], 403);
                }
                // Global share_access (read/write) applies
            }
        }

        return new DiagramResource($diagram);
    }

}
