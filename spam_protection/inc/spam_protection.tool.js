
window.hidden_views = [];
window.open_views = [];

function sp_get_item_id(uri)
{
	var item = uri.match(/\#[0-9]+/i);
	return item.toString().substr(1);
}

function sp_toggle_mark_link(show, item)
{
	switch(show)
	{
		case 'mark':
			$('#sp_link_unmark_item_'+item).hide();
			$('#sp_link_mark_item_'+item).show();
		break;
		case 'unmark':
			$('#sp_link_mark_item_'+item).hide();
			$('#sp_link_unmark_item_'+item).show();
		break;
	}
}

function sp_attach_bindings() {
	$(".sp_view_mark").show();
	$(".sp_link_mark").on('click', function(e){
		e.preventDefault();
		var item = sp_get_item_id($(this).attr('href'));
		$("#"+item).attr('checked', true);
		sp_toggle_mark_link('unmark', item);
	});
	$(".sp_link_unmark").on('click', function(e){
		e.preventDefault();
		var item = sp_get_item_id($(this).attr('href'));
		$("#"+item).attr('checked', false);
		sp_toggle_mark_link('mark', item);
	});
}

$(function() {
	
	$(".sp_withselected_button").hide();
	$(".sp_collapse_all").show();
	$("#sp_checkall").show();
	$(".sp_items").css('cursor', 'pointer');
	$(".sp_actions").css('cursor', 'default');
	$(".sp_collapse_all").on('click', function(e){
		e.preventDefault();
		$(".view_item").remove();
		window.open_views = [];
	});
	
	$("#sp_checkall").on('click', function()
	{
		if($(this).is(':checked'))
		{
			$(".sp_item").attr('checked', true);
			$(".sp_link_mark").hide();
			$(".sp_link_unmark").show();
		}
		else
		{
			$(".sp_item").attr('checked', false);
			$(".sp_link_unmark").hide();		
			$(".sp_link_mark").show();
		}
	});

	$(".sp_items").on('click', function(e)
	{
		var current_attr = $(".sp_item", this);
		var item_id = current_attr.val();
		if(current_attr.is(':checked') && !$(e.target).hasClass('sp_actions') &&
			!$(e.target).hasClass('sp_view_item') && $(e.target).attr('class')!='sp_item' &&
			$(e.target).attr('class')!='sp_item_mark_spam' && $(e.target).attr('class')!='sp_item_mark_ham')
		{
			sp_toggle_mark_link('mark', item_id);
			current_attr.attr('checked', false);
		}
		else if(current_attr.is(':checked')===false && $(e.target).attr('class')!='sp_actions' &&
			!$(e.target).hasClass('sp_view_item') && !$(e.target).hasClass('sp_item') &&
			$(e.target).attr('class')!='sp_item_mark_spam' && $(e.target).attr('class')!='sp_item_mark_ham')
		{
			sp_toggle_mark_link('unmark', item_id);
			current_attr.attr('checked', true);
		}
	});
	$("#sp_view_selected").on('click', function() {
		$(this).remove();
	});
	$("#sp_view_selected").on('mouseover', function() {
		$(this).css('background-color', 'white');
	});
	$(".sp_view").on('click', function(e){
		e.preventDefault();
		var destination = $(this).attr('href');
		var item = sp_get_item_id($(this).attr('href'));
		var loaded = [];

		if(!window.open_views[item] && $.inArray(item, window.hidden_views)=='-1')
		{
			window.open_views[item] = true;
			$.ajax({
				type: 'GET',
				context: this,
				url: destination,
				beforeSend: function()
				{
					if($.inArray(item, window.open_views)=='-1')
					{
						$(this).parent().parent().after('<tr class="sp_loading"><td colspan="5" style="text-align: center"><img src="./plugins/spam_protection/img/loader.gif" /> Loading...</td></tr>');
					}
				},

				success: function(data) {
					var new_data = $("#item_"+item, data).html();
					$(this, "#view_item_"+item).parent().parent().after('<tr id="view_item_'+item+'" class="view_item">'+new_data+'</tr>');

				},
				complete: function()
				{
					sp_attach_bindings();
					var checked = $("#"+item).attr('checked');
					if(checked == 'checked')
					{
						sp_toggle_mark_link('unmark', item);
					}
					$(".sp_loading").remove();
				}
			});
		}
		else if(typeof window.open_views[item] !== 'undefined' && typeof window.hidden_views[item] !== 'undefined')
		{
			delete window.hidden_views[item];
			$("#view_item_"+item).show();
		}
		else
		{
			window.hidden_views[item] = true;
			$("#view_item_"+item).hide();
		}
	});
	$(".sp_withselected").on('change', function() {
		$('#sp_form').submit();
		$(".sp_withselected").val('');
	});
});
