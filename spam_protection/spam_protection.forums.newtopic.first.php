<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=forums.newtopic.newtopic.first
Order=99
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

if((bool)$cfg['plugin']['spam_protection']['filter_forums'])
{
	$sp_topic_title_length = mb_strlen(cot_import('rtopictitle','P','TXT', 255));
	$sp_post_text_length = mb_strlen(cot_import('rmsgtext','P','HTM'));
	// Override updating last poster section information
	$sp_topicmode = (cot_import('rtopicmode','P','BOL') && $cfg['forums']['cat_' . $s]['allowprvtopics']) ? 1 : 0;
	if (($sp_topic_title_length > $cfg['forums']['mintitlelength']) &&
		($sp_post_text_length > $cfg['forums']['minpostlength']))
	{
		// Ensure there won't be an error before changing this. Else it will
		// always mark the topic as private if an error is hit.
		$cfg['forums']['cat_' . $s]['allowprvtopics'] = 1;
		$_POST['rtopicmode'] = 1;
	}
}
?>