<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=forums.newtopic.newtopic.done
Order=99
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL');

if((bool)$cfg['plugin']['spam_protection']['filter_forums'])
{
	require_once cot_incfile('spam_protection', 'plug');
	
	$spam_data = array(
		'content' => $rmsg['fp_text'],
		'authorname' => $rtopic['ft_firstpostername'],
		'authoremail' => isset($ruser['profile']['user_email']) ? $ruser['profile']['user_email'] : '',
		'authorip' => $usr['ip'],
		'date' => $rtopic['ft_creationdate'],
		'subsection' => 'topic',
		'section' => 'forums',
		'data' => array(
			'forum_topics' => array(
				'ft_id' => $q,
				'ft_mode' => $sp_topicmode,
			) + $rtopic,
			'forum_posts' => array(
				'fp_id' => $p,
			) + $rmsg,
		),
	);

	$spam_protection_result = spam_protection_check($spam_data);
	if($spam_protection_result['is_spam'])
	{
		$db->query("DELETE FROM $db_forum_topics WHERE ft_id=?", $q);
		$db->query("DELETE FROM $db_forum_posts WHERE fp_id=?", $p);
		spam_protection_queue_add($spam_data);

		cot_shield_update(45, "New topic");
		cot_redirect(cot_url('forums', "m=topics&s=".$s, '', true));
	}
}
?>