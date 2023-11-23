<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return Product::with('size', 'category')->orderBy('position')->get();
    }

    public function isVisible() {
        return Product::with('size', 'category')->where('visibility', true)->orderBy('position')->get();
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
        $maxPositionInCategory = Product::where('category_id' , $request->category_id)->max('position');
        if (!$maxPositionInCategory){
            $product = Product::create(array_merge($request->all() , ['position' => 1]));
            $this->createSizes($product, $request->sizes);
            return ProductResource::make($product);
        }
        if (!$request->position) {
            $product = Product::create(array_merge($request->all() , ['position' => $maxPositionInCategory + 1]));
            $this->createSizes($product, $request->sizes);
            return ProductResource::make($product);
        }else {
            if ($request->position == $maxPositionInCategory + 1) {
                $product = Product::create($request->all());
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }

            if ($request->position > $maxPositionInCategory + 1){
                $product = Product::create(array_merge($request->all() , ['position' => $maxPositionInCategory + 1]));
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }

            if ($request->position == $maxPositionInCategory){
                $maxProduct = Product::where('category_id' , $request->category_id)->where('position' , $maxPositionInCategory)->first();
                $product = Product::create(array_merge($request->all() , ['position' => $maxPositionInCategory]));
                $maxProduct->update([
                    'position' => $maxPositionInCategory + 1
                ]);
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }

            if ($request->position < $maxPositionInCategory){
                $shouldShiftProducts = Product::where('category_id' , $request->category_id)->
                where('position' , '>=' , $request->position)->get();
                foreach ($shouldShiftProducts as $shouldShiftProduct){
                    $shouldShiftProduct->update([
                        'position' => $shouldShiftProduct['position'] + 1
                    ]);
                }
                $product = Product::create($request->all());
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }
        }
}

    private function createSizes($product, $sizes) {
        foreach ($sizes as $size) {
            Size::create([
                'product_id' => $product->id,
                'size' => $size['size'],
                'size_ar' => $size['size_ar'],
                'price' => $size['price'],
                'calories' => $size['calories']
            ]);
        }
    }


    public function update(Request $request, Product $product) {
        $maxPositionInCategory = Product::where('category_id' , $product->category_id)->max('position');
        if (!$request->position) { // checked
            $product->update($request->all());
            $this->createSizes($product, $request->sizes);
            return ProductResource::make($product);
        }
        else {
            if ($request->position == $product->position){ // checked
                $product->update($request->all());
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }
            else if ($request->position >= $maxPositionInCategory + 1) { // checked
                $productsShouldShift = Product::where('category_id' , $product->category_id)->
                where('position' , '>' ,$product->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $product->update(array_merge($request->except('position') , ['position' => $maxPositionInCategory]));
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }
            else if ($request->position == $maxPositionInCategory){ //checked

                $productsShouldShift = Product::where('category_id' , $product->category_id)->
                where('position' , '>' ,$product->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $product->update($request->all());
                $this->createSizes($product, $request->sizes);
                return ProductResource::make($product);
            }

            else if ($request->position < $maxPositionInCategory){

                if ($request->position < $product->position){
                    if ($request->position == $product->position - 1){
                        $productShouldReplace = Product::where('category_id' , $product->category_id)->
                        where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $product->position
                        ]);
                        $product->update([
                            'position' => $request->position
                        ]);
                        $this->createSizes($product, $request->sizes);
                        return ProductResource::make($product);
                    }
                    else { //checked
                        $productsShouldShift = Product::where('category_id' , $product->category_id)->
                        whereBetween('position', [$request->position, $product->position - 1])->get();
                        foreach ($productsShouldShift as $productShouldShift) {
                            $productShouldShift->update([
                                'position' => $productShouldShift['position'] + 1
                            ]);
                        }
                        $product->update([
                            'position' => $request->position
                        ]);
                        $this->createSizes($product, $request->sizes);
                        return ProductResource::make($product);
                    }
                }
                else {
                    if ($request->position == $product->position + 1){ //checked
                        $productShouldReplace = Product::where('category_id' , $product->category_id)->
                        where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $product->position
                        ]);
                        $product->update([
                            'position' => $request->position
                        ]);
                        $this->createSizes($product, $request->sizes);
                        return ProductResource::make($product);
                    }
                    else{
                        $indexToMove = $request->position;
                        $indexMoved = $product->position;
                        $productsShouldShift = Product::where('category_id' , $product->category_id)->
                        where('position' , '>=' ,  $indexToMove)->get();

                        foreach ($productsShouldShift as $poductShould) {
                            $poductShould->update([
                                'position' => $poductShould['position'] + 1
                            ]);
                        }
                        $product->update([
                            'position' => $request->position
                        ]);

                        $productsShouldGoBackShift = Product::where('category_id' , $product->category_id)->
                        where('position' , '>' , $indexMoved)->get();

                        foreach ($productsShouldGoBackShift as $productShouldGoBackShift){
                            $productShouldGoBackShift->update([
                                'position' => $productShouldGoBackShift['position'] - 1
                            ]);
                        }
                        $this->createSizes($product, $request->sizes);
                        return ProductResource::make($product);
                    }
                }
            }
        }
    }



    public function show(Product $product) {
        return $product;
    }


    public function switchProduct(Product $product) {
        $product->update([
            'visibility' => ! boolval($product ->visibility)
        ]);
        return 'Updated SuccessFully';
    }

    public function delete(Product $product) {
        $shouldShiftProducts = Product::where('position' , '>' , $product->position)->get();
        foreach ($shouldShiftProducts as $shouldShiftProduct){
            $shouldShiftProduct->update([
                'position' => $shouldShiftProduct['position'] - 1
            ]);
        }
        $product->delete();
        return 'One Product Deleted Successfully';
    }
}
