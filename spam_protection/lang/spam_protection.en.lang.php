<?php 
defined('COT_CODE') or die('Wrong URL');

$L['sp_title'] = 'Items currently marked as spam';
$L['sp_error_adapter_not_found'] = 'Adapter "%s" not found. File doesn\'t exist or there was a spelling mistake somewhere.';
$L['sp_error_adapter_required_functions_not_found'] = 'Required callback functions <strong>spam_protection_queue_ham(array $item)</strong> and/or <strong>spam_protection_queue_spam(array $item)</strong> '.
	' were not found for "%s" adapter. These functions are needed to perform an action on an item. For further information, referer to this plugin\'s docs.';
$L['sp_error_service_key_invalid'] = 'Your API key must be valid before marking an item as ham. Make sure your API key is configured correctly.';
$L['sp_notify_comment_marked_spam'] = 'Your comment has been submitted and is pending moderation.';
$L['sp_log_forum_post_deleted'] = 'Forum post #%d was deleted after marked as ham because related topic #%d no longer exists';
$L['sp_action_check_key'] = 'Check API Key';
$L['sp_info_key_valid'] = 'Your API key is valid.';
$L['sp_info_key_invalid'] = 'Your API key is invalid.';
$L['sp_action_markas_ham'] = 'Mark as Ham';
$L['sp_action_markas_spam'] = 'Mark as Spam';
$L['sp_action_view_item'] = 'View item summary';
$L['sp_action_checkall'] = 'Check all items';
$L['sp_action_perform_action'] = 'Perform Action';
$L['sp_empty_queue'] = 'There is currently no spam to review.';
$L['sp_summary_for_item'] = 'Summary  for item';
$L['sp_without_markup'] = 'Without markup';
$L['sp_username'] = 'Username';
$L['sp_content_summary'] = 'Content Summary';
$L['sp_actions'] = 'Actions';
$L['sp_with_selected'] = 'With selected';
$L['sp_items'] = 'items';
$L['sp_items_onpage'] = 'Items on page';
?>