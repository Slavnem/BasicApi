<?php
class UsersGateway {
    // Tablo ve Sütunlar
    protected static $table = "UserList";
    protected static $column1 = "id";
    protected static $column2 = "email";
    protected static $column3 = "username";
    protected static $column4 = "nickname";
    protected static $column5 = "time";

    public static $param_id = "id";
    public static $param_email = "email";
    public static $param_username = "username";
    public static $param_nickname = "nickname";

    private PDO $connection; // veritabanı bağalntısı için obje
    
    
    public function __construct(Database $database) {
        // Veritabanına bağalntı sağlama
        $this->connection = $database->getConnection();
    }

    // Tüm Kayıtları Getirmek
    public function getAll(): array {
        $sql = "SELECT * FROM ". self::$table; // sorgu
        $stmt = $this->connection->query($sql); // sorgu bağlantısı
        $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC); // sorgu yapıp veri çekme

        // alınan veriyi döndürme
        return $fetch;
    }

    // Yeni Kayıt Oluşturmak
    public function create(array $datas): string {
        // kayıt oluşturan sorgu
        $sql = "INSERT INTO ". self::$table
        ."(". self::$column2 .",". self::$column3 .",".
        self::$column4 .") VALUES (:".
            self::$param_email ." :". self::$param_username ." :". self::$param_nickname .")";

        $stmt = $this->connection->prepare($sql); // sorgu parametreleri için bağlantı
        
        // parametre verilerini almak
        $stmt->bindValue(":". self::$param_email, $datas[self::$param_email] ?? "null@golog.email", PDO::PARAM_STR); // email string
        $stmt->bindValue(":". self::$param_username, $datas[self::$param_username] ?? "null_username", PDO::PARAM_STR); // username string
        $stmt->bindValue(":". self::$param_nickname, $datas[self::$param_nickname] ?? "null_nickname", PDO::PARAM_STR); // nickname string
    
        $stmt->execute(); // sorguyu çalıştırmak

        return $this->connection->lastInsertId(); // kayıt edilen id'yi döndürmek
    }

    // Kayıt Getirmek
    public function get(string $id) {
        // sorgu
        $sql = "SELECT * FROM ". self::$table
        ." WHERE ". self::$column1 ." = :". self::$param_id;

        // sorgu bağlantısı
        $stmt = $this->connection->prepare($sql);

        // sorgu parametresi (tamsayı)
        $stmt->bindValue(":". self::$param_id, $id, PDO::PARAM_INT);
        $stmt->execute(); // sorgu çalıştır

        // bulunan sonucu depola
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // depolanan sonucu döndür
        return $datas;
    }

    // Kayıt Güncellemek
    public function update(array $current, array $new): int {
        // current dizisi kendisi 2 boyutlu dizi olarak gördüğünden
        // ama halbuki sadece tek boyutlu dizi olduğundan
        // kendisinin 2 boyutlu halinin ilk indisini kendisine veriyoruz
        $current = $current[0];

        // sorgu
        $sql = "UPDATE ". self::$table ." SET ".
            self::$column2 ." = :". self::$param_email .",".
            self::$column3 ." = :". self::$param_username .",".
            self::$column4 ." = :". self::$param_nickname
            ." WHERE ". self::$column1 ." = :". self::$param_id; 

        // sorgu bağlantısı
        $stmt = $this->connection->prepare($sql);

        // sorgu parametreleri
        $stmt->bindValue(":". self::$param_id, $current[self::$column1], PDO::PARAM_INT);
        $stmt->bindValue(":". self::$param_email, $new[self::$column2] ?? $current[self::$column2], PDO::PARAM_STR);
        $stmt->bindValue(":". self::$param_username, $new[self::$column3] ?? $current[self::$column3], PDO::PARAM_STR);
        $stmt->bindValue(":". self::$param_nickname, $new[self::$column4] ?? $current[self::$column4], PDO::PARAM_STR);

        // sorguyu çalıştırma
        $stmt->execute();

        // uygulandığı satır miktarını döndürme
        return $stmt->rowCount();
    }

    // Kayıt Silmek
    public function delete(string $id): int {
        // sorgu
        $sql = "DELETE FROM ". self::$table
        ." WHERE ". self::$column1 ." = :". self::$param_id;

        $stmt = $this->connection->prepare($sql); // sorgu bağlantısı
        $stmt->bindValue(":". self::$param_id, $id, PDO::PARAM_INT); // sorgu parametresi
        $stmt->execute(); // sorguyu çalıştırma

        return $stmt->rowCount(); // etkilenen satır miktarını döndürme
    }
}
?>