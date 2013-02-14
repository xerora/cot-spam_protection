<?php

defined('COT_CODE') or die('Wrong URL');

$db->query("
	CREATE TABLE IF NOT EXISTS `".$db_x."simple_guestbook` (
	`sg_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`sg_userid` INT( 11 ) NOT NULL ,
	`sg_text` TEXT NOT NULL
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");

?>