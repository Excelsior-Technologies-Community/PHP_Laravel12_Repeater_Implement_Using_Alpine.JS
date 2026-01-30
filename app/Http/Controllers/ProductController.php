<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // ✅ INDEX
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    // ✅ CREATE
    public function create()
    {
        return view('products.create');
    }

    // ✅ STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'images.*' => 'required|image|mimes:jpg,png,jpeg'
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads'), $name);
                $imagePaths[] = 'uploads/' . $name;
            }
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'images' => $imagePaths,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product Created!');
    }

    // ✅ SHOW (MISSING BEFORE)
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // ✅ EDIT
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // ✅ UPDATE
    public function update(Request $request, Product $product)
    {
        $imagePaths = $product->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads'), $name);
                $imagePaths[] = 'uploads/' . $name;
            }
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'images' => $imagePaths,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product Updated!');
    }

    // ✅ DELETE
    public function destroy(Product $product)
    {
        if (!empty($product->images)) {
            foreach ($product->images as $img) {
                File::delete(public_path($img));
            }
        }

        $product->delete();

        return back()->with('success', 'Product Deleted!');
    }

    public function removeImage(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $images = $product->images;

        // Remove selected image from array
        if (($key = array_search($request->image, $images)) !== false) {
            unset($images[$key]);

            // Delete file from folder
            File::delete(public_path($request->image));

            // Update product
            $product->update([
                'images' => array_values($images)
            ]);
        }

        return response()->json(['success' => true]);
    }

}
