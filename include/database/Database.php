<?php
class Database {
    public function __construct(
        private string $db_host,
        private string $db_name,
        private string $db_user,
        private string $db_password)
    {}

    public function getConnection() : PDO {
        // Veritabanı Bağlantı Bilgisini Saklama
        $dbsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8";

        // Veritabanı İçin PDO Objesi Oluşturup Döndürme
        return new PDO($dbsn, $this->db_user, $this->db_password, [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
}
?>