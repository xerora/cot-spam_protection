<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=forums.posts.newpost.first
Order=99
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL');

if(!$merge && (bool)$cfg['plugin']['spam_protection']['filter_forums'])
{
	require_once cot_incfile('spam_protection', 'plug');

	$spam_data = array(
		'content' => $rmsg['fp_text'],
		'authorname' => $usr['name'],
		'subsection' => 'post',
		'authorip' => $rmsg['fp_posterip'],
		'date' => (int)$sys['now'],
		'section' => 'forums',
	);
	$spam_protection_result = spam_protection_check($spam_data);
	if(!cot_error_found() && $spam_protection_result['is_spam'])
	{
		$rmsg['fp_topicid'] = (int)$q;
		$rmsg['fp_cat'] = $s;
		$rmsg['fp_posterid'] = (int)$usr['id'];
		$rmsg['fp_postername'] = $usr['name'];
		$rmsg['fp_creation'] = (int)$sys['now'];
		$rmsg['fp_updater'] = 0;
		$db->insert($db_forum_posts, $rmsg);
		
		$rmsg['fp_id'] = $db->lastInsertId();
		$db->query("DELETE FROM $db_forum_posts WHERE fp_id=?", $rmsg['fp_id']);

		$spam_data += array('data' => array(
			'forum_posts' => $rmsg,
		));

		spam_protection_queue_add($spam_data);

		cot_shield_update(30, "New post");
		cot_redirect(cot_url('forums', "m=posts&q=$q&n=last", '#bottom', true));	
	}
}

?>