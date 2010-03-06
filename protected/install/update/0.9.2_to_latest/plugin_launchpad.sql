SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `launchpad_data` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `source_id` int(10) unsigned NOT NULL,
  `link` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `branch_url` text NOT NULL,
  `branch` text NOT NULL,
  `revision` varchar(256) NOT NULL,
  `project` varchar(256) NOT NULL,
  `message` text NOT NULL,
  `published` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `DUPLICATES` USING BTREE (`source_id`,`link`),
  FULLTEXT KEY `SEARCH` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

