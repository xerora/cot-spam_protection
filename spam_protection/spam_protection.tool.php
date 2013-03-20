<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

defined('COT_CODE') or die('Wrong URL');

$sp_additional_admin_themes = array('bootstrap');
if(!empty($cfg['admintheme']) && in_array($cfg['admintheme'], $sp_additional_admin_themes))
{
	$sp_admin_theme = $cfg['admintheme'];
}
else
{
	$sp_admin_theme = 'default';
}
$t = new XTemplate(cot_tplfile('spam_protection.admin.'.$sp_admin_theme, 'plug', true));
require_once cot_langfile('spam_protection', 'plug');
require_once cot_incfile('spam_protection', 'plug');
require_once cot_incfile('spam_protection', 'plug', 'resources');

cot_rc_link_file(SP_RELPATH.'/tpl/spam_protection.tool.'.$sp_admin_theme.'.css');
(bool)$cfg['plugin']['spam_protection']['use_ajax'] && cot_rc_link_file(SP_RELPATH.'/inc/spam_protection.tool.js');

$out['subtitle'] = $L['sp_title'];
$adminhelp = $L['sp_admin_help'];

$section = cot_import('section', 'G', 'ALP');
$orderby = cot_import('orderby', 'G', 'ALP');
$orderby = empty($orderby) ? SP_DEFAULT_ORDERBY: $orderby;
$orderby = (in_array($orderby, array('asc', 'desc'))) ? $orderby : SP_DEFAULT_ORDERBY;
$id = cot_import('id', 'G', 'INT');
$item = cot_import('item', 'G', 'INT');
$page = cot_import('page', 'G', 'INT');
$action = cot_import('action', 'G', 'ALP');
$maxperpage = (int)$cfg['plugin']['spam_protection']['maxperpage'];
$common_url = "m=other&p=spam_protection";

$sections = spam_protection_get_adapters('sections');
$totalitems = $db->query("SELECT COUNT(*) FROM $db_spam_protection WHERE sp_service=?", $selected_spam_service)->fetchColumn();
list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);
$section_list = "";
$sections_item_count = array();
foreach($sections as $section_return)
{
	$sections_item_count[$section_return] = (int)$db->query("SELECT COUNT(*) FROM $db_spam_protection ".
		"WHERE sp_service=? AND sp_section=?", array($selected_spam_service, $section_return))->fetchColumn();
	if($sections_item_count[$section_return]>0 && empty($section))
	{
		$section = $section_return; 
	}
	if($sections_item_count[$section_return]>0)
	{
		$sections_list .= cot_rc('sp_display_list_link', 
			array(
				'url' => cot_url('admin', $common_url.'&section='.$section_return), 
				'section' => $section_return, 
				'selected' => ($section==$section_return) ? 'style="text-decoration: underline;" ' : '',
				'count' => $sections_item_count[$section_return],
			));
	}
	else 
	{
		$sections_list .= cot_rc('sp_display_list_link_empty', 
			array(
				'section' => $section_return, 
				'count' => $sections_item_count[$section_return],
			));		
	}
}

// Ensures $section is valid
if(($sections_item_count[$section]==0 || !in_array($section, $sections)) && !empty($section))
{
	cot_redirect(cot_url('admin', $common_url, '', true));
}

$pagenav = cot_pagenav('admin', $common_url.'&section='.$section.'&orderby='.$orderby, $d, $sections_item_count[$section], $maxperpage);

if($action=='validate')
{
	$service = spam_protection_service_connection();
	if(spam_protection_service_validate_key($service))
	{
		$t->parse('MAIN.VALIDATE_API_KEY.VALID');
	}
	else 
	{
		$t->parse('MAIN.VALIDATE_API_KEY.INVALID');
	}
	$t->parse('MAIN.VALIDATE_API_KEY');
}

if($action=='mark')
{
	$type = cot_import('type', 'G', 'ALP');
	if(empty($type))
	{
		$types = cot_import('sp_withselected', 'P', 'ARR');
		$items = cot_import('sp_items', 'P', 'ARR');
		foreach($types as $typeiter)
		{
			if(!empty($typeiter))
			{
				$type = $typeiter;
			}
		}	
		$items = is_array($items) ? $items : array($items);
	}
	else
	{
		$item = cot_import('item', 'G', 'INT');
		$items = array($item);
	}

	if(count($items)==$pagenav['onpage'] && $pg!=1)
	{
		--$pg;	
	}
	
	$adapter_section_filepath = SP_RELPATH.'/adapters/sections/'.$section.'.php';
	if(file_exists($adapter_section_filepath))
	{
		include $adapter_section_filepath;
	}
	else
	{
		cot_error(sprintf($L['sp_error_adapter_not_found'], htmlspecialchars($adapter_section_filepath)));
	}

	if(!function_exists('spam_protection_queue_ham') || !function_exists('spam_protection_queue_spam'))
	{
		cot_error(sprintf($L['sp_error_adapter_required_functions_not_found'], htmlspecialchars($section)));
	}

	if(!spam_protection_service_validate_key() && $type=='ham')
	{
		cot_error($L['sp_error_service_key_invalid']);
	}

	if(!cot_error_found())
	{
		$service = spam_protection_service_connection();
		if(function_exists('spam_protection_queue_before'))
		{
			spam_protection_queue_before($type);
		}
		foreach($items as $item)
		{
			$item_content = $db->query("SELECT * FROM $db_spam_protection WHERE sp_id=? LIMIT 1", (int)$item)->fetch();
			$item_data = unserialize($item_content['sp_data']);
			$service_data = spam_protection_fields_to_service_data($item_content);
			if(function_exists('spam_protection_check_data'))
			{
				$item_data = spam_protection_check_data($type, $item_data);
			}
			$service = spam_protection_service_setup($service, $service_data);
			switch($type) 
			{
				case 'ham':
					spam_protection_queue_ham($item_content, $service, $item_data);
				break;
				case 'spam':
					spam_protection_queue_spam($item_content, $service, $item_data);
				break;
			}
		}

		if(count($items)==$sections_item_count[$section])
		{
			unset($section, $pg, $orderby);
		}
	}
	if(function_exists('spam_protection_queue_after'))
	{
		spam_protection_queue_after($type);
	}
	cot_redirect(cot_url('admin', $common_url.'&section='.$section.'&orderby='.$orderby.'&d='.$pg, '', true));
}

$spam_returned = $db->query("SELECT * FROM $db_spam_protection WHERE sp_service=? AND sp_section=? ".
	" ORDER BY sp_date ".$db->prep($orderby)." LIMIT ?, ?", array($selected_spam_service, $section, $d, $maxperpage))->fetchAll();
if(empty($spam_returned)) 
{
	$t->parse('MAIN.NO_SPAM');
}
else
{
	foreach($spam_returned as $spam)
	{
		$view_url = cot_url('admin', $common_url.'&section='.$section.'&d='.$pg.'&orderby='.$orderby.'&action=view&item='.$spam['sp_id'], '#'.$spam['sp_id']);
		$markas_spam_url =  cot_url('admin', $common_url.'&section='.$section.'&action=mark&type=spam&item='.$spam['sp_id']);
		$markas_ham_url = cot_url('admin', $common_url.'&section='.$section.'&action=mark&type=ham&item='.$spam['sp_id']);
		if($item > 0 && $spam['sp_id']==$item)
		{
			$spam_data = unserialize($spam['sp_data']);
			foreach($spam_data as $table)
			{
				if(is_array($table))
				{
					foreach($table as $data_name => $data_value)
					{
						$t->assign(array(
								'SP_ITEM_VIEW_DATA_NAME' => htmlspecialchars($data_name),
								'SP_ITEM_VIEW_DATA_VALUE' => htmlspecialchars(strip_tags($data_value)),
								'SP_ITEM_MARK_LINK' => cot_rc('sp_mark_item_link', array('item_id' => $spam['sp_id'], 'view_url' => $view_url)),

						));
						$t->parse('MAIN.HAS_SPAM.SPAM_ITEMS.VIEW_ITEM.ITEM_DATA');	
					}
				}
				$t->assign(array(
						'SP_ITEM_VIEW_ID' => $spam['sp_id'],
						'SP_ITEM_VIEW_URL' => $view_url
				));
			}
			$t->parse('MAIN.HAS_SPAM.SPAM_ITEMS.VIEW_ITEM');
		}
		$view_summary = trim(htmlspecialchars($spam['sp_content']));
		$pagetotal = $pagenav['total']===null ? 0 : (int)$pagenav['total'];
		$t->assign(array(
			'SP_ITEM_CHECKBOX' => '<input type="checkbox" value="'.$spam['sp_id'].'" id="'.$spam['sp_id'].'" class="sp_item" name="sp_items[]" />',
			'SP_ITEM_SECTION' => htmlspecialchars($spam['sp_section']),
			'SP_ITEM_USERNAME' => htmlspecialchars($spam['sp_authorname']),
			'SP_ITEM_DATE' => cot_date('datetime_medium', (int)$spam['sp_date']),	
			'SP_ITEM_SUMMARY' => (strlen($view_summary)===150) ? $view_summary.'...' : $view_summary,
			'SP_ITEM_ORDER_DATE_DESC_ICON' => cot_rc('sp_order_date_desc_icon', array('src' => SP_RELPATH.'/img/arrow-up.gif')),
			'SP_ITEM_ORDER_DATE_ASC_ICON' => cot_rc('sp_order_date_asc_icon', array('src' => SP_RELPATH.'/img/arrow-down.gif')),
			'SP_ACTION_ICON_VIEW' => cot_rc('sp_view_icon', array('url' => $view_url, 'title' => $L['sp_action_view_item'], 'src' => SP_RELPATH.'/img/database_table.png')),
			'SP_ACTION_VIEW_URL' => $view_url,
			'SP_ACTION_MARKAS_SPAM_URL' => $markas_spam_url,
			'SP_ACTION_MARKAS_HAM_URL' => $markas_ham_url,
			'SP_ACTION_ICON_MARKAS_SPAM' => cot_rc('sp_markas_spam_icon', array('href' => $markas_spam_url,'title' => $L['sp_action_markas_spam'], 'src' => SP_RELPATH.'/img/database_delete.png')),
			'SP_ACTION_ICON_MARKAS_HAM' => cot_rc('sp_markas_ham_icon', array('href' => $markas_ham_url,'title' => $L['sp_action_markas_ham'], 'src' => SP_RELPATH.'/img/database_add.png')),
			));
		$t->parse('MAIN.HAS_SPAM.SPAM_ITEMS');
	}
	$t->assign(array(
		'SP_SECTION_NAME' => ucwords($section),
		'SP_CHECKALL' => cot_checkbox(false, 'sp_checkall', '', array('id' => 'sp_checkall', 'title' => $L['sp_action_checkall'], 'style' => 'display: none;')),	
		'SP_PAGENAV_ONPAGE' => $pagenav['onpage'],
		'SP_PAGENAV_MAIN' => $pagenav['main'],
		'SP_PAGENAV_PREV' => $pagenav['prev'],
		'SP_PAGENAV_NEXT' => $pagenav['next'],
		'SP_PAGENAV_TOTAL' => $pagetotal,
		'SP_FORM_ACTION' => cot_url('admin', $common_url.'&action=mark&section='.$section.'&orderby='.$orderby.'&d='.$pg),
		'SP_WITHSELECTED_SELECTBOX' => cot_selectbox('', 'sp_withselected[]', array('spam', 'ham'), array($L['sp_action_markas_spam'], $L['sp_action_markas_ham']), true, array('class' => 'sp_withselected')),
		'SP_WITHSELECTED_BUTTON' => cot_inputbox('submit', '', $L['sp_action_perform_action'], array('class' => 'sp_withselected_button')), 
		'SP_SECTIONS' => cot_rc('sp_display_sections', 
				array(
					'list' => $sections_list,
					'selectbox' => cot_selectbox($section, 'sp_sections', $sections, $sections, false)
				)),
		'SP_TOTAL_COUNT' => $sections_item_count[$section],
		'SP_COLLAPSE_ALL' => cot_rc('sp_collapse_all', array('url' => '#', 'class' => 'sp_collapse_all')),
	));
	$t->parse('MAIN.HAS_SPAM');
}
$t->assign('SP_VALIDATE_KEY_URL', cot_url('admin', $common_url.'&action=validate'));
cot_display_messages($t);
$t->parse('MAIN');
$plugin_body = $t->text('MAIN');

?>
