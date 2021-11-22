<?php
    namespace Controller;

    use View\Header;
    use View\Admin;

    class AdminController
    {
        public function __construct($page)
        {
            new Header("Admin site");
            new Admin($page);
        }
    }
    