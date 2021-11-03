<?php
    namespace Controller;

    use Model\NavbarItem;
    use Model\NavbarDropdown;

    use View\Header;
    use View\Store;
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
            new Store($page);
        }
    }
    