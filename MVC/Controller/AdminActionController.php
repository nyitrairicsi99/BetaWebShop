<?php
    namespace Controller;

    use Controller\UserController;
    use PDO;

    class AdminActionController
    {
        public function __construct($page,$id,$action,$method)
        {
            UserController::getInstance();
            DatabaseConnection::getInstance();

            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                $pdo = DatabaseConnection::$connection;
                if ($method=="POST") {
                    switch ($action) {
                        case 'password':
                            $password = $_POST['password'];
                            if (isset($password) && passwordsAcceptable($password,$password)==0) {
                                $sql = 'UPDATE users SET password = :password WHERE id = :id';

                                $statement = $pdo->prepare($sql);
    
                                $statement->bindParam(':id', $id, PDO::PARAM_INT);
                                $statement->bindParam(':password', hashPassword($password));
                                $statement->execute();
                                redirect("admin/".$page."/".$id,[
                                    "success" => "Sikeres művelet."
                                ]);
                            } else {
                                redirect("admin/".$page."/".$id,[
                                    "error" => "Jelszó túl rövid."
                                ]);
                            }
                            break;
                        case 'updateuser':
                            $username = $_POST['username'];
                            $email = $_POST['email'];
                            $rank = $_POST['rank'];
                            if ($username && $email && $rank) {
                                $sql = '
                                    UPDATE
                                        users
                                    SET 
                                        ranks_id=(SELECT id FROM ranks WHERE name=:rank),
                                        username=:username,
                                        email=:email
                                    WHERE
                                        id=:id
                                ';

                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':id' => $id,
                                    ':username'=> $username,
                                    ':email'=> $email,
                                    ':rank'=> $rank,
                                ]);

                                redirect("admin/".$page."/".$id,[
                                    "success" => "Sikeres művelet."
                                ]);
                            }
                            break;
                        case 'updatepersonal':
                            $postcode = $_POST['postcode'];
                            $city = $_POST['city'];
                            $street = $_POST['street'];
                            $housenumber = $_POST['housenumber'];
                            $phone = $_POST['phone'];
                            $firstname = $_POST['firstname'];
                            $lastname = $_POST['lastname'];
                            if (!isset($postcode) || !isset($city) || !isset($street) || !isset($housenumber) || !isset($phone) || !isset($firstname) || !isset($lastname)) {
                                redirect("admin/".$page."/".$id,[
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
                            
                            //person
                            $sql = 'SELECT people.id as id FROM people,users WHERE people.id=users.people_id AND users.id=:id';
                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id,
                            ]);
                            $personRow = $statement->fetch(PDO::FETCH_ASSOC);

                            if ($personRow) {
                                $sql = 'UPDATE `people`,`users` SET people.`phone_number`=:phone,people.`addresses_id`=:address,people.`first_name`=:firstname,people.`last_name`=:lastname WHERE users.people_id=people.id AND users.id=:id';

                                $statement = $pdo->prepare($sql);
    
                                $statement->execute([
                                    ':id' => $id,
                                    ':phone' => $phone,
                                    ':address' => $addressId,
                                    ':firstname' => $firstname,
                                    ':lastname' => $lastname,
                                ]);
                            } else {
                                $sql = 'INSERT INTO `people`(`phone_number`, `addresses_id`, `first_name`, `last_name`) VALUES (:phone,:address,:firstname,:lastname)';
                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':phone' => $phone,
                                    ':address' => $addressId,
                                    ':firstname' => $firstname,
                                    ':lastname' => $lastname,
                                ]);
                                $personId = $pdo->lastInsertId();

                                $sql = 'UPDATE `users` SET `people_id`=:person WHERE id=:id';

                                $statement = $pdo->prepare($sql);
    
                                $statement->execute([
                                    ':id' => $id,
                                    ':person' => $personId,
                                ]);

                            }
                            
                            redirect("admin/".$page."/".$id,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                        case 'updatename':
                            $name = $_POST['name'];
                            if (isset($name)) {
                                $sql = 'UPDATE `settings` SET `webshop_name`=:name';
        
                                $statement = $pdo->prepare($sql);
        
                                $statement->execute([
                                    ':name' => $name,
                                ]);
        
                                redirect("admin/".$page,[
                                    "success" => "Sikeres művelet."
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => "Hiányzó paraméterek."
                                ]);
                            }
                            break;
                        case 'updatetheme':
                            $theme = $_POST['theme'];
                            if (isset($theme)) {
                                $sql = '
                                    UPDATE
                                        settings
                                    SET 
                                        themes_id=(SELECT id FROM themes WHERE name=:theme)
                                ';

                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':theme' => $theme
                                ]);
                                
                                redirect("admin/".$page,[
                                    "success" => "Sikeres művelet."
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => "Hiányzó paraméterek."
                                ]);
                            }
                            break;
                        default:
                            break;
                    }
                } else if ($method=="PUT") {

                } else if ($method=="DELETE") {

                }
                
            } else {
                http_response_code(403);
                exit;
            }
        }
    }
    