<?php
    namespace Controller;

    use Model\Product;
    use Model\NavbarItem;
    use Model\NavbarDropdown;

    use Model\Currency;
    use Model\Gallery;

    use View\Header;
    use View\Navbar;
    use View\ProductDetails;

    use PDO;

    class ProductController
    {
        private static $instance = null;

        public function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new ProductController();
            }

            return self::$instance;
        }

        public static function createView($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
 
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
    
                new ProductDetails(new Product($id,$name,$price,$currency,$gallery,null,$desc));
            } else {
                redirect("main",[
                    "error" => "Nem található termék."
                ]);
            }
        }
    }
    