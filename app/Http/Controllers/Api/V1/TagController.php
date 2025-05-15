<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Product;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Requests\AttachTagToProductRequest;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::all();
        return response()->json([
            'message' => 'Tags listadas com sucesso.',
            'data' => TagResource::collection($tags),
        ]);
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::create($request->validated());
        return response()->json([
            'message' => 'Tag criada com sucesso.',
            'data' => new TagResource($tag),
        ], Response::HTTP_CREATED);
    }

    public function show(Tag $tag): JsonResponse
    {
        return response()->json([
            'message' => 'Tag obtida com sucesso.',
            'data' => new TagResource($tag),
        ]);
    }

    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $tag->update($request->validated());
        return response()->json([
            'message' => 'Tag atualizada com sucesso.',
            'data' => new TagResource($tag),
        ]);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();
        return response()->json([
            'message' => 'Tag deletada com sucesso.',
        ], Response::HTTP_NO_CONTENT);
    }
}