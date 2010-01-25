--
-- Table structure for table `ext_timetracking_track`
--

CREATE TABLE `ext_timetracking_track` (
  `id` int(11) NOT NULL auto_increment,
  `date_create` int(10) unsigned NOT NULL default '0',
  `date_update` int(10) unsigned NOT NULL,
  `id_user_create` int(10) unsigned NOT NULL default '0',
  `date_track` int(10) unsigned NOT NULL default '0',
  `id_task` int(10) unsigned NOT NULL default '0',
  `workload_tracked` int(10) unsigned NOT NULL default '0',
  `workload_chargeable` int(10) unsigned NOT NULL default '0',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `task` (`id_task`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_timetracking_tracking`
--

CREATE TABLE `ext_timetracking_tracking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL DEFAULT '0',
  `id_user_create` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_task` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;