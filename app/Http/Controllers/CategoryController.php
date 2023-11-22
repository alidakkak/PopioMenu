<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return Category::with(['product' => function($query) {
            $query->orderBy('position');
        }
        , 'product.size'])->orderBy('position')->get();
    }

    public function store(Request $request) {
        $maxPositionInCategory = Category::max('position');
        if (!$maxPositionInCategory) {
            $category = Category::create(array_merge($request->all(), ['position' => 1]));
            return CategoryResource::make($category);
        }
        if (!$request->position) {
            $category = Category::create(array_merge($request->all(), ['position' => $maxPositionInCategory + 1]));
            return CategoryResource::make($category);
        }
        else {
            if ($request->position == $maxPositionInCategory + 1) {
                $category = Category::create($request->all());
                return CategoryResource::make($category);
            }

            if ($request->position > $maxPositionInCategory + 1) {
                $category = Category::create(array_merge($request->all(), ['position' => $maxPositionInCategory + 1]));
                return CategoryResource::make($category);
            }

            if ($request->position == $maxPositionInCategory) {
                $maxCategory = Category::where('position', $maxPositionInCategory)->first();
                $category = Category::create(array_merge($request->all(), ['position' => $maxPositionInCategory]));
                $maxCategory->update([
                    'position' => $maxPositionInCategory + 1
                ]);
                return CategoryResource::make($category);
            }

            if ($request->position < $maxPositionInCategory) {
                $shouldShiftCategory = Category::where('position', '>=', $request->position)
                    ->get();
                foreach ($shouldShiftCategory as $shouldShiftProduct) {
                    $shouldShiftProduct->update([
                        'position' => $shouldShiftProduct['position'] + 1
                    ]);
                }
                $category = Category::create($request->all());
                return CategoryResource::make($category);
            }
        }
    }

    public function update(Request $request, Category $category) {
        $maxPositionInCategory = Category::max('position');
        if (!$request->position) { // checked
            $category->update($request->all());
            return CategoryResource::make($category);
        }
        else {
            if ($request->position == $category->position){ // checked
                $category->update($request->all());
                return CategoryResource::make($category);
            }
            else if ($request->position >= $maxPositionInCategory + 1) { // checked
                $productsShouldShift = Category::where('position' , '>' ,$category->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $category->update(array_merge($request->except('position') , ['position' => $maxPositionInCategory]));
                return CategoryResource::make($category);
            }
            else if ($request->position == $maxPositionInCategory){ //checked

                $productsShouldShift = Category::where('position' , '>' ,$category->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $category->update($request->all());
                return CategoryResource::make($category);
            }

            else if ($request->position < $maxPositionInCategory){

                if ($request->position < $category->position){
                    if ($request->position == $category->position - 1){
                        $productShouldReplace = Category::where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $category->position
                        ]);
                        $category->update([
                            'position' => $request->position
                        ]);
                        return CategoryResource::make($category);
                    }
                    else { //checked
                        $productsShouldShift = Category::whereBetween('position', [$request->position, $category->position - 1])->get();
                        foreach ($productsShouldShift as $productShouldShift) {
                            $productShouldShift->update([
                                'position' => $productShouldShift['position'] + 1
                            ]);
                        }
                        $category->update([
                            'position' => $request->position
                        ]);
                        return CategoryResource::make($category);
                    }
                }
                else {
                    if ($request->position == $category->position + 1){ //checked
                        $productShouldReplace = Category::where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $category->position
                        ]);
                        $category->update([
                            'position' => $request->position
                        ]);
                        return CategoryResource::make($category);
                    }
                    else{
                        $indexToMove = $request->position;
                        $indexMoved = $category->position;
                        $productsShouldShift = Category::where('position' , '>=' ,  $indexToMove)->get();

                        foreach ($productsShouldShift as $poductShould) {
                            $poductShould->update([
                                'position' => $poductShould['position'] + 1
                            ]);
                        }
                        $category->update([
                            'position' => $request->position
                        ]);

                        $productsShouldGoBackShift = Category::where('position' , '>' , $indexMoved)->get();

                        foreach ($productsShouldGoBackShift as $productShouldGoBackShift){
                            $productShouldGoBackShift->update([
                                'position' => $productShouldGoBackShift['position'] - 1
                            ]);
                        }
                        return CategoryResource::make($category);
                    }
                }
            }
        }
    }

    public function show(Category $category) {
        return $category;
    }

    public function delete(Category $category) {
        $shouldShiftCategories = Category::where('position' , '>' , $category->position)->get();
        foreach ($shouldShiftCategories as $shouldShiftCategory){
            $shouldShiftCategory->update([
                'position' => $shouldShiftCategory['position'] - 1
            ]);
        }
        $category->delete();
        return 'One Category Deleted Successfully';
    }
}
