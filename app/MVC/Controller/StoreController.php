<?php
    namespace Controller;

    use Model\NavbarItem;
    use Model\NavbarDropdown;

    use Model\Currency;
    use Model\Gallery;
    use Model\Product;
    
    use View\Header;
    use View\Store;
    use View\Search;
    use View\Navbar;

    use PDO;
    
    class StoreController
    {
        private static $instance = null;

        public function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new StoreController();
            }

            return self::$instance;
        }

        public static function createView($category,$page) {
            $itemsOnPage = 12;

            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
 
            SettingsController::getInstance();
            $shopname = SettingsController::$shopname;

            new Header($shopname);
            $navbar = new Navbar($shopname);

            $navbar->addItem(new NavbarItem("Főoldal","main","main"==$category));
            
            CategoryController::getInstance();
            $categories = CategoryController::getCategories(true,false);
            foreach ($categories as $main) {
                if (count($main["subcategories"])==0) {
                    $short = $main["short"];
                    $name = $main["name"];
                    $navbar->addItem(new NavbarItem($name,$short,$short==$category));
                } else {
                    $navitems = [];
                    foreach ($main["subcategories"] as $sub) {
                        $short = $sub["short"];
                        $name = $sub["name"];
                        array_push($navitems,new NavbarItem($name,$short,$short==$category));
                    }
                    $name = $main["name"];
                    $navbar->addItem(new NavbarDropdown($name,$navitems));
                }
            }

            $navbar->create();

            $sql = '
                SELECT
                    products.id as id,
                    products.name as name,
                    products.price as price,
                    products.stock as stock,
                    products.description as description,
                    products.sign as sign,
                    products.shortname as shortname,
                    products.longname as longname,
                    product_images.id as imgid,
                    product_images.url as url
                FROM (
                    SELECT
                        products.id as id,
                        products.name as name,
                        products.price as price,
                        products.stock as stock,
                        products.description as description,
                        currencies.sign as sign,
                        currencies.shortname as shortname,
                        currencies.longname as longname
                    FROM
                        products,
                        currencies,
                        categories
                    WHERE
                        categories.id = products.categories_id AND
                        currencies.id=products.currencies_id AND
                        products.deleted=0 AND
                        (
                            (
                                products.active_from<=NOW() AND
                                products.active_to>=NOW()
                            ) OR
                            display_notactive=1
                        ) AND
                        (
                            categories.short = :c OR
                            "main" = :c
                        ) AND
                        (
                            products.name LIKE :search OR
                            :search = ""
                        )
                    ) as products
                LEFT JOIN
                    product_images
                ON 
                    product_images.products_id = products.id
                ORDER BY
                    products.id
                LIMIT
                    :l
                OFFSET
                    :o
            ';

            SearchController::getInstance();
            $search = SearchController::getSearchValue();
            $search = "%".$search."%";

            $statement = $pdo->prepare($sql);
            $statement->bindValue(':c', ($category));
            $statement->bindValue(':o', (int) (($page - 1) * $itemsOnPage), PDO::PARAM_INT);
            $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);
            $statement->bindValue(':search', $search);

            $statement->execute();

            $res = $statement->fetchAll(PDO::FETCH_ASSOC);
            $products = [];

            if ($res) {
                $gallery = new Gallery();
                $lastid = 0;
                foreach ($res as $product) {
                    if ($lastid!=$product["id"]) { //első sor
                        if ($lastid!=0) { //már van termék a tömbben
                            if (count($gallery->images)==0) {
                                $gallery->addImage(0,"none.jpg");
                            }
                            $products[count($products)-1]->gallery = $gallery;
                            $gallery = new Gallery();
                        }
                        array_push($products,
                            new Product(
                                $product["id"],
                                $product["name"],
                                $product["price"],
                                new Currency($product["longname"],$product["shortname"],$product["sign"]),
                                null,
                                "product/".$product["id"],
                                $product["description"],
                                null
                            )
                        );
                        $lastid = $product["id"];
                    }
                    if ($product["imgid"]>0) {
                        $gallery->addImage($product["imgid"],$product["url"]);
                    }
                }
                if ($lastid!=0) {
                    if (count($gallery->images)==0) {
                        $gallery->addImage(0,"none.jpg");
                    }
                    $products[count($products)-1]->gallery = $gallery;
                }
            }
            $sql = '
                SELECT
                    COUNT(products.id) as c
                FROM
                    products,
                    currencies,
                    categories
                WHERE
                    categories.id = products.categories_id AND
                    currencies.id=products.currencies_id AND
                    products.deleted=0 AND
                    (
                        (
                            products.active_from<=NOW() AND
                            products.active_to>=NOW()
                        ) OR
                        display_notactive=1
                    )
            ';
            $statement = $pdo->query($sql);
            $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
            $maxpage = ceil($maxpage['c'] / $itemsOnPage);
           
            new Search($category);
            new Store($category,$products,$page,$maxpage);
        }

    }
    