-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.6.27-log - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              9.3.0.5024
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных testbook
CREATE DATABASE IF NOT EXISTS `testbook` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `testbook`;


-- Дамп структуры для таблица testbook.authors
CREATE TABLE IF NOT EXISTS `authors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `firstname` varchar(32) NOT NULL COMMENT 'Имя',
  `lastname` varchar(32) NOT NULL COMMENT 'Фамилия',
  PRIMARY KEY (`id`),
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы testbook.authors: ~3 rows (приблизительно)
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` (`id`, `firstname`, `lastname`) VALUES
	(1, 'Александр', 'Пушкин'),
	(2, 'Михаил', 'Лермонтов'),
	(3, 'Федор', 'Достоевский'),
	(4, 'Дин', 'Кунц');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;


-- Дамп структуры для таблица testbook.books
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL COMMENT 'Название',
  `date_create` int(11) unsigned DEFAULT NULL COMMENT 'Добавлена',
  `date_update` int(11) unsigned DEFAULT NULL COMMENT 'Обновлена',
  `preview` varchar(255) DEFAULT NULL COMMENT 'Превью',
  `date` date NOT NULL COMMENT 'Дата выхода книги',
  `author_id` int(11) unsigned NOT NULL COMMENT 'Автор',
  PRIMARY KEY (`id`),
  KEY `FK_BOOKS_AUTHOR_ID_AUTHORS_ID` (`author_id`),
  KEY `name` (`name`),
  KEY `date_create` (`date_create`),
  KEY `date` (`date`),
  KEY `date_update` (`date_update`),
  CONSTRAINT `FK_BOOKS_AUTHOR_ID_AUTHORS_ID` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы testbook.books: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` (`id`, `name`, `date_create`, `date_update`, `preview`, `date`, `author_id`) VALUES
	(1, 'Герой нашего времени', 1449524386, 1449524386, '/uploads/books/01/1.jpg', '2015-02-04', 2),
	(2, 'Сказка о попе и его работнике Балде', 1449531533, 1449531533, '/uploads/books/02/2.jpg', '1924-06-04', 1),
	(5, 'Бесы', 1449533417, 1449533417, '/uploads/books/05/5.jpg', '2015-06-10', 3),
	(6, 'Преступление и наказание', 1449533439, 1449533439, '/uploads/books/06/6.jpg', '2015-12-15', 3),
	(7, 'Фантомы', 1449533979, 1449533979, '/uploads/books/07/7.jpg', '2015-08-04', 4);
/*!40000 ALTER TABLE `books` ENABLE KEYS */;


-- Дамп структуры для таблица testbook.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы testbook.migration: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` (`version`, `apply_time`) VALUES
	('m000000_000000_base', 1449495834),
	('m130524_201442_init', 1449495837);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;


-- Дамп структуры для таблица testbook.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Дамп данных таблицы testbook.user: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'pXzsjDqQLaHimqU9oZF9kPrKjfaNYpe1', '$2y$13$.4pbS3e6XcQxF.hfk3e6suVIZTi7DzcgDAuodLZ.PoFLAyFBRJ51K', NULL, 'admin@testbook.loc', 10, 1449503291, 1449503291);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
