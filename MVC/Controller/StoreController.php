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
    
    class StoreController
    {
        public function __construct($page)
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

            $eur = new Currency("Euro","EUR","€");
            $huf = new Currency("Forint","HUF","Ft");
            $testgallery = new Gallery();
            $testgallery->addImage("https://shoestore.io/wp-content/uploads/2020/09/149295_03-1024x730-1.jpg");
            $testgallery2 = new Gallery();
            $testgallery2->addImage("https://media.kohlsimg.com/is/image/kohls/3478017_Gray?wid=600&hei=600&op_sharpen=1");
            $testgallery3 = new Gallery();
            $testgallery3->addImage("https://shoestore.io/wp-content/uploads/2020/09/air-jordan-3-retro-bg-gs-powder-blue-dk-pwdr-blue-wht-blck-wlf-gry-011858_1-1024x730-1.jpg");

            $products = array(
                new Product("Air Jordan XXXVI „Psychic Energy”",150,$eur,$testgallery,"products/1"),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",20000,$huf,$testgallery,"products/1"),
                new Product("Air Jordan XXXVI „Psychic Energy”",200,$eur,$testgallery,"products/1"),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",10000,$huf,$testgallery2,"products/1"),
                new Product("Air Jordan XXXVI „Psychic Energy”",300,$eur,$testgallery2,"products/1"),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",15000,$huf,$testgallery2,"products/1"),
                new Product("Air Jordan XXXVI „Psychic Energy”",100,$eur,$testgallery,"products/1"),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",30000,$huf,$testgallery2,"products/1"),
                new Product("Air Jordan XXXVI „Psychic Energy”",100,$eur,$testgallery,"products/1"),
                new Product("Adidas Yeezy 350 V2 Cipő „Citrin”",30000,$huf,$testgallery2,"products/1"),
                new Product("Air Jordan 3 Cipő „Retro Powder Blue”",26990,$huf,$testgallery3,"products/1"),
            );


            new Search();
            new Store($page,$products,9,10);
        }
    }
    