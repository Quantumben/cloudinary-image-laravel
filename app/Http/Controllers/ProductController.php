<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(config('cloudinary.cloudinary_url'));
    }

    /**
     * Store a new product with image upload to Cloudinary.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            // Attempt to upload the image to Cloudinary
            $uploadedFileUrl = $this->cloudinary->uploadApi()->upload($request->file('product_image')->getRealPath(), [
                'folder' => 'products',
                'verify' => false
            ]);

            // Create the product record in the database
            $product = Product::create([
                'product_name' => $request->input('product_name'),
                'product_description' => $request->input('product_description'),
                'price' => $request->input('price'),
                'product_image' => $uploadedFileUrl['secure_url'] // Save the Cloudinary URL in the database
            ]);

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product
            ], 201);

        } catch (\Exception $e) {
            // Log error and return response
            Log::error("Cloudinary Upload Error: " . $e->getMessage());

            return response()->json([
                'message' => 'Failed to upload product image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all products.
     */
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'message' => 'Products fetched successfully!',
            'products' => $products
        ], 200);
    }
}
