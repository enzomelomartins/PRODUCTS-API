<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductFeatureController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        $product->load('featureGroups.features');
        return response()->json([
            'message' => 'Features obtidas com sucesso.',
            'data' => $product->featureGroups->map(function ($group) {
                return [
                    'group' => $group->name,
                    'items' => $group->features->map(function ($feature) {
                        return [
                            'key' => $feature->key,
                            'value' => $feature->value,
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function update(Product $product): JsonResponse
    {
        
        
        return response()->json([
            'message' => 'Features atualizadas com sucesso.',
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->featureGroups()->each(function ($group) {
            $group->features()->delete();
        });
        $product->featureGroups()->delete();

        return response()->json([
            'message' => 'Features removidas com sucesso.',
        ]);
    }
}
