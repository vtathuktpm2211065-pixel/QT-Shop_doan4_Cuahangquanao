<h2>Gợi ý theo danh mục</h2>
<ul>
@foreach($categoryBased as $product)
    <li>{{ $product->name }} - {{ $product->price }}</li>
@endforeach
</ul>

<h2>Gợi ý dựa trên hành vi người dùng</h2>
<ul>
@foreach($behaviorBased as $product)
    <li>{{ $product->name }} - {{ $product->price }}</li>
@endforeach
</ul>
