<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        return response()->json(['data' => $tags, 'message' => 'Tags listadas com sucesso.']);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::create($request->validated());
        return response()->json(['data' => $tag, 'message' => 'Tag criada com sucesso.'], Response::HTTP_CREATED);
    }

    public function show(Tag $tag): JsonResponse
    {
        return response()->json(['data' => $tag, 'message' => 'Tag obtida com sucesso.']);
    }

    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $tag->update($request->validated());
        return response()->json(['data' => $tag, 'message' => 'Tag atualizada com sucesso.']);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        return response()->json(['message' => 'Tag deletada com sucesso.'], Response::HTTP_NO_CONTENT);
    }
}
