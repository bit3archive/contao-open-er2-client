
--
-- Table `open_er2_repository`
--

CREATE TABLE `open_er2_repository` (
  `name` varchar(100) NOT NULL default '',
  `version` int(10) unsigned NOT NULL default '0',
  `build` int(10) unsigned NOT NULL default '0',
  `releaseDate` int(10) unsigned NOT NULL default '0',
  `author` varchar(255) NOT NULL default '',
  `authorName` varchar(255) NOT NULL default '',
  `authorSite` varchar(255) NOT NULL default '',
  `type` varchar(10) NOT NULL default '',
  `category` varchar(15) NOT NULL default '',
  `coreMinVersion` int(10) unsigned NOT NULL default '0',
  `coreMaxVersion` int(10) unsigned NOT NULL default '0',
  `language` char(2) NOT NULL default 'en',
  `title` varchar(255) NOT NULL default '',
  `teaser` text NULL,
  `description` text NULL,
  `releaseNotes` text NULL,
  `license` varchar(255) NOT NULL default '',
  `copyright` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`, `version`, `language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `open_er2_repository_dependency`
--

CREATE TABLE `open_er2_repository_dependency` (
  `extension` varchar(100) NOT NULL default '',
  `dependsOn` varchar(100) NOT NULL default '',
  `minVersion` int(10) unsigned NOT NULL default '0',
  `maxVersion` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`extension`, `dependsOn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
