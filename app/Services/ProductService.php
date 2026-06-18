<?php

namespace App\Services;

use App\Models\Product; // Kept as you provided
use App\Models\product_image; // Preserved lowercase model naming
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function uploadImages(array $images): array
    {
        $storedFilenames = [];
        $sizes = [
            'products/small_image/'  => 150,
            'products/medium_image/' => 600,
            'products/large_image/'  => 1200,
            'products/'              => 1200,
        ];

        foreach ($images as $image) {
            if ($image && $image->isValid()) {
                $filename = time() . '_' . uniqid() . '.webp';

                foreach ($sizes as $path => $width) {
                    $img = $this->manager->read($image->getRealPath())->scale(width: $width);
                    Storage::disk('public')->put($path . $filename, (string) $img->toWebp(quality: 80));
                }

                $storedFilenames[] = $filename;
            }
        }

        return $storedFilenames;
    }

    public function deleteImagesFromStorage(array $filenames): void
    {
        foreach ($filenames as $filename) {
            Storage::disk('public')->delete([
                'products/small_image/' . $filename,
                'products/medium_image/' . $filename,
                'products/large_image/' . $filename,
                'products/' . $filename
            ]);
        }
    }

    public function createProduct(array $data, array $images = []): Product
    {
        $storedFilenames = $this->uploadImages($images);

        try {
            return DB::transaction(function () use ($data, $storedFilenames) {
                $product = Product::create([
                    'product_name'   => $data['productName'],
                    'description'    => $data['productDescription'],
                    'price'          => $data['price'],
                    'category_id'    => $data['productCategory'],
                    'brand_id'       => $data['productBrand'],
                    'product_status' => $data['productStatus'],
                    'user_id'        => Auth::id() ?? 1,
                ]);

                if (!empty($data['colors'])) {
                    $product->colors()->sync($data['colors']);
                }

                if (!empty($data['size'])) {
                    $product->sizes()->sync($data['size']);
                }

                foreach ($storedFilenames as $index => $filename) {
                    product_image::create([
                        'product_id' => $product->getKey(),
                        'image_url'  => $filename,
                        'sort_order' => $index + 1
                    ]);
                }

                return $product;
        });
        } catch (\Exception $e) {
            $this->deleteImagesFromStorage($storedFilenames);
            throw $e;
        }
    }

    public function updateProduct(Product $product, array $data, array $images = []): Product
    {
        $newFilenames = $this->uploadImages($images);
        $oldFilenames = [];

        try {
            // FIX: Assign to a variable instead of returning early
            $updatedProduct = DB::transaction(function () use ($product, $data, $newFilenames, &$oldFilenames) {
                $product->update([
                    'product_name'   => $data['productName'],
                    'description'    => $data['productDescription'],
                    'price'          => $data['price'],
                    'category_id'    => $data['productCategory'],
                    'brand_id'       => $data['productBrand'],
                    'product_status' => $data['productStatus']
                ]);

                $product->colors()->sync($data['colors'] ?? []);
                $product->sizes()->sync($data['size'] ?? []);

                if (!empty($newFilenames)) {
                    $oldFilenames = $product->images()->pluck('image_url')->filter()->toArray();

                    product_image::where('product_id', $product->getKey())->delete();

                    foreach ($newFilenames as $index => $filename) {
                        product_image::create([
                            'product_id' => $product->getKey(),
                            'image_url'  => $filename,
                            'sort_order' => $index + 1
                        ]);
                    }
                }

                return $product;
            });

            // FIX: This code is now reached and runs perfectly after commit
            if (!empty($oldFilenames)) {
                $this->deleteImagesFromStorage($oldFilenames);
            }

            return $updatedProduct;

        } catch (\Exception $e) {
            $this->deleteImagesFromStorage($newFilenames);
            throw $e;
        }
    }

    public function deleteProduct(string $id): void
    {
        $product = Product::findOrFail($id);
        $filenames = $product->images()->pluck('image_url')->filter()->toArray();

        DB::transaction(function () use ($product) {
            $product->delete(); 
        });

        $this->deleteImagesFromStorage($filenames);
    }
}