<?php

namespace App\Http\Controllers\Category;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddCategoryRequest;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected $category;

    protected $product;

    public function __construct(Category $category, Product $product)
    {
        $this->category = $category;
        $this->product = $product;
    }

    public function addCategory(AddCategoryRequest $request)
    {
        $category = $this->category->create(['name' => $request->name]);

        return response()->json([
            'category' => $category
        ]);
    }

    public function updateCategory(AddCategoryRequest $request, $id)
    {

        $category = $this->category->findOrFail($id);

        $category->update(['name' => $request->name]);

        return response()->json([
            'category' => $category
        ]);
    }

    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();

            $categoryBuilder = $this->category->find($id);

            $productsBuilder = $this->product->where('category_id', $id);

            $products = $productsBuilder->get();

            $images = $products->map(function ($product, $key) {
                return $product->productImages;
            })->flatten();

            $imagePaths = $images->map(function ($image, $key) {
                return $image->path;
            });

            Helper::deleteMultipleFiles($imagePaths);

            foreach ($products as $product) {
                $product->productImages()->where('product_id', $product->id)->delete();
            }

            $productsBuilder->delete();

            $categoryBuilder->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'message' => 'Delete successfully',
            'status' => true
        ]);;
    }

    public function viewCategory()
    {
        $category = $this->category->all();
        return response()->json($category);
    }
}
