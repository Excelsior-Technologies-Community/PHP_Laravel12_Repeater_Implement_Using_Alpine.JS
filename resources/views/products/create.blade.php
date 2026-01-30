<!DOCTYPE html>
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