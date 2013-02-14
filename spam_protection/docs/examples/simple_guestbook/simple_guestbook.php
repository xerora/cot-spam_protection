<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=standalone
Order=99
[END_COT_EXT]
==================== */

/**
*	simple_guestbook Example spam_protection usage
*
*	All in one file for demonstration
*/

defined('COT_CODE') or die('Wrong URL');

$a = cot_import('a', 'G', 'ALP');

require_once cot_incfile('simple_guestbook', 'module');
require_once cot_langfile('simple_guestbook', 'module');
require_once cot_incfile('forms');

if($a=='send')
{
	cot_check_xp();	
	$rmsg['sg_message'] = cot_import('rmessage', 'P', 'HTM');
	$rmsg['sg_userid'] = $usr['id'];

	if(mb_strlen($rmsg['sg_message'])<2)
	{
		cot_error($L['sg_message_too_short']);
	}

	// Spam Protection
	if(cot_plugin_active('spam_protection'))
	{
		require_once cot_incfile('spam_protection', 'plug');
		$spam_data = array(
			'content' => $rmsg['sg_message'],
			'authorname' => $usr['name'],
			'authoremail' => $usr['profile']['user_email'],
			'authorid' => $usr['id'],
			'authorip' => $usr['ip'],
			'date' => $sys['now'],
			'section' => 'guestbook',
			'data' => array('simple_guestbook' => $rmsg),
		);
		$spam_check_result = spam_protection_check($spam_data);
		if($spam_check_result['is_spam'])
		{
			// Item is considered spam. Just skip posting. 
			// Giving indication that the item was rejected is usually not advised
			spam_protection_queue_add($spam_data);
			cot_redirect(cot_url('simple_guestbook'));
		}
	}

	if(!cot_error_found()) 
	{
		if($db->insert($db_simple_guestbook, $rmsg))
		{
			cot_message($L['sg_message_successful']);
		}
		else
		{
			cot_error($L['sg_message_unsuccessful']);
		}
	}

	cot_redirect(cot_url('simple_guestbook'));
}

$out['subtitle'] = $L['sg_title'];
require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(cot_tplfile('simple_guestbook', 'module'));

if($row = $db->query("SELECT g.*,u.user_name FROM $db_simple_guestbook AS g ".
	"LEFT JOIN $db_users as u ON g.sg_userid=u.user_id ORDER BY sg_id DESC LIMIT 25")->fetchAll())
{
	foreach($row as $msg)
	{
		$t->assign(array(
			'MSG_TEXT' => htmlspecialchars($msg['sg_message']),
			'MSG_AUTHOR' => $msg['sg_userid']>0 ? htmlspecialchars($msg['user_name']) : $L['Guest'],
		));
		$t->parse('MAIN.HAS_MESSAGES.MESSAGE_ROW');
	}
	$t->parse('MAIN.HAS_MESSAGES');
}
else
{
	$t->parse('MAIN.NO_MESSAGES');
}

$t->assign(array(
	'FORM_MESSAGE_INPUT' => cot_textarea('rmessage', '', 15, 120),
	'FORM_SEND_BUTTON' => cot_inputbox('submit', 'submit', $L['sg_submit_message']),
	'FORM_ACTION' => cot_url('simple_guestbook', 'a=send'),
));

cot_display_messages($t);
$t->parse('MAIN');
$t->out('MAIN');
require_once $cfg['system_dir'].'/footer.php';

?>