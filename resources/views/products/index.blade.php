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

    <!-- HEADER -->
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Product List</h5>
        <a href="{{ route('products.create') }}" class="btn btn-success btn-sm">+ Add Product</a>
    </div>

    <div class="card-body">

        <!-- LIVE SEARCH -->
        <div class="mb-3">
            <input type="text"
                   id="search"
                   class="form-control"
                   placeholder="Search product by name...">
        </div>

        <!-- TABLE -->
        <table class="table table-bordered table-hover text-center align-middle">

            <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Images</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody id="productTable">

            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ Str::limit($product->description, 50) }}</td>
                    <td>₹ {{ $product->price }}</td>

                    <td>
                        @if($product->images)
                            @foreach($product->images as $img)
                                <img src="{{ asset($img) }}" class="product-img me-1 mb-1">
                            @endforeach
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">No products found</td>
                </tr>
            @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

<!-- SAFE BASE URL -->
<script>
const baseUrl = "{{ url('/products') }}";

let timer;

document.getElementById('search').addEventListener('keyup', function () {

    clearTimeout(timer);

    let query = this.value;

    timer = setTimeout(() => {

        fetch("{{ route('products.search') }}?query=" + query)
            .then(res => res.json())
            .then(data => {

                let table = document.getElementById('productTable');
                table.innerHTML = '';

                if (data.length === 0) {
                    table.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No products found
                            </td>
                        </tr>`;
                    return;
                }

                data.forEach(product => {

                    let images = '';

                    if (product.images && product.images.length > 0) {
                        product.images.forEach(img => {
                            images += `<img src="/${img}" class="product-img me-1 mb-1">`;
                        });
                    }

                    table.innerHTML += `
                        <tr>
                            <td>${product.id}</td>
                            <td>${product.name}</td>
                            <td>${product.description.substring(0,50)}</td>
                            <td>₹ ${product.price}</td>
                            <td>${images}</td>
                            <td>
                                <a href="${baseUrl}/${product.id}" class="btn btn-info btn-sm">View</a>
                                <a href="${baseUrl}/${product.id}/edit" class="btn btn-warning btn-sm">Edit</a>

                                <form method="POST" action="${baseUrl}/${product.id}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `;
                });

            });

    }, 300);

});
</script>

</body>
</html>