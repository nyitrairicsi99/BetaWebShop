-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2021. Nov 22. 23:45
-- Kiszolgáló verziója: 10.4.20-MariaDB
-- PHP verzió: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `webshop`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `2fa_codes`
--

CREATE TABLE `2fa_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `expiry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `cities_id` int(11) DEFAULT NULL,
  `streets_id` int(11) DEFAULT NULL,
  `house_numbers_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `bans`
--

CREATE TABLE `bans` (
  `id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `postcodes_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `shortname` varchar(255) DEFAULT NULL,
  `longname` varchar(255) DEFAULT NULL,
  `sign` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `house_numbers`
--

CREATE TABLE `house_numbers` (
  `id` int(11) NOT NULL,
  `number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `installed_plugins`
--

CREATE TABLE `installed_plugins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `shortname` varchar(255) DEFAULT NULL,
  `longname` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `order_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_states`
--

CREATE TABLE `order_states` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `addresses_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'admin_access', 'Enable to access the admin page.');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `phrases`
--

CREATE TABLE `phrases` (
  `id` int(11) NOT NULL,
  `languages_id` int(11) DEFAULT NULL,
  `phrase` varchar(255) DEFAULT NULL,
  `translated` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `postcodes`
--

CREATE TABLE `postcodes` (
  `id` int(11) NOT NULL,
  `postcode` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `currencies_id` int(11) DEFAULT NULL,
  `units_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `active_from` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active_to` timestamp NULL DEFAULT current_timestamp(),
  `display_notactive` tinyint(1) DEFAULT NULL,
  `categories_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `products_id` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product_order`
--

CREATE TABLE `product_order` (
  `id` int(11) NOT NULL,
  `products_id` int(11) DEFAULT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `discounts_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `ranks`
--

INSERT INTO `ranks` (`id`, `name`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `rank_permission`
--

CREATE TABLE `rank_permission` (
  `id` int(11) NOT NULL,
  `ranks_id` int(11) DEFAULT NULL,
  `permissions_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `rank_permission`
--

INSERT INTO `rank_permission` (`id`, `ranks_id`, `permissions_id`) VALUES
(1, 2, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `settings`
--

CREATE TABLE `settings` (
  `themes_id` int(11) DEFAULT NULL,
  `languages_id` int(11) DEFAULT NULL,
  `license_hash` varchar(255) DEFAULT NULL,
  `webshop_name` varchar(255) DEFAULT NULL,
  `root_directory` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `streets`
--

CREATE TABLE `streets` (
  `id` int(11) NOT NULL,
  `street` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `short` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `used_coupons`
--

CREATE TABLE `used_coupons` (
  `id` int(11) NOT NULL,
  `coupons_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `people_id` int(11) DEFAULT NULL,
  `ranks_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `people_id`, `ranks_id`) VALUES
(15, 'William', '$2y$10$7828jPg2RgqPfP0.4r7D0OLF3FQ0ANHiy26lk9wVZcJ//PwAseMZe', 'nyitrairicsi99@gmail.com', NULL, 1),
(16, 'admin', '$2y$10$4S9.ZJpgj2fXNjdQndjt6u7Fy/m6inFXN8rcyBQ6jkrr2fMHpt2Yy', 'test@freemail.hu', NULL, 2);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `2fa_codes`
--
ALTER TABLE `2fa_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`);

--
-- A tábla indexei `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `house_numbers_id` (`house_numbers_id`),
  ADD KEY `streets_id` (`streets_id`),
  ADD KEY `cities_id` (`cities_id`);

--
-- A tábla indexei `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`);

--
-- A tábla indexei `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postcodes_id` (`postcodes_id`);

--
-- A tábla indexei `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `house_numbers`
--
ALTER TABLE `house_numbers`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `installed_plugins`
--
ALTER TABLE `installed_plugins`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state_id`),
  ADD KEY `users_id` (`users_id`);

--
-- A tábla indexei `order_states`
--
ALTER TABLE `order_states`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_id` (`addresses_id`);

--
-- A tábla indexei `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `phrases`
--
ALTER TABLE `phrases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `languages_id` (`languages_id`);

--
-- A tábla indexei `postcodes`
--
ALTER TABLE `postcodes`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_id` (`units_id`),
  ADD KEY `currencies_id` (`currencies_id`);

--
-- A tábla indexei `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_id` (`products_id`);

--
-- A tábla indexei `product_order`
--
ALTER TABLE `product_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_id` (`discounts_id`),
  ADD KEY `products_id` (`products_id`),
  ADD KEY `orders_id` (`orders_id`);

--
-- A tábla indexei `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `rank_permission`
--
ALTER TABLE `rank_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_id` (`permissions_id`),
  ADD KEY `ranks_id` (`ranks_id`);

--
-- A tábla indexei `streets`
--
ALTER TABLE `streets`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `used_coupons`
--
ALTER TABLE `used_coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupons_id` (`coupons_id`),
  ADD KEY `users_id` (`users_id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `people_id` (`people_id`),
  ADD KEY `ranks_id` (`ranks_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `rank_permission`
--
ALTER TABLE `rank_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `2fa_codes`
--
ALTER TABLE `2fa_codes`
  ADD CONSTRAINT `2fa_codes_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `2fa_codes_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `2fa_codes_ibfk_3` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  ADD CONSTRAINT `addresses_ibfk_2` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  ADD CONSTRAINT `addresses_ibfk_3` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `addresses_ibfk_4` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  ADD CONSTRAINT `addresses_ibfk_5` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  ADD CONSTRAINT `addresses_ibfk_6` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `addresses_ibfk_7` FOREIGN KEY (`house_numbers_id`) REFERENCES `house_numbers` (`id`),
  ADD CONSTRAINT `addresses_ibfk_8` FOREIGN KEY (`streets_id`) REFERENCES `streets` (`id`),
  ADD CONSTRAINT `addresses_ibfk_9` FOREIGN KEY (`cities_id`) REFERENCES `cities` (`id`);

--
-- Megkötések a táblához `bans`
--
ALTER TABLE `bans`
  ADD CONSTRAINT `bans_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bans_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bans_ibfk_3` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`postcodes_id`) REFERENCES `postcodes` (`id`),
  ADD CONSTRAINT `cities_ibfk_2` FOREIGN KEY (`postcodes_id`) REFERENCES `postcodes` (`id`),
  ADD CONSTRAINT `cities_ibfk_3` FOREIGN KEY (`postcodes_id`) REFERENCES `postcodes` (`id`);

--
-- Megkötések a táblához `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `order_states` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`state_id`) REFERENCES `order_states` (`id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`state_id`) REFERENCES `order_states` (`id`),
  ADD CONSTRAINT `orders_ibfk_6` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_ibfk_1` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `people_ibfk_2` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `people_ibfk_3` FOREIGN KEY (`addresses_id`) REFERENCES `addresses` (`id`);

--
-- Megkötések a táblához `phrases`
--
ALTER TABLE `phrases`
  ADD CONSTRAINT `phrases_ibfk_1` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`),
  ADD CONSTRAINT `phrases_ibfk_2` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`),
  ADD CONSTRAINT `phrases_ibfk_3` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`);

--
-- Megkötések a táblához `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`units_id`) REFERENCES `units` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`currencies_id`) REFERENCES `currencies` (`id`);

--
-- Megkötések a táblához `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_images_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_images_ibfk_3` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`);

--
-- Megkötések a táblához `product_order`
--
ALTER TABLE `product_order`
  ADD CONSTRAINT `product_order_ibfk_1` FOREIGN KEY (`discounts_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `product_order_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_order_ibfk_3` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `product_order_ibfk_4` FOREIGN KEY (`discounts_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `product_order_ibfk_5` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_order_ibfk_6` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `product_order_ibfk_7` FOREIGN KEY (`discounts_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `product_order_ibfk_8` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_order_ibfk_9` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`);

--
-- Megkötések a táblához `rank_permission`
--
ALTER TABLE `rank_permission`
  ADD CONSTRAINT `rank_permission_ibfk_1` FOREIGN KEY (`permissions_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `rank_permission_ibfk_2` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`),
  ADD CONSTRAINT `rank_permission_ibfk_3` FOREIGN KEY (`permissions_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `rank_permission_ibfk_4` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`),
  ADD CONSTRAINT `rank_permission_ibfk_5` FOREIGN KEY (`permissions_id`) REFERENCES `permissions` (`id`),
  ADD CONSTRAINT `rank_permission_ibfk_6` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`);

--
-- Megkötések a táblához `used_coupons`
--
ALTER TABLE `used_coupons`
  ADD CONSTRAINT `used_coupons_ibfk_1` FOREIGN KEY (`coupons_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `used_coupons_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `used_coupons_ibfk_3` FOREIGN KEY (`coupons_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `used_coupons_ibfk_4` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `used_coupons_ibfk_5` FOREIGN KEY (`coupons_id`) REFERENCES `coupons` (`id`),
  ADD CONSTRAINT `used_coupons_ibfk_6` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Megkötések a táblához `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`people_id`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`),
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`people_id`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `users_ibfk_4` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`),
  ADD CONSTRAINT `users_ibfk_5` FOREIGN KEY (`people_id`) REFERENCES `people` (`id`),
  ADD CONSTRAINT `users_ibfk_6` FOREIGN KEY (`ranks_id`) REFERENCES `ranks` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
