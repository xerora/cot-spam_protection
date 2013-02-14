<!-- BEGIN: MAIN -->

<div>

<h2 style="margin-bottom: 0px;">{PHP.L.sp_title}</h2>
<br />

<div class="block" style="margin-bottom: 20px;">

	<div>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	</div>

	<p style="padding-bottom: 7px;">
		<a href="{SP_VALIDATE_KEY_URL}" class="btn btn-small btn-info">{PHP.L.sp_action_check_key} &nbsp;<i class="icon-info-sign icon-white"></i></a>
	</p>

	<!-- BEGIN: VALIDATE_API_KEY -->
		<!-- BEGIN: VALID -->
			<div class="alert alert-info" style="margin-top: 0px;">
				<i class="icon-thumbs-up"></i> &nbsp;{PHP.L.sp_info_key_valid}
			</div>
		<!-- END: VALID -->
		<!-- BEGIN: INVALID -->
			<div class="alert alert-error" style="margin-top: 0px;">
				<i class="icon-thumbs-down"></i> &nbsp;{PHP.L.sp_info_key_invalid}
			</div>
		<!-- END: INVALID -->		
	<!--END: VALIDATE_API_KEY -->
<!-- BEGIN: NO_SPAM -->	
	<div class="alert alert-warning">
		{PHP.L.sp_empty_queue}
	</div>
<!-- END: NO_SPAM -->
<!-- BEGIN: HAS_SPAM -->
	<form method="post" action="{SP_FORM_ACTION}" id="sp_form">

	<div style="overflow: hidden; margin-bottom: 20px;">
		<div style="float: left; padding-top: 10px;">
			{SP_SECTIONS}
		</div>
		<div class="pagination pagination-right" style="margin: 0px; padding: 0px;">
			<ul>
				{SP_PAGENAV_PREV}{SP_PAGENAV_MAIN}{SP_PAGENAV_NEXT}
			</ul>
		</div>
	</div>



	<div style="overflow: hidden;">
		<div style="float: left; padding-top: 5px;">
			<span style="font-weight: bold; font-size: 18px;">{SP_SECTION_NAME}</span>
		</div>
		<div style="float: right;">
			<strong>{SP_COLLAPSE_ALL}</strong>
			&nbsp; &nbsp; &nbsp;
			<small><strong>{PHP.L.sp_with_selected}:</strong></small> {SP_WITHSELECTED_SELECTBOX} {SP_WITHSELECTED_BUTTON}
		</div>
	</div>

	<table class="table table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
		<thead>
			<th class="width10" style="wdith: 5%;">{SP_CHECKALL}</th> 
			<th class="width10" style="width: 15%;">{PHP.L.sp_username}</th>
			<th class="width70" style="width: 50%;">{PHP.L.sp_content_summary} ({PHP.L.sp_without_markup})</th>
			<th class="width10" style="width: 15%;">{PHP.L.Date} &nbsp; {SP_ITEM_ORDER_DATE_ASC_ICON} {SP_ITEM_ORDER_DATE_DESC_ICON}</th>
			<th class="width5" style="width: 15%;">{PHP.L.sp_actions}</th>
		</thead>
	<!-- BEGIN: SPAM_ITEMS -->
		<tr class="sp_items">
			<td class="width10 centerall sp-pad">{SP_ITEM_CHECKBOX}</td>
			<td class="sp-pad">{SP_ITEM_USERNAME}&nbsp;</td>
			<td>{SP_ITEM_SUMMARY}</td>
			<td>{SP_ITEM_DATE}</td>
			<td class="sp_actions">
				<a href="{SP_ACTION_VIEW_URL}" title="{PHP.L.sp_action_view_item}" class="btn btn-mini sp_view sp_view_item"><i class="sp_view_item icon-th-list"></i></a> &nbsp; <a href="{SP_ACTION_MARKAS_SPAM_URL}" title="{PHP.L.sp_action_markas_spam}" class="btn btn-mini"><i class="icon-remove-sign"></i></a> &nbsp; <a href="{SP_ACTION_MARKAS_HAM_URL}" title="{PHP.L.sp_action_markas_ham}" class="btn btn-mini"><i class="icon-ok-sign"></i></a>
			</td>
		</tr>
		<!-- BEGIN: VIEW_ITEM -->
			<tr id="item_{SP_ITEM_VIEW_ID}" class="view_item">
				<td colspan="5" style="padding: 17px; background-color: #f0f0f0;">
				<table style="width: 100%; border-top: 2px solid #c9c9c9; border-bottom: 1px solid #ccc;">
					<tr>
						<td colspan="5" style="border: 2px solid #c9c9c9; border-bottom: 0px; background-color: #e0e0e0;"><strong>{PHP.L.sp_summary_for_item} #{SP_ITEM_VIEW_ID}</strong> &nbsp; <span class="label">{PHP.L.sp_without_markup}</span></td>
					</tr>
					<!-- BEGIN: ITEM_DATA -->
						<tr style="border-bottom: 1px solid #ddd;">
							<td  style="width: 20%; border: 0px; border-right: 1px solid #ddd;  border-left: 2px solid #c9c9c9; background-color: #fff; text-align: left;">{SP_ITEM_VIEW_DATA_NAME}</td>
							<td style="border: 0px; border-right: 2px solid #c9c9c9; background-color: #fff;" class="width80">{SP_ITEM_VIEW_DATA_VALUE}</td>
						</tr>
					<!-- END: ITEM_DATA -->
					<tr class="sp_view_mark" style="display: none; border-bottom: 2px solid #c9c9c9; border-top: 0px;">
						<td colspan="2" style="border-left: 2px solid #c9c9c9; border-right: 2px solid #c9c9c9; background-color: #fff;text-align: right;">{SP_ITEM_MARK_LINK} &nbsp; </td>
					</tr>
				</table>
				</td>
			</tr>
		<!-- END: VIEW_ITEM -->
	<!-- END: SPAM_ITEMS -->
	</table>
	<div style="border-top: 2px solid #ccc; overflow: hidden; padding-top: 10px;">
		<div style="float: right;">
			<strong>{SP_COLLAPSE_ALL}</strong>
			&nbsp; &nbsp; &nbsp;
			<small><strong>{PHP.L.sp_with_selected}:</strong></small> {SP_WITHSELECTED_SELECTBOX} {SP_WITHSELECTED_BUTTON}
		</div>
	</div>
	<div class="clearfix pagination pagination-right">
		<ul>
			{SP_PAGENAV_PREV}{SP_PAGENAV_MAIN}{SP_PAGENAV_NEXT}
		</ul>
	</div>
	</form>
<!-- END: HAS_SPAM -->
</div>
</div>

<!-- END: MAIN -->
