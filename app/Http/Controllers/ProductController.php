<?php

namespace App\Http\Controllers;

use App\Models\VideoProvider;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PhotoProduct; // Import the PhotoProduct model
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::getAllProduct();

        return view('backend.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand=Brand::get();
        $category=Category::where('is_parent',1)->get();
        $videoproviders =VideoProvider::where('status',1)->get();
        // return $category;
        return view('backend.product.create')->with('categories',$category)->with('brands',$brand)->with('videoproviders',$videoproviders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     
        $validatedData =  $this->validate($request,[
            'title'=>'string|required',
            'description'=>'string|required',
            'is_featured'=>'sometimes|in:1',
            'sku'=>'string|required',
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'sub_child_cat_id'=>'nullable|exists:categories,id',
            'slug'=>'string|required',
            'price'=>'required|numeric',
            'brand_id'=>'required|exists:brands,id',
            'purchase_price'=>'required|numeric',
            'shipping_type'=>'required|in:flat,percent',
            'shipping_cost'=>'required|numeric',
            'tags' => 'required|array',
            'tags.*' => 'string|required',
            'tax'=>'required|numeric',
            'tax_type'=>'required|in:percent,flat',
            'discount'=>'required|numeric',
            'discount_type'=>'required|in:percent,flat',
            'stock'=>"required|integer|min:0",
            'unit'=>'required|string',
            'min_qty'=>"required|integer|min:1",
            'video_provider_id'=>'nullable|exists:video_providers,id',
            'video_link'=>'nullable|string',
            'todays_deal'=>'sometimes|in:1,0',
            'photo'=>'string|required',
            'meta_title'=>'string|required',
            'meta_description'=>'string|required',
            'status'=>'required|in:active,inactive'
            
        ]);
        
        $data=$request->all();
        // dd($data);
        $data['tags'] = implode(',', $request->tags);
        $status=Product::create($data);
        $newid = $status->id;
        // Handle multiple image paths
        if ($request->has('photo')) {
            $photoPaths = explode(',', $request->photo);
            foreach ($photoPaths as $photoPath) {
                // Trim any whitespace from the path
                $photoPath = trim($photoPath);
                if (!empty($photoPath)) {
                    // Create a new PhotoProduct record for each path
                    PhotoProduct::create([
                        'product_id' => $newid,
                        'photo_path' => $photoPath
                    ]);
                }
            }
        }
        if($status){
            request()->session()->flash('success','Product Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');

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
        $brand=Brand::get();
        $product=Product::findOrFail($id);
        $videoproviders =VideoProvider::where('status',1)->get();
        //dd($product);
        $category=Category::where('is_parent',1)->get();
        $items=Product::where('id',$id)->get();
        // return $items;
        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('videoproviders',$videoproviders)
                    ->with('categories',$category)->with('items',$items);
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
        $product=Product::findOrFail($id);
       // dd($product);
        $validatedData = $this->validate($request,[
            'title'=>'string|required',
            'description'=>'string|nullable',
            'photo'=>'string|required',
            'stock'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'brand_id'=>'nullable|exists:brands,id',
            'status'=>'required|in:active,inactive',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric',
            'slug'=>'string|required',
            'sku'=>'string|required',
            'min_qty'=>"required|numeric",
            'shipping_cost'=>'required|numeric',
            'tax'=>'required|numeric',
            'meta_title'=>'string|required',
            'meta_description'=>'string|required'
        ]);
   //dd( $validatedData );
        $data=$request->all();
       // $data['is_featured']=$request->input('is_featured',0);
//        $size=$request->input('size');
//        if($size){
//            $data['size']=implode(',',$size);
//        }
//        else{
//            $data['size']='';
//        }
//        // return $data;
        $status=$product->fill($data)->save();
        if($status){
            request()->session()->flash('success','Product Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $status=$product->delete();

        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }

    public function bulkDelete(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        if (!empty($productIds)) {
            Product::whereIn('id', $productIds)->delete();
            return redirect()->route('product.index')->with('success', 'Selected products deleted successfully!');
        }
        return redirect()->route('product.index')->with('error', 'No products selected.');
    }
    public function excelupload()
    {
        return view('backend.product.bulkupload');
    }
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) < count($header)) {
                continue; // Skip rows with insufficient columns
            }
            $rowData = array_combine($header, $row);
           // dd($rowData);
            // Create a new product
            Product::create([
                'title' => $rowData['title'],
                'slug' => $rowData['slug'],
                'sku' => $rowData['sku'],
                'description' => $rowData['description'],
                'stock' => $rowData['stock'],
                'status' => $rowData['status'],
                'price' => $rowData['price'],
                'discount' => $rowData['discount'],
                'is_featured' => $rowData['is_featured'],
                'todays_deal' => $rowData['todays_deal'],
                'min_qty' => $rowData['min_qty'],
                'tax' => $rowData['tax'],
                'shipping_cost' => $rowData['shipping_cost'],
                'purchase_price' => $rowData['purchase_price'],
                'tags' => $rowData['tags']
            ]);
        }
        return redirect()->route('product.index')->with('success', 'Products uploaded successfully!');
    }


}
