<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function show($id)
    {
        try {
            $userId = auth()->id();
            Log::info('Fetching address', ['id' => $id, 'user_id' => $userId, 'route' => request()->route()->uri()]);
            if (!$userId) {
                Log::warning('User not authenticated', ['id' => $id]);
                return response()->json(['error' => 'Người dùng chưa đăng nhập'], 401);
            }
            $address = Address::where('user_id', $userId)->find($id);
            if (!$address) {
                Log::warning('Address not found', ['id' => $id, 'user_id' => $userId]);
                return response()->json(['error' => 'Địa chỉ không tồn tại'], 404);
            }
            Log::info('Address fetched successfully', ['id' => $id, 'phone_number' => $address->phone_number]);
            return response()->json(['phone_number' => $address->phone_number ?? '']);
        } catch (\Exception $e) {
            Log::error('Error fetching address', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => request()->all()
            ]);
            return response()->json(['error' => 'Không thể tải thông tin địa chỉ', 'details' => $e->getMessage()], 500);
        }
    }
 public function updateOldAddresses()
{
    $addresses = Address::all();
    $certPath = 'C:\xampp\php\extras\ssl\cacert.pem'; // Đường dẫn tuyệt đối

    foreach ($addresses as $address) {
        try {
            if (is_numeric($address->ward)) {
                $httpOptions = ['verify' => $certPath];

                $provinceResponse = Http::withOptions($httpOptions)->get("https://provinces.open-api.vn/api/p/{$address->province}");
                $province = $provinceResponse->successful() ? $provinceResponse->json()['name'] ?? $address->province : $address->province;

                $districtResponse = Http::withOptions($httpOptions)->get("https://provinces.open-api.vn/api/d/{$address->district}");
                $district = $districtResponse->successful() ? $districtResponse->json()['name'] ?? $address->district : $address->district;

                $wardResponse = Http::withOptions($httpOptions)->get("https://provinces.open-api.vn/api/w/{$address->ward}");
                $ward = $wardResponse->successful() ? $wardResponse->json()['name'] ?? $address->ward : $address->ward;

                $address->update([
                    'province' => $province,
                    'district' => $district,
                    'ward'     => $ward,
                ]);

                \Log::info("✔ Đã cập nhật địa chỉ ID {$address->id}", compact('province', 'district', 'ward'));
            }
        } catch (\Exception $e) {
            \Log::warning("❌ Không thể cập nhật địa chỉ ID {$address->id}: {$e->getMessage()}");
        }
    }

    return response()->json(['message' => 'Đã cập nhật tất cả địa chỉ chứa mã!']);
}

}