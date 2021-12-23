<?php

namespace App\Http\Controllers\Product;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product\Product;
use App\Models\Product\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Throw_;

class ProductController extends Controller
{
    protected $product;

    protected $productImage;

    public function __construct(Product $product, ProductImage $productImage)
    {
        $this->product = $product;
        $this->productImage = $productImage;
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
                    $imgData[] = $name;
                    $productImage = $product->productImages()->create([
                        'product_id' => $product->id,
                        'path' => $name,
                    ]);
                }
            }

            if (count($request->deleted_files) > 0) {

                foreach ($request->deleted_files as $key => $value) {
                    $imageDeleted = $this->productImage->find($value);
                    // Helper::deleteFileByFullPathHelper($imageDeleted->path);
                    $imagePaths[] = $imageDeleted->path;
                    $imageDeleted->delete();
                }

                Helper::deleteMultipleFiles($imagePaths);
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

            foreach ($imageFilesBuilder->get() as $image) {
                $imagePaths[] = $image->path;
            }

            Helper::deleteMultipleFiles($imagePaths);
            $imageDeleted = $imageFilesBuilder->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return "xoa thanh cong";
    }
}
