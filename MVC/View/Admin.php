<?php
    namespace View;

    use Model\NavbarItem;

    class Admin
    {
        public function __construct($page,$details,$selectedPage,$maxpage)
        {
            $theme = "default";
            
            if (!is_dir(__DIR__ . "/themes/" . $theme . "/admin/".$page)) {
                $page = "statistics";
            }

            include __DIR__ . "/themes/" . $theme . "/admin/sidenav/before.html";

            $navbar = array(
                new NavbarItem("Statisztikák","admin/statistics",false,"fas fa-chart-area"),
                new NavbarItem("Kuponok","admin/coupons",false,"fas fa-sticky-note"),
                new NavbarItem("Termékek","admin/products",false,"fas fa-bars"),
                new NavbarItem("Nyelv","admin/language",false,"fas fa-language"),
                new NavbarItem("Felhasználók","admin/users",false,"fas fa-users"),
                new NavbarItem("Rendelések","admin/orders",false,"fas fa-stream"),
                new NavbarItem("Jogok","admin/permissions",false,"fas fa-users-cog"),
                new NavbarItem("Tiltások","admin/bans",false,"fas fa-ban"),
                new NavbarItem("Beállítások","admin/settings",false,"fas fa-cog"),
                new NavbarItem("Bővítmények","admin/addons",false,"fas fa-puzzle-piece"),
                new NavbarItem("Vissza a bolthoz","main",false,"fas fa-shopping-cart"),
            );
            
            foreach($navbar as $item) {
                $url = $item->link;
                $name = $item->title;
                $icon = $item->icon;
                $active = ($GLOBALS['settings']['root_folder']."/admin/".$page==$url) ? "active" : "";
                include __DIR__ . "/themes/" . $theme . "/admin/sidenav/row.html";
            }
            include __DIR__ . "/themes/" . $theme . "/admin/sidenav/after.html";

            echo "<div class='adminpage'><div class='admincontent'><div class='tableholder'>";

            switch ($page) {
                case "users":
                    include __DIR__ . "/themes/" . $theme . "/admin/users/before.html";
                    include __DIR__ . "/themes/" . $theme . "/admin/users/header.html";
                    foreach ($details as $row) {
                        $id = $row['id'];
                        $username = $row['username'];
                        $email = $row['email'];
                        include __DIR__ . "/themes/" . $theme . "/admin/users/row.html";
                    }
                    include __DIR__ . "/themes/" . $theme . "/admin/users/after.html";
                    break;
                case "user":
                    include __DIR__ . "/themes/" . $theme . "/admin/user/before.html";
                    break;
                default:
                   echo "Admin page not set.";
            }

            echo "</div>";
            if ($selectedPage==0) {
                $pageTarget = 1;
                $maxpage = 1;
                $selectedPage = 1;
            }
            $pageTargetUrl = $GLOBALS['settings']['root_folder']."/admin/".$page."/1";
            include __DIR__ . "/themes/" . $theme . "/admin/pagination/before.html";
            for ($i=$selectedPage-2;$i<$selectedPage+3;$i++){
                $pageTarget = $i;
                $pageTargetUrl = $GLOBALS['settings']['root_folder']."/admin/".$page."/".$pageTarget;
                if ($pageTarget>0 && $pageTarget<=$maxpage) {
                    $active = $selectedPage==$i ? "active" : "";
                    include __DIR__ . "/themes/" . $theme . "/admin/pagination/row.html";
                }
            }
            $pageTargetUrl = $GLOBALS['settings']['root_folder']."/admin/".$page."/".$maxpage;
            include __DIR__ . "/themes/" . $theme . "/admin/pagination/after.html";

            echo "</div></div>";
            //echo "<div class='adminpage'>".$page."</div>";
        }
    }
    