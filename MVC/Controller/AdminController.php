<?php
    namespace Controller;

    use View\Header;
    use View\Admin;
    use Model\Product;
    use Model\Gallery;
    use Model\Currency;
    use Controller\UserController;
    use PDO;

    class AdminController
    {
        public function __construct($page,$selectedPage,$selectedSubPage = null)
        {
            $itemsOnPage = 10;

            UserController::getInstance();
            DatabaseConnection::getInstance();

            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                $pdo = DatabaseConnection::$connection;
                new Header("Admin site");


                $details = [];
                $maxpage = 1;
                switch ($page) {
                    case 'users':
                        $sql = 'SELECT id,username,email FROM users LIMIT :l OFFSET :o';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                        $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                        $statement->execute();

                        $users = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($users) {
                            foreach ($users as $user) {
                                array_push($details,[
                                    "id" => $user["id"],
                                    "username" => $user["username"],
                                    "email" => $user["email"],
                                ]);
                            }
                        }

                        $sql = 'SELECT COUNT(id) as c FROM users';
                        $statement = $pdo->query($sql);
                        $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                        $maxpage = ceil($maxpage['c'] / $itemsOnPage);
                        break;
                    case 'user':
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

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':i', (int) $selectedPage, PDO::PARAM_INT);

                        $statement->execute();

                        $user = $statement->fetch(PDO::FETCH_ASSOC);
                        if ($user) {
                            $details = [
                                "id" => $user['id'],
                                "username" => $user['username'],
                                "email" => $user['email'],
                                "rank" => $user['ranks_id'],
                                "ranks" => [],
                                "postcode" => $user['postcode'],
                                "city" => $user['city'],
                                "street" => $user['street'],
                                "house_number" => $user['house_number'],
                                "phone" => $user['phone'],
                                "first_name" => $user['first_name'],
                                "last_name" => $user['last_name'],
                            ];

                            $sql = 'SELECT id,name FROM ranks';

                            $statement = $pdo->query($sql);

                            $statement->execute();

                            $ranks = $statement->fetchAll(PDO::FETCH_ASSOC);

                            if ($ranks) {
                                foreach ($ranks as $rank) {
                                    array_push($details['ranks'],[
                                        "id" => $rank['id'],
                                        "name" => $rank['name'],
                                    ]);
                                }
                            }

                        } else {
                            redirect("admin/users");
                        }
                        break;
                    case 'settings':
                        $sql = "SELECT `themes_id`, `languages_id`, `license_hash`, `webshop_name`, `root_directory` FROM `settings`";
                        $statement = $pdo->prepare($sql);
                        $statement->execute();

                        $settings = $statement->fetch(PDO::FETCH_ASSOC);
                        if ($settings) {
                            $details['shopname'] = $settings['webshop_name'];
                            $details['theme'] = $settings['themes_id'];
                            $details['language'] = $settings['languages_id'];
                        }

                        $details['themes'] = [];
                        $details['languages'] = [];

                        $sql = 'SELECT id,name FROM themes';

                        $statement = $pdo->query($sql);

                        $statement->execute();

                        $themes = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($themes) {
                            foreach ($themes as $theme) {
                                array_push($details['themes'],[
                                    "id" => $theme['id'],
                                    "name" => $theme['name'],
                                ]);
                            }
                        }

                        $sql = 'SELECT id,longname FROM languages';

                        $statement = $pdo->query($sql);

                        $statement->execute();

                        $themes = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($themes) {
                            foreach ($themes as $theme) {
                                array_push($details['languages'],[
                                    "id" => $theme['id'],
                                    "name" => $theme['longname'],
                                ]);
                            }
                        }

                        break;
                    case 'categories':
                        CategoryController::getInstance();
                        $details['used'] = CategoryController::getCategories(true,false);
                        $details['unused'] = CategoryController::getCategories(false,false);
                        $details['main'] = CategoryController::getCategories(true,true);
                        break;
                    case 'products':

                        $sql = 'SELECT products.id as id,products.name as name,products.price as price,products.stock as stock,currencies.sign as sign FROM products,currencies WHERE currencies.id=products.currencies_id AND products.deleted=0 LIMIT :l OFFSET :o';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                        $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                        $statement->execute();

                        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($products) {
                            foreach ($products as $product) {
                                array_push($details,
                                        new Product(
                                            $product["id"],
                                            $product["name"],
                                            $product["price"],
                                            new Currency(null,null,$product["sign"]),
                                            null,
                                            null,
                                            null,
                                            null,
                                            $product["stock"]
                                        )
                                    );
                            }
                        }

                        $sql = 'SELECT COUNT(products.id) as c FROM products,currencies WHERE currencies.id=products.currencies_id AND products.deleted=0';
                        $statement = $pdo->query($sql);
                        $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                        $maxpage = ceil($maxpage['c'] / $itemsOnPage);


                        break;
                    case 'addproduct':
                        $details['categories'] = [];
                        $details['currencies'] = [];
                        CategoryController::getInstance();
                        $categories = CategoryController::getCategories(true,false);
                        for ($i=0; $i < count($categories); $i++) {
                            for ($j=0; $j < count($categories[$i]["subcategories"]); $j++) {
                                array_push($details['categories'],[
                                    "name" => $categories[$i]["subcategories"][$j]["name"],
                                    "id" => $categories[$i]["subcategories"][$j]["id"],
                                ]);
                            }
                            array_push($details['categories'],[
                                "name" => $categories[$i]["name"],
                                "id" => $categories[$i]["id"],
                            ]);
                        }
                        $categories = CategoryController::getCategories(false,false);
                        for ($i=0; $i < count($categories); $i++) {
                            for ($j=0; $j < count($categories[$i]["subcategories"]); $j++) {
                                array_push($details['categories'],[
                                    "name" => $categories[$i]["subcategories"][$j]["name"],
                                    "id" => $categories[$i]["subcategories"][$j]["id"],
                                ]);
                            }
                            array_push($details['categories'],[
                                "name" => $categories[$i]["name"],
                                "id" => $categories[$i]["id"],
                            ]);
                        }
                        $sql = 'SELECT id,longname FROM currencies';

                        $statement = $pdo->query($sql);

                        $statement->execute();

                        $currencies = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($currencies) {
                            foreach ($currencies as $currency) {
                                array_push($details['currencies'],[
                                    "name" => $currency["longname"],
                                    "id" => $currency["id"],
                                ]);
                            }
                        }

                        break;
                    case 'product':
                        $sql = '
                        SELECT
                            products.id as id,
                            products.name as name,
                            products.description as description,
                            products.price as price,
                            products.currencies_id as currencies_id,
                            products.stock as stock,
                            products.availablefrom as availablefrom,
                            products.availableto as availableto,
                            products.display_notactive as display_notactive,
                            products.categories_id as categories_id,
                            products.shortname as shortname,
                            products.longname as longname,
                            products.sign as sign,
                            product_images.url as url,
                            product_images.id as imgid
                        FROM
                            product_images
                        RIGHT JOIN (
                            SELECT 
                                products.id as id,
                                products.name as name,
                                products.description as description,
                                products.price as price,
                                products.currencies_id as currencies_id,
                                products.stock as stock,
                                products.active_from as availablefrom,
                                products.active_to as availableto,
                                products.display_notactive as display_notactive,
                                products.categories_id as categories_id,
                                currencies.shortname as shortname,
                                currencies.longname as longname,
                                currencies.sign as sign
                            FROM
                                products,
                                currencies
                            WHERE
                                products.currencies_id=currencies.id AND
                                products.id=:id
                            ) as products
                        ON
                            product_images.products_id = products.id;
                        ';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':id', (int) ($selectedPage), PDO::PARAM_INT);

                        $statement->execute();

                        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                        
                        $gallery = new Gallery();
                        foreach($rows as $row) {
                            if (isset($row['imgid'])){
                                $gallery->addImage($row['imgid'],$row['url']);
                            }
                        }
                        if (count($gallery->images)==0) {
                            $gallery->addImage(0,"none.jpg");
                        }

                        $details['product'] = new Product(
                            $rows[0]['id'],
                            $rows[0]['name'],
                            $rows[0]['price'],
                            new Currency($rows[0]['longname'],$rows[0]['shortname'],$rows[0]['sign']),
                            $gallery,
                            "",
                            $rows[0]['description'],
                            null,
                            $rows[0]['stock'],
                            $rows[0]['availablefrom'],
                            $rows[0]['availableto'],
                            $rows[0]['display_notactive']==1
                        );
                        $category = $rows[0]['categories_id'];


                        $details['id'] = $selectedPage;
                        $details['category'] = $category;
                        $details['currency'] = $rows[0]['currencies_id'];
                        $details['categories'] = [];
                        $details['currencies'] = [];
                        CategoryController::getInstance();
                        $categories = CategoryController::getCategories(true,false);
                        for ($i=0; $i < count($categories); $i++) {
                            for ($j=0; $j < count($categories[$i]["subcategories"]); $j++) {
                                array_push($details['categories'],[
                                    "name" => $categories[$i]["subcategories"][$j]["name"],
                                    "id" => $categories[$i]["subcategories"][$j]["id"],
                                ]);
                            }
                            array_push($details['categories'],[
                                "name" => $categories[$i]["name"],
                                "id" => $categories[$i]["id"],
                            ]);
                            if ($category==$categories[$i]["id"]) {
                                $details['product']->setCategory($details['categories']);
                            }
                        }
                        $categories = CategoryController::getCategories(false,false);
                        for ($i=0; $i < count($categories); $i++) {
                            for ($j=0; $j < count($categories[$i]["subcategories"]); $j++) {
                                array_push($details['categories'],[
                                    "name" => $categories[$i]["subcategories"][$j]["name"],
                                    "id" => $categories[$i]["subcategories"][$j]["id"],
                                ]);
                            }
                            array_push($details['categories'],[
                                "name" => $categories[$i]["name"],
                                "id" => $categories[$i]["id"],
                            ]);
                            if ($category==$categories[$i]["id"]) {
                                $details['product']->setCategory($details['categories']);
                            }
                        }
                        $sql = 'SELECT id,longname FROM currencies';

                        $statement = $pdo->query($sql);

                        $statement->execute();

                        $currencies = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($currencies) {
                            foreach ($currencies as $currency) {
                                array_push($details['currencies'],[
                                    "name" => $currency["longname"],
                                    "id" => $currency["id"],
                                ]);
                            }
                        }

                        break;
                    case 'languages':
                        if ($selectedSubPage==null) {
                            redirect("admin");
                        } else {
                            $sql = 'SELECT id,phrase,translated,languages_id FROM phrases WHERE languages_id=:id LIMIT :l OFFSET :o';

                            $statement = $pdo->prepare($sql);
                            $statement->bindValue(':o', (int) (($selectedSubPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                            $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);
                            $statement->bindValue(':id', (int) $selectedPage, PDO::PARAM_INT);

                            $statement->execute();

                            $phrases = $statement->fetchAll(PDO::FETCH_ASSOC);

                            if ($phrases) {
                                foreach ($phrases as $phrase) {
                                    array_push($details,[
                                        "id" => $phrase["id"],
                                        "languages_id" => $phrase["languages_id"],
                                        "phrase" => $phrase["phrase"],
                                        "translated" => $phrase["translated"],
                                    ]);
                                }
                            }

                            $sql = 'SELECT COUNT(id) as c FROM phrases';
                            $statement = $pdo->query($sql);
                            $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                            $maxpage = ceil($maxpage['c'] / $itemsOnPage);
                        }
                        break;
                    case 'coupons':
                        $sql = '
                        SELECT
                            coupons.id as id,
                            COUNT(used_coupons.id) as uses,
                            coupons.code as code,
                            coupons.start_time as start_time,
                            coupons.end_time as end_time,
                            coupons.singleuse as single_use,
                            coupons.discount as discount
                        FROM
                            used_coupons
                        RIGHT JOIN
                            coupons
                        ON
                            used_coupons.coupons_id = coupons.id
                        GROUP BY
                            coupons.code
                        LIMIT
                            :l
                        OFFSET
                            :o
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                        $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                        $statement->execute();
                        $coupons = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($coupons) {
                            foreach ($coupons as $coupon) {
                                array_push($details,[
                                    "id" => $coupon["id"],
                                    "used" => $coupon["uses"],
                                    "code" => $coupon["code"],
                                    "start_time" => $coupon["start_time"],
                                    "end_time" => $coupon["end_time"],
                                    "singleuse" => $coupon["single_use"],
                                    "discount" => $coupon["discount"],
                                ]);
                            }
                        }
                        
                        $sql = 'SELECT COUNT(id) as c FROM coupons';
                        $statement = $pdo->query($sql);
                        $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                        $maxpage = ceil($maxpage['c'] / $itemsOnPage);

                        break;
                    default:
                        break;
                }
                

                new Admin($page,$details,$selectedPage,$maxpage,$selectedSubPage);
            } else {
                redirect("main");
            }
        }
    }
    