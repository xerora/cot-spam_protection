<!-- BEGIN: MAIN -->
	<div>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
	</div>
	<!-- BEGIN: HAS_MESSAGES -->
		<!--BEGIN: MESSAGE_ROW -->
			<div>
				<strong>{MSG_AUTHOR}</strong>
			</div>
			<p>{MSG_TEXT}</p> <br />
		<!-- END: MESSAGE_ROW -->
	<!-- END: HAS_MESSAGES -->

	<!-- BEGIN: NO_MESSAGES -->
		<p>
			No messages to display at this time.
		</p>
	<!-- END: NO_MESSAGES -->
	<div>
		<form method="post" action="{FORM_ACTION}">
			{FORM_MESSAGE_INPUT}<br /><br />
			{FORM_SEND_BUTTON}
		</form>
	</div>
<!-- END: MAIN -->