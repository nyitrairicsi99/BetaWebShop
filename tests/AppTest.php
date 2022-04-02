<?php
    
class AppTest extends \PHPUnit\Framework\TestCase {
    public function test() {
        global $settings;
        require __DIR__."/../app/utility/echotranslated.php";
        require __DIR__."/../app/utility/file.php";
        require __DIR__."/../app/utility/ip.php";
        require __DIR__."/../app/utility/password.php";
        require __DIR__."/../app/utility/redirect.php";
        require __DIR__."/../app/utility/routeFunctions.php";
        require __DIR__."/../app/utility/settings.php";
        require __DIR__."/../app/utility/str.php";

        $testUser = 'user2211231323';
        $testPassword = '12345678';
        $testEmail = $testUser.'@testmail.hu';

        Controller\UserController::getInstance();
        $result = Controller\UserController::registerUser($testPassword,$testPassword,$testUser,$testEmail);
        $this->assertEquals(3,$result);

        $result = Controller\UserController::loginUser($testUser,$testPassword,false,false);
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateUserPassword(1,'adminadmin');
        $this->assertTrue($result);
        
        $result = Controller\AdminActionController::updateUserInformations(2,'admin',$testUser.'test','admin@admin.hu',false);
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateUserPersonalInformations(1,'7600','Pécs','Ifjúság útja','6','+36301234567','Nyitrai','Richárd');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateShopName('Tesztshopname');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateShopTheme('Default');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateShopLanguage(1);
        $this->assertTrue($result);

        $result = Controller\AdminActionController::modifyPhrase(2,1,'search','SearchFromTest');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::createNewCategory('NewCategory','newcategory');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::createNewCoupon(false,'COUPON',1,'2022-01-01','2023-01-01');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::createNewRank('newrank');
        $this->assertTrue($result);

        $result = Controller\AdminActionController::checkForAddons();
        $this->assertTrue($result);

        $result = Controller\AdminActionController::checkForThemes();
        $this->assertTrue($result);

        $result = Controller\AdminActionController::updateSMTP('smtp.google.com','szakdolgozat147@gmail.com','WPJV99fmYeHfEqq4');
        $this->assertTrue($result);

        $result = Controller\UserController::logout(true);
        $this->assertTrue($result);

    }
}