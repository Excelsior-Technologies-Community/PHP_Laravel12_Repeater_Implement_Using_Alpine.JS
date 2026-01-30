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