<?php
    
class AdminActionControllerTest extends \PHPUnit\Framework\TestCase {
    public function test() {
        require __DIR__."/../app/utility/password.php";

        global $settings;
        $settings = array();
        $settings["showErrors"] = false;
        $settings["db_host"] = "localhost";
        $settings["db_dbname"] = "webshop";
        $settings["db_username"] = "root";
        $settings["db_password"] = "";
        $settings["db_prefix"] = "ws_";
        $settings["root_folder"] = "/BetaWebShop";
        $settings["superuser"] = "admin";
        $settings["pass_prefix"] = "ABPRCH5E";
        $settings["pass_suffix"] = "Si0W9jTo";


        $result = Controller\AdminActionController::updateUserPassword(1,'adminadmin');

        $this->assertTrue($result);

    }
}