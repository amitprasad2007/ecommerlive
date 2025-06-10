<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //
    public function getcategorieslist(){
        $categories = Category::where('is_parent', '!=', 0)
            ->with(['products' => function($query) {
                $query->take(8);
            }, 'products.photoproduct'])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,                    
                    'name' => $category->title,
                    'icon' => $category->icon_path ? asset('storage/'.$category->icon_path) : null,
                    'image' => $category->photo? asset('storage/'.$category->photo) : null,
                    'slug' => $category->slug,
                    'productCount' => $category->products()->count(),
                    'description' => $category->description,
                    'link' => '/category/' . $category->slug,
                    'gradient' => $this->getGradientForCategory($category->id),
                    'products' => $category->products->map(function ($product) {
                        $photo = $product->photoproduct->first();
                        return [
                            'id' => $product->id,
                            'name' => $product->title,
                            'image' => $photo ? asset('storage/products/photos/thumbnails/'.$photo->photo_path) : null,
                            'price' => $product->price,
                            'originalPrice' => $product->original_price ?? null,
                            'rating' => $product->rating ?? null,
                            'reviewCount' => $product->review_count ?? null,
                            'brand' => $product->brand->title ?? null,
                            'isBestSeller' => $product->is_best_seller ?? false,
                            'isNew' => $product->created_at >= now()->subMonth(),
                        ];
                    })
                ];
            });

        return response()->json(['categories' => $categories]);
    }

    private function getGradientForCategory($categoryId)
    {
        $gradients = [
            'from-blue-200 to-blue-600',
            'from-green-400 to-blue-500',
            'from-purple-400 to-pink-600',
            'from-red-400 to-orange-500',
            'from-yellow-400 to-orange-500',
            'from-teal-400 to-cyan-500'
        ];

        return $gradients[$categoryId % count($gradients)];
    }
    public function getcategorybyid($id){
        $category = Category::find($id);
        return response()->json(['category' => $category]);
    }

    public function getsubcategorieslist($id){
        $subcategories = Category::where('parent_id', $id)->get();
        return response()->json(['subcategories' => $subcategories]);
    }

    public function catsubsub()
    {
        $categories = Category::where('is_parent', '!=', 0)
            ->with(['subcategories.subSubCategories'])
            ->get();

        $formatCategory = function ($category) use (&$formatCategory) {
            $data = [
                'id' => (string)$category->id,
                'name' => $category->title,
                'icon' => $category->icon_path ? asset('storage/'.$category->icon_path) : null,
                'image' => $category->photo,
                'slug' => $category->slug,
                'productCount' => $category->products()->count(),
            ];
            if ($category->subcategories && $category->subcategories->count() > 0) {
                $data['subcategories'] = $category->subcategories->map(function ($sub) use (&$formatCategory) {
                    $subData = [
                        'id' => (string)$sub->id,
                        'name' => $sub->title,
                        'icon' => $sub->icon_path ? asset('storage/'.$sub->icon_path) : null,
                        'image' => $sub->photo,
                        'slug' => $sub->slug,
                        'productCount' => $sub->products()->count(),
                    ];
                    if ($sub->subSubCategories && $sub->subSubCategories->count() > 0) {
                        $subData['subcategories'] = $sub->subSubCategories->map(function ($subsub) {
                            return [
                                'id' => (string)$subsub->id,
                                'name' => $subsub->title,
                                'icon' => $subsub->icon_path ? asset('storage/'.$subsub->icon_path) : null,
                                'image' => $subsub->photo,
                                'slug' => $subsub->slug,
                                'productCount' => $subsub->products()->count(),
                            ];
                        })->values()->toArray();
                    }
                    return $subData;
                })->values()->toArray();
            }
            return $data;
        };

        $result = $categories->map(function ($cat) use ($formatCategory) {
            return $formatCategory($cat);
        })->values()->toArray();

        return response()->json($result);
    }
}
