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
        // Resolve credentials path from config or environment
        $credentials = config('firebase.credentials.file') ?: env('FIREBASE_CREDENTIALS');

        if ($credentials && is_string($credentials) && file_exists($credentials)) {
            try {
                $factory = (new Factory)->withServiceAccount($credentials);
                $this->messaging = $factory->createMessaging();
            } catch (\Throwable $e) {
                // If Kreait fails for any reason, don't break the whole app — log and continue.
                \Log::error('Failed to initialize Firebase messaging: ' . $e->getMessage());
                $this->messaging = null;
            }
        } else {
            // Credentials file missing — log a warning and leave messaging null so calls are no-ops.
            \Log::warning('Firebase credentials not found. Expected at: ' . ($credentials ?: "config('firebase.credentials.file')"));
            $this->messaging = null;
        }
    }

    // Gửi notification đến user
    public function sendToUser($userId, $title, $body, $data = [])
{
    // If messaging is not initialized (missing credentials), skip sending
    if (empty($this->messaging)) {
        \Log::warning('Skipping sendToUser: Firebase messaging not initialized (missing credentials).');
        return;
    }

    $token = $this->getUserToken($userId);

    if (empty($token)) {
        // Ghi log hoặc bỏ qua thay vì tạo lỗi
        \Log::warning("User {$userId} không có FCM token — không gửi được thông báo.");
        return;
    }

    $message = CloudMessage::withTarget('token', $token)
        ->withNotification(Notification::create($title, $body))
        ->withData($data);

    try {
        $this->messaging->send($message);
    } catch (\Throwable $e) {
        \Log::error('Failed to send Firebase message to user ' . $userId . ': ' . $e->getMessage());
    }
}


    // Gửi notification đến topic
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        if (empty($this->messaging)) {
            \Log::warning('Skipping sendToTopic: Firebase messaging not initialized (missing credentials).');
            return;
        }

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        try {
            $this->messaging->send($message);
        } catch (\Throwable $e) {
            \Log::error('Failed to send Firebase message to topic ' . $topic . ': ' . $e->getMessage());
        }
    }

    private function getUserToken($userId)
    {
        // Lấy FCM token từ database
        // Bạn cần lưu token khi user đăng nhập
        return \App\Models\User::find($userId)->fcm_token ?? null;
    }
}