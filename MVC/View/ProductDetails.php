<?php
    namespace View;

    class ProductDetails
    {
        public function __construct($product)
        {
            $theme = "default";
            include __DIR__ . "/themes/" . $theme . "/product/before.html";
            $active = "active";
            foreach ($product->gallery->images as $image) {
                include __DIR__ . "/themes/" . $theme . "/product/indicatorrow.html";
                $active = "";
            }
            include __DIR__ . "/themes/" . $theme . "/product/after_indicators.html";
            $active = "active";
            foreach ($product->gallery->images as $url) {
                include __DIR__ . "/themes/" . $theme . "/product/imagerow.html";
                $active = "";
            }

            $name = $product->name;
            $description = $product->description;
            $price = $product->price;
            $sign = $product->currency->sign;
            include __DIR__ . "/themes/" . $theme . "/product/after_images.html";
        }
    }
    