-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Jun 2025 pada 08.36
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final_project_ppw1`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_table`
--

CREATE TABLE `admin_table` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_table`
--

INSERT INTO `admin_table` (`id`, `email`, `password`) VALUES
(1, 'admin123@gmail.com', 'admintampan123');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog_table`
--

CREATE TABLE `blog_table` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `blog_title` varchar(50) NOT NULL,
  `blog_img` varchar(100) NOT NULL,
  `blog_desc` text NOT NULL,
  `blog_date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `category_table`
--

CREATE TABLE `category_table` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `category_value_table`
--

CREATE TABLE `category_value_table` (
  `id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `catg_value_name` varchar(50) NOT NULL,
  `catg_value_icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `character_categories_table`
--

CREATE TABLE `character_categories_table` (
  `char_id` int(10) UNSIGNED NOT NULL,
  `catg_value_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `character_table`
--

CREATE TABLE `character_table` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_id` int(11) UNSIGNED NOT NULL,
  `tier_id` int(11) UNSIGNED NOT NULL,
  `char_name` varchar(50) NOT NULL,
  `char_icon` varchar(100) NOT NULL,
  `char_base_stat` varchar(200) NOT NULL,
  `char_base_stat_value` varchar(200) NOT NULL,
  `char_bonus_stat` varchar(200) NOT NULL,
  `char_bonus_stat_value` varchar(200) NOT NULL,
  `char_speciality` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dupes_table`
--

CREATE TABLE `dupes_table` (
  `char_id` int(10) UNSIGNED NOT NULL,
  `dupes_name` varchar(100) NOT NULL,
  `dupes_desc` text NOT NULL,
  `dupes_order` int(10) UNSIGNED NOT NULL,
  `dupes_icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `game_table`
--

CREATE TABLE `game_table` (
  `id` int(11) UNSIGNED NOT NULL,
  `game_name` varchar(50) NOT NULL,
  `game_icon` varchar(100) NOT NULL,
  `dupes_name` varchar(50) DEFAULT NULL,
  `skill_name` varchar(50) DEFAULT NULL,
  `stat_amplifier` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `game_table`
--

INSERT INTO `game_table` (`id`, `game_name`, `game_icon`, `dupes_name`, `skill_name`, `stat_amplifier`) VALUES
(18, 'Honkai: Star Rail', '683e8aff8d91e.jpeg', 'eidolons', 'skill', 'light cone,relic,planar'),
(19, 'Genshin Impact', '683e8a9e14652-GI.jpeg', 'Constellation', '', ''),
(20, 'Wuthering Waves', '683e8aaa7150b-WuWajpeg.jpeg', '', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `skill_table`
--

CREATE TABLE `skill_table` (
  `char_id` int(10) UNSIGNED NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `skill_desc` text NOT NULL,
  `skill_order` int(10) UNSIGNED NOT NULL,
  `skill_icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tier_table`
--

CREATE TABLE `tier_table` (
  `id` int(10) UNSIGNED NOT NULL,
  `game_id` int(10) UNSIGNED NOT NULL,
  `tier_name` varchar(50) NOT NULL,
  `tier_order` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `blog_table`
--
ALTER TABLE `blog_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_blog` (`game_id`);

--
-- Indeks untuk tabel `category_table`
--
ALTER TABLE `category_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_category` (`game_id`);

--
-- Indeks untuk tabel `category_value_table`
--
ALTER TABLE `category_value_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_value` (`category_id`);

--
-- Indeks untuk tabel `character_categories_table`
--
ALTER TABLE `character_categories_table`
  ADD KEY `category_character` (`catg_value_id`),
  ADD KEY `character_category` (`char_id`);

--
-- Indeks untuk tabel `character_table`
--
ALTER TABLE `character_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_character` (`game_id`),
  ADD KEY `tier_character` (`tier_id`);

--
-- Indeks untuk tabel `dupes_table`
--
ALTER TABLE `dupes_table`
  ADD KEY `char_dupes` (`char_id`);

--
-- Indeks untuk tabel `game_table`
--
ALTER TABLE `game_table`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `skill_table`
--
ALTER TABLE `skill_table`
  ADD KEY `char_skill` (`char_id`);

--
-- Indeks untuk tabel `tier_table`
--
ALTER TABLE `tier_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_tier` (`game_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `blog_table`
--
ALTER TABLE `blog_table`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `category_table`
--
ALTER TABLE `category_table`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `category_value_table`
--
ALTER TABLE `category_value_table`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `character_table`
--
ALTER TABLE `character_table`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `game_table`
--
ALTER TABLE `game_table`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `tier_table`
--
ALTER TABLE `tier_table`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `blog_table`
--
ALTER TABLE `blog_table`
  ADD CONSTRAINT `game_blog` FOREIGN KEY (`game_id`) REFERENCES `game_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `category_table`
--
ALTER TABLE `category_table`
  ADD CONSTRAINT `game_category` FOREIGN KEY (`game_id`) REFERENCES `game_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `category_value_table`
--
ALTER TABLE `category_value_table`
  ADD CONSTRAINT `category_value` FOREIGN KEY (`category_id`) REFERENCES `category_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `character_categories_table`
--
ALTER TABLE `character_categories_table`
  ADD CONSTRAINT `category_character` FOREIGN KEY (`catg_value_id`) REFERENCES `category_value_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `character_category` FOREIGN KEY (`char_id`) REFERENCES `character_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `character_table`
--
ALTER TABLE `character_table`
  ADD CONSTRAINT `game_character` FOREIGN KEY (`game_id`) REFERENCES `game_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tier_character` FOREIGN KEY (`tier_id`) REFERENCES `tier_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dupes_table`
--
ALTER TABLE `dupes_table`
  ADD CONSTRAINT `char_dupes` FOREIGN KEY (`char_id`) REFERENCES `character_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skill_table`
--
ALTER TABLE `skill_table`
  ADD CONSTRAINT `char_skill` FOREIGN KEY (`char_id`) REFERENCES `character_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tier_table`
--
ALTER TABLE `tier_table`
  ADD CONSTRAINT `game_tier` FOREIGN KEY (`game_id`) REFERENCES `game_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
