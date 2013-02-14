<?php

defined('COT_CODE') or die('Wrong URL');

$cfg['debug_spam_protection'] = TRUE;

define('SP_DEFAULT_ORDERBY', 'desc');
define('SP_RELPATH', $cfg['plugins_dir'].'/spam_protection');
define('SP_SERVICE_KEY', $cfg['plugin']['spam_protection']['service_key']);
define('SP_LIB_RELPATH', SP_RELPATH.'/lib');

$db_spam_protection = !isset($db_spam_protection) ? $db_x.'spam_protection' : $db_spam_protection;
$selected_spam_service = spam_protection_get_service_alias($cfg['plugin']['spam_protection']['service_type']);
$sp_service_adapter = $cfg['plugins_dir'].'/spam_protection/adapters/services/'.$selected_spam_service.'.php';
$sp_fields_map = array(
	'content' => 'sp_content',
	'section' => 'sp_section',
	'subsection' => 'sp_subsection',
	'authorid' => 'sp_authorid',
	'authorip' => 'sp_authorip',
	'authorname' => 'sp_authorname',
	'authoremail' => 'sp_authoremail',
	'service' => 'sp_service',
	'signature' => 'sp_signature',
	'referrer' => 'sp_referrer',
	'date' => 'sp_date',
	'status' => 'sp_status',
	'data' => 'sp_data'
);

!empty($selected_spam_service) && require_once $sp_service_adapter;

/**
* Convert spam service name from configuration options to a more file safe name
*
* @param string $service Spam service name
* @return string File safe name
*/
function spam_protection_get_service_alias($service)
{
	return strtolower(str_replace(array('-', ' '), array('', '_'), $service));
}

/**
* The default action to use to delete a spam item from the admin spam queue.
*
* @param string $item The spam item's ID in spam_protection database table
* @return bool Where the action was successful or not
*/
function spam_protection_default_queue_delete($item)
{
	global $db, $db_spam_protection;
	return $db->query("DELETE FROM $db_spam_protection WHERE sp_id=?", (int)$item);
}

/**
* The default action to use when marking an item in the admin spam queue as "Spam".
*
* @param string $item The spam item's ID in spam_protection database table
* @return bool Where the action was successful or not
*/
function spam_protection_default_queue_spam($item)
{
	return spam_protection_default_queue_delete($item);
}

/**
* Adds an item to the admin spam queue for reviewing.
*
* @param array $spam_data The data to send to the spam queue. See this plugin's docs for more details.
* @return bool Whether the action was successful
*/
function spam_protection_queue_add(array $spam_data = array())
{
	global $db_spam_protection, $db, $sp_fields_map, $selected_spam_service;
	$submit = array();
	$fields = $sp_fields_map;
	foreach($fields as $name => $value) 
	{
		if(isset($spam_data[$name])) 
		{
			$submit[$fields[$name]] = $spam_data[$name];
		}
	}
	$submit['sp_content'] = isset($submit['sp_content']) ? preg_replace('/\s\s+/', ' ', strip_tags($submit['sp_content'])) : '';
	$submit['sp_service'] = isset($submit['sp_service']) ? $submit['sp_service'] : $selected_spam_service;
	$submit['sp_data'] = serialize($submit['sp_data']); 
	return $db->insert($db_spam_protection, $submit);
}

/**
* Convert data from spam_protection table fields to service workable data
*
* @param array $data Data fetched from the spam_protection database table usually
*/
function spam_protection_fields_to_service_data($data)
{
	global $sp_fields_map;
	$fields = array_flip($sp_fields_map);
	$return_data = array();
	foreach($fields as $key => $value)
	{
		if(isset($data[$key]))
		{
			$return_data[$fields[$key]] = $data[$key];
		}
	}
	return $return_data;	
}

/**
* Get available adapters for a type (sections,services) from their respective folders in /adapters
*
* @param string $type An adapter type to retrieve (sections or services)
* @return array A list of available adapters for type entered
*/
function spam_protection_get_adapters($type)
{
	global $cache;
	$cache_id = 'sp_available_'.$type;
	$available_adapters = NULL;
	$cache && $available_adapters = $cache->db->get($cache_id);
	if(!isset($available_adapters))
	{
		if($adapter_directory = opendir(SP_RELPATH.'/adapters/'.$type))
		{
			while(false !== ($adapter = readdir($adapter_directory)))
			{
				if(mb_substr($adapter, -4)=='.php')
				{
					switch($type)
					{
						case 'sections':
							$available_adapters[] = str_replace(array('.php', '_'), array('', ' '), $adapter);
						break;
						case 'services':
							$available_adapters[] = ucwords(str_replace(array('.php', '_'), array('', ' '), $adapter));
						break;
					}
				}
			}
			$cache && $cache->db->store($cache_id, $available_adapters);
		}
	}
	return $available_adapters;
}

/**
* Used to provide this plugin's configuration with options of available spam services
*
* @return array Available spam services
*/
function spam_protection_get_services()
{
	$available_spam_services = spam_protection_get_adapters('services');
	return $available_spam_services;
}

?>
