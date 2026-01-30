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