<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand=Brand::orderBy('id','DESC')->paginate(10);
        return view('backend.brand.index')->with('brands',$brand);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brand.create');
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
            'title'=>'string|required',
            'photo' => 'required|image|max:2048',
            'icon_path' => 'required|image|max:2048',
        ]);
        $data=$request->all();
        $slug=Str::slug($request->title);
        $count=Brand::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = $filename;
            $thumbnailPath = 'photos/1/Brands/'.$filename;
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
        $data['slug']=$slug;
        $status=Brand::create($data);
        if($status){
            request()->session()->flash('success','Brand successfully created');
        }
        else{
            request()->session()->flash('error','Error, Please try again');
        }
        return redirect()->route('brand.index');
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
        $brand=Brand::find($id);
        if(!$brand){
            request()->session()->flash('error','Brand not found');
        }
        return view('backend.brand.edit')->with('brand',$brand);
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
        $brand=Brand::find($id);
        $this->validate($request,[
            'title'=>'string|required',
            'photo' => 'required|image|max:2048',
            'icon_path' => 'required|image|max:2048',
        ]);
        $data=$request->all();
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = uniqid() . '.webp';
            $originalPath = $filename;
            $thumbnailPath = 'photos/1/Brands/'.$filename;
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
        $status=$brand->fill($data)->save();
        if($status){
            request()->session()->flash('success','Brand successfully updated');
        }
        else{
            request()->session()->flash('error','Error, Please try again');
        }
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand=Brand::find($id);
        if($brand){
            $status=$brand->delete();
            if($status){
                request()->session()->flash('success','Brand successfully deleted');
            }
            else{
                request()->session()->flash('error','Error, Please try again');
            }
            return redirect()->route('brand.index');
        }
        else{
            request()->session()->flash('error','Brand not found');
            return redirect()->back();
        }
    }
}
