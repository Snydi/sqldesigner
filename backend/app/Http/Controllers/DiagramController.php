<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramRequest;
use App\Http\Resources\DiagramResource;
use App\Models\Diagram;
use App\Services\DiagramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class DiagramController extends Controller
{
    protected DiagramService $diagramService;

    public function __construct(DiagramService $diagramService)
    {
        $this->diagramService = $diagramService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return DiagramResource::collection($this->diagramService->getUserDiagrams($request->user()));
    }

    public function show(Diagram $diagram): DiagramResource
    {
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

    public function update(Diagram $diagram, DiagramRequest $request): JsonResponse
    {
        return $this->diagramService->updateDiagram($diagram, $request->all())
            ? response()->json(['status' => true, 'message' => 'Diagram updated'])
            : response()->json(['status' => false, 'message' => 'Failed updating the diagram']);
    }

    public function destroy(Diagram $diagram): JsonResponse
    {
        return $this->diagramService->deleteDiagram($diagram)
            ? response()->json(['status' => true, 'message' => 'Diagram deleted'])
            : response()->json(['status' => false, 'message' => 'Failed deleting the diagram']);
    }

    public function validateSQL(Request $request): JsonResponse
    {
        $request->validate(['sql' => 'required|string']);

        $result = $this->diagramService->validateSQL($request->input('sql'));

        return response()->json($result, $result['valid'] ? 200 : 422);
    }

    public function import(Diagram $diagram, Request $request): JsonResponse
    {
        $script = $request->input("script");
        $diagram->schema = $this->diagramService->createSchema(json_decode($script));
        $diagram->save();
        return response()->json($diagram->schema);
    }

    public function export(Diagram $diagram): JsonResponse
    {
        $diagram->script = json_encode($this->diagramService->createScript($diagram->schema));
        $diagram->save();

        return response()->json($diagram->script);
    }
}
