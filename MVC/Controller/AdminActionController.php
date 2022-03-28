<?php
    namespace Controller;

    use Controller\UserController;
    use PDO;

    class AdminActionController
    {
        private static $neededPermissions = [
            'password' => ['manage_users'],
            'updateuser' => ['manage_users'],
            'updatepersonal' => ['manage_users'],
            'updatename' => ['manage_settings'],
            'updatetheme' => ['manage_settings'],
            'updatelanguage' => ['manage_settings'],
            'downloadlanguage' => ['manage_settings'],
            'uploadlanguage' => ['manage_settings'],
            'redirectlanguage' => ['manage_settings'],
            'modifyphrase' => ['manage_settings'],
            'deletelanguage' => ['manage_settings'],
            'removecategory' => ['manage_settings'],
            'deletecategory' => ['manage_settings'],
            'managecategory' => ['manage_settings'],
            'newcategory' => ['manage_settings'],
            'createproduct' => ['create_product'],
            'manageproduct' => ['manage_products'],
            'deleteproduct' => ['delete_product'],
            'deletecoupon' => ['manage_coupons'],
            'createcoupon' => ['manage_coupons'],
            'modifyorderstate' => ['manage_orders'],
            'createrank' => ['manage_permissions'],
            'deleterank' => ['manage_permissions'],
            'editrank' => ['manage_permissions'],
            'switchaddon' => ['manage_addons'],
            'checkforaddons' => ['manage_addons'],
            'checkforthemes' => ['manage_settings'],
            'updatesmtp' => ['manage_settings'],
        ];
        
        private static $instance = null;

        public function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new AdminActionController();
            }

            return self::$instance;
        }

        public static function adminAction($page,$action,$method)
        {
            UserController::getInstance();

            if (UserController::$loggedUser->hasPermission('admin_access')) {
                
                $foundPermission = false;
                foreach (self::$neededPermissions[$action] as $perm) {
                    if (UserController::$loggedUser->hasPermission($perm)) {
                        $foundPermission = true;
                    }
                }
                
                if (!$foundPermission) {
                    redirect("admin",[
                        "error"=>"Nincs jogod ehhez a művelethez."
                    ]);
                }

                DatabaseConnection::getInstance();
                $pdo = DatabaseConnection::$connection;
                if ($method=="POST") {
                    switch ($action) {
                        case 'password':
                            $password = $_POST['password'];
                            $id = $_POST['id'];
                            if (self::updateUserPassword($id,$password)) {
                                redirect("admin/".$page."/".$id,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page."/".$id,[
                                    "error" => translate("notification_short_password")
                                ]);
                            }
                            break;
                        case 'updateuser':
                            $username = $_POST['username'];
                            $email = $_POST['email'];
                            $rank = $_POST['rank'];
                            $id = $_POST['id'];
                            $banned = isset($_POST['banned']);

                            if (self::updateUserInformations($id,$rank,$username,$email,$banned)) {
                                redirect("admin/".$page."/".$id,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin",[
                                    "error"=>"Nincs jogod ehhez a művelethez."
                                ]);
                            }
                            break;
                        case 'updatepersonal':
                            $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : null;
                            $city = isset($_POST['city']) ? $_POST['city'] : null;
                            $street = isset($_POST['street']) ? $_POST['street'] : null;
                            $housenumber = isset($_POST['housenumber']) ? $_POST['housenumber'] : null;
                            $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
                            $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : null;
                            $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : null;
                            $id = isset($_POST['id']) ? $_POST['id'] : null;

                            
                            if (self::updateUserPersonalInformations($id,$postcode,$city,$street,$housenumber,$phone,$firstname,$lastname)) {
                                redirect("admin/".$page."/".$id,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page."/".$id,[
                                    "error" => translate("notification_missing_parameters")
                                ]);
                            }

                            break;
                        case 'updatename':
                            $name = isset($_POST['name']) ? $_POST['name'] : null;
                            if (self::updateShopName($name)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => translate("notification_missing_parameters")
                                ]);
                            }
                            break;
                        case 'updatetheme':
                            $theme = isset($_POST['theme']) ? $_POST['theme'] : null;
                            if (self::updateShopTheme($theme)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => translate("notification_missing_parameters")
                                ]);
                            }
                            break;
                        case 'updatelanguage':
                            $language = isset($_POST['language']) ? $_POST['language'] : null;
                            if (self::updateShopLanguage($language)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => translate("notification_missing_parameters")
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
                                    "success" => translate("notification_success_operation")
                                ]);
                            } else {
                                redirect("admin/".$page,[
                                    "error" => translate("notification_error")
                                ]);
                            }
                            break;
                        case 'redirectlanguage':
                            redirect("admin/languages/".$_POST['language']."/1");
                            break;
                        case 'modifyphrase':
                            $id = $_POST['id'];
                            $translated = $_POST['translated'];
                            $language = $_POST['language'];
                            $phrase = $_POST['phrase'];

                            if (self::modifyPhrase($id,$language,$phrase,$translated)) {
                                redirect("admin/languages/".$language."/".$_POST['page']);
                            }

                            break;
                        case 'deletelanguage':
                            $language = $_POST['language'];
                            if (self::deleteLanguage($language)) {
                                redirect("admin",[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        case 'removecategory':
                            $id = $_POST['id'];
                            
                            if (self::removeCategory($id)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }                           

                            break;
                        case 'deletecategory':
                            $id = $_POST['id'];

                            if (self::deleteCategory($id)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        case 'managecategory':
                            $maincategory = $_POST['maincategory'];
                            $maincategory = $maincategory>0 ? $maincategory : null;
                            $selectedcategory = $_POST['selectedcategory'];

                            if (self::manageCategory($maincategory,$selectedcategory)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }

                            break;
                        case 'newcategory':
                            $name = $_POST['name'];
                            $shortname = $_POST['shortname'];
                            
                            if (self::createNewCategory($name,$shortname)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }

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
                                    "error" => translate("notification_image_needed")
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
                                "success" => translate("notification_success_operation")
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
                                "success" => translate("notification_success_operation")
                            ]);

                            break;
                        case 'deleteproduct':
                            $id = $_POST['id'];
                            
                            if (self::deleteProduct($id)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
    
                            }

                            break;
                            
                        case 'deletecoupon':
                            $id = $_POST['id'];

                            if (self::deleteCoupon($id)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }

                            break;
                        case 'createcoupon':
                            $singleuse = isset($_POST['singleuse']) ? 1 : 0;
                            $code = $_POST['code'];
                            $discount = $_POST['discount'];
                            $availablefrom = $_POST['availablefrom'];
                            $availableto = $_POST['availableto'];

                            if (self::createNewCoupon($singleuse,$code,$discount,$availablefrom,$availableto)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }

                            break;
                        case 'modifyorderstate':
                            $id = $_POST['id'];
                            $orderstate = $_POST['orderstate'];

                            if (self::modifyOrderState($id,$orderstate)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }

                            break;
                        case 'createrank':
                            $name = $_POST['rank'];

                            if (self::createNewRank($name)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        case 'deleterank':
                            $id = $_POST['id'];

                            if (self::deleteRank($id)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        case 'editrank':
                            $rankid = $_POST['id'];

                            
                            $sql = 'DELETE FROM rank_permission WHERE ranks_id=:id';
                            $statement = $pdo->prepare($sql);
                            $statement->execute([
                                ':id' => $rankid,
                            ]);


                            $sql = 'SELECT id FROM permissions';
                            $statement = $pdo->prepare($sql);
                            $statement->execute();
                            $permissions = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($permissions as $permission) {
                                $id = $permission['id'];
                                if (isset($_POST["perm_".$id])) {
                                    $sql = 'INSERT INTO `rank_permission`(`ranks_id`, `permissions_id`) VALUES (:rank,:permission)';
                                    $statement = $pdo->prepare($sql);
                                    $statement->execute([
                                        ':rank' => $rankid,
                                        ':permission' => $id,
                                    ]);
                                }
                            }
                            
                            redirect("admin/".$page."/".$rankid,[
                                "success" => translate("notification_success_operation")
                            ]);
                            break;
                        case 'switchaddon':
                            $id = $_POST['id'];
                            $enabled = $_POST['enabled'];

                            if (self::switchAddon($id,$enabled)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        case 'checkforaddons':
                            if (self::checkForAddons()) {
                                redirect("admin/".$page,[]);
                            }
                            break;
                        case 'checkforthemes':
                            if (self::checkForThemes()) {
                                redirect("admin/".$page,[]);
                            }
                            break;
                        case 'updatesmtp':
                            $smtp_host = $_POST['smtp_host'];
                            $smtp_user = $_POST['smtp_user'];
                            $smtp_pass = $_POST['smtp_pass'];
                            if (self::updateSMTP($smtp_host,$smtp_user,$smtp_pass)) {
                                redirect("admin/".$page,[
                                    "success" => translate("notification_success_operation")
                                ]);
                            }
                            break;
                        default:
                            break;
                    }
                    
                    redirect("admin/".$page,[
                        "error" => translate("notification_error")
                    ]);
                }
                
            } else {
                http_response_code(403);
                exit;
            }
        }

        private static function updateUserPassword($userid,$password) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            if (isset($password) && passwordsAcceptable($password,$password)==0) {
                $sql = 'UPDATE users SET password = :password WHERE id = :id';

                $statement = $pdo->prepare($sql);

                $statement->bindParam(':id', $userid, PDO::PARAM_INT);
                $statement->bindParam(':password', hashPassword($password));
                $statement->execute();
                return true;
            } else {
                return false;
            }
        }

        private static function updateUserInformations($userid,$rank,$username,$email,$banned) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $modifyRank = false;
            $sql = 'SELECT ranks_id as rank FROM users WHERE id=:id';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $userid
            ]);
            $rankrow = $statement->fetch(PDO::FETCH_ASSOC);
            $modifyRank = $rankrow['ranks_id']!=$rank;
            if (($modifyRank && !UserController::$loggedUser->hasPermission('manage_permissions'))) {
                return false;
            }

            if ($username && $email && $rank) {
                $sql = '
                    UPDATE
                        users
                    SET 
                        ranks_id=(SELECT id FROM ranks WHERE name=:rank),
                        username=:username,
                        email=:email,
                        banned=:banned
                    WHERE
                        id=:id
                ';

                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':id' => $userid,
                    ':username'=> $username,
                    ':email'=> $email,
                    ':rank'=> $rank,
                    ':banned'=> $banned ? 1 : 0,
                ]);

                return true;
            }
        }

        private static function updateUserPersonalInformations($userid,$postcode,$city,$street,$housenumber,$phone,$firstname,$lastname) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            if (
                $postcode==null || 
                $city==null || 
                $street==null || 
                $housenumber==null || 
                $phone==null || 
                $firstname==null || 
                $lastname==null
            ) {
                return false;
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
                ':id' => $userid,
            ]);
            $personRow = $statement->fetch(PDO::FETCH_ASSOC);

            if ($personRow) {
                $sql = 'UPDATE `people`,`users` SET people.`phone_number`=:phone,people.`addresses_id`=:address,people.`first_name`=:firstname,people.`last_name`=:lastname WHERE users.people_id=people.id AND users.id=:id';

                $statement = $pdo->prepare($sql);

                $statement->execute([
                    ':id' => $userid,
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
                    ':id' => $userid,
                    ':person' => $personId,
                ]);

            }
        }

        private static function updateShopName($name) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            if ($name!=null) {
                $sql = 'UPDATE `settings` SET `webshop_name`=:name';

                $statement = $pdo->prepare($sql);

                $statement->execute([
                    ':name' => $name,
                ]);

                return true;
            } else {
                return false;
            }
        }

        private static function updateShopTheme($theme) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            if ($theme!=null) {
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
                
                return true; 
            } else {
                return false; 
            }
        }
        
        private static function updateShopLanguage($language) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            if ($language != null) {
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
                
                return true;
            } else {
                return false;
            }
        }

        private static function modifyPhrase($id,$language,$phrase,$translated) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            if ($id>0) {
                $sql = '
                    UPDATE phrases SET translated=:translated WHERE id=:id
                ';

                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':id' => $id,
                    ':translated' => $translated,
                ]);
            } else {    
                $sql = '
                    INSERT INTO `phrases`(`languages_id`, `phrase`, `translated`) VALUES (:language,:phrase,:translated)
                ';

                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':language' => $language,
                    ':phrase' => $phrase,
                    ':translated' => $translated,
                ]);
            }
            return true;
        }

        private static function deleteLanguage($language) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                DELETE FROM languages WHERE id=:id
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $language,
            ]);
            return true;
        }

        private static function removeCategory($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                UPDATE categories SET parentcategory=NULL, display_navbar=0 WHERE id=:id OR parentcategory=:id
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id
            ]);

            return true;
            
        }

        private static function deleteCategory($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                DELETE FROM categories WHERE id=:id
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id
            ]);

            return true;
        }

        private static function manageCategory($maincategory,$selectedcategory) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                UPDATE categories SET parentcategory=:parent, display_navbar=1 WHERE id=:id
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $selectedcategory,
                ':parent' => $maincategory,
            ]);
        
            return true;
            
        }

        private static function createNewCategory($name,$shortname) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                INSERT INTO `categories`(`parentcategory`, `name`, `short`, `display_navbar`) VALUES (NULL,:name,:shortname,0)
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $name,
                ':shortname' => $shortname,
            ]);
            
            return true;
        }

        private static function deleteProduct($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                UPDATE products SET deleted=1 WHERE id=:id
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id,
            ]);

            return true;
        
        }

        private static function deleteCoupon($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                DELETE FROM coupons WHERE id=:id
            ';
            
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id,
            ]);

            return true;
            
        }

        private static function createNewCoupon($singleuse,$code,$discount,$availablefrom,$availableto) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                INSERT INTO `coupons`(`code`, `start_time`, `end_time`, `discount`, `singleuse`) VALUES (:code,:availablefrom,:availableto,:discount,:singleuse)
            ';
            
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':code' => $code,
                ':availablefrom' => $availablefrom,
                ':availableto' => $availableto,
                ':discount' => $discount,
                ':singleuse' => $singleuse,
            ]);

            return true;
            
        }

        private static function modifyOrderState($id,$orderstate) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                UPDATE orders SET state_id = :state WHERE id = :id
            ';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':state' => $orderstate,
                ':id' => $id,
            ]);

            return true;
            
        }

        private static function createNewRank($name) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                INSERT INTO ranks(name) VALUES (:name)
            ';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $name,
            ]);

            return true;

        }

        private static function deleteRank($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                DELETE FROM ranks WHERE id=:id
            ';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id,
            ]);

            $sql = '
                UPDATE users SET ranks_id=1 WHERE ranks_id=:id
            ';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':id' => $id,
            ]);

            return true;
            
        }

        private static function switchAddon($id,$enabled) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = '
                UPDATE installed_plugins SET enabled=:enabled WHERE id=:id
            ';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':enabled' => $enabled,
                ':id' => $id,
            ]);

            return true;
            
        }

        private static function checkForAddons() {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $directories = glob($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/plugins/*' , GLOB_ONLYDIR);
            foreach ($directories as $dir) {
                $arr = explode('/',$dir);
                if (count($arr)>0){
                    $name = end($arr);

                    $sql = 'SELECT id FROM installed_plugins WHERE name=:name';
                    $statement = $pdo->prepare($sql);
                    $statement->execute([
                        ':name' => $name
                    ]);

                    if ($statement->rowCount()==0 && is_file($dir.'/index.php') && is_file($dir.'/index.js')) {
                        $sql = 'INSERT INTO `installed_plugins`(`name`,`enabled`) VALUES (:name,0)';
                        $statement = $pdo->prepare($sql);
                        $statement->execute([
                            ':name' => $name
                        ]);
                    }
                }
            }
            
            return true;

        }

        private static function checkForThemes() {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $directories = glob($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/MVC/View/themes/*' , GLOB_ONLYDIR);
            foreach ($directories as $dir) {
                $arr = explode('/',$dir);
                if (count($arr)>0 && is_file($dir.'/name') && is_file($dir.'/version')){
                    $name = file_get_contents($dir.'/name');
                    $version = file_get_contents($dir.'/version');
                    $folder = end($arr);

                    $sql = 'SELECT id FROM themes WHERE folder=:folder';
                    $statement = $pdo->prepare($sql);
                    $statement->execute([
                        ':folder' => $folder
                    ]);

                    if ($statement->rowCount()==0) {
                        $sql = 'INSERT INTO `themes`(`name`, `folder`, `version`) VALUES (:name,:folder,:version)';
                        $statement = $pdo->prepare($sql);
                        $statement->execute([
                            ':name' => $name,
                            ':folder' => $folder,
                            ':version' => $version,
                        ]);
                    }
                }
            }
            
            return true;
        
        }

        private static function updateSMTP($smtp_host,$smtp_user,$smtp_pass) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            
            
            $sql = 'UPDATE settings SET smtp_host=:smtp_host, smtp_user=:smtp_user, smtp_pass=:smtp_pass';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':smtp_host' => $smtp_host,
                ':smtp_user' => $smtp_user,
                ':smtp_pass' => $smtp_pass,
            ]);
            
            return true;
            
        }
        
    }
    