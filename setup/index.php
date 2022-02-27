<?php
    //define known variables
    
    $rootdir = str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']);
    $filedir = str_replace("\\","/",dirname(__FILE__));
    $webdir = str_replace($rootdir,"",$filedir);

    $shopdir = str_replace("/setup","",$webdir);
    $shopdir = str_replace("/install","",$shopdir);

    $dbhost = isset($_POST['dbhost']) ? $_POST['dbhost'] : '';
    $dbname = isset($_POST['dbname']) ? $_POST['dbname'] : '';
    $dbuser = isset($_POST['dbuser']) ? $_POST['dbuser'] : '';
    $dbpass = isset($_POST['dbpass']) ? $_POST['dbpass'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

    $error = isset($_POST['submit']) ? 'Missing parameters.' : '&#8203;';

    if (
        $dbname!='' &&
        $dbhost!='' &&
        $dbuser!='' &&
        $username!='' &&
        $password!=''
    ) {
        //check mysql connection
        try {
            $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $e) {
            $error = "Unable to connect to database.";
            include __DIR__ . "/html/header.html";
            include __DIR__ . "/html/body.html";
            die();
        }

        //check install directory
        if (!is_dir($rootdir."/".$shopdir."/utility")) {
            $error = "Cannot install to selected folder.";
            include __DIR__ . "/html/header.html";
            include __DIR__ . "/html/body.html";
            die();
        }

        //check password
        $res = passwordsAcceptable($password,$password2);
        if ($res == 2) {
            $error = "Admin password too weak.";
            include __DIR__ . "/html/header.html";
            include __DIR__ . "/html/body.html";
            die();
        } elseif ($res == 1) {
            $error = "Admin passwords not matches.";
            include __DIR__ . "/html/header.html";
            include __DIR__ . "/html/body.html";
            die();
        }

        //install process
        $sqlquery = file_get_contents($rootdir."/".$shopdir."/setup/install.sql");
        $conn->query($sqlquery);

        $str = "";
        $str .= '<?php'."\n";
        $str .= '$settings = array();'."\n";
        $str .= '$settings["showErrors"] = false;'."\n";
        $str .= '$settings["db_host"] = "'.$dbhost.'";'."\n";
        $str .= '$settings["db_dbname"] = "'.$dbname.'";'."\n";
        $str .= '$settings["db_username"] = "'.$dbuser.'";'."\n";
        $str .= '$settings["db_password"] = "'.$dbpass.'";'."\n";
        $str .= '$settings["db_prefix"] = "ws_";'."\n";
        $str .= '$settings["root_folder"] = "'.$shopdir.'";'."\n";

        $file = fopen($rootdir."/".$shopdir."/utility/settings.php", 'w');    
        fwrite($file, $str);
        fclose($file);

        //create admin user

        $password = hashPassword($password);
        $sql = 'INSERT INTO `users`(`username`, `password`, `email`, `people_id`, `ranks_id`, `banned`) VALUES(:username,:password,"",NULL,2,0)';
        $statement = $conn->prepare($sql);
        $statement->execute([
            ':username' => $username,
            ':password' => $password,
        ]);

        header('Location: '.$shopdir.'/');

    } else {
        include __DIR__ . "/html/header.html";
        include __DIR__ . "/html/body.html";
        die();
    }
