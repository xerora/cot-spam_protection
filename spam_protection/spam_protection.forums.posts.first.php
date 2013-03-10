<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.first
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$sp_markas = cot_import('markas', 'G', 'ALP');
if($sp_markas=='spam' && (bool)$cfg['plugin']['spam_protection']['filter_forums'])
{
	cot_check_xg();
	$row = $db->query("SELECT p.fp_text,p.fp_posterid,p.fp_posterip, p.fp_creation,u.user_name FROM $db_forum_posts ".
		"AS p LEFT JOIN $db_users AS u ON u.user_id=p.fp_posterid WHERE fp_id=? LIMIT 1", $p)->fetch();
	require_once cot_incfile('spam_protection', 'plug');
	$service = spam_protection_service_connection();
	$service = spam_protection_service_setup($service, array(
		'content' => $row['fp_text'],
		'authorname' => $row['user_name'],
		'authorid' => $row['fp_posterid'],
		'authorip' => $row['fp_posterip'],
		'date' => $row['fp_creation'],
	));
	if(spam_protection_service_submit_spam($service))
	{
		cot_log('Marked forum post #'.$p.' as spam', 'adm');
		cot_redirect(cot_url('forums', 'm=posts&a=delete&'.cot_xg().'&q='.$q.'&p='.$p, '', true)); // delete the forum post / topic
	}
}