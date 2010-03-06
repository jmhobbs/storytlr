SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `scrnshots_data` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `source_id` int(10) unsigned NOT NULL,
  `created_at` varchar(45) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `link` varchar(256) NOT NULL,
  `href` varchar(256) NOT NULL,
  `thumbnail` varchar(256) NOT NULL,
  `image` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `DUPLICATES` USING BTREE (`source_id`,`link`),
  FULLTEXT KEY `SEARCH` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
