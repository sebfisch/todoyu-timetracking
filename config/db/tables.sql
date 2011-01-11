--
-- Table structure for table `ext_timetracking_track`
--

CREATE TABLE `ext_timetracking_track` (
	`id` int(10) NOT NULL auto_increment,
	`date_create` int(10) unsigned NOT NULL default '0',
	`date_update` int(10) unsigned NOT NULL,
	`id_person_create` int(10) unsigned NOT NULL default '0',
	`date_track` int(10) unsigned NOT NULL default '0',
	`id_task` int(10) unsigned NOT NULL default '0',
	`workload_tracked` int(10) unsigned NOT NULL default '0',
	`workload_chargeable` int(10) unsigned NOT NULL default '0',
	`comment` text NOT NULL,
	PRIMARY KEY  (`id`),
	 KEY `task` (`id_task`),
	 KEY `persondate` (`date_track`,`id_person_create`),
	 KEY `multi` (`id_person_create`,`date_track`,`id_task`,`date_create`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ext_timetracking_active`
--

CREATE TABLE `ext_timetracking_active` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`date_create` int(10) unsigned NOT NULL DEFAULT '0',
	`date_update` int(10) unsigned NOT NULL DEFAULT '0',
	`id_person_create` int(10) unsigned NOT NULL DEFAULT '0',
	`id_task` int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	 KEY `persondate` (`id_person_create`,`date_create`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;