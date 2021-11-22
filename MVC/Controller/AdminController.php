<?php
    namespace Controller;

    use View\Header;
    use View\Admin;
    use Controller\UserController;

    class AdminController
    {
        public function __construct($page)
        {
            UserController::getInstance();
            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                new Header("Admin site");
                new Admin($page);
            } else {
                redirect("main");
            }
        }
    }
    