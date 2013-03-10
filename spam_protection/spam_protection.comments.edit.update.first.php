<?php
/* ====================
 [BEGIN_COT_EXT]
Hooks=comments.edit.update.first
Order=99
[END_COT_EXT]
==================== */
defined('COT_CODE') or die('Wrong URL');
$sp_markas = cot_import('markas', 'G', 'ALP');

if($sp_markas=='spam')
{
	cot_check_xg();
	$row = $db->query("SELECT * FROM $db_com WHERE com_id=? AND com_code=? LIMIT 1", array($id, $item))->fetch();
	require_once cot_incfile('spam_protection', 'plug');
	$service = spam_protection_service_connection();
	$service = spam_protection_service_setup($service, array(
		'content' => $row['com_text'],
		'authorname' => $row['com_author'],
		'authorid' => $row['com_authorid'],
		'authorip' => $row['com_authorip'],
		'date' => $row['com_date'],
	));
	if(spam_protection_service_submit_spam($service))
	{
		$db->delete($db_com, "com_id=$id");
		foreach ($cot_extrafields[$db_com] as $exfld)
		{
			cot_extrafield_unlinkfiles($row['com_' . $exfld['field_name']], $exfld);
		}
		cot_redirect(cot_url($url_area, $url_params, '#comments', true));
	}
}

?>