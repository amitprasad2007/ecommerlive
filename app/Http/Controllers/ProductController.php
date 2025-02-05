<?php

namespace App\Http\Controllers;

use App\Models\VideoProvider;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PhotoProduct;
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
       // dd($request->all());
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
            'tags' => 'required|array',
            'tags.*' => 'string|required',
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
        $data['slug'] = str_replace(' ','-',$data['slug']);
        $status=Product::create($data);
        $newid = $status->id;
        // Handle multiple image paths
        if ($request->has('photo')) {
            $photoPaths = explode(',', $request->photo);
            foreach ($photoPaths as $photoPath) {
                // Trim any whitespace from the path
                $photoPath = trim($photoPath);
                if (!empty($photoPath)) {
                    // Update or create a new PhotoProduct record for each path
                    PhotoProduct::updateOrCreate(
                        ['product_id' => $newid, 'photo_path' => $photoPath],
                        ['photo_path' => $photoPath]
                    );
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
        $category=Category::where('is_parent',1)->get();
        $items=Product::where('id',$id)->get();
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
    public function manageProduct(Request $request, $id=null)
    {
        $product = $id ? Product::find($id) : null;
        $videoproviders =VideoProvider::where('status',1)->get();
        $items=Product::where('id',$id)->get();
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
        $childCategories = $product && $product->cat_id ?
            Category::where('parent_id', $product->cat_id)->get() : [];

        $subChildCategories = $product && $product->child_cat_id ?
            Category::where('sub_cat_id', $product->child_cat_id)->get() : [];

        if ($request->isMethod('post')) {
            $validatedData = $this->validate($request,[
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
            'min_qty'=>"required|integer|min:1",
            'video_provider_id'=>'nullable|exists:video_providers,id',
            'video_link'=>'nullable|string',
            'todays_deal'=>'sometimes|in:1,0',
            'meta_title'=>'string|required',
            'meta_description'=>'string|required',
            'photo' => 'nullable|array',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status'=>'required|in:active,inactive'
        ]);
        $data=$request->all();
        $data['tags'] = implode(',', $request->tags);

        if ($product) {
            $product->update($data);
            $id = $product->id;
            session()->flash('success', 'Product updated successfully.');
        } else {
            $newProduct = Product::create($data);
            $id = $newProduct->id;
            session()->flash('success', 'Product created successfully.');
        }

        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $filename = uniqid() . '.webp';
                $originalPath = 'products/photos/' . $filename;
                $thumbnailPath = 'products/photos/thumbnails/' . $filename;
                $image = Image::make($file)->encode('webp', 90);
                Storage::disk('public')->put($originalPath, $image);
                $thumbnail = Image::make($file)
                    ->resize(240, 240, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 90);
                Storage::disk('public')->put($thumbnailPath, $thumbnail);
                PhotoProduct::updateOrCreate(
                    ['product_id' => $id, 'photo_path' => $filename],
                    ['photo_path' => $filename]
                );
            }
        }



        return redirect()->route('product.index');
        }

        return view('backend.product.edit', compact( 'product', 'brands', 'videoproviders', 'categories', 'childCategories', 'subChildCategories','items' ));
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


    public function photoDelete($photoid)
{
    $photo = PhotoProduct::find($photoid);

    if (!$photo) {
        return response()->json(['success' => false, 'message' => 'Photo not found.'], 404);
    }
    Storage::disk('public')->delete('products/photos/' . $photo->photo_path);
    Storage::disk('public')->delete('products/photos/thumbnails/' . $photo->photo_path);

    $photo->delete();

    return response()->json(['success' => true, 'message' => 'Photo deleted successfully.']);
}


}
