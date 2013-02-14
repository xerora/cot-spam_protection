<?php 
/* ====================
 [BEGIN_COT_EXT]
Hooks=comments.send.new
Order=99
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

if((bool)$cfg['plugin']['spam_protection']['filter_comments'])
{
	require_once cot_incfile('spam_protection', 'plug');
	$spam_data = array(
		'content' => $rtext,
		'authorname' => $comarray['com_author'],
		'authoremail' => $usr['profile']['user_email'],
		'authorid' => $comarray['com_authorid'],
		'authorip' => $comarray['com_authorip'],
		'date' => $comarray['com_date'],
		'subsection' => $comarray['com_area'],
		'section' => 'comments',
		'data' => array(
			'com' => $comarray + array('com_id' => $id)),
	);
	$spam_protection_result = spam_protection_check($spam_data);
	if($spam_protection_result['is_spam'])
	{
		$db->query("DELETE FROM $db_com WHERE com_id=?", $id)->execute();
		spam_protection_queue_add($spam_data);
		if((bool)$cfg['plugin']['spam_protection']['notify_poster'])
		{
			require_once cot_langfile('spam_protection', 'plug');
			cot_message($L['sp_notify_comment_marked_spam']);
		}
	}
}

?>
