
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->string('name'); // name VARCHAR(255) NOT NULL
            $table->string('slug')->unique(); // slug VARCHAR(255) UNIQUE
            $table->text('description')->nullable(); // description TEXT, có thể null
            $table->decimal('price', 10, 2); // price DECIMAL(10,2) NOT NULL
            $table->unsignedBigInteger('category_id')->nullable(); // category_id INT, có thể null
            $table->string('image_url')->nullable(); // image_url VARCHAR(255), có thể null
            // Thêm các trường mới cho phân loại sản phẩm
       
                $table->string('gioi_tinh')->nullable();  // Không dùng ->after() ở đây
    
 $table->boolean('pho_bien')->default(false);
    $table->boolean('noi_bat')->default(false);
            $table->timestamps(); // created_at và updated_at DATETIME tự động

            // Khóa ngoại category_id tham chiếu tới categories(id)
        
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
