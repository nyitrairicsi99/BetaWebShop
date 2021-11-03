<?php
    namespace Controller;

    use Model\NavbarItem;
    use Model\NavbarDropdown;
    use Model\NavbarDivider;

    use View\Header;
    use View\Store;
    use View\Navbar;
    
    class StoreController
    {
        public function __construct($page)
        {
            new Header("Webshop store site");
            $navbar = new Navbar();

            $testdropdown = array(
                new NavbarItem("Menüpont #1","/menu1",false),
                new NavbarItem("Menüpont #2","/menu2",false),
                new NavbarItem("Menüpont #3","/menu3",false),
            );

            $navbar->addItem(new NavbarItem("Főoldal","/",false));
            $navbar->addItem(new NavbarDropdown("Menüpontok",$testdropdown));
            $navbar->create();
            new Store();
        }
    }
    