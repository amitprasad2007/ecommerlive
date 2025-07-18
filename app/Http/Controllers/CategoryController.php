<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Category;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // $category=Category::getAllCategory();
        $category = Category::where('is_parent', '!=', 0)
        ->paginate(10);
        return view('backend.category.index')->with('categories',$category);
    }

       /**
      * Sub Category
      */
      public function subCategory()
      {
        // dd('werwerwerwerwr');
        $categories = Category::where('parent_id', '!=', 0)
        ->where(function ($query) {
            $query->where('sub_cat_id', '=', 0)
                  ->orWhereNull('sub_cat_id');
        })
        ->paginate(10); // Change to paginate
          // Check if query returns results
          // Return the view with categories
          return view('backend.category.subcategory')->with('categories', $categories);
      }


    /**
     * Sub SubCategory
    */
    public function subsubcategory()
    {
        $category=Category::where('sub_cat_id', '!=', '0')->paginate(10);

        return view('backend.category.subsubcategory')->with('categories',$category);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_cats=Category::where('is_parent',1)->orderBy('title','ASC')->get();
        // $sub_cats = Category::where('parent_id', '!=', null)->get();
        // return view('backend.category.create')->with('parent_cats',$parent_cats);
        $sub_cats = Category::where('parent_id', '!=', null)->get();
        return view('backend.category.create', compact('parent_cats', 'sub_cats'));
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subcreate()
    {
        $parent_cats=Category::where('is_parent',1)->orderBy('title','ASC')->get();
        $sub_cats = Category::where('parent_id', '!=', null)->get();
        return view('backend.category.subcreate', compact('parent_cats', 'sub_cats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subsubcreate()
    {
        $parent_cats=Category::where('is_parent',1)->orderBy('title','ASC')->get();
        $sub_cats = Category::where('parent_id', '!=', null)->get();
        return view('backend.category.subsubcreate', compact('parent_cats', 'sub_cats'));
    }

    public function getSubCategories($parent_id)
    {
        $subCategories = Category::where('parent_id', $parent_id)->get();
        return response()->json($subCategories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request)
    {

        // return $request->all();
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|nullable',
           'photo' => 'required|image|max:2048',  
            'icon_path' => 'required|image|max:2048',
            'status'=>'required|in:active,inactive',
            'is_parent'=>'sometimes|in:1',
            'parent_id'=>'nullable|exists:categories,id',
        ]);
        if($request->input('subsubcat') ==1 || $request->input('subsubcat') !='' ){
            $this->validate($request,[
                'parent_id' => 'required|integer|exists:categories,id',
                'sub_cat_id' => 'required|integer|exists:categories,id',
            ]);
        }
         if ($request->parent_id != "") {
            $this->validate($request,[
                'parent_id' => 'required|integer|exists:categories,id',
            ]);
        }
        $data= $request->all();
        $slug=Str::slug($request->title);
        $count=Category::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_parent']=$request->input('is_parent',0);
        $data['sub_cat_id'] = $request->sub_cat_id ? $request->sub_cat_id : null;
         // Handle photo upload
         if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = 'categories/' . $filename;
            $thumbnailPath = 'categories/thumbnails/categories/' . $filename;    
            $image = Image::make($file)->encode('webp', 90);
            Storage::disk('public')->put($originalPath, $image);
    
            $thumbnail = Image::make($file)
                ->resize(120, 120, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 90);
            Storage::disk('public')->put($thumbnailPath, $thumbnail); 
            $data['photo'] = $originalPath;
        }
    
        // Handle icon_path upload
        if ($request->hasFile('icon_path')) {
            $icon = $request->file('icon_path');
            $filename = uniqid() . '.webp';
            $originalPath = 'categories/icons/' . $filename;
            $image = Image::make($icon)->encode('webp', 90);
            Storage::disk('public')->put($originalPath, $image); 
            $data['icon_path'] = $originalPath;
        }

        // return $data;
        $status=Category::create($data);
        if($status){
            request()->session()->flash('success','Category successfully added');
        }
        else{
            request()->session()->flash('error','Error occurred, Please try again!');
        }
        return redirect()->route('category.index');


    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parent_cats= Category::where('is_parent',1)->get();
        $category   = Category::findOrFail($id);
        $sub_cats   = Category::where('sub_cat_id', '!=', null)->get();
        //return view('backend.category.edit')->with('category',$category)->with('parent_cats',$parent_cats);
        return view('backend.category.edit', compact('category', 'parent_cats', 'sub_cats'));
    }


  /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subedit($id)
    {
        $parent_cats= Category::where('is_parent',1)->get();
        $category   = Category::findOrFail($id);
        $sub_cats   = Category::where('sub_cat_id', '!=', null)->get();
        //return view('backend.category.edit')->with('category',$category)->with('parent_cats',$parent_cats);
        return view('backend.category.subedit', compact('category', 'parent_cats', 'sub_cats'));
    }

      /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subsubedit($id)
    {
        $parent_cats= Category::where('is_parent',1)->get();
        $category   = Category::findOrFail($id);
        $sub_cats   = Category::where('sub_cat_id', '!=', null)->get();
        //return view('backend.category.edit')->with('category',$category)->with('parent_cats',$parent_cats);
        return view('backend.category.subsubedit', compact('category', 'parent_cats', 'sub_cats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
    
        $this->validate($request, [
            'title' => 'string|required',
            'summary' => 'string|nullable',
            'photo' => 'nullable|image|max:2048',  
            'icon_path' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
    
        if ($request->input('subsubcat') == 1 || $request->input('subsubcat') != null) {
            $this->validate($request, [
                'parent_id' => 'required|integer|exists:categories,id',
                'sub_cat_id' => 'required|integer|exists:categories,id',
            ]);
        }
    
        if ($request->is_parent != 1) {
            $this->validate($request, [
                'parent_id' => 'required|integer|exists:categories,id',
            ]);
        }
    
        $data = $request->all();
        $data['is_parent'] = $request->input('is_parent', 0);
        $data['parent_id'] = $request->parent_id ?: 0;
        $data['sub_cat_id'] = $request->sub_cat_id ?: 0;
    
        // Add slug generation
        $data['slug'] = Str::slug($request->title); 
        $slug_count = Category::where('slug', $data['slug'])->where('id', '!=', $id)->count();
        if ($slug_count > 0) {
            $data['slug'] = $data['slug'] . '-' . $id;
        }
    
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = 'categories/' . $filename;
            $thumbnailPath = 'categories/thumbnails/categories/' . $filename;    
            $image = Image::make($file)->encode('webp', 90);
            Storage::disk('public')->put($originalPath, $image);
    
            $thumbnail = Image::make($file)
                ->resize(120, 120, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 90);
            Storage::disk('public')->put($thumbnailPath, $thumbnail); 
            $data['photo'] = $originalPath;
        }
    
        // Handle icon_path upload
        if ($request->hasFile('icon_path')) {
            $icon = $request->file('icon_path');
            $filename = uniqid() . '.webp';
            $originalPath = 'categories/icons/' . $filename;
            $image = Image::make($icon)->encode('webp', 90);
            Storage::disk('public')->put($originalPath, $image); 
            $data['icon_path'] = $originalPath;
        } 
        $status = $category->fill($data)->save();
    
        if ($status) { 
            Cache::forget('categories'); 
            request()->session()->flash('success', 'Category successfully updated');
        } else {
            request()->session()->flash('error', 'Error occurred, Please try again!');
        }
    
        return redirect()->route('category.index');
    }
  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category=Category::findOrFail($id);
        $child_cat_id=Category::where('parent_id',$id)->pluck('id');
        // return $child_cat_id;
        $status=$category->delete();

        if($status){
            if(count($child_cat_id)>0){
                Category::shiftChild($child_cat_id);
            }
            request()->session()->flash('success','Category successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting category');
        }
        return redirect()->route('category.index');
    }

    public function getChildByParent(Request $request){
        $category=Category::findOrFail($request->id);
        $child_cat=Category::getChildByParentID($request->id);
        if(count($child_cat)<=0){
            return response()->json(['status'=>false,'msg'=>'','data'=>null]);
        }
        else{
            return response()->json(['status'=>true,'msg'=>'','data'=>$child_cat]);
        }
    }

    public function getSubChildCategories($id)
    {
    $sub_child_categories = Category::getSubChildByParentID($id);
    if ($sub_child_categories->count() > 0) {
        return response()->json(['status' => true, 'data' => $sub_child_categories]);
    } else {
        return response()->json(['status' => false, 'data' => null]);
    }
    }


}
