<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //
    public function getcategorieslist(){
        $categories = Category::where('is_parent', '!=', 0)->with(['products' => function($query) {
            $query->take(8);
        }, 'products.photoproduct'])->get();
        return response()->json(['categories' => $categories]);
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
                'icon' => $category->icon_path,
                'image' => $category->photo,
                'slug' => $category->slug,
                'productCount' => $category->products()->count(),
            ];
            if ($category->subcategories && $category->subcategories->count() > 0) {
                $data['subcategories'] = $category->subcategories->map(function ($sub) use (&$formatCategory) {
                    $subData = [
                        'id' => (string)$sub->id,
                        'name' => $sub->title,
                        'icon' => $sub->icon_path,
                        'image' => $sub->photo,
                        'slug' => $sub->slug,
                        'productCount' => $sub->products()->count(),
                    ];
                    if ($sub->subSubCategories && $sub->subSubCategories->count() > 0) {
                        $subData['subcategories'] = $sub->subSubCategories->map(function ($subsub) {
                            return [
                                'id' => (string)$subsub->id,
                                'name' => $subsub->title,
                                'icon' => $subsub->icon_path,
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
