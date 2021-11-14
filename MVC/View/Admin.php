<?php
    namespace View;

    use Model\NavbarItem;

    class Admin
    {
        public function __construct($page)
        {
            $theme = "default";
            
            if (!is_dir(__DIR__ . "/themes/" . $theme . "/admin/".$page)) {
                $page = "statistics";
            }

            include __DIR__ . "/themes/" . $theme . "/admin/sidenav/before.html";

            $navbar = array(
                new NavbarItem("Vissza a bolthoz","main",false,"fas fa-shopping-cart"),
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
            );
            
            foreach($navbar as $item) {
                $url = $item->link;
                $name = $item->title;
                $icon = $item->icon;
                $active = ($GLOBALS['settings']['root_folder']."/admin/".$page==$url) ? "active" : "";
                include __DIR__ . "/themes/" . $theme . "/admin/sidenav/row.html";
            }
            include __DIR__ . "/themes/" . $theme . "/admin/sidenav/after.html";

            echo "<div>".$page."</div>";
        }
    }
    