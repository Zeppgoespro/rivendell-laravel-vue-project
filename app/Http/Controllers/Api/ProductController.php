<?php
/* Borozepped */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Api\Product;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage        = request('per_page', 10);
        $search         = request('search', '');
        $sortField      = request('sort_field', 'created_at');
        $sortDirection  = request('sort_direction', 'desc');

        $query = Product::query()
            ->where('title', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return ProductListResource::collection($query);
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\ProductRequest $request
     * @return \App\Http\Resources\ProductResource
     */
    public function store(ProductRequest $request): ProductResource
    {
        $data               = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        /**
         * @var \Illuminate\Http\UploadedFile $image
         */
        $image = $data['image'] ?? null;

        // Check if image was given and save on local file system
        if ($image) {
            $relativePath       = $this->saveImage($image);
            $data['image']      = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     * @param \App\Models\Product $product
     * @return \App\Http\Resources\ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     * @param \App\Http\Requests\ProductRequest $request
     * @param \App\Models\Product $product
     * @return \App\Http\Resources\ProductResource
     */
    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $data               = $request->validated();
        $data['updated_by'] = $request->user()->id;

        /**
         * @var \illuminate\Http\UploadedFile $image
         */
        $image = $data ['image'] ?? null;

        // Check if image was given and save on local file system
        if($image) {
            $relativePath       = $this->saveImage($image);
            $data['image']      = URL::to(Storage::url($relativePath));
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();

            // If there is an old image, delete it
            if ($product->image) {
                Storage::deleteDirectory('/public/' . dirname($product->image));
            }
        }

        $product->update($data);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\Api\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): Response
    {
        $product->delete();
        return response()->noContent();
    }

    /**
     * @param \Illuminate\Http\UploadedFile
     * @return string
     */
    private function saveImage(UploadedFile $image): string
    {
        $path = 'images/' . Str::random();
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }
        if (!Storage::putFileAs('public' . $path . $image . $image->getClientOriginalName())) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $path . '/' . $image->getClientOriginalName();
    }
}
