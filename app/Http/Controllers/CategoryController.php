<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return Category::with('product', 'product.size')->get();
    }

    public function store(Request $request) {
       $category = Category::create($request->all());
        return response()->json([
           "Stored SuccessFully",
            $category
        ]);
    }

    public function update(Request $request, Category $category) {
        $category = $category->update($request->all());
        return response()->json([
            "Updated SuccessFully",
            $category
        ]);
    }

    public function show(Category $category) {
        return $category;
    }

    public function delete(Category $category) {
        $category->delete();
        return response()->json([
            "Deleted SuccessFully",
            $category
        ]);
    }
}
