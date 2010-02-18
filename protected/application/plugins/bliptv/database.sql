DROP TABLE IF EXISTS `bliptv_data`;
SET @saved_cs_client = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `bliptv_data` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `source_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `show` varchar(255) NOT NULL,
  `embed_uri` varchar(255) NOT NULL,
  `embed` text NOT NULL,
  `length` int(6) unsigned NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `license` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `published` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `DUPLICATES` (`source_id`,`uri`),
  FULLTEXT KEY `SEARCH` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;