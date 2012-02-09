-- phpMyAdmin SQL Dump
-- version 3.3.7deb6
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 09-Fev-2012 às 11:30
-- Versão do servidor: 5.1.58
-- versão do PHP: 5.3.9-1~dotdeb.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `bb_code`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbl_servers`
--

CREATE TABLE IF NOT EXISTS `tbl_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `host` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `clown` text COLLATE utf8_unicode_ci NOT NULL,
  `joke` text COLLATE utf8_unicode_ci NOT NULL,
  `initial_path` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '/',
  `position` tinyint(4) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
