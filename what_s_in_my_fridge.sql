-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 21, 2022 alle 19:44
-- Versione del server: 10.4.24-MariaDB
-- Versione PHP: 8.1.6

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
  `quantita` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `data_scadenza` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `contain`
--

INSERT INTO `contain` (`id_frigo`, `id_cibo`, `quantita`, `data_scadenza`) VALUES
(18, 12, 2, '2022-06-29');

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
(12, 'melanzane');

-- --------------------------------------------------------

--
-- Struttura della tabella `fridge`
--

CREATE TABLE `fridge` (
  `id` int(11) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `modello` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `fridge`
--

INSERT INTO `fridge` (`id`, `marca`, `modello`) VALUES
(18, '', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `id_fridge` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `id_fridge`) VALUES
(1, 'eri.07@hotmail.it', 'Fuffi', NULL),
(2, 'xxatlas98xx@gmail.com', 'Sunny', NULL),
(3, 'carmelo98.trifiro@gmail.com', '151823428sS', 18);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `contain`
--
ALTER TABLE `contain`
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
-- AUTO_INCREMENT per la tabella `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `fridge`
--
ALTER TABLE `fridge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
