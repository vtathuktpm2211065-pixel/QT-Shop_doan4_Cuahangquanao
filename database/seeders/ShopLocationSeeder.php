<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopLocation;

class ShopLocationSeeder extends Seeder
{
    public function run()
    {
        $shops = [
            [
                'name' => 'QT Shop - Chi nhánh Cần Thơ',
                'address' => '256,Nguyễn Văn Cừ,An Hòa,Ninh Kiều, Cần Thơ',
                'latitude' => 10.05066922328711, 
                'longitude' => 105.77176981074109,
                'phone' => '0948056732',
                'email' => 'q1@qtshop.com',
                'business_hours' => json_encode([
                    'Thứ 2 - Thứ 6' => '8:00 - 21:00',
                    'Thứ 7' => '8:00 - 22:00', 
                    'Chủ nhật' => '8:00 - 20:00'
                ]),
                'is_active' => true
            ],
          
        ];

        foreach ($shops as $shop) {
            ShopLocation::create($shop);
        }
    }
}