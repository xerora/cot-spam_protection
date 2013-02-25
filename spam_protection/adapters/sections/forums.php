<?php

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('forums', 'module');

function spam_protection_queue_ham(array $item, $service, array $data)
{
	switch($item['sp_subsection'])
	{
		case 'post':
			spam_protection_queue_ham_forum_post($item, $service, $data);
		break;
		case 'topic':
			global $db, $db_forum_topics;
			if(spam_protection_service_submit_ham($service))
			{
				if($db->insert($db_forum_topics, $data['forum_topics']) &&
					spam_protection_queue_ham_forum_post($item, $service, $data))
				{
					cot_forums_sectionsetlast($data['forum_posts']['fp_cat'], "fs_postcount+1", "fs_topiccount+1");
					spam_protection_queue_remove($item['sp_id']);
				}
			}
		break;
	}
}

function spam_protection_queue_spam(array $item)
{
	return spam_protection_queue_remove($item['sp_id']);
}

function spam_protection_queue_ham_forum_post(array $item, $service, array $data)
{
	global $db, $db_users, $cfg, $db_forum_topics, $db_forum_posts, $L;
	if(spam_protection_service_submit_ham($service))
	{
		$forum_post_data = $data['forum_posts'];
		$update_topic_lastpost = $db->query("SELECT ft_updated from $db_forum_topics WHERE ft_id=? LIMIT 1", $forum_post_data['fp_topicid'])->fetchColumn();
		if(!$update_topic_lastpost && $item['sp_subsection']=='post')
		{
			spam_protection_queue_remove($item['sp_id']);
			cot_log(sprintf($L['sp_log_forum_post_deleted'], $data['forum_posts']['fp_id'], $data['forum_posts']['fp_topicid']), 'plg');
			return;
		}
		if($inserted = $db->insert($db_forum_posts, $forum_post_data))
		{
			$update_last_query = "";
			if($update_topic_lastpost<$forum_post_data['fp_creation'] && $item['sp_subsection']!='topic')
			{
				$update_last_query = ", ft_updated='".$db->prep($forum_post_data['fp_creation'])."',
					ft_lastposterid='".$forum_post_data['fp_posterid']."', ft_lastpostername='".$db->prep($forum_post_data['fp_postername'])."'";
				cot_forums_sectionsetlast($forum_post_data['fp_cat'], 'fs_postcount+1');

			}
			if($item['sp_subsection']!='topic')
			{
				$db->query("UPDATE $db_forum_topics SET ft_postcount=ft_postcount+1".$update_last_query." WHERE ft_id=?", $forum_post_data['fp_topicid']);
				cot_forums_sectionsetlast($forum_post_data['fp_cat']);
				spam_protection_queue_remove($item['sp_id']);
			}
			if ($cfg['forums']['cat_' . $forum_post_data['fp_cat']]['countposts'])
			{
				$db->query("UPDATE $db_users SET user_postcount=user_postcount+1 WHERE user_id=?", $forum_post_data['fp_posterid']);
			}

			return $inserted;
		}
	}
}

function spam_protection_queue_after($type)
{
	global $cfg, $cache;
	if ($cache)
	{
		($cfg['cache_forums']) && $cache->page->clear('forums');
		($cfg['cache_index']) && $cache->page->clear('index');
	}	
}

?>