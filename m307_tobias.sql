-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 02. Mrz 2021 um 15:15
-- Server-Version: 10.4.11-MariaDB
-- PHP-Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `m307_tobias`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tobias_inventar`
--

DROP TABLE IF EXISTS `tobias_inventar`;
CREATE TABLE `tobias_inventar` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `invnr` varchar(255) NOT NULL,
  `kategorie` enum('Computer','Audio','Monitor') DEFAULT 'Computer',
  `date` date NOT NULL,
  `bemerkung` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tobias_inventar`
--

INSERT INTO `tobias_inventar` (`id`, `name`, `invnr`, `kategorie`, `date`, `bemerkung`) VALUES
(1, 'Apple Macbook Air 13.3\"', 'KL156', 'Computer', '2016-01-01', 'Bemerkung'),
(2, 'Apple Magic Mouse 2', 'ZL862', 'Audio', '2017-01-01', 'Bemerkung'),
(3, 'Apple Thunderbolt/Ethernet', 'DL866', 'Monitor', '2018-01-01', 'Bemerkung');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tobias_inventar`
--
ALTER TABLE `tobias_inventar`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tobias_inventar`
--
ALTER TABLE `tobias_inventar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
