<?php
    namespace View;
    use Controller\SettingsController;

    class ProductDetails
    {
        private $product;
        private $theme;

        public function __construct($product)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            $this->product = $product;

            $id = $this->product->id;
            $name = $this->product->name;
            $description = $this->product->description;
            $price = $this->product->price;
            $sign = $this->product->currency->sign;
            $stock = $this->product->stock;

            include __DIR__ . "/themes/" . $this->theme . "/product/product.html";
        }

        private function createIndicators() {
            $active = "active";
            foreach ($this->product->gallery->images as $image) {
                include __DIR__ . "/themes/" . $this->theme . "/product/indicatorrow.html";
                $active = "";
            }
        }

        private function createImages() {
            $active = "active";
            foreach ($this->product->gallery->images as $image) {
                $url = $image->url;
                include __DIR__ . "/themes/" . $this->theme . "/product/imagerow.html";
                $active = "";
            }
        }
    }
    