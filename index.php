<?php
    // Otomatik Tür Dönüşümü Engelleme
    declare(strict_types= 1);

    // Dosyaları Sınıf Adlarına Göre Otomatik Olarak Ekleme
    spl_autoload_register(function ($class) {
        // Farklı Klasörler
        $folders = array(
            __DIR__ . "/include/database/",
            __DIR__ . "/include/api/api-v1/",
            __DIR__ . "/include/api/api-v1/Users/"
        );

        // Her Klasörü Dosya Dosya Dönüp Eklesin
        foreach($folders as $folder) {
            $classfile = $folder . "{$class}.php"; // dosya

            // eğer dosya var ise
            if(file_exists($classfile)) {
                require $classfile; // dosyayı içe aktarsın
                return; // dönmeye devam etmemesi için çünkü dosya bulundu
            }
        }
    });

    set_error_handler("ErrorHandler::handleError"); // Hata Döndürecek
    set_exception_handler("ErrorHandler::handleException"); // Beklenmedik Durum Döndürecek

    // Veri Dönüşü json Olacağı İçin Sayfa İçeriği json
    header("Content-Type: application/json; charset=UTF-8");

    // Http Kısmına Girilmiş Yazıları Parça Parça Almak
    $parts = explode("/", $_SERVER["REQUEST_URI"]);

    // Eğer girilen parçalarda users yoksa hata dönsün
    if($parts[1] != "users") {
        http_response_code(404);
        exit;
    }

    // Varsa eğer 2.parça id olsun yoksa NULL
    $id = $parts[2] ?? null;

    // Veritabanı bağlantısı oluşturma ve bağlanma
    $database = new Database("localhost", "database-name", "database-user", "mysql-password");
    $gateway = new UsersGateway($database);

    // Veritabanına api ile sorgu yapma
    $apicontroller = new UsersController($gateway);
    $apicontroller->processRequest($_SERVER["REQUEST_METHOD"], $id);
?>
