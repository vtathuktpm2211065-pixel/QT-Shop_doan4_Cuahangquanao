<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
          'order_amount',
        'usage_limit',
        'used_count',
        'only_new_users',
        'start_date',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'only_new_users' => 'boolean',
        'status' => 'boolean',
    ];
    

    public function isActive(): bool
    {
        $now = Carbon::now();

        if (!$this->status) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit > 0 && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

  public function getUsedOrdersCountAttribute()
{
    return \App\Models\Order::where('voucher_id', $this->id)->count();
}

   public function incrementUsedCount()
  {
       $this->increment('used_count');
  }


public function scopeAvailable($query, $total = null, $selectedCode = null)
{
    $now = now();

    return $query->where(function ($query) use ($now, $total, $selectedCode) {
        $query->where(function ($q) use ($now, $total) {
                $q->where(function ($q2) use ($now) {
                        $q2->whereNull('start_date')
                           ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($q2) use ($now) {
                        $q2->whereNull('expires_at')
                           ->orWhere('expires_at', '>=', $now);
                    })
                    ->where(function ($q2) {
                        $q2->whereNull('usage_limit')
                           ->orWhereColumn('used_count', '<', 'usage_limit');
                    })
                    ->where('status', true)
                    ->when($total !== null, function ($q2) use ($total) {
                        $q2->where(function ($q3) use ($total) {
                            $q3->whereNull('min_order_amount')
                               ->orWhere('min_order_amount', '<=', $total);
                        })
                        ->where(function ($q3) use ($total) {
                            $q3->whereNull('order_amount')
                               ->orWhere('order_amount', '>=', $total);
                        });
                    });
            })
            ->orWhere('code', $selectedCode); // 👈 Đưa vào chung nhóm
    });
}

}
