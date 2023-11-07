<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return Product::with('size', 'category')->get();
    }

    public function countCP() {
        $category = Category::count();
        $product = Product::count();
        return response([
           "category" => $category,
           "product"  => $product
        ]);
    }

    public function store(Request $request) {
        $product = Product::create($request->all());
        $sizes = $request->sizes;

        foreach ($sizes as $size) {
            Size::create([
                'product_id' => $product->id,
                'size' => $size['size'],
                'size_ar' => $size['size_ar'],
                'price' => $size['price'],
            ]);
        }

        return response()->json([
            "Stored Successfully",
            $product,
            $sizes
        ]);
    }

    public function update(Request $request, Product $product) {
        $product->fill($request->all());
        $sizes = $request->sizes;

        foreach ($sizes as $size) {
            $size = Size::where('product_id',$product->id)->first();
            $size->fill([
                'size' => $size['size'],
                'size_ar' => $size['size_ar'],
                'price' => $size['price'],
            ]);
        }

        return response()->json([
            "Updated Successfully",
            $product,
            $sizes
        ]);
    }



//    public function update(Request $request, Product $product) {
//        $product = $product->update($request->all());
//        return response()->json([
//            "Updated SuccessFully",
//            $product
//        ]);
//    }

    public function show(Product $product) {
        return $product;
    }

    public function delete(Product $product) {
        $product->delete();
        return response()->json([
            "Deleted SuccessFully",
            $product
        ]);
    }
}
