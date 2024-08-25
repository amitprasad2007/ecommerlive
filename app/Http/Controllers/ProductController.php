<?php

namespace App\Http\Controllers;

use App\Models\VideoProvider;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
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
            'slug'=>'string|required',
            'sku'=>'string|required',
            'description'=>'string|required',
            'photo'=>'required|image|mimes:jpeg,png',
            'stock'=>"required|numeric",
            'min_qty'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'price'=>'required|numeric',
            'shipping_cost'=>'required|numeric',
            'tax'=>'required|numeric',
            'discount'=>'nullable|numeric',
            'meta_title'=>'string|required',
            'meta_description'=>'string|required',
            'pdf' => 'required|mimes:pdf|max:10000',
        ]);
        // Get the uploaded file
        $file = $request->file('photo');
          // Check if the file is valid
          if (!$file->isValid()) {
            return response()->json(['message' => 'Invalid file upload.'], 400);
        }
        // Create an instance of the image
        try {
            $image = Image::make($file->getRealPath());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Image source not readable.'], 400);
        }
        $webpImage = $image->encode('webp');
        $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
        $filePath = 'photos/1/Products/' . $fileName;
        Storage::disk('public')->put($filePath, $webpImage);

        $filepdf = $request->file('pdf');
        if (!$filepdf->isValid()) {
            return response()->json(['message' => 'Invalid file upload.'], 400);
        }
        $fileNamepdf = pathinfo($filepdf->getClientOriginalName(), PATHINFO_FILENAME) . '.pdf';
        $filePathpdf = 'files/pdf/' . $fileNamepdf;
        Storage::disk('public')->put($filePathpdf, file_get_contents($filepdf));

        $data=$request->all();
        //  dd($data);
//        $slug=Str::slug($request->title);
//        $count=Product::where('slug',$slug)->count();
//        if($count>0){
//            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
//        }
//        $data['slug']=$slug;
//        $data['is_featured']=$request->input('is_featured',0);
        $data['photo']      = '/storage/'.$filePath;
        $data['pdf']      = '/storage/'.$filePathpdf;
        $status=Product::create($data);
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
       // dd($product);
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
