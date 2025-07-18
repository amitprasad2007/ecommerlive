<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductController extends Controller
{
    //
    public function getproductlist(){
        $products = Product::with('photoproduct')-> get();
        return response()->json(['products' => $products]);
    }

    public function getproductbyid($slug){
        $product = Product::getProductBySlug($slug);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Calculate rating and review count
        $reviews = $product->getReview;
        $reviewCount = $reviews ? $reviews->count() : 0;
        $rating = $reviewCount > 0 ? round($reviews->avg('rate'), 1) : null;

        // Images array
        $images = $product->photoproduct->map(function($photo) {
            // You may need to adjust the path as per your storage setup
            return asset('storage/products/photos/thumbnails/'.$photo->photo_path);
        })->toArray();

        // Brand name
        $brand = $product->brand ? $product->brand->title : null;

        // Stock logic
        $inStock = $product->stock > 0;
        $stockCount = $product->stock;

        // Price and original price
        $price = $product->price;
        $originalPrice = $product->purchase_price ?? null;

        // Response structure
        $response = [
            'id' => (string)$product->id,
            'name' => $product->title,
            'brand' => $brand,
            'cat_id' => $product->cat_id,
            'sub_sub_cat_id' => $product->sub_child_cat_id,
            'sub_cat_id' => $product->child_cat_id,
            'slug' => $product->slug,
            'price' => $price,
            'originalPrice' => $originalPrice,
            'rating' => $rating ?? 4.8, // fallback to static if not available
            'reviewCount' => $reviewCount ?? 127, // fallback to static if not available
            'inStock' => $inStock,
            'stockCount' => $stockCount,
            'sku' => $product->sku,
            'images' => $images,
            'shortDescription' => $product->meta_description ?? 'Professional-grade frame cutting machine with precision controls and advanced safety features.',
            'fullDescription' => $product->description ?? 'The Professional Frame Cutter Pro 3000 represents the pinnacle of framing technology...',
            'specifications' => [
                'Cutting Capacity' => '48 inches',
                'Motor Power' => '3 HP',
                'Weight' => '450 lbs',
                'Dimensions' => '60" x 36" x 42"',
                'Power Requirements' => '220V, 15A',
                'Warranty' => '3 Years',
            ],
            'features' => [
                'Precision digital measurement system',
                'Automatic dust collection',
                'Emergency stop safety system',
                'LED work lighting',
                'Adjustable cutting angles',
            ],
        ];

        return response()->json($response);
    }

    public function getproductbycategoryid($category){
        $category = Category::where('slug', $category)->first();
        $products = Product::with('photoproduct')-> where('cat_id', $category->id)->get();
       // dd($products);
        $result = $products->map(function($product) {
            $photo = $product->photoproduct->first();
            return [
                'id' => $product->id,
                'name' => $product->title,
                'slug'=> $product->slug,
                'image' => $photo ? asset('storage/products/photos/thumbnails/'.$photo->photo_path) : null,
                'price' => $product->price,
                'originalPrice' => $product->original_price ?? null,
                'rating' => $product->rating ?? 4,
                'reviewCount' => $product->review_count ?? 15,
                'brand' => $product->brand->title ?? null,
                'isBestSeller' => $product->is_best_seller ?? false,
                'isNew' => $product->created_at >= now()->subMonth(),
            ];
        });

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        $result_12 = $recent_products->map(function($recent_product) {
            $photo_12 = $recent_product->photoproduct->first();
            return [
                'id' => $recent_product->id,
                'name' => $recent_product->title,
                'slug'=> $recent_product->slug,
                'image' => $photo_12 ? asset('storage/products/photos/thumbnails/'.$photo_12->photo_path) : null,
                'price' => $recent_product->price,
                'originalPrice' => $recent_product->original_price ?? null,
                'rating' => $recent_product->rating ?? 4,
                'reviewCount' => $recent_product->review_count ?? 15,
                'brand' => $recent_product->brand->title ?? null,
                'isBestSeller' => $recent_product->is_best_seller ?? false,
                'isNew' => $recent_product->created_at >= now()->subMonth(),
            ];
        });
        return response()->json($result);
    }

    public function getis_featuredproduct() {
        $products = Product::with(['photoproduct', 'brand'])
            ->where('is_featured', 1)
            ->paginate(9);

        $result = $products->map(function($product) {
            $photo = $product->photoproduct->first();
            return [
                'id' => $product->id,
                'name' => $product->title,
                'slug'=> $product->slug,
                'image' => $photo ? asset('storage/products/photos/thumbnails/'.$photo->photo_path) : null,
                'price' => $product->price,
                'originalPrice' => $product->original_price ?? null,
                'rating' => $product->rating ?? 4,
                'reviewCount' => $product->review_count ?? 15,
                'brand' => $product->brand->title ?? null,
                'isBestSeller' => $product->is_best_seller ?? false,
                'isNew' => $product->created_at >= now()->subMonth(),
            ];
        });

        return response()->json($result);
    }

    public function getproductSearch(Request $request){
        $recent_products = $this->apiRecentProduct();

        $searchTerm = $request->search ? '%' . $request->search . '%' : null;
        $catslug = $request->cat_id;
        $catId = null;

        if ($catslug) {
            $category = Category::where('slug', $catslug)->first();
            $catId = $category ? $category->id : null;
        }

        $query1 = Product::query();
        if ($catId) {
            $query1->where('cat_id', $catId);
        }
        if ($searchTerm!="%''%") {
            $query1->where(function($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('slug', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('summary', 'like', $searchTerm);
                    //   ->orWhere('price', 'like', $searchTerm);
            });
        }
        $query1->with('photoproduct')->orderBy('id', 'DESC');

        $query2 = Product::query();
        if ($searchTerm!=="%''%") {
            $query2->where(function($query) use ($searchTerm) {
                $query->where('title', 'like', $searchTerm)
                      ->orWhere('slug', 'like', $searchTerm)
                    //   ->orWhere('description', 'like', $searchTerm)
                      ->orWhere('summary', 'like', $searchTerm);
                    //   ->orWhere('price', 'like', $searchTerm);
            });
        }
        if ($catId) {
            $query2->where('cat_id', '!=', $catId);
        }
        $query2->with('photoproduct')->orderBy('id', 'DESC');

        $query3 = Product::query();
        if ($catId) {
            $query3->where('cat_id', $catId);
        }
        $query3->with('photoproduct')->orderBy('id', 'DESC');

        $products = $query1->union($query2)->union($query3)->distinct()->paginate(9);

        // Get the product IDs from the search results
        $productIds = $products->pluck('id');

        // Retrieve brands associated with these products
        $brands = $this->apiBrand($productIds);

        $result = $products->map(function($product) {
            $photo = $product->photoproduct->first();
            return [
                'id' => $product->id,
                'name' => $product->title,
                'slug'=> $product->slug,
                'image' => $photo ? asset('storage/products/photos/thumbnails/'.$photo->photo_path) : null,
                'price' => $product->price,
                'originalPrice' => $product->original_price ?? null,
                'rating' => $product->rating ?? 4,
                'reviewCount' => $product->review_count ?? 15,
                'brand' => $product->brand->title ?? null,
                'isBestSeller' => $product->is_best_seller ?? false,
                'isNew' => $product->created_at >= now()->subMonth(),
            ];
        });

        // Return both products and brands in the response
        return response()->json($result);
    }

    protected function apiBrand($productIds){

        return Brand::whereHas('products', function($query) use ($productIds) {
            $query->whereIn('id', $productIds);
        })->get();
    }

    protected function apiRecentProduct(){

        return Product::with('photoproduct')
                            ->where('status', 'active')
                            ->orderBy('id', 'DESC')
                            ->limit(4)
                            ->get();
    }

    public function getCateidProduct(Request $request){
        $recent_products =  $recent_products = $this->apiRecentProduct();

        $products = Product::with('photoproduct')-> where('cat_id', $request->cat_id)->paginate(9);
        // Get the product IDs from the search results
        $productIds = $products->pluck('id');

        // Retrieve brands associated with these products
        $brands = $this->apiBrand($productIds);
        // Return both products and brands in the response
        return response()->json([
            'product' => $products,
            'brands' => $brands,
            'recent_products' => $recent_products
        ]);
    }

    public function getSubCateidProduct(Request $request){
        $recent_products =  $recent_products = $this->apiRecentProduct();

        $products = Product::with('photoproduct')-> where('child_cat_id', $request->cat_id)->paginate(9);
        // Get the product IDs from the search results
        $productIds = $products->pluck('id');

        // Retrieve brands associated with these products
        $brands = $this->apiBrand($productIds);
        // Return both products and brands in the response
        return response()->json([
            'product' => $products,
            'brands' => $brands,
            'recent_products' => $recent_products
        ]);
    }

    public function getSubSubCateidProduct(Request $request){
        $recent_products =  $recent_products = $this->apiRecentProduct();

        $products = Product::with('photoproduct')-> where('sub_child_cat_id', $request->cat_id)->paginate(9);
        // Get the product IDs from the search results
        $productIds = $products->pluck('id');

        // Retrieve brands associated with these products
        $brands = $this->apiBrand($productIds);
        // Return both products and brands in the response
        return response()->json([
            'product' => $products,
            'brands' => $brands,
            'recent_products' => $recent_products
        ]);
    }
}
