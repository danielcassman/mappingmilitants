# Global settings variable
settings =
	MIN_GROUP_WIDTH: 60		# Minimum width in pixels for a group div
	MIN_YEAR_HEIGHT: 13		# Minimum height in pixels for a timeline increment
	SCROLL_BAR_WIDTH: 30	# Space to leave at screen edges for scroll bars
	ANIMATION_SPEED: 400	# Speed in miliseconds for animations
	ICON_ADJUST: -10		# Vertical offset in pixels for attack and leader icons
	MIN_START_YEAR: 1950	# Minimum year that will appear on the timeline slider in the settings dialog
	# DO NOT CHANGE BELOW THIS LINE
	resolution_values: [10, 5, 1, .5, .25]
	year_height: 0
	startdate: 0
	enddate: 0
	zooms: []
	map_id: 0

###
 * Function: sizeLink
 * ------------------
 * Animates a link into place horizontally so it connects the appropriate groups.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 ###
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

###
 * Function: fitUmbrella
 * ---------------------
 * Fits an umbrella in the map area, both vertically and horizontally.
 *
 * @param div: the div object for the umbrella.
 * @param increment: the timeline increment (in years)
 ###
fitUmbrella = (div, increment) ->
	if processDate($(div).attr("data-startdate"), "y") > settings.enddate or ($(div).attr("data-enddate") isnt "0000-00-00" and processDate($(div).attr("data-enddate"), "y") < settings.startdate)
		$(div).addClass "inactive"
		return false
	groups = $(div).attr("data-groups").split ","
	left = Math.pow(2, 53)
	right = 0
	groups_on_map = 0
	for i in [0...groups.length]
		if !groups[i]? or $("#group-" + groups[i]).css("display") is "none"
			continue
		groups_on_map += 1
		left = Math.min left, $("#group-" + groups[i]).position().left
		right = Math.max right, $("#group-" + groups[i]).position().left
	if groups_on_map < 2
		$(div).addClass "inactive"
		return false
	$(div).removeClass "inactive"
	top = findDateOnTimeline $(div).attr("data-startdate"), increment
	$(div).css
		left: left + parseInt($("div.group","#map_container").width() / 2, 10)
		width: right - left
		top: top
		height: findDateOnTimeline($(div).attr("data-enddate"), increment) - top
	$(div).children("span").css
		top: parseInt(($(div).height() / 2), 10) - parseInt($(div).children("span").height() / 2, 10)

###
 * Function: sizeLinksOnMap
 * ------------------------
 * Animates the visible links on the map into place so they connect the
 * appropriate groups AFTER a timeout equivalent to the animation speed to
 * allow for the group divs to be animated into place.
 ###
sizeLinksOnMap = ->
	if $(".group", "#map_container").is(":animated")
		setTimeout sizeLinksOnMap, settings.ANIMATION_SPEED
	else
		$(".link", "#map_container").each ->
			if($(@).css("display") isnt "none")
				sizeLink(@)
		$("div.umbrella", "#map_container").each ->
			fitUmbrella $(@), settings.resolution_values[$("#time_zoom_slider").slider("value")]
			return true

###
 * Function: zoomGeographic
 * ------------------------
 * Zooms the map to the given geographic zoom level.
 *
 * @param zoom: the desired zoom level [INTEGER]
 ###
zoomGeographic = (zoom) ->
	$(".group:not(.zoom-" + zoom + ")", "div#map_container").addClass "zoom_inactive"
	$(".link:not(.zoom-" + zoom + ")", "div#map_container").addClass "zoom_inactive"
	$("div.link.zoom-" + zoom, "#map_container").removeClass "zoom_inactive"
	$("div.group.zoom-" + zoom, "#map_container").removeClass "zoom_inactive"
	fitGroups true, countVisibleGroups()
	sizeLinksOnMap()

###
 * Function: getLinkType
 * ---------------------
 * Gets a full description of a link type for an abbreviated link type.
 *
 * @param type: the abbreviated link type (i.e. "all" for allies)
 * @return: the full name of the link typ (i.e. "Allies")
 ###
getLinkType = (type) ->
	switch type
		when "all" then "Allies"
		when "spl" then "Split"
		when "aff" then "Affiliates"
		when "mer" then "Merge"
		when "riv" then "Rivals"
		else false

###
 * Function: addLinkToMap
 * ----------------------
 * Creates and adds a fully functional DOM object for a link.
 *
 * @param link: the link JSON object
 ###
addLinkToMap = (link) ->
	left_first = false
	active = "active-both"
	if $("#group-" + link.group1).position().left < $("#group-" + link.group2).position().left
		left_first = true
	if $("#group-" + link.group1).hasClass("active") and $("#group-" + link.group2).hasClass("active")
		active = "active-active"
	else if $("#group-" + link.group1).hasClass("inactive") and $("#group-" + link.group2).hasClass("inactive")
		active = "active-inactive"
	div = $("<div/>"
		id: "link-" + link.id
		class: "link " + link.type + " group" + link.group1 + " group" + link.group2 + " " + active
		css:
			top: findDateOnTimeline link.date
		"data-group1": link.group1
		"data-group2": link.group2
		"data-date": link.date
		click: linkClickFunction link
	).data("dialog_open", false)
	for i in [0...settings.zooms.length]
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

###
 * Function: getTimelineLabel
 * --------------------------
 * Creates a human readable label for the given date.
 *
 * @param date: the date (in years), so 1999.5 is June 1995, etc.
 * @param increment: the timeline increment (in years), so .25 is quarters
 * @return: a label of the form "Jan 2000"
 ###
getTimelineLabel = (date, increment) ->
	if(increment is .5)
		return (if Math.floor(date) is date then Math.floor(date) + " Jan" else "Jun")
	else if increment is .25
		switch (date - Math.floor(date))
			when 0 then return Math.floor(date) + " Jan"
			when .25 then return "Apr"
			when .5 then return "Jul"
			when .75 then return "Oct"
			else return false
	return false

###
 * Function: makeTimeLine
 * ----------------------
 * Makes a timeline by adding the proper increments to the timeline div.
 *
 * @param startyear: the map start year
 * @param endyear: the map end year
 * @param increment: the timeline increment in years (default = 1)
 * @param move_divs: boolean indicating whether the groups and links need
 *   to be moved into place.
 ###
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
	$("#timeline").height $(window).height() - $("#header").outerHeight() - ($("#timeline").outerHeight() - $("#timeline").height()) - settings.SCROLL_BAR_WIDTH
	settings.year_height = Math.max(Math.floor(($("#timeline").height() / num_ticks) - $("li", "#timeline").first().outerHeight()), settings.MIN_YEAR_HEIGHT)
	$("li", "#timeline").css
		"margin-bottom": settings.year_height
	$("#timeline").height($("ul", "#timeline").height()) if $("#timeline").height() < $("ul", "#timeline").height()
	if move_divs
		fitGroupToTimeline(group, increment, startyear, endyear) for group in $(".group", "#map_container")
		fitGroups false, countVisibleGroups()
		$(".link","#map_container").each ->
			if processDate($(@).data('date') ,'y') < startyear or processDate($(@).data('date') ,'y') > endyear
				$(@).addClass "timeline_inactive"
			else
				$(@).removeClass "timeline_inactive"
			$(@).animate(
				top: findDateOnTimeline $(@).attr("data-date"), increment
			settings.ANIMATION_SPEED
			)
		sizeLinksOnMap()

###
 * Function: setUpTimeline
 * -----------------------
 * Initializes the timeline by creating and adding the timeline div and adding
 * year increments.
 *
 * @param startyear: the map start year
 * @param endyear: the map end year
 ###
setUpTimeline = (startyear, endyear) ->
	container = $("<div/>"
		id: "timeline"
		css:
			top: $("#header").outerHeight()
	)
	$("body").append container
	$(document).scroll ->
		$("#timeline").css("left", -1 * $("html").offset().left);
	makeTimeline(startyear, endyear, 1, false)

###
 * Function: addUmbrellaToMap
 * --------------------------
 * Creates and adds an umbrella to the map.
 *
 * @param umbrella: the JSON object for the umbrella to add.
 ###
addUmbrellaToMap = (umbrella) ->
	div = $("<div/>"
		class: "umbrella"
		html: $("<span/>"
			text: umbrella.shortname
		)
		"data-groups": umbrella.groups.join(",")
		"data-startdate": umbrella.startdate
		"data-enddate": umbrella.enddate
		click: (e) ->
			if $(@).data "dialog_open"
				return false
			$(@).data "dialog_open", true
			opener = @
			$("<div/>"
				html: umbrella.description
			).dialog
				title: umbrella.name
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
				beforeClose: ->
					$(opener).data "dialog_open", false
	).data "dialog_open", false
	$("#map_container").append div

###
 * Function: setUpMapArea
 * ----------------------
 * Initializes the map area.
 *
 * @param groups: the groups array from the JSON object
 * @param links: the links array from the JSON object
 * @param startdate: the map's initial start date
 * @param enddate: the map's initial end date
 ###
setUpMapArea = (groups, links, umbrellas, startdate, enddate) ->
	return false if !groups? or !links?
	
	container = $("<div/>"
		id: "map_container"
		css:
			left: $("#timeline").outerWidth()
			top: $("#header").outerHeight()
	)
	
	addGroupToMap i, groups[i], startdate, enddate, container for i in [0...groups.length]
	
	$("body").append container
	
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
	
	addUmbrellaToMap umbrella for umbrella in umbrellas

###
 * Function: fixGroupNames
 * -----------------------
 * Properly aligns the spans in the group divs so that they are even with the
 * top of the group timeline.
 ###
fixGroupNames = ->
	$("span", ".group").each(->
		$(@).css "margin-top", -1 * $(@).outerHeight());

###
 * Function: fitGroups
 * -------------------
 * Fits the groups in the map area into the screen.
 *
 * @param animate: boolean indicating whether the groups should be animated
 *   into place.
 * @param num_groups: the number of visible groups. Usually can be retrieved
 *   through countVisibleGroups.
 ###
fitGroups = (animate, num_groups) ->
	group_width = Math.max(Math.floor(($(window).width() - $("#timeline").outerWidth() - settings.SCROLL_BAR_WIDTH) / num_groups), settings.MIN_GROUP_WIDTH)
	$("#map_container").width num_groups * group_width + settings.SCROLL_BAR_WIDTH
	if animate
		$(".group","#map_container").animate({width:group_width}, fixGroupNames)
	else
		$(".group","#map_container").width group_width
		fixGroupNames()

###
 * Function: numberToMonth
 * -----------------------
 * Converts a number to an English month.
 *
 * @param m: the number of the month (1-12)
 * @return: the English month (i.e. "January")
 ###
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

###
 * Function: englishDate
 * ---------------------
 * Returns a string containing a human readable version of the given date.
 *
 * @param d: an array containing the date as [year, month, date]
 * @return: a string; either just the year or "January 2000"
 ###
englishDate = (d) ->
	return false if !d?
	if d[0] is '0000' then return 'Unknown'
	if d[0] is '0001' then return 'Current'
	if d[1] == "00" then d[0] else numberToMonth(parseInt(d[1], 10)) + " " + d[0]

###
 * Function: processDate
 * ---------------------
 * Processes a date string into a variety of formats.
 *
 * @param d: the date to process in the format yyyy-mm-dd
 * @param return: the part of the date to return [STRING]
 *   if y: the four digit year
 *   if m: the month as a parsed integer
 *   if d: the date as a parsed integer
 *   if e: the English date as "January 2000"
 ###
processDate = (d, part)	->
	d = d.split "-"
	return false if !(d instanceof Array)
	switch part
		when "y" then parseInt d[0], 10
		when "m" then parseInt d[1], 10
		when "d" then parseInt d[2], 10
		when "e" then englishDate d
		else false

###
 * Function: findDateOnTimeline
 * ----------------------------
 * Finds the location (in pixels from the top of the map area) of a date on the
 * timeline. Checks for dates outside the timeline and returns the start date
 * or end date if appropriate.
 *
 * @param date: the date in the format yyyy-mm-dd
 * @param increment: the timeline increment (in years)
 * @return: the number of pixels from the top of the map area closest to the
 *   given date.
 ###
findDateOnTimeline = (date, increment = 1) ->
	year = processDate date, "y"
	month = processDate date, "m"
	if month is 0
		month = 1
	if year is 0
		year = settings.enddate
	if year > settings.enddate then year = settings.enddate
	if year < settings.startdate then year = settings.startdate
	if increment is 1
		closest_year = year
	else if increment < 1
		closest_year = Math.floor year
	else
		closest_year = settings.startdate + Math.floor((year - settings.startdate) / increment) * increment
	$("#year-" + closest_year).position().top + ((year - settings.startdate) % increment) / increment * ($("li", "#timeline").first().outerHeight() + settings.year_height) + Math.floor(month * (($("li", "#timeline").first().outerHeight() + settings.year_height) / 12)) / increment

###
 * Function: fitGroupToTimeline
 * ----------------------------
 * Fits a group vertically into the timeline.
 *
 * @param div: the group div
 * @param increment: the timeline increment (in years)
 * @param startyear: the start year of the timeline
 * @param endyear: the end year of the timeline
 * @param animate: boolean indicating whether to animate the group into place
 ###
fitGroupToTimeline = (div, increment, startyear, endyear, animate = true) ->
	if (processDate($(div).attr("data-enddate"), "y") < startyear and processDate($(div).attr("data-enddate"), "y") isnt 0) or processDate($(div).attr("data-startdate"), "y") > endyear
		$(div).addClass "timeline_inactive"
		$("div.link.group" + $(div).attr("id").substring(6)).addClass "timeline_inactive"
		return false
	$(div).removeClass "timeline_inactive"
	$("div.link.group" + $(div).attr("id").substring(6)).removeClass "timeline_inactive"
	if processDate($(div).attr("data-startdate"), "y") >= startyear
		top = findDateOnTimeline($(div).attr("data-startdate"), increment)
	else
		top = $("#year-" + startyear).position().top
	$(div).animate(
		"margin-top": top
		height: findDateOnTimeline($(div).attr("data-enddate"), increment) - top + (if $(div).hasClass("active") then $("li", "#timeline").first().outerHeight() + (parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH) else 0)
	settings.ANIMATION_SPEED)
	$(div).children("div").children(".attack, .leader").each ->
		$(@).animate(
			top: findDateOnTimeline($(@).attr("data-date"), increment) - top + settings.ICON_ADJUST
		settings.ANIMATION_SPEED
		)

###
 * Function: addLeaderToGroup
 * --------------------------
 * Creates and adds a fully functional DOM object for a leader.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 ###
addLeaderToGroup = (div, leader, top) ->
	# We need to determine whether the leader has an end date, start date, both
	# or neither. If neither, we can't add the leader at all.
	end = false
	start = false
	if processDate(leader.startdate, "y") isnt 0 and leader.startdate isnt "?" and leader.startdate.toLowerCase() isnt "unknown"
		date = leader.startdate
		start = true
		if leader.enddate isnt "?" and leader.enddate.toLowerCase() isnt "unknown"
			end = true
	else if processDate(leader.enddate, "y") isnt 0
		date = leader.enddate
		end = true
	else
		return false
	if date is "?" or date.toLowerCase() is "unknown"
		return false
	html = "<p>" + processDate(leader.startdate, "e") + " - " + processDate(leader.enddate, "e") + "</p><p>" + leader.description + "</p>"
	$(div).append($ "<div/>"
		class: "leader"
		css:
			top: Math.max 0, findDateOnTimeline(date) - top - settings.ICON_ADJUST
		"data-date": date
		click: (e) ->
			opener = @
			if $(opener).data "dialog_open"
				return false
			$(opener).data "dialog_open", true
			$("<div/>"
				html: html
			).dialog
				title: "Leadership Change: " + leader.name
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
				beforeClose:  ->
					$(opener).data "dialog_open", false
	).data "dialog_open", false

###
 * Function: addAttackToGroup
 * --------------------------
 * Creates and adds a fully functional DOM object for an attack.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 ###
addAttackToGroup = (div, attack, top) ->
	$(div).append($ "<div/>"
		class: "attack"
		"data-date": attack.date
		css:
			top: findDateOnTimeline(attack.date) - top - settings.ICON_ADJUST
		click: (e) ->
			opener = @
			if $(opener).data "dialog_open"
				return false
			$(opener).data "dialog_open", true
			$("<div/>"
				html: "<p><b>" + processDate(attack.date, "e") + ":</b> " + attack.description + (if attack.casualties? then " (" + attack.casualties + ")" else "") + ".</p>"
			).dialog
				title: "Major Attack"
				position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
				beforeClose:  ->
					$(opener).data "dialog_open", false
	).data "dialog_open", false

###
 * Function: addGroupToMap
 * -----------------------
 * Adds a group to the map by placing its DOM into the map_container object.
 *
 * @param order: the order in which the group is added to the map
 * @param group: the JSON data for the group
 * @param startdate: the map's startdate
 * @param container: the jQuery object for the container to which the group DOM
 *   should be added
 ###
addGroupToMap = (order, group_data, startdate, enddate, container) ->
	return false if !group_data? or !container?
	group = group_data.Group
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
		"data-startdate": group.startdate
		"data-enddate": group.enddate
		"data-id": group.id
		html: $ "<div/>"
			class: "group_timeline"
	)
	for attack in group_data.Attack
		if attack.date != '0000-00-00'
			addAttackToGroup div.children("div"), attack, top
	addLeaderToGroup div.children("div"), leader, top for leader in group_data.Leader
	div.addClass("zoom-" + i) for i in [parseInt(group.min_zoom, 10)..parseInt(group.max_zoom, 10)]
	buttons = {}
	if group.dummy is false
		buttons["See Full Profile"] =->
				window.open("/group/mappingmilitants/cgi-bin/groups/view/" + group.id)
	buttons["Trace Group"] =-> 
		$("div.link:not(.group" + group.id + ")", "#map_container").addClass "trace_inactive"
		$("div.link.group" + group.id, "#map_container").removeClass "zoom_inactive"
		$("#group-" + group.id).removeClass("zoom_inactive").addClass "trace_active"
		for link in $ "div.link:not(.trace_inactive)", "#map_container"
			class_attr = $(link).attr("class").split " "
			for c in class_attr
				if(c.indexOf("group") is -1)
					continue;
				if(c is "group" + group.id)
					continue;
				other_group = c.substring 5
				$("#group-" + other_group).removeClass("zoom_inactive").addClass "trace_active"
		$("div.group:not(.trace_active)", "#map_container").addClass "trace_inactive"
		fitGroups true, countVisibleGroups()
		sizeLinksOnMap()
		$("#geo_zoom_slider,#geo_zoom_label").css "display", "none"
		$("#stop_trace_button").css("display","inline").val("Stop Tracing " + group.shortname).click ->
			$("div.trace_active").removeClass "trace_active"
			$("div.trace_inactive").removeClass "trace_inactive"
			if settings.zooms.length > 1
				$("#geo_zoom_slider,#geo_zoom_label").css "display", "inline-block"
				zoomGeographic $("#geo_zoom_slider").slider("value")
			else
				zoomGeographic 0
			$(@).css("display","none")
		$(@).dialog("destroy").remove()
	buttons["Close"] =->
		$(@).dialog("destroy").remove()
	div.prepend $("<span/>"
		text: group.shortname
		mouseenter: ->
			id = $(@).parent().data "id"
			$("div.link:not(.group" + id + ")", "#map_container").addClass "nav_inactive"
			for link in $ "div.link:not(.nav_inactive)", "#map_container"
				class_attr = $(link).attr("class").split " "
				for c in class_attr
					if(c.indexOf("group") is -1)
						continue;
					if(c is "group" + id)
						continue;
					other_group = c.substring 5
					$("#group-" + other_group).addClass "nav_active"
			$(@).parent().addClass "nav_active"
			$("div.group:not(.nav_active)", "#map_container").addClass "nav_inactive"
			$(@).text(group.name).css
				"background-color": "#A3B2FF"
				"font-weight": "bold"
				"font-size":"13px"
			$(@).text(group.name).css
				"margin-top": -1 * $(@).outerHeight()
			$(@).parent().css "background-color", "#D6E3FF"
		mouseleave: ->
			$(@).parent().css "background-color", "transparent"
			$("div.link.nav_inactive").removeClass "nav_inactive"
			$("div.group.nav_inactive").removeClass "nav_inactive"
			$("div.group.nav_active").removeClass "nav_active"
			$(@).text(group.shortname).css
				"background-color": "#fff"
				"font-weight":"normal"
				"font-size":"12px"
			$(@).text(group.shortname).css
				"margin-top": -1 * $(@).outerHeight()
		click: groupClickFunction group, buttons
	)
	container.append div

###
 * Function: progressBar
 * ---------------------
 * Creates a dialog with a progress bar to show while the page is loading. Then
 * starts a timeout to update the progress bar at set increments.
 ###
progressBar = ->
	$("<div/>"
		id: "progress_dialog"
		html: "<p>Please wait. The map is loading.</p><p>&nbsp;</p><div id=\"progress_bar\"></div>"
		css:
			height: 10
	).dialog
		modal: true
		draggable: false
		resizable: false
		width: 400
		closeOnEscape: false
		open: (event, ui) ->
			$(".ui-dialog-titlebar-close").hide()	# Hides the close button
	$("#progress_bar").progressbar
		value: 20
	updateProgressBar = ->
		$("#progress_bar").progressbar("value", $("#progress_bar").progressbar("value") + 10)
	setTimeout updateProgressBar, 500 * (i + 1) for i in [1..4]

###
 * Function: countVisibleGroups
 * ----------------------------
 * Counts the number of groups visible in the map area.
 *
 * @return: the integer number of groups visible in the map area.
 ###
countVisibleGroups = ->
	count = 0
	for group in $ "div.group", "#map_container"
		if $(group).css("display") isnt "none"
			count++
	return count

###
 * Function: setUpControls
 * -----------------------
 * Activates the control set in the horizontal bar across the top of the page.
 *
 * @param zooms: an array containing the English labels for the geographic zoom.
 ###
setUpControls = (zooms) ->
	return false if !zooms?
	$("#legend_button").click ->
		$("#legend_dialog").dialog
			title: "Legend"
			width: 220
	resolutions = ["Decade", "5 Years", "Year", "6 Months", "Quarter"]
	# Set up the buttons with jQueryUI
	$("input.button").button()
	$("#settings_button").click ->
		$("#settings_dialog").dialog
			modal: true
			width: 600
			draggable: false
			resizable: false
			title: "Map Settings"
	
	# Active/Inactive/Both radio buttons
	$("#both_radio").prop "checked", true
	$("input[name=select_organizations]").change ->
		if $("input[name=select_organizations]:checked").val() is "both"
			$("div.group","#map_container").removeClass  "settings_inactive"
			$("div.link","#map_container").removeClass "group_activity_inactive"
		else
			$(".group:not(." + $("input[name=select_organizations]:checked").val() + ")", "div#map_container").addClass "settings_inactive"
			$(".link:not(.active-" + $("input[name=select_organizations]:checked").val() + ")", "#map_container").addClass "group_activity_inactive"
			$(".group." + $("input[name=select_organizations]:checked").val(), "div#map_container").removeClass "settings_inactive"
			$(".link.active-" + $("input[name=select_organizations]:checked").val(), "#map_container").removeClass "group_activity_inactive"
		fitGroups true, countVisibleGroups()
		sizeLinksOnMap()
	
	# Leaders and attacks
	$(".toggle_checkbox").prop("checked", true).change ->
		if $(@).is(":checked")
			$("." + $(@).attr("data-class"), "#map_container").removeClass("settings_inactive")
			$("." + $(@).attr("data-class") + ":not(.zoom_inactive)", "#map_container").removeClass("settings_inactive").fadeIn settings.ANIMATION_SPEED
		else
			$("." + $(@).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut settings.ANIMATION_SPEED
	
	# Toggle links by type
	$(".link_toggle_checkbox").prop("checked", true).change ->
		linktype = $(@).attr("data-class")
		if $(@).is(":checked")
			$("." + linktype, "#map_container").removeClass("settings_inactive")
			sizeLinksOnMap()
		else
			$("." + linktype, "#map_container").addClass("settings_inactive")
	
	# Geographic zoom
	if zooms.length > 1
		$("#geo_zoom_slider").slider
			value: 0
			min: 0
			max: zooms.length - 1
			slide: (e, ui) -> 
				$("#geo_zoom_label").text "Geo Zoom: " + zooms[ui.value].name
				zoomGeographic ui.value
		$("#geo_zoom_label").text "Geo Zoom: " + zooms[0].name
	else
		$("#geo_zoom_label").css("display":"none")
		#("#geo_zoom_slider").css("display":"none")
	
	# Timeline resolution
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
		change: (event, ui) ->
			settings.startdate = ui.values[0]
			settings.enddate = ui.values[1]
			makeTimeline settings.startdate, settings.enddate, settings.resolution_values[$("#time_zoom_slider").slider("value")]
	$("#timeline_header").text("Timeline: " + settings.startdate + "-" + n.getFullYear())