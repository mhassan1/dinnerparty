CREATE TABLE IF NOT EXISTS `shabbat_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) DEFAULT NULL,
  `event_where` varchar(200) DEFAULT NULL,
  `event_where_full` varchar(200) DEFAULT NULL,
  `event_description` varchar(1000) DEFAULT NULL,
  `event_start` datetime DEFAULT NULL,
  `event_end` datetime DEFAULT NULL,
  `active` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `shabbat_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_content` varchar(2000) DEFAULT NULL,
  `post_ts` datetime DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

CREATE TABLE IF NOT EXISTS `shabbat_rsvps` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resp` varchar(45) DEFAULT NULL,
  `addl_guests` int(11) DEFAULT '0',
  `note` varchar(1000) DEFAULT NULL,
  `rsvp_ts` datetime DEFAULT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `event_fk_idx` (`event_id`),
  KEY `user_fk_idx` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `shabbat_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `friend` varchar(100) DEFAULT NULL,
  `admin` varchar(1) DEFAULT 'N',
  `active` varchar(1) DEFAULT 'Y',
  `join_ts` datetime DEFAULT NULL,
  `last_ts` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_UNIQUE` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;
