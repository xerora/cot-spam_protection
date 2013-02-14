<?php 

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('spam_protection', 'plug');

$install_query = "
CREATE TABLE IF NOT EXISTS `".$db_spam_protection."` (
  `sp_id` int(11) NOT NULL AUTO_INCREMENT,
  `sp_section` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sp_subsection` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sp_authorid` int(11) NOT NULL,
  `sp_authorip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `sp_authoremail` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `sp_authorname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sp_service` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `sp_signature` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `sp_content` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `sp_referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sp_date` int(11) NOT NULL,
  `sp_status` tinyint(1) NOT NULL,
  `sp_data` mediumblob,
  PRIMARY KEY (`sp_id`),
  KEY `sp_section` (`sp_section`),
  KEY `sp_service_section` (`sp_service`, `sp_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;		
";

$db->query($install_query)->execute();

?>
