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
                            if (isset($_POST['theme'])) {
                                $theme = $_POST['theme'];
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
                        case 'updatelanguage':
                            if (isset($_POST['language'])) {
                                $language = $_POST['language'];
                                $sql = '
                                    UPDATE
                                        settings
                                    SET 
                                        languages_id=:language
                                ';

                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':language' => $language
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
                        case 'downloadlanguage':
                            LanguageController::getInstance();
                            SettingsController::getInstance();
                            LanguageController::setLanguage($_POST['language']);
                            $language = SettingsController::$language;
                            $longname = strtolower(LanguageController::$longname);
                            $json = LanguageController::getString();
                            header("Content-type: text/plain");
                            header("Content-Disposition: attachment; filename=".$longname.".json");
                            print $json;
                            LanguageController::setLanguage($language);
                            break;
                        case 'uploadlanguage':
                            $data = getFileContent();
                            LanguageController::getInstance();
                            if (LanguageController::createLanguage($data)) {
                                redirect("admin/".$page,[
                                    "success" => "Sikeres művelet."
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => "Hiba a művelet végrehajtása során."
                                ]);
                            }
                            break;
                        case 'redirectlanguage':
                            redirect("admin/languages/".$_POST['language']."/1");
                            break;
                        case 'modifyphrase':
                            $id = $_POST['id'];
                            $translated = $_POST['translated'];

                            $sql = '
                                UPDATE phrases SET translated=:translated WHERE id=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id,
                                ':translated' => $translated,
                            ]);

                            redirect("admin/languages/".$_POST['language']."/".$_POST['page']);
                            break;
                        case 'deletelanguage':
                            $language = $_POST['language'];
                            $sql = '
                                DELETE FROM languages WHERE id=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $language,
                            ]);
                            redirect("admin",[
                                "success" => "Sikeres művelet."
                            ]);
                            break;
                        case 'removecategory':
                            $id = $_POST['id'];

                            $sql = '
                                UPDATE categories SET parentcategory=NULL, display_navbar=0 WHERE id=:id OR parentcategory=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id
                            ]);
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                        case 'deletecategory':
                            $id = $_POST['id'];

                            $sql = '
                                DELETE FROM categories WHERE id=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id
                            ]);
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);
                            break;
                        case 'managecategory':
                            $maincategory = $_POST['maincategory'];
                            $maincategory = $maincategory>0 ? $maincategory : null;
                            $selectedcategory = $_POST['selectedcategory'];

                            $sql = '
                                UPDATE categories SET parentcategory=:parent, display_navbar=1 WHERE id=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $selectedcategory,
                                ':parent' => $maincategory,
                            ]);
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                        case 'newcategory':
                            $name = $_POST['name'];
                            $shortname = $_POST['shortname'];
                            $sql = '
                                INSERT INTO `categories`(`parentcategory`, `name`, `short`, `display_navbar`) VALUES (NULL,:name,:shortname,0)
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':name' => $name,
                                ':shortname' => $shortname,
                            ]);
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                            
                        case 'createproduct':
                            $name = $_POST['name'];
                            $description = $_POST['description'];
                            $price = $_POST['price'];
                            $currency_id = $_POST['currency'];
                            $stock = $_POST['stock'];
                            $category_id = $_POST['category'];
                            $availablefrom = $_POST['availablefrom'];
                            $availableto = $_POST['availableto'];
                            $alwaysavailable = isset($_POST['alwaysavailable']);

                            
                            $uploadedNames = uploadFiles("files");
                            
                            if (count($uploadedNames)==0) {
                                redirect("admin/".$page,[
                                    "error" => "Kép nélkül nem hozható létre termék."
                                ]);
                            }

                            $sql = '
                                INSERT INTO `products`(`name`, `description`, `price`, `currencies_id`, `units_id`, `stock`, `active_from`, `active_to`, `display_notactive`, `categories_id`)
                                VALUES (:name,:description,:price,:currency_id,NULL,:stock,:availablefrom,:availableto,:alwaysavailable,:category_id)
                            ';

                            
                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':name' => $name,
                                ':description' => $description,
                                ':price' => $price,
                                ':currency_id' => $currency_id,
                                ':stock' => $stock,
                                ':availablefrom' => $availablefrom,
                                ':availableto' => $availableto,
                                ':alwaysavailable' => $alwaysavailable ? '1' : '0',
                                ':category_id' => $category_id
                            ]);

                            $id = $pdo->lastInsertId();

                            foreach ($uploadedNames as $file) {
                                $sql = '
                                    INSERT INTO `product_images`(`products_id`, `url`) VALUES (:id,:file)
                                ';

                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':id' => $id,
                                    ':file' => $file,
                                ]);
                            }
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                        case 'manageproduct':
                            
                            $sql = 'SELECT id FROM product_images';
                            $statement = $pdo->query($sql);
                            $statement->execute();
                            $imgs = $statement->fetchAll(PDO::FETCH_ASSOC);

                            if ($imgs) {
                                foreach ($imgs as $img) {
                                    if (isset($_POST['delete_'.$img['id']])) {
                                        $id = $img['id'];
                                        $sql = 'DELETE FROM `product_images` WHERE id=:id';
                                        $statement = $pdo->prepare($sql);
                                        $statement->execute([
                                            ':id' => $id
                                        ]);
                                        break;
                                    }
                                }
                            }
                            
                            $id = $_POST['id'];
                            $description = $_POST['description'];
                            $price = $_POST['price'];
                            $name = $_POST['name'];
                            $currency = $_POST['currency'];
                            $stock = $_POST['stock'];
                            $category = $_POST['category'];
                            $availablefrom = $_POST['availablefrom'];
                            $availableto = $_POST['availableto'];
                            $alwaysavailable = isset($_POST['alwaysavailable']) ? '1' : '0';

                            $sql = '
                                UPDATE
                                    products
                                SET
                                    name=:name,
                                    description=:description,
                                    price=:price,
                                    currencies_id=:currency,
                                    stock=:stock,
                                    active_from=:availablefrom,
                                    active_to=:availableto,
                                    display_notactive=:alwaysavailable,
                                    categories_id=:category
                                WHERE
                                    id=:id
                                ';
                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id,
                                ':description' => $description,
                                ':name' => $name,
                                ':price' => $price,
                                ':currency' => $currency,
                                ':stock' => $stock,
                                ':category' => $category,
                                ':availablefrom' => $availablefrom,
                                ':availableto' => $availableto,
                                ':alwaysavailable' => $alwaysavailable,
                            ]);

                            
                            $uploadedNames = uploadFiles("files");
                            foreach ($uploadedNames as $file) {
                                $sql = '
                                    INSERT INTO `product_images`(`products_id`, `url`) VALUES (:id,:file)
                                ';

                                $statement = $pdo->prepare($sql);
                                $statement->execute([
                                    ':id' => $id,
                                    ':file' => $file,
                                ]);
                            }

                            
                            redirect("admin/".$page."/".$id,[
                                "success" => "Sikeres művelet."
                            ]);

                            break;
                        case 'deleteproduct':
                            $id = $_POST['id'];
                            
                            $sql = '
                                UPDATE products SET deleted=1 WHERE id=:id
                            ';

                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $id,
                            ]);
                            
                            redirect("admin/".$page,[
                                "success" => "Sikeres művelet."
                            ]);

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
    