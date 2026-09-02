<?php
// core/Response.php

class Response {
    public static function json(mixed $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'Thao tác thành công', int $statusCode = 200): void {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error(string $message = 'Đã xảy ra lỗi. Vui lòng thử lại sau.', int $statusCode = 400, mixed $errors = null): void {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    public static function redirect(string $path): void {
        $url = str_starts_with($path, 'http') ? $path : BASE_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    public static function download(string $filePath, ?string $downloadName = null): void {
        if (!file_exists($filePath)) {
            http_response_code(404);
            die("Tệp tin không tồn tại.");
        }

        $filename = $downloadName ?? basename($filePath);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
