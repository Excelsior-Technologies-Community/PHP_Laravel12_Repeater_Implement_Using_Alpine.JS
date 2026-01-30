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

                    <!-- ✅ EXISTING IMAGES WITH REMOVE -->
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