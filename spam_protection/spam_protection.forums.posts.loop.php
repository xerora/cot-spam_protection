<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.loop
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

if((bool)$cfg['plugin']['spam_protection']['filter_forums'] && $usr['isadmin'])
{
	require_once cot_incfile('spam_protection', 'plug', 'resources');
	$markas_spam_url = cot_url('forums', 'm=posts&markas=spam&q='.$q.'&p='.$row['fp_id'].'&'.cot_xg());
	$t->assign(array(
		'SP_MARK_AS_SPAM_URL' => $markas_spam_url,
		'SP_MARK_AS_SPAM_LINK' => cot_rc('sp_admin_link_markas_spam', array('href' => $markas_spam_url)),
	));
}
