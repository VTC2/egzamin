-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 08, 2025 at 02:19 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rzeki`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pomiary`
--

CREATE TABLE `pomiary` (
  `id` int(10) UNSIGNED NOT NULL,
  `wodowskazy_id` int(10) UNSIGNED NOT NULL,
  `dataPomiaru` date DEFAULT NULL,
  `stanWody` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pomiary`
--

INSERT INTO `pomiary` (`id`, `wodowskazy_id`, `dataPomiaru`, `stanWody`) VALUES
(1, 1, '2022-05-05', 100),
(2, 1, '2022-05-06', 120),
(3, 2, '2022-05-05', 240),
(4, 2, '2022-05-06', 240),
(5, 3, '2022-05-05', 300),
(6, 3, '2022-05-06', 300),
(7, 4, '2022-05-05', 120),
(8, 4, '2022-05-06', 130),
(9, 5, '2022-05-05', 100),
(10, 5, '2022-05-06', 100),
(11, 6, '2022-05-05', 200),
(12, 6, '2022-05-06', 250),
(13, 7, '2022-05-05', 90),
(14, 7, '2022-05-06', 93),
(15, 8, '2022-05-05', 100),
(16, 8, '2022-05-06', 60),
(17, 9, '2022-05-05', 170),
(18, 9, '2022-05-06', 200),
(19, 10, '2022-05-05', 100),
(20, 10, '2022-05-06', 100),
(21, 11, '2022-05-05', 200),
(22, 11, '2022-05-06', 250),
(23, 12, '2022-05-05', 340),
(24, 12, '2022-05-06', 360),
(25, 13, '2022-05-05', 200),
(26, 13, '2022-05-06', 230),
(27, 14, '2022-05-05', 100),
(28, 14, '2022-05-06', 90),
(29, 15, '2022-05-05', 100),
(30, 15, '2022-05-06', 150),
(31, 16, '2022-05-05', 190),
(32, 16, '2022-05-06', 190),
(33, 17, '2022-05-05', 200),
(34, 17, '2022-05-06', 190),
(36, 12, '2022-05-07', 350);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `uprawnienia` int(11) NOT NULL CHECK (`uprawnienia` in (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id`, `login`, `haslo`, `uprawnienia`) VALUES
(1, 'user123', 'prostehaslo', 0),
(2, 'admin', 'superhaslo1', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wodowskazy`
--

CREATE TABLE `wodowskazy` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazwa` varchar(100) DEFAULT NULL,
  `rzeka` varchar(30) DEFAULT NULL,
  `stanOstrzegawczy` int(10) UNSIGNED DEFAULT NULL,
  `stanAlarmowy` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wodowskazy`
--

INSERT INTO `wodowskazy` (`id`, `nazwa`, `rzeka`, `stanOstrzegawczy`, `stanAlarmowy`) VALUES
(1, 'Lubachów', 'Bystrzyca', 190, 210),
(2, 'Jarnołtów', 'Bystrzyca', 230, 270),
(3, 'Łazany', 'Strzegomka', 200, 240),
(4, 'Krzyżanowice', 'Widawa', 150, 200),
(5, 'Świerzawa', 'Kaczawa', 150, 200),
(6, 'Piątnica', 'Kaczawa', 300, 370),
(7, 'Jawor', 'Nysa Szalona', 100, 150),
(8, 'Osetno', 'Barycz', 260, 330),
(9, 'Jelenia Góra', 'Bóbr', 160, 220),
(10, 'Dąbrowa Bolesławicka', 'Bóbr', 300, 350),
(11, 'Mirsk', 'Kwisa', 420, 470),
(12, 'Nowogrodziec', 'Kwisa', 330, 380),
(13, 'Zgorzelec', 'Nysa Łużycka', 340, 400),
(14, 'Trestno', 'Odra', 380, 450),
(15, 'Głogów', 'Odra', 400, 450),
(16, 'Bardo', 'Nysa Kłodzka', 180, 250),
(17, 'Ślęza', 'Ślęza', 270, 300);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `pomiary`
--
ALTER TABLE `pomiary`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `wodowskazy`
--
ALTER TABLE `wodowskazy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pomiary`
--
ALTER TABLE `pomiary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wodowskazy`
--
ALTER TABLE `wodowskazy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
