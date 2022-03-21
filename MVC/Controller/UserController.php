<?php
    namespace Controller;

    use Model\Rank;
    use Model\User;
    use PDO;

    class UserController {
        private static $instance = null;
        public static $loggedUser = null;
        public static $islogged = false;
        private function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new UserController();

                if (isset($_SESSION["loggedUser"])) {
                    self::$loggedUser = unserialize($_SESSION["loggedUser"]);
                    if (self::$loggedUser->id==null) {
                        self::$islogged = false;
                    } else {
                        self::$islogged = true;
                    }
                } else {
                    self::$loggedUser = new User();
                    self::$loggedUser->rank = new Rank('user');
                    self::$islogged = false;
                }
            }

            return self::$instance;
        }

        public static function logout($noredirect = false) {
            self::$loggedUser = null;
            self::$islogged = false;
            unset($_SESSION["loggedUser"]);
            if (!$noredirect) {
                self::deletelogincookie();
                redirect("main");
            }
        }

        private static function saveloggeddatas($user) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            self::$loggedUser = new User();
            self::$islogged = true;

            
            //basic informations
            self::$loggedUser->id = $user['id'];
            self::$loggedUser->username = $user['username'];
            self::$loggedUser->email = $user['email'];
            self::$loggedUser->banned = $user['banned']==1;
            if (self::$loggedUser->banned) {
                http_response_code(403);
                die();
            }

            //rank
            $sql = 'SELECT permissions.name as name,ranks.name as rank FROM ranks,rank_permission,permissions WHERE ranks.id=rank_permission.ranks_id AND rank_permission.permissions_id=permissions.id AND ranks.id=:rank';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':rank' => $user['ranks_id']
            ]);
            $perms = $statement->fetchAll(PDO::FETCH_ASSOC);
            if ($perms) {
                self::$loggedUser->rank = new Rank($perms[0]["rank"]);
                foreach ($perms as $perm) {
                    self::$loggedUser->rank->addPermission($perm['name']);
                }
            } else {
                self::$loggedUser->rank = new Rank('user');
            }
            $_SESSION["loggedUser"] = serialize(self::$loggedUser);
        }

        private static function updatelogincookie($id) {
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $logincookie = generateRandomString(128);
            $sql = 'UPDATE users SET logincookie=:logincookie WHERE id=:id';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':logincookie' => $logincookie,
                ':id' => $id,
            ]);
            setcookie("logincookie", $logincookie, time() + (86400 * 30 * 6));
        }

        private static function deletelogincookie() {
            setcookie('logincookie', '', time()-1000);
            unset($_COOKIE['logincookie']);
        }

        public static function login($fromregitser = false) {
            $rememberme = isset($_POST['rememberme']);
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (!(self::$islogged)) {
                self::logout(true);
            }

            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $sql = 'SELECT `id`, `username`, `password`, `email`, `people_id`, `ranks_id`, `banned` FROM `users` WHERE `username`=:username OR `email`=:username';
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':username' => $username
            ]);
            $users = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($users && sizeof($users)==1) {
                $user = $users[0];
                if (hashMatches($password,$user['password'])) {
                    self::saveloggeddatas($user);

                    if ($rememberme) {
                        self::updatelogincookie(self::$loggedUser->id);
                    }

                    if (!$fromregitser) {
                        redirect("main",[
                            "success" => translate("notification_success_login"),
                        ]);
                    }
                } else {                    
                    redirect("main",[
                        "error" => translate("notification_incorrect_username_or_password"),
                    ]);
                }
            } else {
                redirect("main",[
                    "error" => translate("notification_incorrect_username_or_password"),
                ]);
            }

        }

        public static function checklogincookie() {
            if (!(self::$islogged)) {
                DatabaseConnection::getInstance();
                $pdo = DatabaseConnection::$connection;

                $logincookie = $_COOKIE['logincookie'];

                if (isset($logincookie)) {
                    $sql = 'SELECT `id`, `username`, `password`, `email`, `people_id`, `ranks_id`, `banned` FROM `users` WHERE `logincookie`=:logincookie';
                    $statement = $pdo->prepare($sql);
                    $statement->execute([
                        ':logincookie' => $logincookie
                    ]);
                    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
                    if ($users && sizeof($users)==1) {
                        $user = $users[0];
                        self::saveloggeddatas($user);
                    }
                }
            }
            
        }

        
        public static function register() {
            $password1 = $_POST['password'];
            $password2 = $_POST['password2'];
            $username = $_POST['username'];
            $email = $_POST['email'];

            $passCheck = passwordsAcceptable($password1,$password2);
            if ($passCheck==0) {
                DatabaseConnection::getInstance();
                $pdo = DatabaseConnection::$connection;

                $sql = 'SELECT `id` FROM `users` WHERE `username`=:username OR `email`=:email';
                $statement = $pdo->prepare($sql);
                $statement->execute([
                    ':username' => $username,
                    ':email' => $email,
                ]);
                $users = $statement->fetchAll(PDO::FETCH_ASSOC);
                if (!$users) {
                    $sql = 'INSERT INTO `users`(`username`, `password`, `email`, `people_id`, `ranks_id`) VALUES (:username,:password,:email,NULL,1)';
                    $statement = $pdo->prepare($sql);
                    $statement->execute([
                        ':username' => $username,
                        ':password' => hashPassword($password1),
                        ':email' => $email,
                    ]);
                    $user_id = $pdo->lastInsertId();
                    self::login(true);
                    redirect("main",[
                        "success" => translate("notification_success_register"),
                    ]);
                } else {
                    redirect("main",[
                        "error" => translate("notification_reserved_username_or_email"),
                    ]);
                }
            } else {
                switch ($passCheck) {
                    case 1:
                        redirect("main",[
                            "error" => translate("notification_passwords_not_match"),
                        ]);
                        break;
                    case 2:
                        redirect("main",[
                            "error" => translate("notification_short_password"),
                        ]);
                        break;
                    default:
                        break;
                }
            }
        }
    }