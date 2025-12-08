<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-3">Gửi đánh giá của bạn</h5>

        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Rating --}}
            <div class="mb-3">
                <label class="form-label">Số sao:</label>
                <select name="rating" class="form-select" required>
                    <option value="">Chọn số sao</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                </select>
            </div>

            {{-- Comment --}}
            <div class="mb-3">
                <label class="form-label">Nội dung:</label>
                <textarea name="comment" rows="3" class="form-control" required></textarea>
            </div>

            {{-- Upload multiple images --}}
            <div class="mb-3">
                <label class="form-label">Hình ảnh (tối đa 5 ảnh)</label>
                <input type="file" 
                       name="images[]" 
                       id="review-images" 
                       multiple 
                       accept="image/*" 
                       class="form-control">
            </div>

            {{-- Preview --}}
            <div id="preview-container" class="d-flex gap-2 flex-wrap mt-2"></div>

            <button class="btn btn-primary mt-3">Gửi đánh giá</button>
        </form>
    </div>
</div>

{{-- Preview script --}}
<script>
    document.getElementById('review-images').addEventListener('change', function (e) {
        const preview = document.getElementById('preview-container');
        preview.innerHTML = ""; // clear

        let files = Array.from(e.target.files);

        // Giới hạn 5 ảnh
        if (files.length > 5) {
            alert("Bạn chỉ được chọn tối đa 5 ảnh!");
            e.target.value = "";
            return;
        }

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                let img = document.createElement('img');
                img.src = event.target.result;
                img.className = "rounded border";
                img.style.width = "100px";
                img.style.height = "100px";
                img.style.objectFit = "cover";

                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
</script>
