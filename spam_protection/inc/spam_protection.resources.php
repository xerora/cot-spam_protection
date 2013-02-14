<?php 

$R['sp_display_sections'] = '
	<div id="sp_display_list">
		{$list}
	</div>
';

$R['sp_order_date_desc_icon'] = '<img src="{$src}" />';
$R['sp_order_date_asc_icon'] = '<img src="{$src}" />';
$R['sp_view_icon'] = '<a class="sp_view" href="{$url}"><img class="sp_view_item" title="{$title}" src="{$src}" /></a>';
$R['sp_markas_spam_icon'] = '<a class="sp_item_mark_spam" href="{$href}"><img class="sp_item_mark_spam"  title="{$title}" src="{$src}" /></a>';
$R['sp_markas_ham_icon'] = '<a class="sp_item_mark_ham" href="{$href}"><img class="sp_item_mark_ham" title="{$title}" src="{$src}" /></a>';
$R['sp_pagenav_only_one_page'] = '<span class="pagenav pagenav_current"><a href="{$href}">1</a>';
$R['sp_display_list_link'] = '<strong><a {$selected}href="{$url}">{$section} ({$count})</a></strong> &nbsp; ';
$R['sp_display_list_link_empty'] = '<strong>{$section} ({$count})</strong> &nbsp; ';
$R['sp_collapse_all'] = '<a style="display: none;" class="{$class} btn btn-small" href="{$url}">Collapse all</a>';
$R['sp_admin_link_markas_spam'] = '<a href="{$href}">Mark as spam</a>';
$R['sp_mark_item_link'] = '<a id="sp_link_mark_item_{$item}" class="sp_link_mark" href="{$view_url}">Mark this item</a><a style="display: none;" id="sp_link_unmark_item_{$item}" class="sp_link_unmark" href="{$view_url}">Unmark this item</a>';

?>