CREATE DATABASE IF NOT EXISTS `feels` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `feels`;

CREATE TABLE `answers` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`uuid` varchar(200) NOT NULL,
	`therating` tinyint(3) unsigned NOT NULL,
	`thereason` varchar(200) NOT NULL,
	`saved_ts` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `thefeels` (
	`uuid` varchar(200) NOT NULL,
	`username` varchar(200) NOT NULL,
	`therating` tinyint(3) unsigned DEFAULT NULL,
	`thecomment` text,
	`sent_ts` int(10) unsigned NOT NULL,
	`rated_ts` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `answers`
ADD PRIMARY KEY (`id`);

ALTER TABLE `thefeels`
ADD PRIMARY KEY (`uuid`), ADD KEY `username` (`username`);
