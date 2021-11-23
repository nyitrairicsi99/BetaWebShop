<?php
    namespace Controller;

    use View\Header;
    use View\Admin;
    use Controller\UserController;
    use PDO;

    class AdminController
    {
        public function __construct($page,$selectedPage)
        {
            $itemsOnPage = 20;

            UserController::getInstance();
            DatabaseConnection::getInstance();

            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                new Header("Admin site");
                $pdo = DatabaseConnection::$connection;
                $rows = [];

                $sql = 'SELECT id,username,email FROM users LIMIT :l OFFSET :o';

                $statement = $pdo->prepare($sql);
                $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                $statement->execute();

                $users = $statement->fetchAll(PDO::FETCH_ASSOC);

                if ($users) {
                    foreach ($users as $user) {
                        array_push($rows,[
                            "id" => $user["id"],
                            "username" => $user["username"],
                            "email" => $user["email"],
                        ]);
                    }
                }

                $sql = 'SELECT COUNT(id) as c FROM users';
                $statement = $pdo->query($sql);
                $maxpage = $statement->fetch(PDO::FETCH_ASSOC);
                $maxpage = ceil($maxpage['c'] / $itemsOnPage);

                new Admin($page,$rows,$selectedPage,$maxpage);
            } else {
                redirect("main");
            }
        }
    }
    