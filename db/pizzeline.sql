-- Version du serveur :  10.4.16-MariaDB
-- Version de PHP : 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pizzeline`
--

-- --------------------------------------------------------

--
-- Structure de la table `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL,
  `nb_people` int(2) UNSIGNED NOT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp(),
  `tms_updating` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price_ht` decimal(6,2) NOT NULL,
  `total_price_tva` decimal(6,2) NOT NULL,
  `total_price_ttc` decimal(6,2) NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reception_time` datetime NOT NULL,
  `type` enum('salle','emporte','livraison') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'salle, emporte ou livraison',
  `table_number` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'if type is ''salle''',
  `is_delivered` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = false (no); \r\n1 = true (yes)',
  `tms` timestamp NOT NULL DEFAULT current_timestamp(),
  `tms_updating` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orders_detail`
--

CREATE TABLE `orders_detail` (
  `id` int(11) NOT NULL,
  `orders_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` tinyint(2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('entrees','pizzas','pates','salades','desserts','boissons') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ingredients` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_ht` decimal(6,2) UNSIGNED NOT NULL,
  `tva` decimal(4,3) UNSIGNED NOT NULL COMMENT 'Boisson conditionnée (hermétique) : 0.055 (5,5%); \r\nAlcool : 0.20 (20%); \r\nAlimentaire : 0.10 (10%)',
  `price_ttc` decimal(6,2) UNSIGNED NOT NULL,
  `menu_number` tinyint(2) UNSIGNED DEFAULT NULL COMMENT 'one number by menu (ex : 1 for a plate in menu 1)',
  `is_vegetarian` tinyint(1) UNSIGNED NOT NULL COMMENT '0 = false (no); 1 = true (yes)',
  `is_orderable` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0 = false (no); 1 = true (yes)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `category`, `ingredients`, `price_ht`, `tva`, `price_ttc`, `menu_number`, `is_vegetarian`, `is_orderable`) VALUES
(1, 'Assiette de Crudités', 'entrees', 'salade, tomates, concombre, carottes, maïs', '5.91', '0.100', '6.50', NULL, 1, 1),
(2, 'Oeufs Mayonnaise', 'entrees', 'oeufs, mayonnaise, salade', '4.55', '0.100', '5.00', NULL, 0, 1),
(3, 'Carbonara', 'pates', 'crème fraîche, lardons, oeuf', '10.00', '0.100', '11.00', NULL, 0, 1),
(4, 'Bolognaise', 'pates', 'tomate, viande, carotte, oignons', '8.64', '0.100', '9.50', NULL, 0, 1),
(5, 'Salade du Chef', 'salades', 'salade, avocat, asperges, saumon fumé', '8.18', '0.100', '9.00', NULL, 0, 1),
(6, 'Chèvre Chaud', 'salades', 'salade, tomates, crottin de chèvre, noix', '7.27', '0.100', '8.00', NULL, 1, 1),
(7, 'Marguerita', 'pizzas', 'tomate, mozzarella, épaule, olives', '11.82', '0.100', '13.00', NULL, 0, 1),
(8, 'Chèvre Miel', 'pizzas', 'tomate, mozzarella, olives, chèvre, miel', '13.18', '0.100', '14.50', NULL, 1, 1),
(9, 'Tarte Tatin', 'desserts', 'pommes, beurre salé, crème fraîche', '4.55', '0.100', '5.00', NULL, 1, 1),
(10, 'Gâteau Chocolat', 'desserts', 'moelleux au chocolat', '3.64', '0.100', '4.00', NULL, 1, 1),
(11, 'Coca-cola', 'boissons', '50cl', '4.27', '0.055', '4.50', NULL, 0, 1),
(12, 'Orangina', 'boissons', '50cl', '4.27', '0.055', '4.50', NULL, 0, 1),
(13, '1/2 Avocat au Crabe', 'entrees', 'avocat, crabe, sauce des îles', '6.82', '0.100', '7.50', NULL, 0, 1),
(14, 'Saumon Fumé', 'entrees', 'saumon fumé, toasts', '9.09', '0.100', '10.00', NULL, 0, 1),
(15, 'Salade Verte', 'entrees', 'salade, tomates', '2.73', '0.100', '3.00', NULL, 1, 1),
(16, 'Salade Mama', 'salades', 'salade, gruyère, jambon, tomates, oeufs durs', '7.27', '0.100', '8.00', NULL, 0, 1),
(17, 'Salade Primavera', 'salades', 'salade, tomates, thon, oeufs, asperges', '7.27', '0.100', '8.00', NULL, 0, 1),
(18, 'Salade à L\'italienne', 'salades', 'salade, tomates, mozzarella, lamelles d\'avocat', '7.73', '0.100', '8.50', NULL, 1, 1),
(19, 'Quatre Fromages', 'pates', 'mozzarella, gorgonzola, parmesan, comté', '10.00', '0.100', '11.00', NULL, 1, 1),
(20, 'Champignons', 'pates', 'crème fraîche, champignons, persil, ail', '9.09', '0.100', '10.00', NULL, 1, 1),
(21, 'Saumon', 'pates', 'saumon fumé, ciboulette, crème', '11.82', '0.100', '13.00', NULL, 0, 1),
(22, 'Quatre Fromages', 'pizzas', 'tomate, mozzarella, gorgonzola, parmesan, comté', '13.18', '0.100', '14.50', NULL, 1, 1),
(23, 'Campionne', 'pizzas', 'tomate, mozzarella, champignons, bolognaise', '12.73', '0.100', '14.00', NULL, 0, 1),
(24, 'Sicilienne', 'pizzas', 'tomate, mozzarella, oignons, merguez, oeuf', '12.73', '0.100', '14.00', NULL, 0, 1),
(25, 'Tiramisu', 'desserts', 'mascarpone, amaretto, oeufs, biscuits, café, cacao', '4.55', '0.100', '5.00', NULL, 0, 1),
(26, 'Tarte au Citron', 'desserts', 'citron, oeuf meringué', '3.64', '0.100', '4.00', NULL, 0, 1),
(27, 'Crème Brûlée', 'desserts', 'crème, jaunes d\'oeufs, sucre vanillé', '3.64', '0.100', '4.00', NULL, 0, 1),
(28, 'vin Italien', 'boissons', '75cl (blanc, rosé ou rouge)', '15.00', '0.200', '18.00', NULL, 0, 1),
(29, 'Perrier', 'boissons', '50cl', '4.27', '0.055', '4.50', NULL, 0, 1),
(30, 'Bière', 'boissons', '33cl', '5.00', '0.200', '6.00', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` enum('1','2','3','4','5','6','7','8','9','10') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'from 1 to 10',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp(),
  `tms_updating` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `town` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fidelity` smallint(4) NOT NULL DEFAULT 0,
  `status` enum('admin','utilisateur') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `zipcode`, `town`, `fidelity`, `status`) VALUES
-- password of admin : Password+1 / password of user : Password+0
(1, 'Céline', 'ADMIN', 'celine.admin@mail.com', '$2y$10$XUZVh6CBdVYAwen7AF8mv.hfJ7Cx319azClpfzQLrvvidNq.23Rhy', '0123456789', '17 Grande Rue', '78180', 'Montigny', 14, 'admin'),
(2, 'Martin', 'USER', 'martin.user@mail.com', '$2y$10$OEbqncLbzpVA8XO4zePpEeLhMo4cdzNuR55.MQ41XL8BuY2w2omli', '0678901234', '3 Square Voltaire', '78000', 'Versailles', 43, 'utilisateur'),
(3, 'David', 'ADMIN', 'david.admin@mail.com', '$2y$10$XUZVh6CBdVYAwen7AF8mv.hfJ7Cx319azClpfzQLrvvidNq.23Rhy', '0426758536', '50 Avenue Du Sud', '06250', 'Mougins', 132, 'admin'),
(4, 'Roger', 'USER', 'roger.user@mail.com', '$2y$10$OEbqncLbzpVA8XO4zePpEeLhMo4cdzNuR55.MQ41XL8BuY2w2omli', '0297586245', '1045 Rue Des Hauts-plateaux', '25680', 'Rougemont', 28, 'utilisateur'),
(5, 'Audrey', 'USER', 'audrey.user@mail.com', '$2y$10$OEbqncLbzpVA8XO4zePpEeLhMo4cdzNuR55.MQ41XL8BuY2w2omli', '0752984588', '24 Allée Du Carrefour', '95440', 'Ecouen', 0, 'utilisateur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_id` (`orders_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `orders_detail`
--
ALTER TABLE `orders_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD CONSTRAINT `orders_detail_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
