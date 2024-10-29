<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //
    public function getcategorieslist(){
        $categories = Category::where('is_parent', '!=', 0)->with(['products','products.photoproduct'])->get();
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
            ->with(['subCategories', 'subCategories.subSubCategories'])
            ->get();
        return response()->json(['categories' => $categories]);
    }
}
