<?php
    namespace Controller;

    use Model\NavbarItem;
    use Model\NavbarDropdown;
    
    use View\Header;
    use View\Navbar;
    use View\Profile;

    use PDO;
    
    class ProfileController
    {
        private static $pdo = null;
        private static $instance = null;

        public function __construct()
        {
            DatabaseConnection::getInstance();
            self::$pdo = DatabaseConnection::$connection; 
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new ProfileController();
            }
            UserController::getInstance();
            if (!UserController::$islogged) {
                redirect("main");
            }
            
            return self::$instance;
        }

        public static function show() {
            SettingsController::getInstance();
            $shopname = SettingsController::$shopname;

            new Header($shopname);
            $navbar = new Navbar($shopname);

            $navbar->addItem(new NavbarItem("Főoldal","main",true));
            
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

            $id = UserController::$loggedUser->id;
            $details = [];
            $sql = '
                SELECT
                    users.id as id,
                    users.username as username,
                    users.email as email,
                    users.ranks_id as ranks_id,
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

            $statement = self::$pdo->prepare($sql);
            $statement->bindValue(':i', (int) $id, PDO::PARAM_INT);

            $statement->execute();

            $user = $statement->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $details = [
                    "id" => $user['id'],
                    "username" => $user['username'],
                    "email" => $user['email'],
                    "rank" => $user['ranks_id'],
                    "postcode" => $user['postcode'],
                    "city" => $user['city'],
                    "street" => $user['street'],
                    "house_number" => $user['house_number'],
                    "phone" => $user['phone'],
                    "first_name" => $user['first_name'],
                    "last_name" => $user['last_name'],
                ];

            } else {
                redirect("admin/users");
            }


            new Profile($details);
        }

        public static function modify() {
            $id = UserController::$loggedUser->id;
            $password = $_POST['passwordnow'];

            $sql = 'SELECT `password` FROM `users` WHERE `id`=:id';
            $statement = self::$pdo->prepare($sql);
            $statement->execute([
                ':id' => $id
            ]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (hashMatches($password,$user['password'])) {
                self::updateUserDetails($id,$_POST);
            } else {              
                redirect("profile",[
                    "error" => translate("notification_incorrect_password"),
                ]);
            }
        }

        private static function updateUserDetails($id,$details) {
            $postcode = $details['postcode'];
            $city = $details['city'];
            $street = $details['street'];
            $housenumber = $details['housenumber'];
            $phone = $details['phone'];
            $firstname = $details['firstname'];
            $lastname = $details['lastname'];
            $pass1 = $details['password1'];
            $pass2 = $details['password2'];
            $email = $details['email'];

            if (
                isset($postcode) && isset($city) && isset($street) && isset($housenumber) && isset($phone) && isset($firstname) && isset($lastname) &&
                strlen($postcode)>0 && strlen($city)>0 && strlen($street)>0 && strlen($housenumber)>0 && strlen($phone)>0 && strlen($firstname)>0 && strlen($lastname)>0
            ) {
                //postcode mentés
                $sql = 'SELECT id FROM postcodes WHERE postcode=:postcode';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':postcode' => $postcode
                ]);
                $postcodeRow = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$postcodeRow) {
                    $sql = 'INSERT INTO `postcodes`(`postcode`) VALUES (:postcode)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':postcode' => $postcode
                    ]);
                    $postcodeId = self::$pdo->lastInsertId();
                } else {
                    $postcodeId = $postcodeRow['id'];
                }

                //city
                $sql = 'SELECT id FROM cities WHERE name=:city AND postcodes_id=:postcode';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':city' => $city,
                    ':postcode' => $postcodeId,
                ]);
                $cityRow = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$cityRow) {
                    $sql = 'INSERT INTO `cities`(`name`,`postcodes_id`) VALUES (:city,:postcode)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':city' => $city,
                        ':postcode' => $postcodeId,
                    ]);
                    $cityId = self::$pdo->lastInsertId();
                } else {
                    $cityId = $cityRow['id'];
                }

                //street
                $sql = 'SELECT id FROM streets WHERE street=:street';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':street' => $street
                ]);
                $streetRow = $statement->fetch(PDO::FETCH_ASSOC);
                
                if (!$streetRow) {
                    $sql = 'INSERT INTO `streets`(`street`) VALUES (:street)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':street' => $street
                    ]);
                    $streetId = self::$pdo->lastInsertId();
                } else {
                    $streetId = $streetRow['id'];
                }

                //house number
                $sql = 'SELECT id FROM house_numbers WHERE number=:number';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':number' => $housenumber
                ]);
                $houseNumberRow = $statement->fetch(PDO::FETCH_ASSOC);

                
                if (!$houseNumberRow) {
                    $sql = 'INSERT INTO `house_numbers`(`number`) VALUES (:housenumber)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':housenumber' => $housenumber
                    ]);
                    $houseNumberId = self::$pdo->lastInsertId();
                } else {
                    $houseNumberId = $houseNumberRow['id'];
                }

                //address
                $sql = 'SELECT id FROM addresses WHERE cities_id=:city AND streets_id=:street AND house_numbers_id=:housenumber';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':city' => $cityId,
                    ':street' => $streetId,
                    ':housenumber' => $houseNumberId
                ]);
                $addressRow = $statement->fetch(PDO::FETCH_ASSOC);

                if (!$addressRow) {
                    $sql = 'INSERT INTO `addresses`(`cities_id`,`streets_id`,`house_numbers_id`) VALUES (:city,:street,:housenumber)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':city' => $cityId,
                        ':street' => $streetId,
                        ':housenumber' => $houseNumberId
                    ]);
                    $addressId = self::$pdo->lastInsertId();
                } else {
                    $addressId = $addressRow['id'];
                }
                
                //person
                $sql = 'SELECT people.id as id FROM people,users WHERE people.id=users.people_id AND users.id=:id';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':id' => $id,
                ]);
                $personRow = $statement->fetch(PDO::FETCH_ASSOC);

                if ($personRow) {
                    $sql = 'UPDATE `people`,`users` SET people.`phone_number`=:phone,people.`addresses_id`=:address,people.`first_name`=:firstname,people.`last_name`=:lastname WHERE users.people_id=people.id AND users.id=:id';

                    $statement = self::$pdo->prepare($sql);
        
                    $statement->execute([
                        ':id' => $id,
                        ':phone' => $phone,
                        ':address' => $addressId,
                        ':firstname' => $firstname,
                        ':lastname' => $lastname,
                    ]);
                } else {
                    $sql = 'INSERT INTO `people`(`phone_number`, `addresses_id`, `first_name`, `last_name`) VALUES (:phone,:address,:firstname,:lastname)';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':phone' => $phone,
                        ':address' => $addressId,
                        ':firstname' => $firstname,
                        ':lastname' => $lastname,
                    ]);
                    $personId = self::$pdo->lastInsertId();

                    $sql = 'UPDATE `users` SET `people_id`=:person WHERE id=:id';

                    $statement = self::$pdo->prepare($sql);
        
                    $statement->execute([
                        ':id' => $id,
                        ':person' => $personId,
                    ]);
                }
            }
            
            if (
                isset($pass1) && isset($pass2) &&
                strlen($pass1)>0 && strlen($pass2)>0
            ) {
                $passCheck = passwordsAcceptable($pass1,$pass2);
                if ($passCheck==0) {
                    $sql = 'UPDATE `users` SET `password`=:password WHERE id=:id';
                    $statement = self::$pdo->prepare($sql);
                    $statement->execute([
                        ':id' => $id,
                        ':password' => hashPassword($pass1),
                    ]);
                } else {          
                    switch ($passCheck) {
                        case 1:
                            redirect("profile",[
                                "error" => translate("notification_passwords_not_match"),
                            ]);
                            break;
                        case 2:
                            redirect("profile",[
                                "error" => translate("notification_short_password"),
                            ]);
                            break;
                        default:
                            break;
                    }
                }
            }

            if (
                isset($email) &&
                strlen($email)>0
            ) {
                $sql = 'UPDATE `users` SET `email`=:email WHERE id=:id';
                $statement = self::$pdo->prepare($sql);
                $statement->execute([
                    ':id' => $id,
                    ':email' => $email,
                ]);
            }
                   
            redirect("profile",[
                "success" => translate("notification_success_operation"),
            ]);
        }
        
        

    }
    