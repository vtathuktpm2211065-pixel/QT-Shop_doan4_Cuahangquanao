<?php
namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials.file'));
        $this->messaging = $factory->createMessaging();
    }

    // Gửi notification đến user
    public function sendToUser($userId, $title, $body, $data = [])
{
    $token = $this->getUserToken($userId);

    if (empty($token)) {
        // Ghi log hoặc bỏ qua thay vì tạo lỗi
        \Log::warning("User {$userId} không có FCM token — không gửi được thông báo.");
        return;
    }

    $message = CloudMessage::withTarget('token', $token)
        ->withNotification(Notification::create($title, $body))
        ->withData($data);

    $this->messaging->send($message);
}


    // Gửi notification đến topic
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        $this->messaging->send($message);
    }

    private function getUserToken($userId)
    {
        // Lấy FCM token từ database
        // Bạn cần lưu token khi user đăng nhập
        return \App\Models\User::find($userId)->fcm_token ?? null;
    }
}