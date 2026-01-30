# PHP_Laravel12_Repeater_Implement_Using_Alpine.JS

## Project Overview

This is a beginner-friendly Laravel 12 project demonstrating a dynamic repeater form functionality using Alpine.js. The main goal is to allow users to add multiple product images dynamically while creating or editing a product, and manage the uploaded products with full CRUD operations.

All uploaded images are stored in the public/uploads folder, and image removal can be done individually without deleting the entire product.

This project combines Laravel backend, Alpine.js for dynamic frontend behavior, and Bootstrap 5 for responsive UI.



## Features
- Create, Read, Update, Delete (CRUD) products
- Upload multiple images dynamically using Alpine.js
- Remove individual images without deleting the product
- Responsive UI with Bootstrap 5


## Technology Stack

Backend: PHP 8+, Laravel 12

Frontend: HTML, CSS, Bootstrap 5, Alpine.js

Database: MySQL

Image Handling: Laravel Request::file() and File facade for upload/delete




---



# Project SetUp

---



## STEP 1: Create New Laravel 12 Project

### Run Command :

```
composer create-project laravel/laravel PHP_Laravel12_Repeater_Implement_Using_Alpine.JS "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Repeater_Implement_Using_Alpine.JS

```

Make sure Laravel 12 is installed successfully.





## STEP 2: Setup Database

### Open .env

```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repeater_implemente_alpine.js
DB_USERNAME=root
DB_PASSWORD=

```
### Create database:

```
repeater_implemente_alpine.js

```



## STEP 3: Create Migration

### Run:

```
php artisan make:migration create_products_table

```

### database/migrations/xxxx_create_products_table.php

```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->json('images'); // repeater images
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};


```

### Run migration:

```
php artisan migrate

```



## STEP 4: Create Model

### Run:

```
php artisan make:model Product

```

### app/Models/Product.php

```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];
}

```


## STEP 5: Create Controller

### Run:

```
php artisan make:controller ProductController

```

### app/Http/Controllers/ProductController.php

```

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //  INDEX
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    //  CREATE
    public function create()
    {
        return view('products.create');
    }

    //  STORE
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

    //  SHOW (MISSING BEFORE)
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    //  EDIT
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    //  UPDATE
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

    //  DELETE
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

```


## STEP 6: Routes

### routes/web.php:

```

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('products.index');
});

/*
|--------------------------------------------------------------------------
| Product CRUD Routes
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/create', [ProductController::class, 'create'])
    ->name('products.create');

Route::post('/products', [ProductController::class, 'store'])
    ->name('products.store');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->name('products.edit');

Route::delete('/products/image/remove', [ProductController::class, 'removeImage'])
    ->name('products.image.remove');

Route::put('/products/{product}', [ProductController::class, 'update'])
    ->name('products.update');

Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('products.destroy');

```



## STEP 7: Blade View

### Create Folder:

```
resources/views/products

```

### CREATE (Repeater Image Upload) - resources/views/products/create.blade.php

```<!DOCTYPE html>
<html>

<head>
    <title>Create Product</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center mt-5">
        <div class="card shadow w-100" style="max-width: 600px;">
            <div class="card-header bg-primary text-white text-center">
                <h5>Create Product</h5>
            </div>

            <div class="card-body" x-data="{ images: [1] }">
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control mb-3">

                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control mb-3"></textarea>

                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control mb-3">

                    <label class="form-label">Product Images</label>

                    <template x-for="(img,index) in images" :key="index">
                        <div class="d-flex mb-2">
                            <input type="file" name="images[]" class="form-control">
                            <button type="button" class="btn btn-danger ms-2" x-show="images.length > 1"
                                @click="images.splice(index,1)">X</button>
                        </div>
                    </template>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" @click="images.push(1)">+ Add
                        Image</button>

                    <div class="text-center">
                        <button class="btn btn-success px-4">Save</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

```


### INDEX (List) - resources/views/products/index.blade.php

```

<!DOCTYPE html>
<html>

<head>
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-img {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>

</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center mt-5">
        <div class="card shadow w-100" style="max-width: 1200px;">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product List</h5>
                <a href="{{ route('products.create') }}" class="btn btn-success btn-sm">+ Add Product</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th width="60">ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th width="100">Price</th>
                            <th width="200">Images</th>
                            <th width="240">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>

                                <td>{{ $product->name }}</td>

                                <td class="text-start">
                                    {{ Str::limit($product->description, 50) }}
                                </td>

                                <td>₹ {{ $product->price }}</td>

                                <td>
                                    @foreach($product->images as $img)
                                        <img src="{{ asset($img) }}" class="product-img me-1 mb-1">
                                    @endforeach
                                </td>


                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">View</a>

                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are You Sure Delete This Product?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted text-center">
                                    No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</body>

</html>

```

### EDIT (Same Images + Add New) - resources/views/products/edit.blade.php

```

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center mt-5">
        <div class="card shadow w-100" style="max-width: 600px;">
            <div class="card-header bg-warning text-dark text-center">
                <h5>Edit Product</h5>
            </div>

            <div class="card-body" x-data="{ images: [1] }">
                <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" value="{{ $product->name }}" class="form-control mb-3">

                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control mb-3">{{ $product->description }}</textarea>

                    <label class="form-label">Price</label>
                    <input type="number" name="price" value="{{ $product->price }}" class="form-control mb-3">

                    <!--  EXISTING IMAGES WITH REMOVE -->
                    <label class="form-label">Existing Images</label>
                    <div class="row mb-3">
                        @foreach($product->images as $img)
                            <div class="col-4 mb-3">
                                <div class="position-relative">
                                    <img src="{{ asset($img) }}" class="img-fluid rounded">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                        onclick="removeImage('{{ $img }}', {{ $product->id }}, this)">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- ADD NEW IMAGES -->
                    <label class="form-label">Add New Images</label>
                    <template x-for="(img,index) in images">
                        <input type="file" name="images[]" class="form-control mb-2">
                    </template>

                    <button type="button" class="btn btn-secondary btn-sm mb-3" @click="images.push(1)">+ Add
                        Image</button>

                    <div class="text-center">
                        <button class="btn btn-success px-4">Update</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function removeImage(image, productId, btn) {
            if (!confirm('Remove this image?')) return;

            fetch("{{ route('products.image.remove') }}", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    image: image,
                    product_id: productId
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.closest('.col-4').remove();
                    }
                });
        }
    </script>

</body>

</html>

```

### Show (Product Details – Center Card) - resources/views/products/show.blade.php

```

<!DOCTYPE html>
<html>

<head>
    <title>View Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-view-img {
            width: 180px;
            /* medium-large */
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }
    </style>

</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center mt-5">
        <div class="card shadow w-100" style="max-width: 600px;">
            <div class="card-header bg-info text-white text-center">
                <h5>Product Details</h5>
            </div>

            <div class="card-body text-center">
                <h4>{{ $product->name }}</h4>
                <p class="text-muted">{{ $product->description }}</p>
                <h5 class="text-success">₹ {{ $product->price }}</h5>

                <hr>

                <div>
                    @foreach($product->images as $img)
                        <img src="{{ asset($img) }}" class="product-view-img m-2">
                    @endforeach

                </div>

                <div class="mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">Back</a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

```


## STEP 8: Run Server

### Run:

```
php artisan serve

```

### Open in browser:

```
 http://127.0.0.1:8000/products

```


## So you can see this type output:

### Main Page:


<img width="1915" height="960" alt="Screenshot 2026-01-30 115012" src="https://github.com/user-attachments/assets/fe2b8031-bb1d-4d11-bc06-706e45b0aa27" />


### Create Product Page:


<img width="1919" height="966" alt="Screenshot 2026-01-30 115604" src="https://github.com/user-attachments/assets/9794f2b2-6b80-4b56-b577-a98cdf82a2e1" />

after create:

<img width="1919" height="938" alt="Screenshot 2026-01-30 120232" src="https://github.com/user-attachments/assets/8e18d83b-1906-48ed-b3d1-e0419477ccb6" />


### Edit Product Page:


<img width="1885" height="955" alt="Screenshot 2026-01-30 120307" src="https://github.com/user-attachments/assets/e7a5d325-b3aa-4f0c-be38-ccf166a1845c" />

after edit:

<img width="1909" height="954" alt="Screenshot 2026-01-30 120319" src="https://github.com/user-attachments/assets/0e7b03a5-827e-4433-a7ff-d782f4a6b99b" />


### Details Product Page(Show Page):


<img width="1919" height="958" alt="Screenshot 2026-01-30 120446" src="https://github.com/user-attachments/assets/1e9c8818-1c72-4431-acb8-43adea51bc7d" />


### Delete Product:


<img width="1919" height="956" alt="Screenshot 2026-01-30 120550" src="https://github.com/user-attachments/assets/0235f429-84e1-4c7b-8017-c4345054a784" />




---


# Project Folder Structure:

```

PHP_Laravel12_Repeater_Implement_Using_Alpine.JS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── ProductController.php   # Handles all CRUD + image removal
│   │   └── Middleware/
│   ├── Models/
│   │   └── Product.php                 # Product Eloquent model
│   └── ...
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   │   └── xxxx_create_products_table.php
│   └── seeders/
├── public/
│   ├── uploads/                        # Stores all uploaded images
│   └── index.php
├── resources/
│   ├── views/
│   │   └── products/
│   │       ├── create.blade.php        # Add new product with repeater images
│   │       ├── edit.blade.php          # Edit product + add/remove images
│   │       ├── index.blade.php         # List all products
│   │       └── show.blade.php          # View single product details
│   └── ...
├── routes/
│   └── web.php                          # Routes for CRUD operations
├── storage/
├── tests/
├── .env                                 # Environment variables
├── composer.json
├── package.json
├── artisan
└── README.md

```
