<!DOCTYPE html>
<html>

<head>
    <title>Create Product</title>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .drop-zone {
            border: 2px dashed #0d6efd;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            margin-bottom: 15px;
            cursor: pointer;
            border-radius: 8px;
        }

        img {
            max-height: 100px;
            margin-top: 10px;
            border-radius: 6px;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5 d-flex justify-content-center">

<div class="card w-100" style="max-width:650px;" x-data="uploader()">

    <div class="card-header bg-primary text-white text-center">
        <h5>Create Product</h5>
    </div>

    <div class="card-body">

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- NAME -->
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control mb-3" placeholder="Enter product name">

            <!-- DESCRIPTION -->
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control mb-3" placeholder="Enter description"></textarea>

            <!-- PRICE -->
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control mb-3" placeholder="Enter price">

            <!-- DRAG DROP -->
            <label class="form-label">Drag & Drop Images</label>

            <div class="drop-zone"
                 @dragover.prevent
                 @drop.prevent="drop($event)">
                📂 Drag & Drop Images Here
                <br>
                <small class="text-muted">or use file inputs below</small>
            </div>

            <!-- REPEATER -->
            <label class="form-label">Product Images</label>

            <template x-for="(item,i) in files" :key="i">

                <div class="border p-2 mb-2 rounded bg-white">

                    <input type="file"
                           name="images[]"
                           class="form-control"
                           @change="preview($event,i)">

                    <img x-show="item.preview" :src="item.preview">

                    <button type="button"
                            class="btn btn-danger btn-sm mt-2"
                            @click="remove(i)">
                        Remove
                    </button>

                </div>

            </template>

            <button type="button"
                    class="btn btn-secondary btn-sm mb-3"
                    @click="add()">
                + Add Image
            </button>

            <!-- SUBMIT -->
            <button class="btn btn-success w-100">
                Save Product
            </button>

        </form>

    </div>

</div>

</div>

<script>
function uploader() {
    return {
        files: [{ preview: null }],

        add() {
            this.files.push({ preview: null });
        },

        remove(i) {
            this.files.splice(i,1);
        },

        preview(e,i) {
            this.files[i].preview = URL.createObjectURL(e.target.files[0]);
        },

        drop(e) {
            let files = e.dataTransfer.files;

            for (let file of files) {

                if (!file.type.startsWith("image/")) continue;

                let input = document.createElement('input');
                input.type = 'file';

                let dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;

                input.name = "images[]";
                input.style.display = "none";

                document.querySelector("form").appendChild(input);

                this.files.push({
                    preview: URL.createObjectURL(file)
                });
            }
        }
    }
}
</script>

</body>
</html>