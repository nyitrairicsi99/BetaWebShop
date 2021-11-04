<?php
    namespace View;

    class Store
    {
        public function __construct($text,$products,$page,$maxpage)
        {
            $theme = "default";
            include __DIR__ . "/themes/" . $theme . "/store/before.html";
            foreach($products as $product) {
                $name = $product->name;
                $price = $product->price;
                $sign = $product->currency->sign;
                $picture = $product->gallery->first;
                $url = $product->url;
                include __DIR__ . "/themes/" . $theme . "/store/item.html";
            }
            include __DIR__ . "/themes/" . $theme . "/store/after_items.html";

            for ($i=$page-2;$i<$page+3;$i++){
                $pageTarget = $i;
                if ($pageTarget>0 && $pageTarget<=$maxpage) {
                    $active = $page==$i ? "active" : "";
                    include __DIR__ . "/themes/" . $theme . "/store/pagination.html";
                }
            }

            include __DIR__ . "/themes/" . $theme . "/store/after_pagination.html";
        }
    }
    