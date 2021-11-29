<?php
    namespace View;
    
    use Controller\SettingsController;

    class Store
    {
        public $theme = 'default';
        public $products = [];

        public function __construct($text,$products,$page,$maxpage)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            $this->products = $products;
            $this->page = $page;
            $this->maxpage = $maxpage;

            include __DIR__ . "/themes/" . $this->theme . "/store/store.html";
        }

        private function createProducts() {
            foreach($this->products as $product) {
                $name = $product->name;
                $price = $product->price;
                $sign = $product->currency->sign;
                $picture = $product->gallery->first;
                $url = $product->url;
                include __DIR__ . "/themes/" . $this->theme . "/store/item.html";
            }
        }

        private function createPageRows() {
            $page = $this->page;
            $maxpage = $this->maxpage;
            for ($i=$page-2;$i<$page+3;$i++){
                $pageTarget = $i;
                if ($pageTarget>0 && $pageTarget<=$maxpage) {
                    $active = $page==$i ? "active" : "";
                    include __DIR__ . "/themes/" . $this->theme . "/store/pagination.html";
                }
            }
        }
    }
    