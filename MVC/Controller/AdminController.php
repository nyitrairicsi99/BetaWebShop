<?php
    namespace Controller;

    use View\Header;
    use View\Admin;

    class AdminController
    {
        public function __construct($page)
        {
            new Header("Webshop store site");
            new Admin($page);
        }
    }
    