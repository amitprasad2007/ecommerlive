<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banner=Banner::orderBy('id','DESC')->paginate(10);
        return view('backend.banner.index')->with('banners',$banner);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category=Category::where('is_parent',1)->get();
        return view('backend.banner.create')->with('categories',$category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'photo' => 'required|image|max:2048',
            'status'=>'required|in:active,inactive',
            // At least one category must be present
            'cat_id' => 'nullable|integer|exists:categories,id',
            'child_cat_id' => 'nullable|integer|exists:categories,id',
            'sub_child_cat_id' => 'nullable|integer|exists:categories,id',
        ]);

        // Ensure at least one category is present
        if (!$request->cat_id && !$request->child_cat_id && !$request->sub_child_cat_id) {
            return back()->withErrors(['cat_id' => 'At least one category must be selected.'])->withInput();
        }

        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = Banner::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        // Generate categoryurl based on the most specific category present
        if ($request->sub_child_cat_id) {
            $category = \App\Models\Category::find($request->sub_child_cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } elseif ($request->child_cat_id) {
            $category = \App\Models\Category::find($request->child_cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } elseif ($request->cat_id) {
            $category = \App\Models\Category::find($request->cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } else {
            $data['categoryurl'] = null;
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = $filename;
            $thumbnailPath = 'photos/1/Banner/' . $filename;
            $image = Image::make($file)->encode('webp');
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
        $status = Banner::create($data);
        if ($status) {
            request()->session()->flash('success', 'Banner successfully added');
        } else {
            request()->session()->flash('error', 'Error occurred while adding banner');
        }
        return redirect()->route('banner.index');
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
        $banner=Banner::with('category')->findOrFail($id);
        $parent_cats= Category::where('is_parent',1)->get();

        // Get subcategories where parent_id is not 0 and parent has is_parent = 1
        $sub_cats = Category::where('parent_id', '!=', 0)
            ->where('is_parent',0)
            ->where ('sub_cat_id', 0)
            ->get();

        // Get sub-subcategories where sub_cat_id is not 0 and sub-category has parent_id != 0
        $sub_sub_cats = Category::where('sub_cat_id', '!=', 0)            
            ->get();

        return view('backend.banner.edit', compact('banner', 'parent_cats', 'sub_cats', 'sub_sub_cats'));
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
        $banner=Banner::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'photo' => 'required|image|max:2048',
            'status'=>'required|in:active,inactive',
            // At least one category must be present
            'cat_id' => 'nullable|integer|exists:categories,id',
            'child_cat_id' => 'nullable|integer|exists:categories,id',
            'sub_child_cat_id' => 'nullable|integer|exists:categories,id',
        ]);

         // Ensure at least one category is present
         if (!$request->cat_id && !$request->child_cat_id && !$request->sub_child_cat_id) {
            return back()->withErrors(['cat_id' => 'At least one category must be selected.'])->withInput();
        }


        $data=$request->all();

        $slug = Str::slug($request->title);
        $count = Banner::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }
        $data['slug'] = $slug;

        // Generate categoryurl based on the most specific category present
        if ($request->sub_child_cat_id) {
            $category = \App\Models\Category::find($request->sub_child_cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } elseif ($request->child_cat_id) {
            $category = \App\Models\Category::find($request->child_cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } elseif ($request->cat_id) {
            $category = \App\Models\Category::find($request->cat_id);
            $data['categoryurl'] = $category ? $category->slug : null;
        } else {
            $data['categoryurl'] = null;
        }


        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = $filename;
            $thumbnailPath = 'photos/1/Banner/'.$filename;
            $image = Image::make($file)->encode('webp', 90);
            Storage::disk('public')->put($thumbnailPath,  $image);
            $data['photo'] = $originalPath;
        }
        $status=$banner->fill($data)->save();
        if($status){
            request()->session()->flash('success','Banner successfully updated');
        }
        else{
            request()->session()->flash('error','Error occurred while updating banner');
        }
        return redirect()->route('banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner=Banner::findOrFail($id);
        $status=$banner->delete();
        if($status){
            request()->session()->flash('success','Banner successfully deleted');
        }
        else{
            request()->session()->flash('error','Error occurred while deleting banner');
        }
        return redirect()->route('banner.index');
    }
}
