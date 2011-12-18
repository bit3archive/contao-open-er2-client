--
-- Table `open_er2_repository`
--

CREATE TABLE `open_er2_repository` (
  `name` varchar(255) NOT NULL default '',
  `version` int(10) unsigned NOT NULL default '0',
  `build` int(10) unsigned NOT NULL default '0',
  `releasedate` int(10) unsigned NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `authorName` varchar(255) NOT NULL default '',
  `authorSite` varchar(255) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `category` varchar(15) NOT NULL default '',
  `coreMinVersion` int(10) unsigned NOT NULL default '0',
  `coreMaxVersion` int(10) unsigned NOT NULL default '0',
  `license` varchar(255) NOT NULL default '',
  `language` char(2) NOT NULL default 'en',
  `title` varchar(255) NOT NULL default '',
  `teaser` text NULL,
  PRIMARY KEY  (`name`, `version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
