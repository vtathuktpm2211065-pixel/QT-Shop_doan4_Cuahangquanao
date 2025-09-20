<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color',
        'size',
        'price',
        'stock_quantity',
        'real_stock',
    ];

    // Quan hệ ngược đến sản phẩm cha
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ đến lịch sử nhập xuất kho
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'variant_id');
    }

    // Accessor lấy tồn kho khả dụng, ưu tiên real_stock nếu có
    public function getAvailableStockAttribute()
    {
        return $this->real_stock ?? $this->stock_quantity ?? 0;
    }

    /**
     * Tăng tồn kho.
     *
     * @param int $quantity Số lượng cần tăng (phải > 0)
     * @param bool $useRealStock Nếu true cập nhật real_stock, false cập nhật stock_quantity
     * @throws \Exception Nếu số lượng không hợp lệ
     */
    public function increaseStock(int $quantity, bool $useRealStock = false)
    {
        if ($quantity <= 0) {
            throw new \Exception('Số lượng tăng phải lớn hơn 0');
        }

        DB::transaction(function () use ($quantity, $useRealStock) {
            if ($useRealStock) {
                $this->increment('real_stock', $quantity);
            } else {
                $this->increment('stock_quantity', $quantity);
            }
        });
    }

    /**
     * Giảm tồn kho.
     *
     * @param int $quantity Số lượng cần giảm (phải > 0)
     * @param bool $useRealStock Nếu true cập nhật real_stock, false cập nhật stock_quantity
     * @throws \Exception Nếu số lượng không hợp lệ hoặc không đủ tồn kho
     */
    public function decreaseStock(int $quantity, bool $useRealStock = false)
    {
        if ($quantity <= 0) {
            throw new \Exception('Số lượng giảm phải lớn hơn 0');
        }

        DB::transaction(function () use ($quantity, $useRealStock) {
            if ($useRealStock) {
                $this->refresh(); // lấy dữ liệu mới nhất
                if ($this->real_stock < $quantity) {
                    throw new \Exception('Không đủ tồn kho thực tế');
                }
                $this->decrement('real_stock', $quantity);
            } else {
                $this->refresh();
                if ($this->stock_quantity < $quantity) {
                    throw new \Exception('Không đủ tồn kho');
                }
                $this->decrement('stock_quantity', $quantity);
            }
        });
    }
}
