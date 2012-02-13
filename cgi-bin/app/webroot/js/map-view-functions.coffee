linkClickFunction = (link) ->
	(e) ->
		opener = @
		if $(opener).data("dialog_open")
			return false;
		$(opener).data("dialog_open", true)
		$("<div/>"
			html: processDate(link.date, "e") + ": " + link.description
		).dialog
			modal: false
			title: $("#group-" + link.group1).attr("data-shortname") + " and " + $("#group-" + link.group2).attr("data-shortname") + " " + getLinkType(link.type)
			width: 300
			"min-height": 100
			position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
			beforeClose:  ->
				$(opener).data "dialog_open", false

groupClickFunction = (group, buttons) ->
	(e) ->
		$("<div/>"
			html: group.description
		).dialog
			title: group.name
			modal: true
			resizable: false
			draggable: false
			width: 400
			buttons: buttons