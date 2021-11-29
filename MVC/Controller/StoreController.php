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
        public function __construct($page)
        {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            
            SettingsController::getInstance();
            $shopname = SettingsController::$shopname;

            new Header($shopname);
            $navbar = new Navbar($shopname);

            $navbar->addItem(new NavbarItem("Főoldal","main",true));
            
            $sql = '
            SELECT
                main.id as main_id,
                sub.id as sub_id,
                sub.name as name,
                sub.short as short
            FROM 
                categories as main
            RIGHT JOIN
                categories as sub 
            ON
                main.id=sub.parentcategory
            WHERE
                sub.display_navbar=1
            ORDER BY
                sub.parentcategory
            DESC;
            ';

            $statement = $pdo->prepare($sql);
            $statement->execute();

            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($categories) {
                $orderedcategories = [];
                foreach ($categories as $category) {
                    $main = $category['main_id'];
                    $sub = $category['sub_id'];
                    $name = $category['name'];
                    $short = $category['short'];
                    if (!in_array($sub,$orderedcategories) && isset($main)) {
                        if (!isset($orderedcategories[$main])) {
                            $orderedcategories[$main] = [];
                        }
                        array_push($orderedcategories[$main],new NavbarItem($name,$short,false));
                    } else {
                        if (isset($orderedcategories[$sub])) {
                            $navbar->addItem(new NavbarDropdown($name,$orderedcategories[$sub]));
                        } else {
                            $navbar->addItem(new NavbarItem($name,$short,false));
                        }
                    }
                }
            }
            $navbar->create();

            $desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.";
            $eur = new Currency("Euro","EUR","€");
            $huf = new Currency("Forint","HUF","Ft");
            $testgallery = new Gallery();
            $testgallery->addImage("https://shoestore.io/wp-content/uploads/2020/09/149295_03-1024x730-1.jpg");
            $testgallery2 = new Gallery();
            $testgallery2->addImage("https://media.kohlsimg.com/is/image/kohls/3478017_Gray?wid=600&hei=600&op_sharpen=1");
            $testgallery3 = new Gallery();
            $testgallery3->addImage("https://shoestore.io/wp-content/uploads/2020/09/air-jordan-3-retro-bg-gs-powder-blue-dk-pwdr-blue-wht-blck-wlf-gry-011858_1-1024x730-1.jpg");

            $products = array(
                new Product("Air Jordan XXXVI „Psychic Energy”",150,$eur,$testgallery,"product/1",$desc),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",20000,$huf,$testgallery,"product/1",$desc),
                new Product("Air Jordan XXXVI „Psychic Energy”",200,$eur,$testgallery,"product/1",$desc),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",10000,$huf,$testgallery2,"product/1",$desc),
                new Product("Air Jordan XXXVI „Psychic Energy”",300,$eur,$testgallery2,"product/1",$desc),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",15000,$huf,$testgallery2,"product/1",$desc),
                new Product("Air Jordan XXXVI „Psychic Energy”",100,$eur,$testgallery,"product/1",$desc),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",30000,$huf,$testgallery2,"product/1",$desc),
                new Product("Air Jordan XXXVI „Psychic Energy”",100,$eur,$testgallery,"product/1",$desc),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",30000,$huf,$testgallery2,"product/1",$desc),
                new Product("Air Jordan 3 Cipő „Retro Powder Blue”",26990,$huf,$testgallery3,"product/1",$desc),
            );


            new Search();
            new Store($page,$products,9,10);
        }
    }
    