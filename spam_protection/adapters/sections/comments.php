<?php

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('comments', 'plug');

function spam_protection_queue_ham(array $item, $service, array $data)
{
	global $db, $db_com;
	$finished = FALSE;
	$finished = spam_protection_service_submit_ham($service);
	if($finished)
	{
		if($db->insert($db_com, $data['com']))
		{
			spam_protection_default_queue_delete($item['sp_id']);
		}
	}
}

function spam_protection_queue_spam(array $item, $service, array $data)
{	
	spam_protection_default_queue_spam($item['sp_id']);
}

?>
