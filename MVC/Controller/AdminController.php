<?php
    namespace Controller;

    use View\Header;
    use View\Admin;
    use Model\Product;
    use Model\Gallery;
    use Model\Currency;
    use Controller\UserController;
    use Controller\AddonController;
    use PDO;

    class AdminController
    {
        private static $neededPermissions = [
            'users' => ['view_users','manage_users'],
            'user' => ['manage_users'],
            'orders' => ['view_orders','manage_orders'],
            'settings' => ['manage_settings'],
            'categories' => ['manage_settings'],
            'products' => ['view_products'],
            'addproduct' => ['create_product'],
            'product' => ['view_products'],
            'languages' => ['manage_settings'],
            'coupons' => ['view_coupons'],
            'permissions' => ['view_permissions'],
            'permission' => ['view_permissions'],
            'statistics' => ['view_statistics'],
            'addons' => ['manage_addons']
        ];

        private static $instance = null;

        public function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new AdminController();
            }

            return self::$instance;
        }

        public static function createView($page,$selectedPage,$selectedSubPage = null)
        {
            $itemsOnPage = 10;

            UserController::getInstance();
            DatabaseConnection::getInstance();

            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                
                $foundPermission = false;
                foreach (self::$neededPermissions[$page] as $perm) {
                    if (UserController::$loggedUser->rank->hasPermission($perm)) {
                        $foundPermission = true;
                    }
                }
                
                if (!$foundPermission) {
                    redirect("admin");
                }

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
                                users.banned as banned,
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
                                "banned" => $user['banned'],
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
                        
                    case 'orders':
                        $sql = '
                            SELECT  
                                orders.id as id,
                                orders.order_time as date,
                                users.email as email,
                                SUM(products.price * product_order.piece * ((100-product_order.discount_percent)/100)) as price,
                                pay_types.type as type,
                                orders.state_id as state,
                                currencies.sign as sign,
                                postcodes.postcode as postcode,
                                cities.name as city,
                                streets.street as street,
                                house_numbers.number as house_number,
                                people.first_name as first_name,
                                people.last_name as last_name
                            FROM
                                orders,
                                order_states,
                                users,
                                pay_types,
                                product_order,
                                products,
                                currencies,
                                postcodes,
                                cities,
                                streets,
                                house_numbers,
                                addresses,
                                people
                            WHERE
                                orders.state_id=order_states.id AND
                                users.id = orders.users_id AND
                                orders.pay_types_id=pay_types.id AND
                                product_order.orders_id=orders.id AND
                                products.id = product_order.products_id AND
                                products.currencies_id = currencies.id AND
                                postcodes.id = cities.postcodes_id AND
                                cities.id = addresses.cities_id AND
                                streets.id = addresses.streets_id AND
                                house_numbers.id = addresses.house_numbers_id AND
                                addresses.id=orders.addresses_id AND
                                orders.people_id = people.id
                            GROUP BY
                                orders.id
                            LIMIT
                                :l
                            OFFSET
                                :o';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                        $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                        $statement->execute();

                        $orders = $statement->fetchAll(PDO::FETCH_ASSOC);
                        $details['orders'] = [];
                        if ($orders) {
                            foreach ($orders as $order) {
                                array_push($details['orders'],[
                                    "id" => $order["id"],
                                    "date" => $order["date"],
                                    "email" => $order["email"],
                                    "price" => $order["price"],
                                    "type" => $order["type"],
                                    "state" => $order["state"],
                                    "sign" => $order["sign"],
                                    "address" => $order["postcode"].' '.$order["city"].' '.$order["street"].' '.$order["house_number"],
                                    "name" => $order["first_name"].' '.$order["last_name"],
                                ]);
                            }
                        }

                        $sql = '
                        SELECT
                            COUNT(c) as c
                        FROM
                            (SELECT  
                                1 as c
                            FROM
                                orders,
                                order_states,
                                users,
                                pay_types,
                                product_order,
                                products
                            WHERE
                                orders.state_id=order_states.id AND
                                users.id = orders.users_id AND
                                orders.pay_types_id=pay_types.id AND
                                product_order.orders_id=orders.id AND
                                products.id = product_order.products_id
                            GROUP BY
                                orders.id) as orders
                        ';
                        $statement = $pdo->query($sql);
                        $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                        $maxpage = ceil($maxpage['c'] / $itemsOnPage);



                        $sql = 'SELECT * FROM order_states';
                        $statement = $pdo->prepare($sql);

                        $statement->execute();

                        $states = $statement->fetchAll(PDO::FETCH_ASSOC);
                        $details['states'] = [];
                        foreach($states as $state) {
                            array_push($details['states'],[
                                "id" => $state["id"],
                                "name" => $state["name"],
                            ]);
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
                            $sql = 'SELECT * FROM (SELECT id,phrase,translated,languages_id FROM phrases WHERE languages_id=:id) as language RIGHT JOIN (SELECT phrase FROM phrases GROUP BY phrase) as phrase ON phrase.phrase = language.phrase LIMIT :l OFFSET :o';

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
                                        "languages_id" => $selectedPage,
                                        "phrase" => $phrase["phrase"],
                                        "translated" => $phrase["translated"],
                                    ]);
                                }
                            }

                            $sql = 'SELECT COUNT(1) as c FROM (SELECT id,phrase,translated,languages_id FROM phrases WHERE languages_id=:id) as language RIGHT JOIN (SELECT phrase FROM phrases GROUP BY phrase) as phrase ON phrase.phrase = language.phrase';
                            $statement = $pdo->prepare($sql);
                            $statement->bindValue(':id', (int) $selectedPage, PDO::PARAM_INT);
                            $statement->execute();
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
                    case 'permissions':
                        $sql = '
                            SELECT id,name FROM ranks
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
                        $ranks = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($ranks) {
                            foreach ($ranks as $rank) {
                                array_push($details,[
                                    "id" => $rank["id"],
                                    "name" => $rank["name"],
                                ]);
                            }
                        }
                        break;
                    case 'permission':
                        $details['permissions'] = [];
                        $sql = '
                            SELECT permissions.name as name,permissions.id as id,ranks.permissions_id as permissions_id
                            FROM (SELECT rank_permission.permissions_id as permissions_id,ranks.name FROM ranks,rank_permission WHERE ranks.id = rank_permission.ranks_id AND ranks.id = :id) as ranks
                            RIGHT JOIN (SELECT id,name FROM `permissions`) as permissions
                            ON permissions.id = ranks.permissions_id;
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':id', (int) $selectedPage, PDO::PARAM_INT);
                        $statement->execute();
                        $permissions = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($permissions) {
                            foreach ($permissions as $permission) {
                                array_push($details['permissions'],[
                                    "id" => $permission["id"],
                                    "name" => $permission["name"],
                                    "granted" => $permission["permissions_id"]!=null ? 1 : 0,
                                ]);
                            }
                        }

                        $sql = '
                            SELECT id,name FROM `ranks` WHERE id=:id;
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':id', (int) $selectedPage, PDO::PARAM_INT);
                        $statement->execute();
                        $rank = $statement->fetch(PDO::FETCH_ASSOC);
                        $details['rank'] = $rank['name'];
                        $details['id'] = $rank['id'];

                        break;
                    case 'addons':
                        AddonController::getInstance();
                        $details = AddonController::getAddons();
                        break;
                    case 'statistics':
                        $details['visitors_daily'] = [];
                        $details['visitors_monthly'] = [];
                        $details['orders_daily'] = [];
                        $details['orders_monthly'] = [];
                        //látogató 1 hónap alatt (napi)
                        $sql = '
                            SELECT
                                CONCAT(YEAR(date),"-",MONTH(date),"-",DAY(date)) as d,
                                COUNT(1) as c
                            FROM
                                visitors
                            WHERE
                                DATE_SUB(NOW(),INTERVAL 1 MONTH)<=date
                            GROUP BY
                                d
                            ORDER BY
                                date DESC
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
                        $stats = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($stats as $stat) {
                            array_push($details['visitors_daily'],[
                                "tag" => $stat['d'],
                                "count" => $stat['c'],
                            ]);
                        }

                        //látogató 1 év alatt (havi)
                        $sql = '
                            SELECT
                                CONCAT(YEAR(date),"-",MONTH(date)) as d,
                                COUNT(1) as c
                            FROM
                                visitors
                            WHERE
                                DATE_SUB(NOW(),INTERVAL 1 YEAR)<=date
                            GROUP BY
                                d
                            ORDER BY
                                date DESC
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
                        $stats = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($stats as $stat) {
                            array_push($details['visitors_monthly'],[
                                "tag" => $stat['d'],
                                "count" => $stat['c'],
                            ]);
                        }

                        //vásárlások 1 hónap alatt (napi)
                        $sql = '
                            SELECT
                                CONCAT(YEAR(order_time),"-",MONTH(order_time),"-",DAY(order_time)) as d,
                                COUNT(1) as c
                            FROM
                                orders
                            WHERE
                                DATE_SUB(NOW(),INTERVAL 1 MONTH)<=order_time
                            GROUP BY
                                d
                            ORDER BY
                                order_time DESC
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
                        $stats = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($stats as $stat) {
                            array_push($details['orders_daily'],[
                                "tag" => $stat['d'],
                                "count" => $stat['c'],
                            ]);
                        }
                        
                        //vásárlások 1 év alatt (havi)
                        $sql = '
                            SELECT
                                CONCAT(YEAR(order_time),"-",MONTH(order_time)) as d,
                                COUNT(1) as c
                            FROM
                                orders
                            WHERE
                                DATE_SUB(NOW(),INTERVAL 1 YEAR)<=order_time
                            GROUP BY
                                d
                            ORDER BY
                                order_time DESC
                        ';
                        $statement = $pdo->prepare($sql);
                        $statement->execute();
                        $stats = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($stats as $stat) {
                            array_push($details['orders_monthly'],[
                                "tag" => $stat['d'],
                                "count" => $stat['c'],
                            ]);
                        }

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
    