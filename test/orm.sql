--
-- 表的结构 `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE IF NOT EXISTS `author` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nationality` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `author`
--

INSERT INTO `author` (`id`, `name`, `gender`, `nationality`) VALUES
(1, 'J. K. Rowling', 'female', 'UK'),
(2, 'Ernest Hemingway', 'male', 'US');

-- --------------------------------------------------------

--
-- 表的结构 `book`
--

DROP TABLE IF EXISTS `book`;
CREATE TABLE IF NOT EXISTS `book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` int(10) unsigned NOT NULL COMMENT '作者id',
  `language` enum('en','zh') COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` int(10) unsigned NOT NULL COMMENT '出版商，对应的表为company',
  `hit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读次数',
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `visited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后阅读时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `book`
--

INSERT INTO `book` (`id`, `name`, `author`, `language`, `publisher`, `hit`, `created`, `visited`) VALUES
(1, 'Harry Potter and the Philosopher''s Stone', 1, 'en', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Harry Potter and the Chamber of Secrets', 1, 'en', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Harry Potter and the Prisoner of Azkaban', 1, 'en', 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'The Old man and the Sea', 2, 'en', 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `company`
--

INSERT INTO `company` (`id`, `name`) VALUES
(1, '清华大学出版社'),
(2, '三联出版社');


-- --------------------------------------------------------

--
-- 表的结构 `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int(3) NOT NULL,
  `nation` int(10) NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `person`
--

INSERT INTO `person` (`id`, `name`, `age`, `nation`, `gender`) VALUES
(1, 'bill', 22, 1, 'male'),
(2, 'rose', 24, 1, 'female'),
(3, 'rose', 18, 2, 'female');



--
-- Table structure for table `nation`
--

DROP TABLE IF EXISTS `nation`;
CREATE TABLE IF NOT EXISTS `nation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `nation`
--

INSERT INTO `nation` (`id`, `name`) VALUES
(1, 'us'),
(2, 'uk');

