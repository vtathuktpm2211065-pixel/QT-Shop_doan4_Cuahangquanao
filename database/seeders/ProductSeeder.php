<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // nhớ thêm ở đầu file nếu chưa có


    // Xoá dữ liệu cũ
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \App\Models\ProductVariant::truncate();
    \App\Models\Product::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
class ProductSeeder extends Seeder
{
    public function run(): void
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \App\Models\ProductVariant::truncate();
    \App\Models\Product::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
$product = Product::create([
    'name' => 'Bộ đồ bà ba',
'slug' => 'Bộ đồ bà ba',
    'description' => 'Bộ đồ bà ba màu trơn, kiểu dáng truyền thống, mang nét đẹp mộc mạc, gần gũi của người nông dân.',
    'price' => 300,
    'category_id' => 1,
    'image_url' => 'do_nu1.jpg',
    'gioi_tinh' => 'nu',
    'pho_bien' => false,
    'noi_bat' => false,
]);

$product->variants()->createMany([
    ['color' => 'Đỏ', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Đỏ', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Đỏ', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Đỏ', 'size' => 'L', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Xanh', 'size' => 'S', 'stock_quantity' => 12,'price' => 300000],
    ['color' => 'Xanh', 'size' => 'L', 'stock_quantity' => 5,'price' => 300000],
     ['color' => 'Xanh', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 300000],
    
]);
       $product = Product::create([
            'name' => 'Đồ bà ba hiện đại',
            'slug' => 'Bộ đồ bà ba hiện đại',
            'description' => 'Đồ bà ba kiểu cách hiện đại hơn với họa tiết in hoa, kết hợp màu sắc trẻ trung nhưng vẫn giữ được nét truyền thống.',
            'price' => 350,
            'category_id' => 1,
            'image_url' => 'do_nu2.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
$product->variants()->createMany([
    ['color' => 'Đỏ', 'size' => 'S', 'stock_quantity' => 10,'price' => 350000],
    ['color' => 'Đỏ', 'size' => 'M', 'stock_quantity' => 8,'price' => 350000],
    ['color' => 'Đỏ', 'size' => 'L', 'stock_quantity' => 10,'price' => 350000],
    ['color' => 'Xanh', 'size' => 'S', 'stock_quantity' => 12,'price' => 350000],
    ['color' => 'Xanh', 'size' => 'L', 'stock_quantity' => 5,'price' => 350000],
     ['color' => 'Xanh', 'size' => 'M', 'stock_quantity' => 8,'price' => 350000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 350000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 350000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 350000],
    
]);
        $product = Product::create([
            'name' => 'Chân váy',
'slug' => 'Chân váy',
            'description' => 'Chân váy xòe dài — Phong cách trẻ trung, dễ phối cùng áo phông hoặc áo sơ mi, phù hợp mọi dịp.',
            'price' => 200,
            'category_id' => 1,
            'image_url' => 'do_nu4.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
        $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Xám', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Xám', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Xám', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

       $product = Product::create([
            'name' => 'Set đồ công sở',
 'slug' =>'Set đồ công sở',
            'description' => 'Áo sơ mi và chân váy nữ công sở — Thiết kế thanh lịch, chất liệu cotton thoáng mát, phù hợp mặc đi làm hoặc đi học.',
            'price' => 400,
            'category_id' => 1,
            'image_url' => 'do_nu3.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
$product->variants()->createMany([
    ['color' => 'Xanh_Xám', 'size' => 'S', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Xanh_Xám', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Xanh_Xám', 'size' => 'L', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 12,'price' => 400000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 5,'price' => 400000],
     ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Hồng_trắng', 'size' => 'S', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Hồng_trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Hồng_trắng', 'size' => 'L', 'stock_quantity' => 12,'price' => 400000],
    
]);
     $product = Product::create([
            'name' => 'Set đồ hoa nhí',
           'slug' =>'Set đồ hoa nhí',
            'description' => 'Set đồ ngắn nhẹ nhàng, tươi tắn với áo ngắn tay in họa tiết bông nhí nhỏ xinh trên nền vải mềm mại, thoáng mát. Áo thiết kế trẻ trung, thoải mái, giúp bạn luôn cảm thấy dễ chịu dưới ánh nắng hè.',
            'price' => 280,
            'category_id' => 1,
            'image_url' => 'do_nu5.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
      $product->variants()->createMany([
    ['color' => 'Trắng_hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 280000],
    ['color' => 'Trắng_hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 280000],
    ['color' => 'Trắng_hồng', 'size' => 'L', 'stock_quantity' => 10,'price' => 280000],
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 12,'price' => 280000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 5,'price' => 280000],
     ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 280000],
    ['color' => 'Hồng_trắng', 'size' => 'S', 'stock_quantity' => 10,'price' => 280000],
    ['color' => 'Hồng_trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 280000],
    ['color' => 'Hồng_trắng', 'size' => 'L', 'stock_quantity' => 12,'price' => 280000],
    
]);  

       $product = Product::create([
            'name' => 'Áo kiểu',
            'slug' => 'Áo kiểu',
            'description' => 'Áo kiểu croptop tay dài mang phong cách hiện đại, trẻ trung và năng động. Thiết kế ngắn ngang eo tôn lên vòng eo thon gọn, kết hợp cùng tay dài ôm vừa vặn, tạo cảm giác thanh lịch nhưng vẫn sexy nhẹ nhàng.',
            'price' => 120,
            'category_id' => 1,
            'image_url' => 'do_nu6.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
$product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 120000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 120000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 120000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 120000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' =>  120000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 120000],
    
]);
        $product = Product::create([
            'name' => 'Sơ mi nữ',
            'slug' => 'Sơ mi nữ',
            'description' => 'Áo sơ mi nữ công sở — Thiết kế thanh lịch, chất liệu cotton thoáng mát, phù hợp mặc đi làm hoặc đi học.',
            'price' => 150,
            'category_id' => 1,
            'image_url' => 'do_nu7.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
$product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 150000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 150000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 150000],
    
]);
      $product = Product::create([
            'name' => 'Set đồ pijama',
             'slug' => 'Set đồ pijama',
            'description' => 'Set đồ pijama mặc nhà thường mang phong cách thoải mái, nhẹ nhàng và dễ chịu, phù hợp để nghỉ ngơi hoặc ngủ ngon.',
            'price' => 200,
            'category_id' => 1,
            'image_url' => 'do_nu8.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
        $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

        $product = Product::create([
            'name' => 'Set đồ  pijama hoa nhí',
             'slug' => 'Set đồ pijama hoa nhí',
            'description' => 'Set đồ pijama màu sắc nhẹ nhàng như pastel, hoa nhí nhỏ hoặc họa tiết đơn giản tạo cảm giác thư giãn.',
            'price' => 200,
            'category_id' => 1,
            'image_url' => 'do_nu9.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
        $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

        $product = Product::create([
            'name' => 'Set đồ tay dài',
             'slug' => 'Set đồ tay dài',
            'description' => 'Bộ đồ thể thao nữ thoải mái, thấm hút mồ hôi tốt cho các hoạt động vận động.',
            'price' => 300,
            'category_id' => 1,
            'image_url' => 'do_nu10.jpg',
            'gioi_tinh' => 'nu',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
        $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Be', 'size' => 'S', 'stock_quantity' => 12,'price' => 300000],
    ['color' => 'Be', 'size' => 'L', 'stock_quantity' => 5,'price' => 300000],
     ['color' => 'Be', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 300000],
    
]);
        $product = Product::create([
            'name' => 'set đồ nam',
            'slug' =>'set đồ nam',
            'description' => 'Áo sơ mi dài tay công sở – Áo sơ mi kiểu dáng ôm nhẹ, chất liệu vải thoáng mát, dễ dàng phối cùng quần âu hoặc jeans.',
            'price' => 450,
            'category_id' => 2,
            'image_url' => 'do_nam1.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 450000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 450000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 450000],
    
]);

       $product = Product::create([
            'name' => 'Quần tây ống rộng',
            'slug' => 'Quần tây ống rộng',
            'description' => 'Quần tây ống rộng trẻ trung tạo phong cách cá tính và năng động cho nam giới.',
            'price' => 400,
            'category_id' => 2,
            'image_url' => 'do_nam2.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
                 $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 400000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 400000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 400000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 400000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 400000],
    
]);

       $product = Product::create([
            'name' => 'Áo thun nam',
            'slug' => 'Áo thun nam',
            'description' => 'Áo thun dành cho nam',
            'price' => 250,
            'category_id' => 2,
            'image_url' => 'do_nam3.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
               $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 250000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 250000],
    
]);
        $product = Product::create([
            'name' => 'Quần jean nam',
           'slug' => 'Quần jean nam',
            'description' => 'Quần jeans  trẻ trung – Quần jeans nam dáng slim fit, thiết kế suông tạo phong cách cá tính và năng động cho nam giới.',
            'price' => 300,
            'category_id' => 2,
            'image_url' => 'do_nam4.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 300000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 300000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 300000],
    
]);
      $product = Product::create([
            'name' => 'Áo khoác',
            'slug' => 'Áo khoác',
            'description' => 'Áo khoác bomber cá tính – Áo khoác bomber dáng ngắn, chất liệu chống gió, phối màu đen và xám phù hợp đi chơi hoặc dạo phố.',
            'price' => 500,
            'category_id' => 2,
            'image_url' => 'do_nam5.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
               $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 500000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 500000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 500000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 500000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 500000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 500000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 500000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 500000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 500000],
    
]);

       $product = Product::create([
            'name' => 'Set đồ gen z',
            'slug' =>'Set đồ gen z',
            'description' => 'Set đồ gen Z cực cuốn bao gồm một áo thun một áo sơ mi cùng với 1 quần tây ống suông',
            'price' => 200,
            'category_id' => 2,
            'image_url' => 'do_nam6.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

      $product = Product::create([
            'name' => 'Áo thun',
           'slug' => 'Áo thun' ,
            'description' => 'Áo thun tay dài basic – Áo thun dài tay màu đen, chất liệu cotton co giãn tốt, thích hợp mặc trong những ngày se lạnh.',
            'price' => 150,
            'category_id' => 2,
            'image_url' => 'do_nam7.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
               $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 150000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 150000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 150000],
    
]);

       $product = Product::create([
            'name' => 'Áo kiểu dành cho  nam',
           'slug' => 'Áo kiểu dành cho  nam',
            'description' => 'Áo thun cổ chữ v đơn giản – Áo thun nam màu trơn, chất liệu cotton mềm mại, thoáng mát, thích hợp mặc hàng ngày hoặc đi chơi.',
            'price' => 120,
            'category_id' => 2,
            'image_url' => 'do_nam8.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 120000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 120000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 120000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 120000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 120000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 120000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 120000],
    
]);
        $product = Product::create([
            'name' => 'Áo polo nam',
          'slug' => Str::slug('Áo polo nam') . '-' . Str::random(5),
            'description' => 'Áo polo cổ bẻ lịch lãm – Áo polo cotton cao cấp, màu trắng tinh tế, thiết kế cổ bẻ vừa phải, phù hợp mặc đi làm hoặc gặp gỡ bạn bè.',
            'price' => 200,
            'category_id' => 2,
            'image_url' => 'do_nam9.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

       $product = Product::create([
            'name' => 'Áo polo nam',
         'slug' => Str::slug('Áo polo nam cá tính') . '-' . Str::random(5),
            'description' => 'Áo polo cổ bẻ lịch lãm – Áo polo cotton cao cấp, màu trắng tinh tế, thiết kế cổ bẻ vừa phải, phù hợp mặc đi làm hoặc gặp gỡ bạn bè.',
            'price' => 200,
            'category_id' => 2,
            'image_url' => 'do_nam10.jpg',
            'gioi_tinh' => 'nam',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
                $product->variants()->createMany([
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 200000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 200000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 200000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 200000],
    
]);

$product = Product::create([
            'name' => 'Quần áo trẻ em',
            'slug' => 'quan-ao-tre-em',
            'description' => 'Quần áo trẻ em thoải mái, tiện lợi',
            'price' => 150,
            'category_id' => 3,
            'image_url' => 'tre_em1.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Trắng_hồng', 'size' => 'S', 'stock_quantity' => 12,'price' => 150000],
    ['color' => 'Trắng_hồng', 'size' => 'L', 'stock_quantity' => 5,'price' => 150000],
     ['color' => 'Trắng_hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu-be', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Nâu_be', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu-be', 'size' => 'L', 'stock_quantity' => 12,'price' => 150000],
    
]);

        $product = Product::create([
            'name' => 'Đầm công chúa',
            'slug' => 'dam-cong-chua',
            'description' => 'Đầm váy hoa xinh xắn, chất liệu nhẹ nhàng, giúp bé luôn nổi bật trong các buổi tiệc.',
            'price' => 300,
            'category_id' => 3,
            'image_url' => 'tre_em2.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
                $product->variants()->createMany([
    ['color' => 'Cam', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Cam', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Cam', 'size' => 'L', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Tím', 'size' => 'S', 'stock_quantity' => 12,'price' => 300000],
    ['color' => 'Tím', 'size' => 'L', 'stock_quantity' => 5,'price' => 300000],
     ['color' => 'Tím', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 300000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 300000],
    
]);

       $product = Product::create([
            'name' => 'Áo và chân váy xinh',
            'slug' => 'ao-chan-vay',
            'description' => 'Bộ quần áo cotton mềm mại, thoáng khí, tuyệt vời cho làn da nhạy cảm của bé.',
            'price' => 150,
            'category_id' => 3,
            'image_url' => 'tre_em3.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
                $product->variants()->createMany([
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Vàng', 'size' => 'S', 'stock_quantity' => 12,'price' => 150000],
    ['color' => 'Vàng', 'size' => 'L', 'stock_quantity' => 5,'price' => 150000],
    ['color' => 'Vàng', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'S', 'stock_quantity' => 10,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'M', 'stock_quantity' => 8,'price' => 150000],
    ['color' => 'Nâu', 'size' => 'L', 'stock_quantity' => 12,'price' => 150000],
    
]);

       $product = Product::create([
            'name' => 'Quần áo dàitrẻ em',
            'slug' => 'quan-ao-dai-tre-em',
            'description' => 'Quần dài giữ ấm nhẹ nhàng cho bé trong những ngày se lạnh.',
            'price' => 250,
            'category_id' => 3,
            'image_url' => 'tre_em4.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => false,
            'noi_bat' => true,
        ]);
                        $product->variants()->createMany([
    ['color' => 'Tím', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Tím', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Tím', 'size' => 'L', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Vàng', 'size' => 'S', 'stock_quantity' => 12,'price' => 250000],
    ['color' => 'Vàng', 'size' => 'L', 'stock_quantity' => 5,'price' => 250000],
    ['color' => 'Vàng', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 12,'price' => 250000],
    
]);
        $product = Product::create([
            'name' => 'Quần áo trẻ em nữ',
            'slug' => 'quan-ao-tre-em-nữ',
            'description' => 'Áo thun năng động, chất liệu co giãn giúp bé thoải mái vận động cả ngày.',
            'price' => 199,
            'category_id' => 3,
            'image_url' => 'tre_em6.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
                $product->variants()->createMany([
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 199000],
    ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 199000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 199000],
    ['color' => 'Trắng_hồng', 'size' => 'S', 'stock_quantity' => 12,'price' => 199000],
    ['color' => 'Trắng_hồng', 'size' => 'L', 'stock_quantity' => 5,'price' => 199000],
     ['color' => 'Trắng_hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 199000],
    ['color' => 'Nâu_be', 'size' => 'S', 'stock_quantity' => 10,'price' => 199000],
    ['color' => 'Nâu_be', 'size' => 'M', 'stock_quantity' => 8,'price' => 199000],
    ['color' => 'Nâu_be', 'size' => 'L', 'stock_quantity' => 12,'price' => 199000],
    
]);

       $product = Product::create([
            'name' => 'Đồ bộ dành cho  trẻ em',
            'slug' => 'do-bo-danh-cho-tre-em',
            'description' => 'Bộ đồ ngủ tiện lợi, chất liệu vải thoáng mát, giúp bé ngủ ngon hơn.',
            'price' => 230,
            'category_id' => 3,
            'image_url' => 'tre_em5.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => false,
        ]);
                        $product->variants()->createMany([
    ['color' => 'Cam', 'size' => 'S', 'stock_quantity' => 10,'price' => 230000],
    ['color' => 'Cam', 'size' => 'M', 'stock_quantity' => 8,'price' => 230000],
    ['color' => 'Cam', 'size' => 'L', 'stock_quantity' => 10,'price' => 230000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 230000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 230000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 230000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 230000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 230000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 230000],
    
]);
        
       $product = Product::create([
            'name' => 'Đầm xinh',
            'slug' => 'dam-xinh-tre-em',
            'description' => 'Đầm công chúa dễ thương, thiết kế thời trang giúp bé gái thêm phần xinh xắn.',
            'price' => 320,
            'category_id' => 3,
            'image_url' => 'tre_em7.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
                        $product->variants()->createMany([
    ['color' => 'Cam', 'size' => 'S', 'stock_quantity' => 10,'price' => 320000],
    ['color' => 'Cam', 'size' => 'M', 'stock_quantity' => 8,'price' => 320000],
    ['color' => 'Cam', 'size' => 'L', 'stock_quantity' => 10,'price' => 320000],
    ['color' => 'Đen', 'size' => 'S', 'stock_quantity' => 12,'price' => 320000],
    ['color' => 'Đen', 'size' => 'L', 'stock_quantity' => 5,'price' => 320000],
     ['color' => 'Đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 320000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 320000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 320000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 320000],
    
]);
      $product = Product::create([
            'name' => 'Đồ xinh cho bé',
            'slug' => 'do-xinh-xan-tre-em',
            'description' => 'Quần dài giữ ấm nhẹ nhàng cho bé trong những ngày se lạnh.',
            'price' => 250,
            'category_id' => 3,
            'image_url' => 'tre_em8.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => false,
            'noi_bat' => false,
        ]);
                        $product->variants()->createMany([
    ['color' => 'Cam', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Cam', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Cam', 'size' => 'L', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'S', 'stock_quantity' => 12,'price' => 250000],
    ['color' => 'Trắng', 'size' => 'L', 'stock_quantity' => 5,'price' => 250000],
     ['color' => 'Trắng', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Hồng', 'size' => 'S', 'stock_quantity' => 10,'price' => 250000],
    ['color' => 'Hồng', 'size' => 'M', 'stock_quantity' => 8,'price' => 250000],
    ['color' => 'Hồng', 'size' => 'L', 'stock_quantity' => 12,'price' => 250000],
    
]);
  $product = Product::create([
            'name' => 'Quần áo trẻ em nam',
            'slug' => 'quan-ao-tre-em-nam',
            'description' => 'Áo thun và quần short cho bé trai mùa hè mát mẻ.',
            'price' => 450,
            'category_id' => 3,
            'image_url' => 'tre_em9.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
          $product->variants()->createMany([
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Xám_Xanh', 'size' => 'S', 'stock_quantity' => 12,'price' => 450000],
    ['color' => 'Xám_Xanh', 'size' => 'L', 'stock_quantity' => 5,'price' => 450000],
    ['color' => 'Xám_Xanh', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'L', 'stock_quantity' => 12,'price' => 450000],
    
]);
      $product = Product::create([
            'name' => 'bộ đồ năng động dành cho bé trai',
            'slug' => 'nang-dong',
            'description' => 'Bộ đồ trẻ em thiết kế năng động, phù hợp cho bé trai.',
            'price' => 450,
            'category_id' => 3,
            'image_url' => 'tre_em10.jpg',
            'gioi_tinh' => 'tre_em',
            'pho_bien' => true,
            'noi_bat' => true,
        ]);
                 $product->variants()->createMany([
    ['color' => 'Xanh_đen', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Xanh_đen', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Xanh_đen', 'size' => 'L', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Xám_Xanh', 'size' => 'S', 'stock_quantity' => 12,'price' => 450000],
    ['color' => 'Xám_Xanh', 'size' => 'L', 'stock_quantity' => 5,'price' => 450000],
     ['color' => 'Xám_Xanh', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'S', 'stock_quantity' => 10,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'M', 'stock_quantity' => 8,'price' => 450000],
    ['color' => 'Nâu_be', 'size' => 'L', 'stock_quantity' => 12,'price' => 450000],
    
]);
    }
}
