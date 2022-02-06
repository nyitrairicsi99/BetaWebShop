<?php
    namespace Controller;

    use PDO;
    use View\Order;
    use View\Header;
    use View\Navbar;
    use Model\NavbarItem;
    use Model\NavbarDropdown;

    class OrderController {

        private static $instance = null;
        public static $basket;

        private function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new OrderController();
            }

            return self::$instance;
        }

        public static function makeOrder() {
            UserController::getInstance();
            BasketController::getInstance();

            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $postcode = $_POST['postcode'];
            $city = $_POST['city'];
            $street = $_POST['street'];
            $housenumber = $_POST['housenumber'];
            $paytype = $_POST['paytype'];
            $user = null;
            if (UserController::$loggedUser!=null) {
                $user = UserController::$loggedUser->id;
            }
            
            if (!isset($postcode) || !isset($city) || !isset($street) || !isset($housenumber)) {
                redirect("orderdetails",[
                    "error" => "Hiányzó paraméterek."
                ]);
                return;
            }
            //postcode mentés
            $sql = 'SELECT id FROM postcodes WHERE postcode=:postcode';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':postcode' => $postcode
            ]);
            $postcodeRow = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$postcodeRow) {
                $sql = 'INSERT INTO `postcodes`(`postcode`) VALUES (:postcode)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':postcode' => $postcode
                ]);
                $postcodeId = $pdo->lastInsertId();
            } else {
                $postcodeId = $postcodeRow['id'];
            }

            //city
            $sql = 'SELECT id FROM cities WHERE name=:city AND postcodes_id=:postcode';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':city' => $city,
                ':postcode' => $postcodeId,
            ]);
            $cityRow = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$cityRow) {
                $sql = 'INSERT INTO `cities`(`name`,`postcodes_id`) VALUES (:city,:postcode)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':city' => $city,
                    ':postcode' => $postcodeId,
                ]);
                $cityId = $pdo->lastInsertId();
            } else {
                $cityId = $cityRow['id'];
            }

            //street
            $sql = 'SELECT id FROM streets WHERE street=:street';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':street' => $street
            ]);
            $streetRow = $statement->fetch(PDO::FETCH_ASSOC);
            
            if (!$streetRow) {
                $sql = 'INSERT INTO `streets`(`street`) VALUES (:street)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':street' => $street
                ]);
                $streetId = $pdo->lastInsertId();
            } else {
                $streetId = $streetRow['id'];
            }

            //house number
            $sql = 'SELECT id FROM house_numbers WHERE number=:number';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':number' => $housenumber
            ]);
            $houseNumberRow = $statement->fetch(PDO::FETCH_ASSOC);

            
            if (!$houseNumberRow) {
                $sql = 'INSERT INTO `house_numbers`(`number`) VALUES (:housenumber)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':housenumber' => $housenumber
                ]);
                $houseNumberId = $pdo->lastInsertId();
            } else {
                $houseNumberId = $houseNumberRow['id'];
            }

            //address
            $sql = 'SELECT id FROM addresses WHERE cities_id=:city AND streets_id=:street AND house_numbers_id=:housenumber';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':city' => $cityId,
                ':street' => $streetId,
                ':housenumber' => $houseNumberId
            ]);
            $addressRow = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$addressRow) {
                $sql = 'INSERT INTO `addresses`(`cities_id`,`streets_id`,`house_numbers_id`) VALUES (:city,:street,:housenumber)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':city' => $cityId,
                    ':street' => $streetId,
                    ':housenumber' => $houseNumberId
                ]);
                $addressId = $pdo->lastInsertId();
            } else {
                $addressId = $addressRow['id'];
            }

            $sql = 'INSERT INTO `orders`(`state_id`, `users_id`, `pay_types_id`, `addresses_id`) VALUES (1,:user,:pay_type,:address)';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':user' => $user,
                ':pay_type' => $paytype,
                ':address' => $addressId
            ]);
            $orderId = $pdo->lastInsertId();

            
            $basket = BasketController::getItems();
            foreach ($basket as $basketitem) {
                $sql = 'INSERT INTO `product_order`(`products_id`, `orders_id`, `discounts_id`, `piece`) VALUES (:product,:order,NULL,:piece)';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':product' => $basketitem->id,
                    ':order' => $orderId,
                    ':piece' => $basketitem->piece
                ]);
            }

            BasketController::clearItems();

            //TODO: KUPON ÉRVÉNYESÍTÉS
            //TODO: KUPON BESZÚRÁSA


        }

        public static function createView() {
            UserController::getInstance();
            
            $details = [
                "id" => "",
                "postcode" => "",
                "city" => "",
                "street" => "",
                "house_number" => "",
                "phone" => "",
                "first_name" => "",
                "last_name" => "",
            ];

            if (UserController::$loggedUser!=null) {
                $id = UserController::$loggedUser->id;
                DatabaseConnection::getInstance();
                $pdo = DatabaseConnection::$connection;

                $sql = '
                    SELECT
                        users.id as id,
                        people.postcode as postcode,
                        people.city as city,
                        people.street as street,
                        people.house_number as house_number,
                        people.phone as phone,
                        people.first_name as first_name,
                        people.last_name as last_name
                    FROM
                        users
                    LEFT JOIN 
                        (SELECT
                            postcodes.postcode as postcode,
                            cities.name as city,
                            streets.street as street,
                            house_numbers.number as house_number,
                            people.phone_number as phone,
                            people.first_name as first_name,
                            people.last_name as last_name,
                            people.id as id
                        FROM
                            `postcodes`,
                            `cities`,
                            `streets`,
                            `house_numbers`,
                            `addresses`,
                            `people`
                        WHERE
                            postcodes.id = cities.postcodes_id AND
                            cities.id = addresses.cities_id AND
                            streets.id = addresses.streets_id AND
                            house_numbers.id = addresses.house_numbers_id AND
                            addresses.id=people.addresses_id
                        ) as people
                    ON
                        people.id = users.people_id
                    WHERE
                        users.id=:i;
                ';

                $statement = $pdo->prepare($sql);
                $statement->bindValue(':i', (int) $id, PDO::PARAM_INT);

                $statement->execute();

                $user = $statement->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $details = [
                        "id" => $user['id'],
                        "postcode" => $user['postcode'],
                        "city" => $user['city'],
                        "street" => $user['street'],
                        "house_number" => $user['house_number'],
                        "phone" => $user['phone'],
                        "first_name" => $user['first_name'],
                        "last_name" => $user['last_name'],
                    ];
                }
            }

            $sql = 'SELECT * FROM pay_types';
            $statement = $pdo->prepare($sql);
            $statement->execute();

            $payTypes = $statement->fetchAll(PDO::FETCH_ASSOC);
            if ($payTypes) {
                $details['paytypes'] = [];
                foreach($payTypes as $payType) {
                    array_push($details['paytypes'],[
                        "id" => $payType['id'],
                        "type" => $payType['type'],
                    ]);
                }
            }
            
            
            SettingsController::getInstance();
            $shopname = SettingsController::$shopname;

            new Header($shopname);
            $navbar = new Navbar($shopname);

            $navbar->addItem(new NavbarItem("Főoldal","main",false));
            
            CategoryController::getInstance();
            $categories = CategoryController::getCategories(true,false);
            foreach ($categories as $main) {
                if (count($main["subcategories"])==0) {
                    $short = $main["short"];
                    $name = $main["name"];
                    $navbar->addItem(new NavbarItem($name,$short,false));
                } else {
                    $navitems = [];
                    foreach ($main["subcategories"] as $sub) {
                        $short = $sub["short"];
                        $name = $sub["name"];
                        array_push($navitems,new NavbarItem($name,$short,false));
                    }
                    $name = $main["name"];
                    $navbar->addItem(new NavbarDropdown($name,$navitems));
                }
            }

            $navbar->create();

            new Order($details);
        }
        
    }