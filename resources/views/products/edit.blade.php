<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .drop-zone {
            border: 2px dashed #ffc107;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
            background: #fff8e1;
            cursor: pointer;
        }

        .preview-img {
            max-height: 100px;
            border-radius: 6px;
            margin-top: 5px;
         
        }
    </style>
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center mt-5">

<div class="card shadow w-100" style="max-width: 700px;">

    <div class="card-header bg-warning text-dark text-center">
        <h5>Edit Product</h5>
    </div>

    <div class="card-body" x-data="editUploader()">

        <form method="POST"
              action="{{ route('products.update', $product) }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <!-- PRODUCT INFO -->
            <input type="text" name="name" value="{{ $product->name }}" class="form-control mb-2">
            <textarea name="description" class="form-control mb-2">{{ $product->description }}</textarea>
            <input type="number" name="price" value="{{ $product->price }}" class="form-control mb-3">

            <!-- EXISTING IMAGES -->
            <label class="form-label">Existing Images</label>

            <div class="row mb-3">

                @foreach($product->images as $img)
                    <div class="col-4 mb-2">
                        <div class="position-relative">

                            <img src="{{ asset($img) }}"
                                 class="img-fluid rounded">

                            <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                    onclick="removeImage('{{ $img }}', {{ $product->id }}, this)">
                                ✕
                            </button>

                        </div>
                    </div>
                @endforeach

            </div>

            <!-- DROP ZONE -->
            <div class="drop-zone"
                 @dragover.prevent
                 @drop.prevent="handleDrop($event)">
                📂 Drag & Drop New Images Here
            </div>

            <!-- NEW IMAGES -->
            <label class="form-label">Add New Images</label>

            <template x-for="(img,index) in images" :key="index">

                <div class="border p-2 mb-2 rounded bg-white">

                    <input type="file"
                           name="images[]"
                           class="form-control"
                           @change="preview($event,index)">

                    <img x-show="img.preview"
                         :src="img.preview"
                         class="preview-img">

                    <button type="button"
                            class="btn btn-danger btn-sm mt-2"
                            @click="remove(index)">
                        Remove
                    </button>

                </div>

            </template>

            <button type="button"
                    class="btn btn-secondary btn-sm mb-3"
                    @click="add()">
                + Add Image
            </button>

            <div class="text-center mt-3">
                <button class="btn btn-success px-4">Update</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">Back</a>
            </div>

        </form>

    </div>

</div>

</div>

<script>
function editUploader() {
    return {
        images: [{ file: null, preview: null }],

        add() {
            this.images.push({ file: null, preview: null });
        },

        remove(index) {
            this.images.splice(index, 1);
        },

        preview(event, index) {
            let file = event.target.files[0];

            if (!file) return;

            this.images[index].file = file;
            this.images[index].preview = URL.createObjectURL(file);
        },

        handleDrop(event) {
            let files = event.dataTransfer.files;

            for (let file of files) {

                if (!file.type.startsWith('image/')) continue;

                // CREATE REAL FILE INPUT (IMPORTANT FIX)
                let dt = new DataTransfer();
                dt.items.add(file);

                let input = document.createElement('input');
                input.type = "file";
                input.name = "images[]";
                input.files = dt.files;
                input.style.display = "none";

                document.querySelector("form").appendChild(input);

                this.images.push({
                    file: file,
                    preview: URL.createObjectURL(file)
                });
            }
        }
    }
}

// REMOVE OLD IMAGE
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