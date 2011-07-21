var settings = {
	MIN_GROUP_WIDTH:			60,
	MIN_YEAR_HEIGHT:			5,
	SCROLL_BAR_WIDTH:			30,
	ANIMATION_SPEED:			400,
	resolutions:				['Decade','Five Years','Year','6 months','Quarter'],
	resolution_values:			[10,5,1,.5,.25],
	startdate:					0,
	enddate:					0,
	groupsDOM:					[],
	linksDOM:					[]
}

/* Function: setUpTimeline
 * -----------------------
 * Sets up the timeline on the left edge of the screen and initializes the relevant
 * global variables.
 */
function setUpTimeline(startyear, endyear) {
	var container, date_list, i, numyears;
	numyears = endyear - startyear +  1;
	container = $('<div/>', {'id': 'timeline'});
	date_list = $('<ul/>');
	for(i = startyear; i <= endyear; i++) {
		date_list.append($('<li/>',{
			'text': i,
			'id':'year-' + i
		}));
	}
	container.append(date_list);
	$('#wrap').height($(window).height() - 
		$('#header').outerHeight()).append(container);
	container.height($('#wrap').height() - 
		($('#timeline').outerHeight() - $('#timeline').height())
	);
	settings.year_height = Math.max(Math.floor(($('#timeline').height() / 
		numyears) - $('li', '#timeline').first().height()), settings.MIN_YEAR_HEIGHT);
	$('li', '#timeline').css('margin-bottom', settings.year_height);
	if($('#timeline').height() < $('ul', '#timeline').height()) {
		$('#timeline').height($('ul', '#timeline').height());
	}
}

function setUpMapArea(groups, links, startdate, enddate) {
	var num_groups, container, wrapper, i;
	wrapper = $('<div/>', {
		'id':'map_wrapper',
		'css':{
			'width':$(window).width() - $('#timeline').outerWidth() - 
				settings.SCROLL_BAR_WIDTH,
			'height':$('#timeline').outerHeight()
		}
	});
	container = $('<div/>', {
		'id':'map_container',
		'css': {
			'height': $('#timeline').outerHeight() - 
				parseInt($('#timeline').css('padding-bottom'), 10) - settings.SCROLL_BAR_WIDTH
		}
	});
	
	// Add the groups to the map
	for(i = 0; i < groups.length; i++) {
		addGroupToMap(groups[i].Profile, i, container, startdate, enddate)
	}
	
	$(wrapper).append(container);
	$('#wrap').append(wrapper);
	
	fitGroups(false, groups.length);
	
	$('div.group.active', '#map_container').each(function() {
		$(this).height($(this).height() + $('li','#timeline').first().outerHeight() + 
			(parseInt($('#timeline').css('padding-bottom'),10) - settings.SCROLL_BAR_WIDTH));
	});
	
	for(i = 0; i < links.length; i++) {
		addLinkToMap(links[i].Link);
	}
	$('.link.all,.link.riv', '#map_container').each(function() {
		$(this).append($('<div/>',{'class':'dot left'})).append($('<div/>',{'class':'dot right'}));
	});
}

/* Function: addLinkToMap
 * ----------------------
 * Creates a link DOM object and adds it to the map wrapper.
 *
 * @param i: the index of the desired link in the links array.
 * @param groups: the json groups array.
 * @param links: the json links array.
 */
function addLinkToMap(link) {
	var div, left_first, i;
	left_first = true;
	if($('#group-' + link.group1).position().left >= $('#group-' + link.group2).position().left) {
		left_first = false;
	}

	var div = $('<div/>', {
		'id': 'link-' + link.id,
		'class': 'link ' + link.type,
		'css': {
			/*'left': left_group.position().left + Math.floor(left_group.width() / 2) +
				(!left_first && link.type == 'spl' ? 5 : 0),
			'width': right_group.position().left - left_group.position().left,*/
			'top': findDateOnTimeline(link.date)
		},
		'data-group1': link.group1,
		'data-group2': link.group2,
		'click': function(e) {
			$('<div/>',{
				'html': processDate(link.date, 'e') + ': ' + link.description
			}).dialog({
				'modal':false,
				'title':$('#group-' + link.group1).attr('data-shortname') + ' and ' +
					$('#group-' + link.group2).attr('data-shortname') + ' ' + getLinkType(link.type),
				'draggable':false,
				'resizable':false,
				'width':300,
				'min-height':100,
				'position': [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
			});
		}
	});
	
	for(i = 0; i < settings.zooms.length; i++) {
		if($("#group-" + link.group1).hasClass("zoom-" + i) && 
			$("#group-" + link.group2).hasClass("zoom-" + i)) {
			div.addClass('zoom-' + i);
		}
	}
	
	if(link.type == 'spl') {
		div.append($('<div/>', {'class':'split-icon'}));
		if(left_first) {
			div.append($('<div/>', {'class':'split-right'}));
		}	else {
			div.append($('<div/>', {'class':'split-left'}));
		}
		div.width(div.width() - 5);
	}
	
	$('#map_container').append(div);
};

/** Function: getLinkType
  * ----------------------
  * Turns a link type class name into a full readable version of the type (i.e. "all"
  * becomes "Allies").
  *
  * @param: the abbreviated (CSS class name) of the link type.
  * @return: the full English name of the link type.
  */
function getLinkType(type) {
	switch(type) {
		case 'all':
			return 'Allies';
		case 'spl':
			return 'Split';
		case 'riv':
			return 'Rivals';
		case 'mer':
			return 'Merge';
		case 'aff':
			return 'Affiliates';
	}
}

function fitGroups(animate, num_groups) {
	var group_width = Math.max(Math.floor(($(window).width() - 
			$('#timeline').outerWidth() - settings.SCROLL_BAR_WIDTH) / num_groups),
			settings.MIN_GROUP_WIDTH);
	$('#map_container').width(num_groups * group_width);
	if(animate) {
		$('.group','#map_container').animate({width:group_width}, fixGroupNames);
	}	else {
		$('.group','#map_container').width(group_width);
		fixGroupNames();
	}
}

function fixGroupNames() {
	$('span', '#map_container').each(function() {
		$(this).css('margin-top', -1 * $(this).outerHeight());
	});
}

function findDateOnTimeline(date) {
	var year, month;
	year = processDate(date,'y');
	if(year == '0000') {
		year = settings.enddate;
	}
	month = processDate(date,'m');
	if(month == 0) {
		month = 1;
	}
	return $('#year-' + year).position().top  + Math.floor(month * 
		(($('li', '#timeline').first().outerHeight() + settings.year_height) / 12)) *
		settings.resolution_values[$('#time_zoom_slider').slider('value')];
}

/** Function: sizeLinks
  * -------------------
  * Properly sizes and positions links so that they connect the proper groups. This
  * function does NOT do error checking to make sure the links are visible or that
  * the groups they connect are in the DOM.
  *
  * @param links: a jQuery object containing the links to size.
  *
  */
function sizeLinks(links, animate) {
	var div, left_group, right_group, left_first;
	links.each(function() {
		left_first = true;
		if($('#group-' + $(this).attr("data-group1")).position().left < $('#group-' + $(this).attr("data-group2")).position().left) {
			left_group = $('#group-' + $(this).attr("data-group1"));
			right_group = $('#group-' + $(this).attr("data-group2"));
			
		}	else {
			left_group = $('#group-' + $(this).attr("data-group2"));
			right_group = $('#group-' + $(this).attr("data-group1"));
			left_first = false;
		}

		$(this).animate({
			left: left_group.position().left + Math.floor(left_group.width() / 2) +
				(!left_first && $(this).hasClass('spl') ? 5 : 0),
			width: right_group.position().left - left_group.position().left
		}, settings.ANIMATION_SPEED);
	});
};

function placeGroupDOMOnMap(group) {
	console.log(group);
	var attached_groups, i;
	attached_groups = $(".group", "#map_container");
	for(i = 0; i < attached_groups.length; i++) {
		if(parseInt($(attached_groups[i]).attr("data-order"), 10) > 
		parseInt(group.attr("data-order"), 10)) {
			break;
		}
	}
	$(attached_groups[i]).before(group);
	
};

function zoomGeographic(zoom) {
	console.log(zoom);
	var i;
	$(".group:not(.zoom-" + zoom + ")", "div#map_container").detach();
	$(".link:not(.zoom-" + zoom + ")", "div#map_container").fadeOut(settings.ANIMATION_SPEED);
	for(i = 0; i < settings.groupsDOM.length; i++) {
		var group = $(settings.groupsDOM[i]);
		if(group.hasClass("zoom-" + zoom) && $("#" + group.attr('id'), "#map_container").length === 0) {
			placeGroupDOMOnMap(group);
		}
	}
	fitGroups(true, $(".group", "#map_container").length);
	setTimeout(function() {
		sizeLinks($('.link.zoom-' + zoom, "#map_container"), true);
	}, settings.ANIMATION_SPEED);
}

function addGroupToMap(group, order, container, startdate, enddate) {
		var top, div, i;
		top = findDateOnTimeline((processDate(group.startdate, 'y') > 
			startdate ? group.startdate : startdate + '-00-00'));
		div = $('<div/>', {
			'class':'group ' + (processDate(group.enddate,'y') == '0000' ?
				'active' : 'inactive'),
			'id':'group-' + group.id,
			'css':{
				'margin-top': top,
				'height': findDateOnTimeline(group.enddate) - top
			},
			'data-name': group.name,
			'data-shortname': group.shortname,
			'data-order':order,
			'html':$('<div/>')
		});
		for(i = parseInt(group.min_zoom, 10); i <= parseInt(group.max_zoom, 10); i++) {
			div.addClass('zoom-' + i);
		}
		div.prepend($('<span/>', {
			'text': group.shortname,
			'mouseenter': function() {
				$(this).text(group.name).css('margin-top', -1 * $(this).outerHeight());
			},
			'mouseleave': function() {
				$(this).text(group.shortname).css('margin-top', -1 * $(this).outerHeight());
			},
			'click': function() {
				$('<div/>', {'html':group.description}).dialog({
					'title': group.name,
					'modal': true,
					'resizable':false,
					'draggable':false,
					'width':400,
					'buttons':{
						'See Full Profile': function() {
							window.open('/group/mappingmilitants/cgi-bin/profiles/view/' + group.id);
						},
						'Trace Group':function() {
							$(this).dialog('destroy').remove();
						},
						'Close':function() {
							$(this).dialog('destroy').remove();
						}
					}
				});
			}
		}));
		container.append(div);
		settings.groupsDOM.push(div);
}

function processDate(d, part) {
	d = d.split('-');
	switch(part) {
		case 'y':
			return d[0];
		case 'm':
			return parseInt(d[1],10);
		case 'd':
			return parseInt(d[2],10);
		case 'e':
			return englishDate(d);
		default:
			return false;
	}
}

function englishDate(d) {
	if(d[1] == '00') {
		return d[0];
	}
	return numberToMonth(parseInt(d[1],10)) + ' ' + d[0];
}

/* Function: numberToMonth
 * -----------------------
 * Turns a number 1-12 into an English month.
 *
 * @param m: the number of the month (1-12).
 * @return: an English month (i.e. "January") or false if number is not in 1-12.
 */
function numberToMonth(m) {
	switch(m) {
		case 1:
			return 'January';
		case 2:
			return 'February';
		case 3:
			return 'March';
		case 4:
			return 'Apri';
		case 5:
			return 'May';
		case 6:
			return 'June';
		case 7:
			return 'July';
		case 8:
			return 'August';
		case 9:
			return 'September';
		case 10:
			return 'October';
		case 11:
			return 'November';
		case 12:
			return 'December';
		default:
			return false;
	}
}

function setUpControls(zooms) {
	$('#geo_zoom_slider').slider({
		'value': 0,
		'min:': 0,
		'max':zooms.length - 1,
		'slide':function(e, ui) {
			$('#geo_zoom_label').text('Geo Zoom: ' + zooms[ui.value]);
			zoomGeographic(ui.value);
		}
	}).slider('value',0);
	$('#geo_zoom_label').text('Geo Zoom: ' + zooms[0]);
	
	$('#time_zoom_slider').slider({
		'value': 2,
		'min:': 0,
		'max':4,
		'slide':function(e, ui) {
			$('#time_zoom_label').text('Timeline Resolution: ' + settings.resolutions[ui.value]);
		}
	}).slider('value',2);
	$('#time_zoom_label').text('Timeline Resolution: ' + settings.resolutions[$('#time_zoom_slider').slider('value')]);
};

$(document).ready(function() {
	var url, id, startyear, endyear, i;
	$('<div/>', {
		'id':'progress_dialog',
		'html':'<p>Please wait. The map is loading.</p><p>&nbsp;</p><div id="progress_bar"></div>',
		'css':{
			'height':'10px'
		}
	}).dialog({
		'modal':true,
		'draggable':false,
		'resizable':false,
		'width':400,
		'closeOnEscape': false,
		'open': function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
	});
	$('#progress_bar').progressbar({
		'value':20
	});
	$("input.button").button();
	$('body').height($(window).height());
	//url = "a" + window.location.href;
	//id = url.substring(url.lastIndexOf('/') + 1);
	
	for(var i = 0; i < 4; i++) {
		setTimeout(function() {
			$('#progress_bar').progressbar('value', $('#progress_bar').progressbar('value') + 10);
		}, 500 * (i +1));
	}
	
	$.getJSON('/group/mappingmilitants/cgi-bin/maps/jsondata/' + 3, {id: 3}, function(data) {
		$("#progress_bar").progressbar('value', 70);
		settings.startdate = parseInt(data.Map.startyear,10);
		settings.enddate = parseInt(data.Map.endyear,10);
		setUpTimeline(settings.startdate, settings.enddate);
		$("#progress_bar").progressbar('value', 80);
		settings.zooms = data.Map.zooms;
		setUpControls(settings.zooms);
		$("#progress_bar").progressbar('value', 90);
		setUpMapArea(data.groups, data.links, settings.startdate, settings.enddate);
		$("#progress_bar").progressbar('value', 100);
		$('#progress_dialog').delay(200).dialog('destroy');
		zoomGeographic(0);
	});
});