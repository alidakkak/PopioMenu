<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index() {
        return Size::all();
    }

    public function store(Request $request) {
        $size = Size::create($request->all());
        return response()->json([
            "Stored SuccessFully",
            $size
        ]);
    }

    public function update(Request $request, Size $size) {
        $size = $size->update($request->all());
        return response()->json([
            "Updated SuccessFully",
            $size
        ]);
    }

    public function delete(Size $size) {
        $size->delete();
        return response()->json([
            "Deleted SuccessFully",
            $size
        ]);
    }
}
