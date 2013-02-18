<?php

defined('COT_CODE') or die('Wrong URL');
require_once SP_LIB_RELPATH.'/defensio/Defensio.php';

function spam_protection_service_connection()
{
	$service = FALSE;
	try 
	{
		$service = new Defensio(SP_SERVICE_KEY);
	}
	catch (Exception $e)
	{
		$service = FALSE;
	}
	return $service;
}

function spam_protection_service_validate_key($service = null)
{
	if(!isset($service))
	{
		$service = spam_protection_service_connection();
	}
	$status = $service->getUser();
 	return array_shift($status)==200 ? TRUE : FALSE;
}

function spam_protection_service_submit_ham(Defensio $service)
{
	global $cfg;
	$finished = FALSE;
	$is_considered_spam = TRUE;
	try 
	{
		$params = $service->getParams();			
		if((bool)$cfg['plugin']['spam_protection']['force_all_as_spam'] && !empty($params['signature']))
		{
			$result = $service->getDocument($params['signature']);
			$is_considered_spam = $result[1]->allow=="true" ? FALSE : TRUE;
		}
		if(!$cfg['debug_spam_protection'] && $is_considered_spam)
		{
			$result = $service->putDocument($params['signature'], array('allow' => 'true'));
		}
		$finished = TRUE;
	}
	catch(Exception $e)
	{
		$finished = FALSE;
	}
	return $finished;
}

function spam_protection_service_submit_spam(Defensio $service)
{
	$post_result = $service->postDocument();
	$put_result = $service->putDocument($post_result[1]->signature, array('allow' => 'false'));
}

function spam_protection_service_setup(Defensio $service, array $data) 
{
	global $cfg;
	$presets = array(
			'type' => 'comment',
			'platform' => 'cotonti',
			'client' => 'Spam Protection for Cotonti Siena'
	);
	
	if($service && !empty($data))
	{
		$document = array();
		$verify_document = array();
	
		if(isset($data['type']))
		{
			$document['type'] = $data['type'];
		}
		if(isset($data['content']))
		{
			$document['content'] = $data['content'];
		}
		if(isset($data['authorname']))
		{
			$document['author-name'] = $data['authorname'];
		}
		if(isset($data['author-email']))
		{
			$document['author-email'] = $data['authoremail'];
		}
		if(isset($data['authorip']))
		{
			$document['author-ip'] = $data['authorip'];
		}
		if(isset($data['authorurl']))
		{
			$document['author-url'] = $data['authorurl'];
		}
		if(isset($data['permalink']))
		{
			$document['document-permalink'] = $data['permalink'];
		}
		if(isset($data['referrer']))
		{
			$document['referrer'] = $data['referrer'];
		}
		// $document['author-logged-in'] = (int)$usr['id'] > 0 ? TRUE : FALSE;
		if($cfg['debug_spam_protection'])
		{
			$document['author-name'] = "viagra-test-123"; // always fails - testing
		}
		if(isset($data['signature']))
		{
			$document['signature'] = $data['signature'];
		}
		$verify_document = array_merge($presets, $document);	
		$service->setParams($verify_document);
	}
	return $service;
}

function spam_protection_check(array &$data = array())
{
	global $cfg;
	
	$is_spam = FALSE;
	$service = spam_protection_service_connection();
	$service = spam_protection_service_setup($service, $data);
	if($service && !empty($data)) 
	{
		try {
			$result = $service->postDocument();
			$data['signature'] = (string)$result[1]->signature;
			if((bool)$cfg['plugin']['spam_protection']['force_all_as_spam']) 
			{
				return array('is_spam' => TRUE);
			}
			$is_spam = $result[1]->allow=="true" ? FALSE : TRUE;	
		} catch (Exception $e) 
		{
			$is_spam = FALSE;
		}
		return array('is_spam' => (bool)$is_spam);
	}
	
}

?>