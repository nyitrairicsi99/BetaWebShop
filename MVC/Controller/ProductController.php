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

    class ProductController
    {


        public function __construct()
        {
            new Header("Webshop store site");
            $navbar = new Navbar("Beta Webshop");

            $navbar->addItem(new NavbarItem("Főoldal","main",true));
            $navbar->addItem(new NavbarDropdown("Menüpontok",array(
                new NavbarItem("Menüpont #1","menu1",false),
                new NavbarItem("Menüpont #2","menu2",false),
                new NavbarItem("Menüpont #3","menu3",false),
            )));
            $navbar->addItem(new NavbarDropdown("Menüpontok2",array(
                new NavbarItem("Menüpont #21","menu21",false),
                new NavbarItem("Menüpont #22","menu22",false),
                new NavbarItem("Menüpont #23","menu23",false),
            )));
            $navbar->create();
            $desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.";
            $eur = new Currency("Euro","EUR","€");
            $testgallery = new Gallery();
            $testgallery->addImage("https://shoestore.io/wp-content/uploads/2020/09/149295_03-1024x730-1.jpg");
            $testgallery->addImage("https://media.kohlsimg.com/is/image/kohls/3478017_Gray?wid=600&hei=600&op_sharpen=1");

            new ProductDetails(new Product("Air Jordan XXXVI „Psychic Energy”",150,$eur,$testgallery,"products/1",$desc));
        }
    }
    