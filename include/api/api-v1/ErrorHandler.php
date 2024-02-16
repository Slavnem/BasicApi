<?php
class ErrorHandler {
    public static function handleException(Throwable $exception): void {
        // Http kodu döndürmek
        http_response_code(500);

        // Dönen hatayı çıktı vermek
        // Güvenlik amaçlı dosya ve satır bilgisini çıktı vermiyoruz
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            // "file" => $exception->getFile(),
            // "line" => $exception->getLine()
        ]);
    }

    public static function handleError(
        int $errno, // hata kodu
        string $errstr, // hata yazısı
        string $errfile, // hata dosyası
        int $errline // hata satırı
    ): bool
    {
        // güvenlik amaçlı doya adı ve satırı boş
        throw new ErrorException(
            $errstr,
            0,
            $errno,
            NULL, // $errfile,
            NULL // $errline
        );
    }
}
?>