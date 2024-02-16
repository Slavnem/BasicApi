<?php
    class UsersController {
        public function __construct(private UsersGateway $gateway) {}

        public function processRequest(string $method, ?string $id): void {
            switch($id) {
                // id true
                case true:
                    $this->processResourceRequest($method, $id);
                break;
                // no id
                default:
                    $this->processCollectionRequest($method);
                break;
            }
        }

        private function processResourceRequest(string $method, string $id): void {
            // kullanıcı bilgisini fetch edip getirme
            $user = $this->gateway->get($id);

            // eğer kullanıcı bulunamassa
            if(!$user) {
                http_response_code(404); // bulunamadı hata kodu
                echo json_encode(["message" => "User Not Found"]); // json objesi olarak bilgi çıktısı
                return; // fonksiyon sonlandırma
            }

            // Methoda göre işlem
            switch($method) {
                // getir
                case "GET":
                    // depolanan kullanıcıya ait verileri json objesi olarak çıktı verme
                    echo json_encode($user);
                break;
                // güncelle
                case "PATCH":
                    // Http uzantı kısmına yazılan aramayı json objesi olarak almak
                    // fakat NULL olmaması için dizi olarak alıyoruz
                    $datas = (array)json_decode(file_get_contents("php://input"), true);

                    // hataları depolamak
                    $errors = $this->getValidationErrors($datas, false);

                    // eğer hatalar boş değilse yani hata varsa
                    if(!empty($errors)) {
                        http_response_code(422); // http hata kodunu döndürmek
                        echo json_encode(["errors" => $errors]); // hataları json olan çıktı vermek
                    }

                    // Kullanıcı kayıtı güncelleme
                    $updatedrows = $this->gateway->update($user, $datas);

                    // Kullanıcı kaydedilmesine dair çıktı verme
                    echo json_encode([
                        "message" => "User $id Updated",
                        "rows" => $updatedrows
                    ]);
                break;
                // sil
                case "DELETE":
                    // id silsin ve etkilenen satır miktarını alsın
                    $deletedrows = $this->gateway->delete($id);

                    // silinen kullanıcı ve satır miktarı çıktısı
                    echo json_encode([
                        "message" => "User $id Deleted",
                        "rows" => $deletedrows
                    ]);
                break;
                // geçersiz
                default:
                    http_response_code(405);
                    header("Allow: GET, PATCH, DELETE");
            }
        }

        private function processCollectionRequest(string $method) : void {
            switch($method) {
                // GET
                case "GET":
                    echo json_encode($this->gateway->getAll());
                break;
                // POST
                case "POST":
                    // Http uzantı kısmına yazılan aramayı json objesi olarak almak
                    // fakat NULL olmaması için dizi olarak alıyoruz
                    $datas = (array)json_decode(file_get_contents("php://input"), true);

                    // hataları depolamak
                    $errors = $this->getValidationErrors($datas);

                    // eğer hatalar boş değilse yani hata varsa
                    if(!empty($errors)) {
                        http_response_code(422); // http hata kodunu döndürmek
                        echo json_encode(["errors" => $errors]); // hataları json olan çıktı vermek
                    }

                    // Kullanıcı kayıtı oluşturma
                    $userid = $this->gateway->create($datas);

                    // Http oluşturma kodu
                    http_response_code(201);

                    // Kullanıcı kaydedilmesine dair çıktı verme
                    echo json_encode([
                        "message" => "User",
                        "id" => "$userid"
                    ]);
                break;
                // METHOD KABUL EDİLMEDİ
                default:
                    http_response_code(405);
                    header("Allow: GET, POST");
            }
        }

        private function getValidationErrors(array $datas, bool $is_new = true): array {
            $errors = []; // hataları tutacak

            // email yoksa
            if($is_new && empty($datas[UsersGateway::$param_email]))
                $errors[] = UsersGateway::$param_email ."!";

            // username yoksa
            if($is_new && empty($datas[UsersGateway::$param_username]))
                $errors[] = UsersGateway::$param_username ."!";

            // nickname yoksa
            if($is_new && empty($datas[UsersGateway::$param_nickname]))
                $errors[] = UsersGateway::$param_nickname ."!";

            // hata dizisini döndür
            return $errors;
        }
    }
?>
