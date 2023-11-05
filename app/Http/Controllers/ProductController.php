<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return Product::with('size', 'category')->get();
    }

    public function store(Request $request) {
        $product = Product::create($request->all());
        return response()->json([
            "Stored SuccessFully",
            $product
        ]);
    }

    public function update(Request $request, Product $product) {
        $product = $product->update($request->all());
        return response()->json([
            "Updated SuccessFully",
            $product
        ]);
    }

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
