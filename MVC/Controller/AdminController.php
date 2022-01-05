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
            $itemsOnPage = 1;

            UserController::getInstance();
            DatabaseConnection::getInstance();

            if (UserController::$loggedUser->rank->hasPermission('admin_access')) {
                $pdo = DatabaseConnection::$connection;
                new Header("Admin site");


                $details = [];
                $maxpage = 1;
                switch ($page) {
                    case 'users':
                        $sql = 'SELECT id,username,email FROM users LIMIT :l OFFSET :o';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':o', (int) (($selectedPage - 1) * $itemsOnPage), PDO::PARAM_INT);
                        $statement->bindValue(':l', (int) $itemsOnPage, PDO::PARAM_INT);

                        $statement->execute();

                        $users = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($users) {
                            foreach ($users as $user) {
                                array_push($details,[
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
                        break;
                    case 'user':
                        $sql = '
                            SELECT
                                users.id as id,
                                users.username as username,
                                users.email as email,
                                users.ranks_id as ranks_id,
                                people.postcode as postcode,
                                people.city as city,
                                people.street as street,
                                people.house_number as house_number,
                                people.phone as phone,
                                people.first_name as first_name,
                                people.last_name as last_name
                            FROM
                                users
                            LEFT JOIN 
                                (SELECT
                                    postcodes.postcode as postcode,
                                    cities.name as city,
                                    streets.street as street,
                                    house_numbers.number as house_number,
                                    people.phone_number as phone,
                                    people.first_name as first_name,
                                    people.last_name as last_name,
                                    people.id as id
                                FROM
                                    `postcodes`,
                                    `cities`,
                                    `streets`,
                                    `house_numbers`,
                                    `addresses`,
                                    `people`
                                WHERE
                                    postcodes.id = cities.postcodes_id AND
                                    cities.id = addresses.cities_id AND
                                    streets.id = addresses.streets_id AND
                                    house_numbers.id = addresses.house_numbers_id AND
                                    addresses.id=people.addresses_id
                                ) as people
                            ON
                                people.id = users.people_id
                            WHERE
                                users.id=:i;
                        ';

                        $statement = $pdo->prepare($sql);
                        $statement->bindValue(':i', (int) $selectedPage, PDO::PARAM_INT);

                        $statement->execute();

                        $user = $statement->fetch(PDO::FETCH_ASSOC);
                        if ($user) {
                            $details = [
                                "id" => $user['id'],
                                "username" => $user['username'],
                                "email" => $user['email'],
                                "rank" => $user['ranks_id'],
                                "ranks" => [],
                                "postcode" => $user['postcode'],
                                "city" => $user['city'],
                                "street" => $user['street'],
                                "house_number" => $user['house_number'],
                                "phone" => $user['phone'],
                                "first_name" => $user['first_name'],
                                "last_name" => $user['last_name'],
                            ];

                            $sql = 'SELECT id,name FROM ranks';

                            $statement = $pdo->query($sql);

                            $statement->execute();

                            $ranks = $statement->fetchAll(PDO::FETCH_ASSOC);

                            if ($ranks) {
                                foreach ($ranks as $rank) {
                                    array_push($details['ranks'],[
                                        "id" => $rank['id'],
                                        "name" => $rank['name'],
                                    ]);
                                }
                            }

                        } else {
                            redirect("admin/users");
                        }
                        break;
                    case 'settings':
                        $sql = "SELECT `themes_id`, `languages_id`, `license_hash`, `webshop_name`, `root_directory` FROM `settings`";
                        $statement = $pdo->prepare($sql);
                        $statement->execute();

                        $settings = $statement->fetch(PDO::FETCH_ASSOC);
                        if ($settings) {
                            $details['shopname'] = $settings['webshop_name'];
                            $details['theme'] = $settings['themes_id'];
                        }

                        $details['themes'] = [];

                        $sql = 'SELECT id,name FROM themes';

                        $statement = $pdo->query($sql);

                        $statement->execute();

                        $themes = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if ($themes) {
                            foreach ($themes as $theme) {
                                array_push($details['themes'],[
                                    "id" => $theme['id'],
                                    "name" => $theme['name'],
                                ]);
                            }
                        }

                        break;
                    case 'categories':
                        CategoryController::getInstance();
                        $details['used'] = CategoryController::getCategories(true,false);
                        $details['unused'] = CategoryController::getCategories(false,false);
                        $details['main'] = CategoryController::getCategories(true,true);
                        break;
                    default:
                        break;
                }
                

                new Admin($page,$details,$selectedPage,$maxpage);
            } else {
                redirect("main");
            }
        }
    }
    