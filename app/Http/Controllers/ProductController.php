<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Cloudinary\Cloudinary;

class ProductController extends Controller
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(config('cloudinary.cloudinary_url'));
    }

    public function store(StoreProductRequest $request)
    {
        // Upload the image to Cloudinary
        $uploadedFileUrl = $this->cloudinary->uploadApi()->upload($request->file('product_image')->getRealPath(), [
            'folder' => 'products' , // Optional: specify a folder in Cloudinary
            'ssl_verify' => false ,// Disable SSL verification (not recommended for production)
            //'ca_bundle' => public_path('cacert.pem')
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
