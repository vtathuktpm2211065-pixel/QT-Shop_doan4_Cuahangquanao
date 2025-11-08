<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    protected $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    protected $allowedVideoTypes = ['mp4', 'mov', 'avi', 'mkv'];
    protected $allowedDocumentTypes = ['pdf', 'doc', 'docx', 'txt'];
    protected $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function uploadFiles($files, $folder = 'ai-chat-attachments')
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            try {
                $uploadedFile = $this->processFile($file, $folder);
                if ($uploadedFile) {
                    $uploadedFiles[] = $uploadedFile;
                }
            } catch (\Exception $e) {
                \Log::error('File upload error: ' . $e->getMessage());
                continue;
            }
        }

        return $uploadedFiles;
    }

    private function processFile($file, $folder)
    {
        // Kiểm tra kích thước file
        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('File quá lớn. Kích thước tối đa: 5MB');
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::random(20) . '_' . time() . '.' . $extension;
        $fileType = $this->getFileType($extension);

        // Validate file type
        if (!$this->isAllowedFileType($extension, $fileType)) {
            throw new \Exception('Loại file không được hỗ trợ: ' . $extension);
        }

        $path = $file->storeAs($folder, $fileName, 'public');

        // Xử lý ảnh - tạo thumbnail
        $thumbnailPath = null;
        if ($fileType === 'image') {
            $thumbnailPath = $this->createThumbnail($file, $fileName, $folder);
        }

        return [
            'original_name' => $originalName,
            'file_name' => $fileName,
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'type' => $fileType,
            'extension' => $extension,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'url' => Storage::url($path),
            'thumbnail_url' => $thumbnailPath ? Storage::url($thumbnailPath) : null,
            'uploaded_at' => now()->toISOString()
        ];
    }

    private function getFileType($extension)
    {
        if (in_array($extension, $this->allowedImageTypes)) {
            return 'image';
        } elseif (in_array($extension, $this->allowedVideoTypes)) {
            return 'video';
        } elseif (in_array($extension, $this->allowedDocumentTypes)) {
            return 'document';
        } else {
            return 'other';
        }
    }

    private function isAllowedFileType($extension, $fileType)
    {
        $allowedTypes = array_merge(
            $this->allowedImageTypes,
            $this->allowedVideoTypes,
            $this->allowedDocumentTypes
        );

        return in_array($extension, $allowedTypes);
    }

    private function createThumbnail($file, $fileName, $folder)
    {
        try {
            // Kiểm tra xem Intervention Image có được cài đặt không
            if (!class_exists('Intervention\Image\Facades\Image')) {
                return null;
            }

            $image = Image::make($file->getRealPath());
            
            // Resize ảnh với tỷ lệ giữ nguyên
            $image->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $thumbnailName = 'thumb_' . $fileName;
            $thumbnailPath = $folder . '/thumbs/' . $thumbnailName;

            // Tạo thư mục thumbs nếu chưa tồn tại
            Storage::disk('public')->makeDirectory($folder . '/thumbs');

            Storage::disk('public')->put($thumbnailPath, $image->encode());

            return $thumbnailPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteFile($filePath)
    {
        try {
            // Xóa file chính
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Xóa thumbnail nếu có
            $thumbnailPath = dirname($filePath) . '/thumbs/thumb_' . basename($filePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('File deletion error: ' . $e->getMessage());
            return false;
        }
    }

    public function getFileInfo($filePath)
    {
        if (!Storage::disk('public')->exists($filePath)) {
            return null;
        }

        return [
            'url' => Storage::url($filePath),
            'size' => Storage::disk('public')->size($filePath),
            'last_modified' => Storage::disk('public')->lastModified($filePath),
        ];
    }
}