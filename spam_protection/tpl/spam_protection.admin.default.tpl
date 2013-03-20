<!-- BEGIN: MAIN -->

<div>

<h2 style="margin-bottom: 0px;">{PHP.L.sp_title}</h2>
<br />

<div class="block" style="margin-bottom: 20px;">

	<div>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	</div>

	<p style="padding-bottom: 7px;">
		<strong><a href="{SP_VALIDATE_KEY_URL}" style="text-decoration: underline;">{PHP.L.sp_action_check_key}</a></strong>
	</p>

	<!-- BEGIN: VALIDATE_API_KEY -->
		<!-- BEGIN: VALID -->
			<div class="done" style="margin-top: 0px;">
				{PHP.L.sp_info_key_valid}
			</div>
		<!-- END: VALID -->
		<!-- BEGIN: INVALID -->
			<div class="error" style="margin-top: 0px;">
				{PHP.L.sp_info_key_invalid}
			</div>
		<!-- END: INVALID -->		
	<!--END: VALIDATE_API_KEY -->
<!-- BEGIN: NO_SPAM -->	
	<div class="warning">
		{PHP.L.sp_empty_queue}
	</div>
<!-- END: NO_SPAM -->
<!-- BEGIN: HAS_SPAM -->
	<form method="post" action="{SP_FORM_ACTION}" id="sp_form">
	<div class="button-toolbar sp-sections">
		<div>
			{SP_SECTIONS} 
		</div>
		<div class="sp-page-nav">
			<!-- IF {PHP.pagetotal} != 0 -->
			<strong>{PHP.L.Pages}:</strong> {SP_PAGENAV_MAIN} &nbsp;
			<!-- ENDIF -->
			&nbsp;
		</div>
	</div>

	<div>
		<h2 style="text-decoration: none; border-bottom: 0px;">{SP_SECTION_NAME}</h2>
	</div>

	<div class="button-toolbar sp-items-queue">
		<div style="float: left;">
			&nbsp; &nbsp; &nbsp;
			<strong>{PHP.L.sp_items_onpage}:</strong> {SP_PAGENAV_ONPAGE}
			&nbsp; &nbsp; &nbsp;
		</div>
		<div style="float: right;">
			<strong>{SP_COLLAPSE_ALL}</strong>
			&nbsp; &nbsp; &nbsp;
			<strong>{PHP.L.sp_with_selected}:</strong> {SP_WITHSELECTED_SELECTBOX} {SP_WITHSELECTED_BUTTON}
		</div>
	</div>
	<table class="cells sp-table" style="width: 100%;">
		<tr>
			<td class="coltop width5">{SP_CHECKALL}</td> 
			<td class="coltop width10">{PHP.L.Username}</td>
			<td class="coltop width40">{PHP.L.sp_content_summary} ( {PHP.L.sp_without_markup} )</td>
			<td class="coltop width10">{PHP.L.Date} &nbsp; {SP_ITEM_ORDER_DATE_ASC_ICON} {SP_ITEM_ORDER_DATE_DESC_ICON}</td>
			<td class="coltop width10">{PHP.L.sp_actions}</td>
		</tr>
	<!-- BEGIN: SPAM_ITEMS -->
		<tr class="sp_items">
			<td class="centerall sp-pad">{SP_ITEM_CHECKBOX}</td>
			<td class="sp-pad" style="text-overflow: ellipsis;">{SP_ITEM_USERNAME}&nbsp;</td>
			<td style="text-overflow: ellipsis;">{SP_ITEM_SUMMARY}</td>
			<td style="text-align: center;">{SP_ITEM_DATE}</td>
			<td class="sp_actions" style="text-align:center;">
				{SP_ACTION_ICON_VIEW} &nbsp;&nbsp; &nbsp; {SP_ACTION_ICON_MARKAS_SPAM} &nbsp;&nbsp; &nbsp; {SP_ACTION_ICON_MARKAS_HAM}
			</td>
		</tr>
		<!-- BEGIN: VIEW_ITEM -->
			<tr id="item_{SP_ITEM_VIEW_ID}" class="view_item">
				<td colspan="5" style="padding: 15px; background-color: #f0f0f0;">
				<table class="sp-view-item-table sp-table" cellpadding="0" cellspacing="0">
					<!-- BEGIN: ITEM_DATA -->
						<tr>
							<td class="width20" style="border: 1px solid #b8b8b8; background-color: #fff; text-align: left;">{SP_ITEM_VIEW_DATA_NAME}</td>
							<td colspan="4" style="border: 1px solid #b8b8b8; background-color: #fff; overflow: auto;" class="width80">{SP_ITEM_VIEW_DATA_VALUE}</td>
						</tr>
					<!-- END: ITEM_DATA -->
					<tr class="sp_view_mark" style="display: none;">
						<td colspan="5" style="border: 1px solid #b8b8b8; background-color: #fff;text-align: right;">{SP_ITEM_MARK_LINK} &nbsp; </td>
					</tr>
				</table>
				</td>
			</tr>
		<!-- END: VIEW_ITEM -->
	<!-- END: SPAM_ITEMS -->
	</table>
	<div class="button-toolbar sp-items-queue" style="border-top: 0px; border-bottom: 1px solid #ccc;">
		<div style="float: left;">
			&nbsp; &nbsp; &nbsp;
			<strong>{PHP.L.sp_items_onpage}:</strong> {SP_PAGENAV_ONPAGE}
			&nbsp; &nbsp; &nbsp;
		</div>
		<div style="float: right;">
			<strong>{SP_COLLAPSE_ALL}</strong>
			&nbsp; &nbsp; &nbsp;
			<strong>{PHP.L.sp_with_selected}:</strong> {SP_WITHSELECTED_SELECTBOX} {SP_WITHSELECTED_BUTTON}
		</div>
	</div>
	</form>
<!-- END: HAS_SPAM -->
</div>
</div>

<!-- END: MAIN -->
