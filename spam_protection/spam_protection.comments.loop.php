<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=comments.loop
Order=99
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL');
if((bool)$cfg['plugin']['spam_protection']['filter_comments'])
{	
	if($force_admin)
	{
		list($sp_auth_read, $sp_auth_write, $sp_auth_admin) = cot_auth('plug', 'comments');
	}
	else
	{
		$sp_auth_admin = $auth_admin;
	}

	if($sp_auth_admin)
	{
		require_once cot_incfile('spam_protection', 'plug', 'resources');
		$markas_spam_url = cot_url('comments', 'm=edit&a=update&cat='.$cat.'&markas=spam&id='.$row['com_id'].'&'.cot_xg());
		$t->assign(array(
			'SP_MARK_AS_SPAM_LINK' => cot_rc('sp_admin_link_markas_spam', array('href' => $markas_spam_url)),
			'SP_MARK_AS_SPAM_URL' => $markas_spam_url,
		));
	}
}
?>