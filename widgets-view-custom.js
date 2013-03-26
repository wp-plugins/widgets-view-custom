jQuery(document).ready(function($) {

	var $Form = $("#widget_view_custom_form");

	// sortable
	$("#use, #not_use").sortable({
		placeholder: "widget-placeholder",
		connectWith: ".widget-list",
		stop: function(event, ui) {
			var Before = event.target.id;
			var After = ui.item.parent().attr("id");
			
			if(Before != After) {
				ui.item.children(".widget-inside").children("input").each(function() {
					var ItemName = $(this).attr("name");
					$(this).attr("name", ItemName.replace(Before, After));
				});
			}
		}
	}).disableSelection();

});
