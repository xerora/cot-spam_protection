<?php

defined('COT_CODE') or die('Wrong URL');
require_once SP_LIB_RELPATH.'/akismet/Akismet.class.php';

function spam_protection_service_connection()
{
	$service = FALSE;
	try {
		$service = new Akismet(COT_ABSOLUTE_URL, SP_SERVICE_KEY);
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
	return $service->isKeyValid();
}

function spam_protection_service_submit_ham(Akismet $service)
{
	global $cfg;
	$finished = FALSE;
	$is_considered_spam = TRUE;
	try
	{
		if((bool)$cfg['plugin']['spam_protection']['force_all_as_spam'])
		{
			$is_considered_spam = $service->isCommentSpam();	
		}		
		if(!$cfg['debug_spam_protection'] && $is_considered_spam)
		{
			$service->submitHam();
		}
		$finished = TRUE;
	}
	catch(Exception $e)
	{
		$finished = FALSE;
	}
	return $finished;
}

function spam_protection_service_submit_spam(Akismet $service)
{
	$finished = FALSE;
	try 
	{
		$service->submitSpam();
		$finished = TRUE;
	}
	catch(Exception $e)
	{
		$finished = FALSE;
	}
	return $finished;
}

function spam_protection_service_setup(Akismet $service, array $data)
{
	global $cfg;

	if(!empty($data) && $service) {
		if(isset($data['content']))
		{
			$service->setCommentContent($data['content']);
		}
		if($cfg['debug_spam_protection'])
		{
			$data['authorname'] = "viagra-test-123"; // Akismet spam test - always fails
		}
		if(isset($data['authorname']))
		{
			$service->setCommentAuthor($data['authorname']);
		}
		if(isset($data['authoremail']))
		{
			$service->setCommentAuthorEmail($data['authoremail']);
		}
		if(isset($data['authorurl']))
		{
			$service->setCommentAuthorURL($data['authoremail']);
		}
		if(isset($data['section']))
		{
			$service->setCommentType($data['section']);
		}
		if(isset($data['authorip']))
		{
			$service->setUserIP($data['authorip']);
		}
		if(isset($data['permalink']))
		{
			$service->setPermalink($data['permalink']);
		}
		if(isset($data['referrer']))
		{
			$service->setReferrer($data['referrer']);
		}
	}
	return $service;	
}

function spam_protection_check(array $data = array())
{
	global $cfg;
	if((bool)$cfg['plugin']['spam_protection']['force_all_as_spam'])
	{
		return array('is_spam' => TRUE);
	}
	$is_spam = FALSE;
	$service = spam_protection_service_connection();
	$service = spam_protection_service_setup($service, $data);
	
	if($service)
	{
		try 
		{
			$is_spam = $service->isCommentSpam();	
		}
		catch(Exception $e)
		{
			// continue working anyway
			$is_spam = FALSE;
		}
	}
	return array('is_spam' => (bool)$is_spam);
}

?>