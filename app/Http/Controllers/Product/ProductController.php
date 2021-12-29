<?php

namespace App\Http\Controllers\Product;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Product\ProductImage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $product;

    protected $productImage;

    protected $category;

    public function __construct(Product $product, ProductImage $productImage, Category $category)
    {
        $this->product = $product;
        $this->productImage = $productImage;
        $this->category = $category;
    }

    public function getAllProducts()
    {
        return response()->json([
            'products' => $this->product->all()
        ]);
    }

    public function getProductDetail($id)
    {
        return response()->json([
            'product' => $this->product->with(['productImages', 'category'])->findOrFail($id),
            'categories' => $this->category->all()
        ]);
    }

    public function add(AddProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity
            ];

            $product = $this->product->create($data);

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $name = Helper::uploadFileHelper(ProductImage::PRODUCT_IMAGE_DISK, $file);
                    $imgData[] = $name;
                    $productImage = $product->productImages()->create([
                        'product_id' => $product->id,
                        'path' => $name,
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'product' => $product,
            'paths' => $product->productImages
        ]);
    }

    public function updateProduct(UpdateProductRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity
            ];

            $product = $this->product->findOrFail($id);
            $product->update($data);

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $name = Helper::uploadFileHelper(ProductImage::PRODUCT_IMAGE_DISK, $file);
                    $productImage = $product->productImages()->create([
                        'product_id' => $id,
                        'path' => $name,
                    ]);
                }
            }

            if (count($request->deleted_files) > 0) {
                $imageDeleted = $this->productImage->find($request->deleted_files);
                $imagePaths = $imageDeleted->map(function ($image) {
                    return $image->path;
                });

                Helper::deleteMultipleFiles($imagePaths);
                Helper::destroyMultipleRecord($imageDeleted);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'product' => $product,
            'paths' => $product->productImages
        ]);
    }

    public function deleteProduct($id)
    {
        try {
            DB::beginTransaction();

            $product = $this->product->find($id);
            $product->delete();

            $imageFilesBuilder = $this->productImage->where('product_id', $id);

            $imagePaths = $imageFilesBuilder->get()->map(function ($image) {
                return $image->path;
            });

            Helper::deleteMultipleFiles($imagePaths);
            $imageDeleted = $imageFilesBuilder->delete();

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
}
