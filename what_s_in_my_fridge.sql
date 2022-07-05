-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Lug 05, 2022 alle 11:07
-- Versione del server: 10.4.24-MariaDB
-- Versione PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `what_s_in_my_fridge`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `contain`
--

CREATE TABLE `contain` (
  `id_frigo` int(11) NOT NULL,
  `id_cibo` int(11) NOT NULL,
  `quantita` int(10) UNSIGNED DEFAULT NULL,
  `grammi` int(11) UNSIGNED DEFAULT NULL,
  `data_scadenza` date NOT NULL,
  `id_riga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `contain`
--

INSERT INTO `contain` (`id_frigo`, `id_cibo`, `quantita`, `grammi`, `data_scadenza`, `id_riga`) VALUES
(19, 3, 12, NULL, '2022-07-02', 31),
(19, 10, NULL, 5000, '2022-07-09', 32),
(19, 12, 3, NULL, '2022-07-10', 34),
(19, 14, 4, NULL, '2022-06-29', 35),
(18, 9, NULL, 150, '2022-07-15', 55),
(18, 5, NULL, 350, '2022-07-14', 58),
(18, 1, NULL, 500, '2022-07-03', 63),
(18, 3, 6, NULL, '2022-07-09', 65),
(18, 18, NULL, 200, '2022-07-06', 66),
(18, 14, 12, NULL, '2022-07-10', 67),
(18, 16, NULL, 500, '2022-07-15', 70),
(18, 15, 4, NULL, '2022-07-14', 71),
(18, 21, NULL, 100, '2022-07-09', 73),
(18, 22, NULL, 200, '2022-07-10', 74),
(18, 23, NULL, 150, '2022-07-16', 75),
(18, 25, 3, NULL, '2022-07-03', 77);

-- --------------------------------------------------------

--
-- Struttura della tabella `food`
--

CREATE TABLE `food` (
  `id` int(11) NOT NULL,
  `nome_cibo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `food`
--

INSERT INTO `food` (`id`, `nome_cibo`) VALUES
(1, 'pomodori'),
(2, 'fragole'),
(3, 'yogurt'),
(5, 'carne'),
(9, 'merluzzo'),
(10, 'pesce spada'),
(12, 'melanzane'),
(13, 'carciofi'),
(14, 'uova'),
(15, 'hamburger'),
(16, 'pesche'),
(17, 'anguria'),
(18, 'zucchine'),
(19, 'anguria perla nera'),
(21, 'salame'),
(22, 'prosciutto cotto'),
(23, 'speck'),
(25, 'pinsa');

-- --------------------------------------------------------

--
-- Struttura della tabella `fridge`
--

CREATE TABLE `fridge` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `fridge`
--

INSERT INTO `fridge` (`id`) VALUES
(18),
(19),
(27),
(28);

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_fridge` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `id_fridge`) VALUES
(12, 'crashsum41@gmail.com', '$2y$10$fFlVR5gxGDwxihNnr6y.lO2qg/CoDdGOspC2gXz5KgKg/w5WcrnPW', NULL),
(14, 'carmelo98.trifiro@gmail.com', '$2y$10$13HIfgKotXKdpGSYrrWsD.piSaV9Eo4EHatR.NDr1lQ9EaWeUn2/m', 18),
(18, 'xxatlas98xx@gmail.com', '$2y$10$vFdATkJyewveJ7bQm8Xvs.JyPLrZthcUj4RR5.5qubSM2MwA.A6YG', 18),
(19, 'eri.07@hotmail.it', '$2y$10$pK.l76BKqVDLQrI9K/y/KOVYpEWevHGAdsrObsHx/xdg1HtW/7msO', NULL),
(21, 'mailprova@gmail.com', '$2y$10$bwZn70RAlQnlAlvQXO07jO/clKMmgCZHM8GlbfwihK4wzDSbK9sJC', 18),
(22, 'xxatlas98xx@gmail.com', '$2y$10$dbVo4uPyMLbqHWZh9eyhVul49qMyF2tv0mQxhgQGHG2ctuw14okvi', 18);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `contain`
--
ALTER TABLE `contain`
  ADD PRIMARY KEY (`id_riga`),
  ADD KEY `fk_frigo` (`id_frigo`),
  ADD KEY `fk_cibo` (`id_cibo`);

--
-- Indici per le tabelle `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `fridge`
--
ALTER TABLE `fridge`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_fridge` (`id_fridge`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `contain`
--
ALTER TABLE `contain`
  MODIFY `id_riga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT per la tabella `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT per la tabella `fridge`
--
ALTER TABLE `fridge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `contain`
--
ALTER TABLE `contain`
  ADD CONSTRAINT `fk_cibo` FOREIGN KEY (`id_cibo`) REFERENCES `food` (`id`),
  ADD CONSTRAINT `fk_frigo` FOREIGN KEY (`id_frigo`) REFERENCES `fridge` (`id`);

--
-- Limiti per la tabella `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_fridge`) REFERENCES `fridge` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
