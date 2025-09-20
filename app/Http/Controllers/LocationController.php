<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    /**  
     * Đường dẫn đến file chứng chỉ CA (chỉ dùng khi production)
     */
    protected $caCertPath = 'C:\xampp\php\extras\ssl\cacert.pem';

    /**
     * Helper tạo client HTTP:
     * - local  => withoutVerifying()
     * - prod   => withOptions(['verify' => cacert.pem])
     */
    protected function http()
    {
        if (app()->environment('local')) {
            return Http::withoutVerifying();
        }

        return Http::withOptions([
            'verify' => $this->caCertPath,
        ]);
    }

    /**
     * Lấy danh sách tỉnh/thành
     */
    public function getProvinces()
    {
        $response = $this->http()->get('https://provinces.open-api.vn/api/p/');
        return response()->json($response->json());
    }

    /**
     * Lấy danh sách quận/huyện theo tên province
     */
    public function getDistricts(Request $request)
    {
        $provinceName = $request->query('province');
        $provinces = $this->http()->get('https://provinces.open-api.vn/api/p/')->json();
        $province = collect($provinces)->firstWhere('name', $provinceName);

        if (! $province) {
            return response()->json(['error' => 'Không tìm thấy tỉnh'], 404);
        }

        $response = $this->http()
            ->get("https://provinces.open-api.vn/api/p/{$province['code']}?depth=2");

        if ($response->successful()) {
            return response()->json([
                'districts' => $response->json()['districts'] ?? []
            ]);
        }

        return response()->json(['error' => 'Không lấy được quận/huyện'], 500);
    }

    /**
     * Lấy danh sách xã/phường theo tên district
     */
    public function getWards(Request $request)
    {
        $districtName = $request->query('district');
        $districts = $this->http()->get('https://provinces.open-api.vn/api/d/')->json();
        $district = collect($districts)->firstWhere('name', $districtName);

        if (! $district) {
            return response()->json(['error' => 'Không tìm thấy quận/huyện'], 404);
        }

        $response = $this->http()
            ->get("https://provinces.open-api.vn/api/d/{$district['code']}?depth=2");

        if ($response->successful()) {
            return response()->json([
                'wards' => $response->json()['wards'] ?? []
            ]);
        }

        return response()->json(['error' => 'Không lấy được xã/phường'], 500);
    }

    /**
     * Lấy quận/huyện theo mã tỉnh
     */
    public function getDistrictsByProvinceCode($code)
    {
        $response = $this->http()->get("https://provinces.open-api.vn/api/p/{$code}?depth=2");

        if ($response->successful()) {
            return response()->json([
                'districts' => $response->json()['districts'] ?? []
            ]);
        }

        return response()->json(['error' => 'Không lấy được quận/huyện'], 500);
    }

    /**
     * Lấy xã/phường theo mã quận/huyện
     */
    public function getWardsByDistrictCode($code)
    {
        $response = $this->http()->get("https://provinces.open-api.vn/api/d/{$code}?depth=2");

        if ($response->successful()) {
            return response()->json([
                'wards' => $response->json()['wards'] ?? []
            ]);
        }

        return response()->json(['error' => 'Không lấy được xã/phường'], 500);
    }
}
