<?php
    namespace Controller;

    use PDO;
    use Model\Basket;
    use Model\BasketItem;
    use View\Header;
    use View\Navbar;
    use Model\NavbarItem;
    use Model\NavbarDropdown;
    use Model\Product;
    use Model\Currency;
    use Model\Gallery;
    use View\Basket as BasketView;

    class BasketController {

        private static $instance = null;
        public static $basket;

        private function __construct()
        {
            if (isset($_SESSION["basket"])) {
                self::$basket = unserialize($_SESSION["basket"]);
            } else {
                self::$basket = new Basket();
            }
            self::saveBasket();
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new BasketController();
            }

            return self::$instance;
        }

        public static function addItem() {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $id = $_POST['id'];
            $piece = $_POST['piece'];

            $product = null;


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
                        products.id = :id
                    ) as products
                LEFT JOIN
                    product_images
                ON 
                    product_images.products_id = products.id
            ';

            $statement = $pdo->prepare($sql);
            $statement->bindValue(':id', ($id));

            $statement->execute();

            $res = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if ($res) {
                $name = $res[0]['name'];
                $desc = $res[0]['description'];
                $price = $res[0]['price'];
                $id = $res[0]['id'];
                $currency = new Currency($res[0]['longname'],$res[0]['shortname'],$res[0]['sign']);
                $gallery = new Gallery();
                foreach($res as $img) {
                    $imgid = $img['imgid'];
                    $url = $img['url'];
                    if ($imgid>0) {
                        $gallery->addImage($imgid,$url);
                    }
                }
                if (count($gallery->images)==0) {
                    $gallery->addImage(0,"none.jpg");
                }
    
                $product = new Product($id,$name,$price,$currency,$gallery,"product/".$id,$desc);

                $firstItem = self::$basket->getItem(0);

                if ($firstItem!=null) {
                    $firstItem = $firstItem->product;
                    if ($firstItem->currency->shortName!=$currency->shortName) {
                        redirect("product/".$id,[
                            "error" => "Csak azonos valutájú tárgyak rakhatók egy kosárba.",
                        ]);
                    }
                }

                self::$basket->addItem(new BasketItem($id,$piece,$product));
                self::saveBasket();

                redirect("product/".$id,[
                    "success" => "Sikeres művelet.",
                ]);
            } else {
                redirect("product/".$id,[
                    "error" => "Hiba a kosárba rakás során.",
                ]);
            }

        }

        public static function getItems() {
            return self::$basket->getItems();
        }

        public static function getPieceSum() {
            $sum = 0;
            foreach (self::$basket->getItems() as $item) {
                $sum += $item->piece;
            }
            return $sum;
        }

        public static function removeItem($index) {
            self::$basket->removeItem($index);
            self::saveBasket();
            redirect("basket",[
                "success" => "Sikeres művelet.",
            ]);
        }

        public static function clearItems() {
            self::$basket = new Basket();
            self::saveBasket();
        }

        private static function saveBasket() {
            $_SESSION["basket"] = serialize(self::$basket);
        }

        public static function createView() {
            
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

            new BasketView(self::$basket);
        }
    }