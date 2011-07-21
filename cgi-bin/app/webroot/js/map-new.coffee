settings =
	MIN_GROUP_WIDTH: 60
	MIN_YEAR_HEIGHT: 13
	SCROLL_BAR_WIDTH: 30
	ANIMATION_SPEED: 400
	ICON_ADJUST: -10
	MIN_START_YEAR: 1950
	resolution_values: [10, 5, 1, .5, .25]
	detached_groups: []
	detached_links: []
	year_height: 0
	startdate: 0
	enddate: 0
	zooms: []

sizeLink = (link) ->
	return false if !link?
	left_first = true
	if $("#group-" + $(link).attr("data-group1")).position().left < $("#group-" + $(link).attr("data-group2")).position().left
		left_group = $ "#group-" + $(link).attr("data-group1")
		right_group = $ "#group-" + $(link).attr("data-group2")
	else
		left_group = $ "#group-" + $(link).attr("data-group2")
		right_group = $ "#group-" + $(link).attr("data-group1")
		left_first = false;
	adjust = if !left_first && $(@).hasClass "spl" then 5 else 0
	$(link).animate {
		left: left_group.position().left + Math.floor(left_group.width() / 2) + adjust
		width: right_group.position().left - left_group.position().left - (if $(link).hasClass "spl" then 5 else 0)
	}, settings.ANIMATION_SPEED

placeGroupDOMOnMap = (group) ->
	attached_groups = $ ".group", "#map_container"
	added = false
	for attached_group in attached_groups
		if parseInt($(attached_group).attr("data-order"), 10) > parseInt($(group).attr("data-order"), 10)
			$(attached_group).before group
			added = true
			break
	if not added
		$(attached_group).after group

spliceItems = (items, arr) ->
	arr.splice item for item in items

sizeLinksOnMap = ->
	setTimeout (->
		$(".link", "#map_container").each ->
			sizeLink(@)),
		settings.ANIMATION_SPEED

zoomGeographic = (zoom) ->
	$(".group:not(.zoom-" + zoom + ")", "div#map_container").each ->
		settings.detached_groups.push $(@)
		$(@).addClass("zoom_inactive").detach()
	$(".link:not(.zoom-" + zoom + ")", "div#map_container").each ->
		$(@).addClass("zoom_inactive").fadeOut settings.ANIMATION_SPEED, ->
			settings.detached_links.push $(@)
			$(@).detach()
	to_splice = []
	for i in [0..settings.detached_links.length]
		link = settings.detached_links[i]
		if $(link).hasClass("zoom-" + zoom)
			$(link).removeClass "zoom_inactive"
			if !$(link).hasClass("settings_inactive")
				$("#map_container").append(link)
				$(link).fadeIn(settings.ANIMATION_SPEED)
				to_splice.push[i]
	spliceItems to_splice, settings.detached_links
	to_splice = []
	for i in [0..settings.detached_groups.length]
		group = $ settings.detached_groups[i]
		if group.hasClass("zoom-" + zoom) and !group.hasClass("settings_inactive") and $("#" + group.attr("id"), "#map_container").length == 0
			placeGroupDOMOnMap group
			group.removeClass("zoom_inactive")
			to_splice.push[i]
	spliceItems to_splice, settings.detached_groups
	fitGroups true, $(".group", "#map_container").length
	sizeLinksOnMap()

getLinkType = (type) ->
	switch type
		when "all" then "Allies"
		when "spl" then "Split"
		when "aff" then "Affiliates"
		when "mer" then "Merge"
		when "riv" then "Rivals"
		else false

addLinkToMap = (link) ->
	left_first = false
	active = "active-both"
	if $('#group-' + link.group1).position().left < $('#group-' + link.group2).position().left
		left_first = true
	if $('#group-' + link.group1).hasClass("active") and $('#group-' + link.group2).hasClass("active")
		active = "active-active"
	else if $('#group-' + link.group1).hasClass("inactive") and $('#group-' + link.group2).hasClass("inactive")
		active = "active-inactive"
	div = $ "<div/>"
		id: "link-" + link.id
		class: "link " + link.type + " group" + link.group1 + " group" + link.group2 + " " + active
		css:
			top: findDateOnTimeline link.date
		"data-group1": link.group1
		"data-group2": link.group2
		"data-date": link.date
		click: (e) ->
			$("<div/>",
				html: processDate(link.date, "e") + ": " + link.description
			).dialog
				modal: false
				title: $('#group-' + link.group1).attr('data-shortname') + ' and ' + $('#group-' + link.group2).attr('data-shortname') + ' ' + getLinkType(link.type)
				width: 300
				"min-height": 100
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
	for i in [0..settings.zooms.length]
		if $("#group-" + link.group1).hasClass("zoom-" + i) and $("#group-" + link.group2).hasClass("zoom-" + i)
			div.addClass "zoom-" + i
	if link.type == "spl"
		div.append($ "<div/>"
			class: "split-icon"
		)
		if left_first
			div.append($ "<div/>"
				class: "split-right"
			)
		else
			div.append($ "<div/>"
				class: "split-left"
			)
		div.width div.width() - 5
	$("#map_container").append div

getTimelineLabel = (date, increment) ->
	if(increment is .5)
		return Math.floor(date) + " " + (if Math.floor(date) is date then "Jan" else "Jun")
	else if increment is .25
		switch (date - Math.floor(date))
			when 0 then month = "Jan"
			when .25 then month = "Apr"
			when .5 then month = "Jul"
			when .75 then month = "Oct"
			else month = false
		return Math.floor(date) + " " + month
	return false

makeTimeline = (startyear, endyear, increment = 1, move_divs = true)	->
	num_ticks = Math.floor (endyear - startyear) / increment + 1
	date_list = $("<ul/>")
	for i in [startyear..endyear] by increment
		if increment > 1
			label = if i > endyear then endyear else i
		else if increment < 1
			label = getTimelineLabel i, increment
		else
			label = i
		date_list.append($("<li/>"
			text: label
			id: "year-" + i
		))
	$("#timeline").empty().append date_list
	$("#wrap").height($(window).height() - $("#header").outerHeight())
	$("#timeline").height $("#wrap").height() - ($("#timeline").outerHeight() - $("#timeline").height())
	settings.year_height = Math.max(Math.floor(($("#timeline").height() / num_ticks) - $("li", "#timeline").first().outerHeight()), settings.MIN_YEAR_HEIGHT)
	$("li", "#timeline").css
		"margin-bottom": settings.year_height
	$("#timeline").height($("ul", "#timeline").height()) if $("#timeline").height() < $("ul", "#timeline").height()
	$("#map_wrapper").height $("#timeline").outerHeight()
	$("#map_container").height($("#timeline").outerHeight() - parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH)
	if move_divs
		fitGroupToTimeline(group, increment) for group in $(".group")
		$(".link","#map_container").each ->
			$(@).animate(
				top: findDateOnTimeline $(@).attr("data-date"), increment
			settings.ANIMATION_SPEED
			)

setUpTimeline = (startyear, endyear) ->
	container = $("<div/>"
		id: "timeline"
	)
	$("#wrap").append container
	makeTimeline(startyear, endyear, 1, false)

setUpMapArea = (groups, links, startdate, enddate) ->
	return false if !groups? or !links?
	wrapper = $("<div/>"
		id: "map_wrapper"
		css:
			width: $(window).width() - $("#timeline").outerWidth() - settings.SCROLL_BAR_WIDTH,
			height: $("#timeline").outerHeight()
	)
	container = $("<div/>"
		id: "map_container"
		css:
			height: $("#timeline").outerHeight() - parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH
	)
	
	addGroupToMap i, groups[i].Profile, startdate, enddate, container for i in [0..groups.length - 1]
	
	$(wrapper).append container
	$("#wrap").append wrapper
	
	fitGroups false, groups.length
	
	$("div.group.active", "#map_container").each ->
		$(@).height($(@).height() + $("li","#timeline").first().outerHeight() + (parseInt($("#timeline").css("padding-bottom"),10) - settings.SCROLL_BAR_WIDTH))
	
	addLinkToMap link.Link for link in links
	$(".link.all, .link.riv", "#map_container").each ->
		$(@).append($ "<div/>"
			"class": "dot left"
		).append($ "<div/>"
			"class": "dot right"
		)

fixGroupNames = ->
	$("span", "#map_container").each(->
		$(@).css "margin-top", -1 * $(@).outerHeight());

fitGroups = (animate, num_groups) ->
	group_width = Math.max(Math.floor(($(window).width() - $('#timeline').outerWidth() - settings.SCROLL_BAR_WIDTH) / num_groups), settings.MIN_GROUP_WIDTH)
	$("#map_container").width num_groups * group_width
	if animate
		$('.group','#map_container').animate({width:group_width}, fixGroupNames)
	else
		$('.group','#map_container').width group_width
		fixGroupNames()

numberToMonth = (m) ->
	switch m
		when 1 then "January"
		when 2 then "February"
		when 3 then "March"
		when 4 then "April"
		when 5 then "May"
		when 6 then "June"
		when 7 then "July"
		when 8 then "August"
		when 9 then "September"
		when 10 then "October"
		when 11 then "November"
		when 12 then "December"
		else false

englishDate = (d) ->
	return false if !d?
	if d[1] == "00" then d[0] else numberToMonth(parseInt(d[1], 10)) + " " + d[0]

processDate = (d, part)	->
	d = d.split "-"
	return false if !(d instanceof Array)
	switch part
		when "y" then parseInt d[0], 10
		when "m" then parseInt d[1], 10
		when "d" then parseInt d[2], 10
		when "e" then englishDate d
		else false

findDateOnTimeline = (date, increment = 1) ->
	year = processDate date, "y"
	if year is 0 then year = settings.enddate
	month = processDate date, "m"
	if month is 0 then month = 1
	if increment is 1
		closest_year = year
	else if increment < 1
		closest_year = Math.floor year
	else
		closest_year = settings.startdate + Math.floor((year - settings.startdate) / increment) * increment
	$("#year-" + closest_year).position().top + ((year - settings.startdate) % increment) / increment * ($("li", "#timeline").first().outerHeight() + settings.year_height) + Math.floor(month * (($("li", "#timeline").first().outerHeight() + settings.year_height) / 12)) / increment

fitGroupToTimeline = (div, increment, animate = true) ->
	top = findDateOnTimeline($(div).attr("data-startdate"), increment)
	$(div).animate(
		"margin-top": top
		height: findDateOnTimeline($(div).attr("data-enddate"), increment) - top + (if $(div).hasClass("active") then $("li", "#timeline").first().outerHeight() + (parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH) else 0)
	settings.ANIMATION_SPEED)
	$(div).children("div").children(".attack, .leader").each ->
		$(@).animate(
			top: findDateOnTimeline($(@).attr("data-date"), increment) - top + settings.ICON_ADJUST
		settings.ANIMATION_SPEED
		)

addLeaderToGroup = (div, leader, top) ->
	end = false
	start = false
	if processDate(leader.startdate, "y") isnt 0 and leader.startdate isnt "?" and leader.startdate.toLowerCase() isnt "unknown"
		date = leader.startdate
		start = true
		if leader.enddate isnt "?" and leader.enddate.toLowerCase() isnt "unknown"
		#if processDate(leader.enddate, "y") isnt 0
			end = true
	else if processDate(leader.enddate, "y") isnt 0
		date = leader.enddate
		end = true
	else
		return false
	if date is "?" or date.toLowerCase() is "unknown"
		return false
	if start
		html = "<p>Assumed leadership " + processDate(leader.startdate, "e") + ".</p>"
		html += "<p>" + (if leader.enddate.toLowerCase() isnt "unknown" and leader.enddate isnt "?" then processDate(leader.enddate, "e") + ": " else "") + leader.status + "</p>"
	else
		html = "<p>" + processDate(leader.enddate) + ": " + leader.status + "</p>"
	$(div).append($ "<div/>"
		class: "leader"
		css:
			top: Math.max 0, findDateOnTimeline(date) - top - settings.ICON_ADJUST
		"data-date": date
		click: (e) ->
			$("<div/>"
				html: html
			).dialog
				title: "Leadership Change: " + leader.name
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
	)

addAttackToGroup = (div, attack, top) ->
	$(div).append($ "<div/>"
		class: "attack"
		"data-date": attack.date
		css:
			top: findDateOnTimeline(attack.date) - top - settings.ICON_ADJUST
		click: (e) ->
			$("<div/>"
				html: "<p><b>" + processDate(attack.date, "e") + ":</b> " + attack.description + (if attack.casualties? then " (" + attack.casualties + ")" else "") + ".</p>"
			).dialog
				title: "Major Attack"
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
	)

addGroupToMap = (order, group, startdate, enddate, container) ->
	return false if !group? or !container?
	start_year = if processDate(group.startdate, "y") >= startdate then group.startdate else startdate + "-00-00"
	top = findDateOnTimeline(start_year)
	div = $("<div/>"
		class: "group " + if processDate(group.enddate,'y') == 0 then "active" else "inactive"
		id: "group-" + group.id
		css:
			"margin-top": top
			height: findDateOnTimeline(group.enddate) - top
		"data-name": group.name
		"data-shortname": group.shortname
		"data-order": order
		"data-startdate": start_year
		"data-enddate": group.enddate
		html: $ "<div/>"
			class: "group_timeline"
	)
	addAttackToGroup div.children("div"), attack, top for attack in group.majorattacks
	addLeaderToGroup div.children("div"), leader, top for leader in group.leadership
	div.addClass("zoom-" + i) for i in [parseInt(group.min_zoom, 10)..parseInt(group.max_zoom, 10)]
	div.prepend $("<span/>"
		text: group.shortname
		mouseenter: ->
			$(@).text(group.name).css "margin-top", -1 * $(@).outerHeight()
		mouseleave: ->
			$(@).text(group.shortname).css 'margin-top', -1 * $(@).outerHeight()
		click: ->
			$("<div/>"
				html: group.description
			).dialog
				title: group.name
				modal: true
				resizable: false
				draggable: false
				width: 400
				buttons:
					"See Full Profile": ->
						window.open("/group/mappingmilitants/cgi-bin/profiles/view/" + group.id)
					"Trace Group": ->
						$(@).dialog("destroy").remove()
					"Close": ->
						$(@).dialog("destroy").remove()
	)
	container.append div

progressBar = ->
	$("<div/>"
		id: "progress_dialog"
		html: '<p>Please wait. The map is loading.</p><p>&nbsp;</p><div id="progress_bar"></div>'
		css:
			height: '10px'
	).dialog
		modal: true
		draggable: false
		resizable: false
		width: 400
		closeOnEscape: false
		open: (event, ui) ->
			$(".ui-dialog-titlebar-close").hide()
	$("#progress_bar").progressbar
		value: 20
	updateProgressBar = ->
		$("#progress_bar").progressbar("value", $("#progress_bar").progressbar("value") + 10)
	setTimeout updateProgressBar, 500 * (i + 1) for i in [1..4]

setUpControls = (zooms) ->
	return false if !zooms?
	resolutions = ["Decade", "5 Years", "Year", "6 Months", "Quarter"]
	$("input.button").button()
	$("#settings_button").click ->
		$("#settings_dialog").dialog
			modal: true
			width: 600
			draggable: false
			resizable: true
			title: "Map Settings"
	$("input[name=select_organizations]").change ->
		to_splice = []
		links_to_splice = []
		if $("input[name=select_organizations]:checked").val() is "both"
			for i in [0..settings.detached_groups.length]
				group = $ settings.detached_groups[i]
				if !group.hasClass("zoom_inactive")
					placeGroupDOMOnMap group
					group.removeClass("settings_inactive")
					to_splice.push i
			for i in [0..settings.detached_links.length]
				link = $ settings.detached_links[i]
				if !link.hasClass("zoom_inactive") and !link.hasClass("settings_inactive")
					link.removeClass("group_activity_inactive")
					$("#map_container").append(link)
					$(link).fadeIn settings.ANIMATION_SPEED
					links_to_splice.push(i)
		else
			$(".group:not(." + $("input[name=select_organizations]:checked").val() + ")", "div#map_container").each ->
				settings.detached_groups.push $(@)
				$(@).addClass("settings_inactive").detach()
			for i in [0..settings.detached_groups.length]
				group = $ settings.detached_groups[i]
				if !group.hasClass("zoom_inactive") and group.hasClass($("input[name=select_organizations]:checked").val())
					placeGroupDOMOnMap group
					group.removeClass("settings_inactive")
					to_splice.push i
			$(".link", "#map_container").each ->
				if !$(@).hasClass("active-" + $("input[name=select_organizations]:checked").val())
					$(@).addClass("group_activity_inactive").fadeOut settings.ANIMATION_SPEED, ->
						$(@).detach()
					settings.detached_links.push $(@)
			for i in [0..settings.detached_links.length]
				link = $ settings.detached_links[i]
				if !link.hasClass("zoom_inactive") and !link.hasClass("settings_inactive") and link.hasClass("active-" + $("input[name=select_organizations]:checked").val())
					link.removeClass("group_activity_inactive")
					$("#map_container").append(link)
					$(link).fadeIn settings.ANIMATION_SPEED
					links_to_splice.push(i)
		spliceItems to_splice, settings.detached_groups
		spliceItems links_to_splice, settings.detached_links
		fitGroups true, $(".group", "#map_container").length
		sizeLinksOnMap()
	$(".toggle_checkbox").prop("checked", true).change ->
		if $(@).is(":checked")
			$("." + $(@).attr("data-class"), "#map_container").removeClass("settings_inactive")
			$("." + $(@).attr("data-class") + ":not(.zoom_inactive)", "#map_container").removeClass("settings_inactive").fadeIn settings.ANIMATION_SPEED
		else
			$("." + $(@).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut settings.ANIMATION_SPEED
	$(".link_toggle_checkbox").prop("checked", true).change ->
		linktype = $(@).attr("data-class")
		if $(@).is(":checked")
			$("." + linktype, "#map_container").removeClass("settings_inactive")
			$("." + linktype + ":not(.zoom_inactive)", "#map_container").removeClass("settings_inactive").fadeIn settings.ANIMATION_SPEED
			to_splice = []
			for i in [0..settings.detached_links.length]
				link = settings.detached_links[i]
				if $(link).hasClass(linktype)
					$(link).removeClass("settings_inactive")
					if $(link).hasClass("zoom-" + $("#geo_zoom_slider").slider("value"))
						$("#map_container").append $(link)
						$(link).fadeIn settings.ANIMATION_SPEED
						sizeLink link
						to_splice.push i
			spliceItems to_splice, settings.detached_links
		else
			for link in settings.detached_links
				if $(link).hasClass(linktype)
					$(link).addClass("settings_inactive")
			$("." + linktype, "#map_container").addClass("settings_inactive").fadeOut settings.ANIMATION_SPEED, ->
				$(@).detach()
				settings.detached_links.push $(@)
	$("#geo_zoom_slider").slider
		value: 0
		min: 0
		max: zooms.length - 1
		slide: (e, ui) -> 
			$("#geo_zoom_label").text "Geo Zoom: " + zooms[ui.value]
			zoomGeographic ui.value
	$("#geo_zoom_label").text "Geo Zoom: " + zooms[0]
	$("#time_zoom_slider").slider(
		value: 2
		min: 0
		max: 4
		slide: (e, ui) ->
			$("#time_zoom_label").text "Timeline Resolution: " + resolutions[ui.value]
			makeTimeline(settings.startdate, settings.enddate, settings.resolution_values[ui.value])
	).slider "value", 2
	$("#time_zoom_label").text "Timeline Resolution: Year"
	n = new Date()
	$("#timeline_selector").slider
		range: true
		min: settings.MIN_START_YEAR
		max: n.getFullYear()
		values: [settings.startdate, settings.enddate]
		slide: (event, ui) ->
			$("#timeline_header").text("Timeline: " + ui.values[0] + "-" + ui.values[1])
			settings.startdate = ui.values[0]
			settings.enddate = ui.values[1]
			makeTimeline settings.startdate, settings.enddate, settings.resolution_values[$("#time_zoom_slider").slider("value")]
	$("#timeline_header").text("Timeline: " + settings.startdate + "-" + n.getFullYear())

$ ->
	$("body").height $(window).height();
	progressBar()
	setUpControls()
	$.getJSON(
		"/group/mappingmilitants/cgi-bin/maps/jsondata/3"
		(data) ->
			$("#progress_bar").progressbar "value", 70
			settings.startdate = parseInt data.Map.startyear, 10
			settings.enddate = parseInt data.Map.endyear, 10
			settings.zooms = data.Map.zooms
			setUpControls settings.zooms
			$("#progress_bar").progressbar "value", 80
			setUpTimeline settings.startdate, settings.enddate
			$("#progress_bar").progressbar "value", 90
			setUpMapArea data.groups, data.links, settings.startdate, settings.enddate
			$(".toggle_checkbox.start_unchecked").prop("checked", false).each ->
				$("." + $(@).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut(settings.ANIMATION_SPEED)
			$("#progress_bar").progressbar "value", 100
			$('#progress_dialog').delay(200).dialog 'destroy'
			zoomGeographic 0
	)