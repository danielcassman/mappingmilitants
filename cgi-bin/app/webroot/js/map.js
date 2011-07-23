var addAttackToGroup, addGroupToMap, addLeaderToGroup, addLinkToMap, addUmbrellaToMap, countVisibleGroups, englishDate, findDateOnTimeline, fitGroupToTimeline, fitGroups, fitUmbrella, fixGroupNames, getLinkType, getTimelineLabel, makeTimeline, numberToMonth, processDate, progressBar, setUpControls, setUpMapArea, setUpTimeline, settings, sizeLink, sizeLinksOnMap, zoomGeographic;
settings = {
  MIN_GROUP_WIDTH: 60,
  MIN_YEAR_HEIGHT: 13,
  SCROLL_BAR_WIDTH: 30,
  ANIMATION_SPEED: 400,
  ICON_ADJUST: -10,
  MIN_START_YEAR: 1950,
  resolution_values: [10, 5, 1, .5, .25],
  year_height: 0,
  startdate: 0,
  enddate: 0,
  zooms: []
};
/*
 * Function: sizeLink
 * ------------------
 * Animates a link into place horizontally so it connects the appropriate groups.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 */
sizeLink = function(link) {
  var adjust, left_first, left_group, right_group;
  if (!(link != null)) {
    return false;
  }
  left_first = true;
  if ($("#group-" + $(link).attr("data-group1")).position().left < $("#group-" + $(link).attr("data-group2")).position().left) {
    left_group = $("#group-" + $(link).attr("data-group1"));
    right_group = $("#group-" + $(link).attr("data-group2"));
  } else {
    left_group = $("#group-" + $(link).attr("data-group2"));
    right_group = $("#group-" + $(link).attr("data-group1"));
    left_first = false;
  }
  adjust = !left_first && $(this).hasClass("spl") ? 5 : 0;
  return $(link).animate({
    left: left_group.position().left + Math.floor(left_group.width() / 2) + adjust,
    width: right_group.position().left - left_group.position().left - ($(link).hasClass("spl") ? 5 : 0)
  }, settings.ANIMATION_SPEED);
};
/*
 * Function: fitUmbrella
 * ---------------------
 * Fits an umbrella in the map area, both vertically and horizontally.
 *
 * @param div: the div object for the umbrella.
 * @param increment: the timeline increment (in years)
 */
fitUmbrella = function(div, increment) {
  var groups, groups_on_map, i, left, right, top, _ref;
  if (processDate($(div).attr("data-startdate"), "y") > settings.enddate || ($(div).attr("data-enddate") !== "0000-00-00" && processDate($(div).attr("data-enddate"), "y") < settings.startdate)) {
    $(div).addClass("inactive");
    return false;
  }
  groups = $(div).attr("data-groups").split(",");
  left = Math.pow(2, 53);
  right = 0;
  groups_on_map = 0;
  for (i = 0, _ref = groups.length; 0 <= _ref ? i <= _ref : i >= _ref; 0 <= _ref ? i++ : i--) {
    if (!(groups[i] != null) || $("#group-" + groups[i]).css("display") === "none") {
      continue;
    }
    groups_on_map += 1;
    left = Math.min(left, $("#group-" + groups[i]).position().left);
    right = Math.max(right, $("#group-" + groups[i]).position().left);
  }
  if (groups_on_map < 2) {
    $(div).addClass("inactive");
    return false;
  }
  $(div).removeClass("inactive");
  top = findDateOnTimeline($(div).attr("data-startdate"), increment);
  $(div).css({
    left: left + parseInt($("div.group", "#map_container").width() / 2, 10),
    width: right - left,
    top: top,
    height: findDateOnTimeline($(div).attr("data-enddate"), increment) - top
  });
  return $(div).children("span").css({
    top: parseInt($(div).height() / 2, 10) - parseInt($(div).children("span").height() / 2, 10)
  });
};
/*
 * Function: sizeLinksOnMap
 * ------------------------
 * Animates the visible links on the map into place so they connect the
 * appropriate groups AFTER a timeout equivalent to the animation speed to
 * allow for the group divs to be animated into place.
 */
sizeLinksOnMap = function() {
  return setTimeout((function() {
    $(".link", "#map_container").each(function() {
      if ($(this).css("display") !== "none") {
        return sizeLink(this);
      }
    });
    return $("div.umbrella", "#map_container").each(function() {
      fitUmbrella($(this), settings.resolution_values[$("#time_zoom_slider").slider("value")]);
      return true;
    });
  }), settings.ANIMATION_SPEED + 20);
};
/*
 * Function: zoomGeographic
 * ------------------------
 * Zooms the map to the given geographic zoom level.
 *
 * @param zoom: the desired zoom level [INTEGER]
 */
zoomGeographic = function(zoom) {
  $(".group:not(.zoom-" + zoom + ")", "div#map_container").addClass("zoom_inactive");
  $(".link:not(.zoom-" + zoom + ")", "div#map_container").addClass("zoom_inactive");
  $("div.link.zoom-" + zoom, "#map_container").removeClass("zoom_inactive");
  $("div.group.zoom-" + zoom, "#map_container").removeClass("zoom_inactive");
  fitGroups(true, countVisibleGroups());
  return sizeLinksOnMap();
};
/*
 * Function: getLinkType
 * ---------------------
 * Gets a full description of a link type for an abbreviated link type.
 *
 * @param type: the abbreviated link type (i.e. "all" for allies)
 * @return: the full name of the link typ (i.e. "Allies")
 */
getLinkType = function(type) {
  switch (type) {
    case "all":
      return "Allies";
    case "spl":
      return "Split";
    case "aff":
      return "Affiliates";
    case "mer":
      return "Merge";
    case "riv":
      return "Rivals";
    default:
      return false;
  }
};
/*
 * Function: addLinkToMap
 * ----------------------
 * Creates and adds a fully functional DOM object for a link.
 *
 * @param link: the link JSON object
 */
addLinkToMap = function(link) {
  var active, div, i, left_first, _ref;
  left_first = false;
  active = "active-both";
  if ($('#group-' + link.group1).position().left < $('#group-' + link.group2).position().left) {
    left_first = true;
  }
  if ($('#group-' + link.group1).hasClass("active") && $('#group-' + link.group2).hasClass("active")) {
    active = "active-active";
  } else if ($('#group-' + link.group1).hasClass("inactive") && $('#group-' + link.group2).hasClass("inactive")) {
    active = "active-inactive";
  }
  div = $("<div/>", {
    id: "link-" + link.id,
    "class": "link " + link.type + " group" + link.group1 + " group" + link.group2 + " " + active,
    css: {
      top: findDateOnTimeline(link.date)
    },
    "data-group1": link.group1,
    "data-group2": link.group2,
    "data-date": link.date,
    click: function(e) {
      return $("<div/>", {
        html: processDate(link.date, "e") + ": " + link.description
      }).dialog({
        modal: false,
        title: $('#group-' + link.group1).attr('data-shortname') + ' and ' + $('#group-' + link.group2).attr('data-shortname') + ' ' + getLinkType(link.type),
        width: 300,
        "min-height": 100,
        position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
      });
    }
  });
  for (i = 0, _ref = settings.zooms.length; 0 <= _ref ? i <= _ref : i >= _ref; 0 <= _ref ? i++ : i--) {
    if ($("#group-" + link.group1).hasClass("zoom-" + i) && $("#group-" + link.group2).hasClass("zoom-" + i)) {
      div.addClass("zoom-" + i);
    }
  }
  if (link.type === "spl") {
    div.append($("<div/>", {
      "class": "split-icon"
    }));
    if (left_first) {
      div.append($("<div/>", {
        "class": "split-right"
      }));
    } else {
      div.append($("<div/>", {
        "class": "split-left"
      }));
    }
    div.width(div.width() - 5);
  }
  return $("#map_container").append(div);
};
/*
 * Function: getTimelineLabel
 * --------------------------
 * Creates a human readable label for the given date.
 *
 * @param date: the date (in years), so 1999.5 is June 1995, etc.
 * @param increment: the timeline increment (in years), so .25 is quarters
 * @return: a label of the form "Jan 2000"
 */
getTimelineLabel = function(date, increment) {
  var month;
  if (increment === .5) {
    return Math.floor(date) + " " + (Math.floor(date) === date ? "Jan" : "Jun");
  } else if (increment === .25) {
    switch (date - Math.floor(date)) {
      case 0:
        month = "Jan";
        break;
      case .25:
        month = "Apr";
        break;
      case .5:
        month = "Jul";
        break;
      case .75:
        month = "Oct";
        break;
      default:
        month = false;
    }
    return Math.floor(date) + " " + month;
  }
  return false;
};
/*
 * Function: makeTimeLine
 * ----------------------
 * Makes a timeline by adding the proper increments to the timeline div.
 *
 * @param startyear: the map start year
 * @param endyear: the map end year
 * @param increment: the timeline increment in years (default = 1)
 * @param move_divs: boolean indicating whether the groups and links need
 *   to be moved into place.
 */
makeTimeline = function(startyear, endyear, increment, move_divs) {
  var date_list, group, i, label, num_ticks, _i, _len, _ref, _step;
  if (increment == null) {
    increment = 1;
  }
  if (move_divs == null) {
    move_divs = true;
  }
  num_ticks = Math.floor((endyear - startyear) / increment + 1);
  date_list = $("<ul/>");
  for (i = startyear, _step = increment; startyear <= endyear ? i <= endyear : i >= endyear; i += _step) {
    if (increment > 1) {
      label = i > endyear ? endyear : i;
    } else if (increment < 1) {
      label = getTimelineLabel(i, increment);
    } else {
      label = i;
    }
    date_list.append($("<li/>", {
      text: label,
      id: "year-" + i
    }));
  }
  $("#timeline").empty().append(date_list);
  $("#wrap").height($(window).height() - $("#header").outerHeight());
  $("#timeline").height($("#wrap").height() - ($("#timeline").outerHeight() - $("#timeline").height()));
  settings.year_height = Math.max(Math.floor(($("#timeline").height() / num_ticks) - $("li", "#timeline").first().outerHeight()), settings.MIN_YEAR_HEIGHT);
  $("li", "#timeline").css({
    "margin-bottom": settings.year_height
  });
  if ($("#timeline").height() < $("ul", "#timeline").height()) {
    $("#timeline").height($("ul", "#timeline").height());
  }
  $("#map_wrapper").height($("#timeline").outerHeight());
  $("#map_container").height($("#timeline").outerHeight() - parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH);
  if (move_divs) {
    _ref = $(".group", "#map_container");
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      group = _ref[_i];
      fitGroupToTimeline(group, increment, startyear, endyear);
    }
    fitGroups(false, countVisibleGroups());
    $(".link", "#map_container").each(function() {
      return $(this).animate({
        top: findDateOnTimeline($(this).attr("data-date"), increment)
      }, settings.ANIMATION_SPEED);
    });
    return sizeLinksOnMap();
  }
};
/*
 * Function: setUpTimeline
 * -----------------------
 * Initializes the timeline by creating and adding the timeline div and adding
 * year increments.
 *
 * @param startyear: the map start year
 * @param endyear: the map end year
 */
setUpTimeline = function(startyear, endyear) {
  var container;
  container = $("<div/>", {
    id: "timeline"
  });
  $("#wrap").append(container);
  return makeTimeline(startyear, endyear, 1, false);
};
/*
 * Function: addUmbrellaToMap
 * --------------------------
 * Creates and adds an umbrella to the map.
 *
 * @param umbrella: the JSON object for the umbrella to add.
 */
addUmbrellaToMap = function(umbrella) {
  var div;
  div = $("<div/>", {
    "class": "umbrella",
    html: $("<span/>", {
      text: umbrella.shortname
    }),
    "data-groups": umbrella.groups.join(","),
    "data-startdate": umbrella.startdate,
    "data-enddate": umbrella.enddate,
    click: function(e) {
      return $("<div/>", {
        html: umbrella.description
      }).dialog({
        title: umbrella.name,
        position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
      });
    }
  });
  return $("#map_container").append(div);
};
/*
 * Function: setUpMapArea
 * ----------------------
 * Initializes the map area.
 *
 * @param groups: the groups array from the JSON object
 * @param links: the links array from the JSON object
 * @param startdate: the map's initial start date
 * @param enddate: the map's initial end date
 */
setUpMapArea = function(groups, links, umbrellas, startdate, enddate) {
  var container, i, link, umbrella, wrapper, _i, _j, _len, _len2, _ref, _results;
  if (!(groups != null) || !(links != null)) {
    return false;
  }
  wrapper = $("<div/>", {
    id: "map_wrapper",
    css: {
      width: $(window).width() - $("#timeline").outerWidth() - settings.SCROLL_BAR_WIDTH,
      height: $("#timeline").outerHeight()
    }
  });
  container = $("<div/>", {
    id: "map_container",
    css: {
      height: $("#timeline").outerHeight() - parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH
    }
  });
  for (i = 0, _ref = groups.length - 1; 0 <= _ref ? i <= _ref : i >= _ref; 0 <= _ref ? i++ : i--) {
    addGroupToMap(i, groups[i], startdate, enddate, container);
  }
  $(wrapper).append(container);
  $("#wrap").append(wrapper);
  fitGroups(false, groups.length);
  $("div.group.active", "#map_container").each(function() {
    return $(this).height($(this).height() + $("li", "#timeline").first().outerHeight() + (parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH));
  });
  for (_i = 0, _len = links.length; _i < _len; _i++) {
    link = links[_i];
    addLinkToMap(link.Link);
  }
  $(".link.all, .link.riv", "#map_container").each(function() {
    return $(this).append($("<div/>", {
      "class": "dot left"
    })).append($("<div/>", {
      "class": "dot right"
    }));
  });
  _results = [];
  for (_j = 0, _len2 = umbrellas.length; _j < _len2; _j++) {
    umbrella = umbrellas[_j];
    _results.push(addUmbrellaToMap(umbrella));
  }
  return _results;
};
/*
 * Function: fixGroupNames
 * -----------------------
 * Properly aligns the spans in the group divs so that they are even with the
 * top of the group timeline.
 */
fixGroupNames = function() {
  return $("span", ".group").each(function() {
    return $(this).css("margin-top", -1 * $(this).outerHeight());
  });
};
/*
 * Function: fitGroups
 * -------------------
 * Fits the groups in the map area into the screen.
 *
 * @param animate: boolean indicating whether the groups should be animated
 *   into place.
 * @param num_groups: the number of visible groups. Usually can be retrieved
 *   through countVisibleGroups.
 */
fitGroups = function(animate, num_groups) {
  var group_width;
  group_width = Math.max(Math.floor(($(window).width() - $('#timeline').outerWidth() - settings.SCROLL_BAR_WIDTH) / num_groups), settings.MIN_GROUP_WIDTH);
  $("#map_container").width(num_groups * group_width);
  if (animate) {
    return $('.group', '#map_container').animate({
      width: group_width
    }, fixGroupNames);
  } else {
    $('.group', '#map_container').width(group_width);
    return fixGroupNames();
  }
};
/*
 * Function: numberToMonth
 * -----------------------
 * Converts a number to an English month.
 *
 * @param m: the number of the month (1-12)
 * @return: the English month (i.e. "January")
 */
numberToMonth = function(m) {
  switch (m) {
    case 1:
      return "January";
    case 2:
      return "February";
    case 3:
      return "March";
    case 4:
      return "April";
    case 5:
      return "May";
    case 6:
      return "June";
    case 7:
      return "July";
    case 8:
      return "August";
    case 9:
      return "September";
    case 10:
      return "October";
    case 11:
      return "November";
    case 12:
      return "December";
    default:
      return false;
  }
};
/*
 * Function: englishDate
 * ---------------------
 * Returns a string containing a human readable version of the given date.
 *
 * @param d: an array containing the date as [year, month, date]
 * @return: a string; either just the year or "January 2000"
 */
englishDate = function(d) {
  if (!(d != null)) {
    return false;
  }
  if (d[1] === "00") {
    return d[0];
  } else {
    return numberToMonth(parseInt(d[1], 10)) + " " + d[0];
  }
};
/*
 * Function: processDate
 * ---------------------
 * Processes a date string into a variaety of formats.
 *
 * @param d: the date to process in the format yyyy-mm-dd
 * @param return: the part of the date to return [STRING]
 *   if y: the four digit year
 *   if m: the month as a parsed integer
 *   if d: the date as a parsed integer
 *   if e: the English date as "January 2000"
 */
processDate = function(d, part) {
  d = d.split("-");
  if (!(d instanceof Array)) {
    return false;
  }
  switch (part) {
    case "y":
      return parseInt(d[0], 10);
    case "m":
      return parseInt(d[1], 10);
    case "d":
      return parseInt(d[2], 10);
    case "e":
      return englishDate(d);
    default:
      return false;
  }
};
/*
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
 */
findDateOnTimeline = function(date, increment) {
  var closest_year, month, year;
  if (increment == null) {
    increment = 1;
  }
  year = processDate(date, "y");
  month = processDate(date, "m");
  if (month === 0) {
    if (year !== 0) {
      month = 1;
    } else {
      month = 12;
    }
  }
  if (year === 0) {
    year = settings.enddate;
  }
  if (year > settings.enddate) {
    year = settings.enddate;
  }
  if (year < settings.startdate) {
    year = settings.startdate;
  }
  if (increment === 1) {
    closest_year = year;
  } else if (increment < 1) {
    closest_year = Math.floor(year);
  } else {
    closest_year = settings.startdate + Math.floor((year - settings.startdate) / increment) * increment;
  }
  return $("#year-" + closest_year).position().top + ((year - settings.startdate) % increment) / increment * ($("li", "#timeline").first().outerHeight() + settings.year_height) + Math.floor(month * (($("li", "#timeline").first().outerHeight() + settings.year_height) / 12)) / increment;
};
/*
 * Function: fitGroupToTimeline
 * ----------------------------
 * Fits a group vertically into the timeline.
 *
 * @param div: the group div
 * @param increment: the timeline increment (in years)
 * @param startyear: the start year of the timeline
 * @param endyear: the end year of the timeline
 * @param animate: boolean indicating whether to animate the group into place
 */
fitGroupToTimeline = function(div, increment, startyear, endyear, animate) {
  var top;
  if (animate == null) {
    animate = true;
  }
  if ((processDate($(div).attr("data-enddate"), "y") < startyear && processDate($(div).attr("data-enddate"), "y") !== 0) || processDate($(div).attr("data-startdate"), "y") > endyear) {
    $(div).addClass("timeline_inactive");
    $("div.link.group" + $(div).attr("id").substring(6)).addClass("timeline_inactive");
    return false;
  }
  $(div).removeClass("timeline_inactive");
  $("div.link.group" + $(div).attr("id").substring(6)).removeClass("timeline_inactive");
  if (processDate($(div).attr("data-startdate"), "y") >= startyear) {
    top = findDateOnTimeline($(div).attr("data-startdate"), increment);
  } else {
    top = $("#year-" + startyear).position().top;
  }
  $(div).animate({
    "margin-top": top,
    height: findDateOnTimeline($(div).attr("data-enddate"), increment) - top + ($(div).hasClass("active") ? $("li", "#timeline").first().outerHeight() + (parseInt($("#timeline").css("padding-bottom"), 10) - settings.SCROLL_BAR_WIDTH) : 0)
  }, settings.ANIMATION_SPEED);
  return $(div).children("div").children(".attack, .leader").each(function() {
    return $(this).animate({
      top: findDateOnTimeline($(this).attr("data-date"), increment) - top + settings.ICON_ADJUST
    }, settings.ANIMATION_SPEED);
  });
};
/*
 * Function: addLeaderToGroup
 * --------------------------
 * Creates and adds a fully functional DOM object for a leader.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 */
addLeaderToGroup = function(div, leader, top) {
  var date, end, html, start;
  end = false;
  start = false;
  if (processDate(leader.startdate, "y") !== 0 && leader.startdate !== "?" && leader.startdate.toLowerCase() !== "unknown") {
    date = leader.startdate;
    start = true;
    if (leader.enddate !== "?" && leader.enddate.toLowerCase() !== "unknown") {
      end = true;
    }
  } else if (processDate(leader.enddate, "y") !== 0) {
    date = leader.enddate;
    end = true;
  } else {
    return false;
  }
  if (date === "?" || date.toLowerCase() === "unknown") {
    return false;
  }
  if (start) {
    html = "<p>Assumed leadership " + processDate(leader.startdate, "e") + ".</p>";
    html += "<p>" + (leader.enddate.toLowerCase() !== "unknown" && leader.enddate !== "?" && leader.enddate !== "0000-00-00" ? processDate(leader.enddate, "e") + ": " : "") + leader.summary + "</p>";
  } else {
    html = "<p>" + processDate(leader.enddate, "e") + ": " + leader.summary + "</p>";
  }
  return $(div).append($("<div/>", {
    "class": "leader",
    css: {
      top: Math.max(0, findDateOnTimeline(date) - top - settings.ICON_ADJUST)
    },
    "data-date": date,
    click: function(e) {
      return $("<div/>", {
        html: html
      }).dialog({
        title: "Leadership Change: " + leader.name,
        position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
      });
    }
  }));
};
/*
 * Function: addAttackToGroup
 * --------------------------
 * Creates and adds a fully functional DOM object for an attack.
 *
 * @param div: the group div
 * @param attack: the JSON object for the attack
 * @param top: the absolute position (in pixels) of the top of the group div
 */
addAttackToGroup = function(div, attack, top) {
  return $(div).append($("<div/>", {
    "class": "attack",
    "data-date": attack.date,
    css: {
      top: findDateOnTimeline(attack.date) - top - settings.ICON_ADJUST
    },
    click: function(e) {
      return $("<div/>", {
        html: "<p><b>" + processDate(attack.date, "e") + ":</b> " + attack.description + (attack.casualties != null ? " (" + attack.casualties + ")" : "") + ".</p>"
      }).dialog({
        title: "Major Attack",
        position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70]
      });
    }
  }));
};
/*
 * Function: addGroupToMap
 * -----------------------
 * Adds a group to the map by placing its DOM into the map_container object.
 *
 * @param order: the order in which the group is added to the map
 * @param group: the JSON data for the group
 * @param startdate: the map's startdate
 * @param container: the jQuery object for the container to which the group DOM
 *   should be added
 */
addGroupToMap = function(order, group_data, startdate, enddate, container) {
  var attack, div, group, i, leader, start_year, top, _i, _j, _len, _len2, _ref, _ref2, _ref3, _ref4;
  if (!(group_data != null) || !(container != null)) {
    return false;
  }
  group = group_data.Profile;
  start_year = processDate(group.startdate, "y") >= startdate ? group.startdate : startdate + "-00-00";
  top = findDateOnTimeline(start_year);
  div = $("<div/>", {
    "class": "group " + (processDate(group.enddate, 'y') === 0 ? "active" : "inactive"),
    id: "group-" + group.id,
    css: {
      "margin-top": top,
      height: findDateOnTimeline(group.enddate) - top
    },
    "data-name": group.name,
    "data-shortname": group.shortname,
    "data-order": order,
    "data-startdate": start_year,
    "data-enddate": group.enddate,
    html: $("<div/>", {
      "class": "group_timeline"
    })
  });
  _ref = group_data.Attack;
  for (_i = 0, _len = _ref.length; _i < _len; _i++) {
    attack = _ref[_i];
    addAttackToGroup(div.children("div"), attack, top);
  }
  _ref2 = group_data.Leader;
  for (_j = 0, _len2 = _ref2.length; _j < _len2; _j++) {
    leader = _ref2[_j];
    addLeaderToGroup(div.children("div"), leader, top);
  }
  for (i = _ref3 = parseInt(group.min_zoom, 10), _ref4 = parseInt(group.max_zoom, 10); _ref3 <= _ref4 ? i <= _ref4 : i >= _ref4; _ref3 <= _ref4 ? i++ : i--) {
    div.addClass("zoom-" + i);
  }
  div.prepend($("<span/>", {
    text: group.shortname,
    mouseenter: function() {
      return $(this).text(group.name).css("margin-top", -1 * $(this).outerHeight());
    },
    mouseleave: function() {
      return $(this).text(group.shortname).css('margin-top', -1 * $(this).outerHeight());
    },
    click: function() {
      return $("<div/>", {
        html: group.description
      }).dialog({
        title: group.name,
        modal: true,
        resizable: false,
        draggable: false,
        width: 400,
        buttons: {
          "See Full Profile": function() {
            return window.open("/group/mappingmilitants/cgi-bin/profiles/view/" + group.id);
          },
          "Trace Group": function() {
            var c, class_attr, link, other_group, _k, _l, _len3, _len4, _ref5;
            $("div.link:not(.group" + group.id + ")", "#map_container").addClass("trace_inactive");
            $("div.link.group" + group.id, "#map_container").removeClass("zoom_inactive");
            $("#group-" + group.id).removeClass("zoom_inactive").addClass("trace_active");
            _ref5 = $("div.link:not(.trace_inactive)", "#map_container");
            for (_k = 0, _len3 = _ref5.length; _k < _len3; _k++) {
              link = _ref5[_k];
              class_attr = $(link).attr("class").split(" ");
              for (_l = 0, _len4 = class_attr.length; _l < _len4; _l++) {
                c = class_attr[_l];
                if (c.indexOf("group") === -1) {
                  continue;
                }
                if (c === "group" + group.id) {
                  continue;
                }
                other_group = c.substring(5);
                $("#group-" + other_group).removeClass("zoom_inactive").addClass("trace_active");
              }
            }
            $("div.group:not(.trace_active)", "#map_container").addClass("trace_inactive");
            fitGroups(true, countVisibleGroups());
            sizeLinksOnMap();
            $("#geo_zoom_slider,#geo_zoom_label").css("display", "none");
            $("#stop_trace_button").css("display", "inline").val("Stop Tracing " + group.shortname).click(function() {
              $("div.trace_active").removeClass("trace_active");
              $("div.trace_inactive").removeClass("trace_inactive");
              $("#geo_zoom_slider,#geo_zoom_label").css("display", "inline-block");
              $(this).css("display", "none");
              return zoomGeographic($("#geo_zoom_slider").slider("value"));
            });
            return $(this).dialog("destroy").remove();
          },
          "Close": function() {
            return $(this).dialog("destroy").remove();
          }
        }
      });
    }
  }));
  return container.append(div);
};
/*
 * Function: progressBar
 * ---------------------
 * Creates a dialog with a progress bar to show while the page is loading. Then
 * starts a timeout to update the progress bar at set increments.
 */
progressBar = function() {
  var i, updateProgressBar, _results;
  $("<div/>", {
    id: "progress_dialog",
    html: '<p>Please wait. The map is loading.</p><p>&nbsp;</p><div id="progress_bar"></div>',
    css: {
      height: '10px'
    }
  }).dialog({
    modal: true,
    draggable: false,
    resizable: false,
    width: 400,
    closeOnEscape: false,
    open: function(event, ui) {
      return $(".ui-dialog-titlebar-close").hide();
    }
  });
  $("#progress_bar").progressbar({
    value: 20
  });
  updateProgressBar = function() {
    return $("#progress_bar").progressbar("value", $("#progress_bar").progressbar("value") + 10);
  };
  _results = [];
  for (i = 1; i <= 4; i++) {
    _results.push(setTimeout(updateProgressBar, 500 * (i + 1)));
  }
  return _results;
};
/*
 * Function: countVisibleGroups
 * ----------------------------
 * Counts the number of groups visible in the map area.
 *
 * @return: the integer number of groups visible in the map area.
 */
countVisibleGroups = function() {
  var count, group, _i, _len, _ref;
  count = 0;
  _ref = $("div.group", "#map_container");
  for (_i = 0, _len = _ref.length; _i < _len; _i++) {
    group = _ref[_i];
    if ($(group).css("display") !== "none") {
      count++;
    }
  }
  return count;
};
/*
 * Function: setUpControls
 * -----------------------
 * Activates the control set in the horizontal bar across the top of the page.
 *
 * @param zooms: an array containing the English labels for the geographic zoom.
 */
setUpControls = function(zooms) {
  var n, resolutions;
  if (!(zooms != null)) {
    return false;
  }
  $("#legend_button").click(function() {
    return $("#legend_dialog").dialog({
      title: "Legend",
      width: 220
    });
  });
  resolutions = ["Decade", "5 Years", "Year", "6 Months", "Quarter"];
  $("input.button").button();
  $("#settings_button").click(function() {
    return $("#settings_dialog").dialog({
      modal: true,
      width: 600,
      draggable: false,
      resizable: false,
      title: "Map Settings"
    });
  });
  $("#both_radio").prop("checked", true);
  $("input[name=select_organizations]").change(function() {
    if ($("input[name=select_organizations]:checked").val() === "both") {
      $("div.group", "#map_container").removeClass("settings_inactive");
      $("div.link", "#map_container").removeClass("group_activity_inactive");
    } else {
      $(".group:not(." + $("input[name=select_organizations]:checked").val() + ")", "div#map_container").addClass("settings_inactive");
      $(".link:not(.active-" + $("input[name=select_organizations]:checked").val() + ")", "#map_container").addClass("group_activity_inactive");
      $(".group." + $("input[name=select_organizations]:checked").val(), "div#map_container").removeClass("settings_inactive");
      $(".link.active-" + $("input[name=select_organizations]:checked").val(), "#map_container").removeClass("group_activity_inactive");
    }
    fitGroups(true, countVisibleGroups());
    return sizeLinksOnMap();
  });
  $(".toggle_checkbox").prop("checked", true).change(function() {
    if ($(this).is(":checked")) {
      $("." + $(this).attr("data-class"), "#map_container").removeClass("settings_inactive");
      return $("." + $(this).attr("data-class") + ":not(.zoom_inactive)", "#map_container").removeClass("settings_inactive").fadeIn(settings.ANIMATION_SPEED);
    } else {
      return $("." + $(this).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut(settings.ANIMATION_SPEED);
    }
  });
  $(".link_toggle_checkbox").prop("checked", true).change(function() {
    var linktype;
    linktype = $(this).attr("data-class");
    if ($(this).is(":checked")) {
      $("." + linktype, "#map_container").removeClass("settings_inactive");
      return sizeLinksOnMap();
    } else {
      return $("." + linktype, "#map_container").addClass("settings_inactive");
    }
  });
  $("#geo_zoom_slider").slider({
    value: 0,
    min: 0,
    max: zooms.length - 1,
    slide: function(e, ui) {
      $("#geo_zoom_label").text("Geo Zoom: " + zooms[ui.value]);
      return zoomGeographic(ui.value);
    }
  });
  $("#geo_zoom_label").text("Geo Zoom: " + zooms[0]);
  $("#time_zoom_slider").slider({
    value: 2,
    min: 0,
    max: 4,
    slide: function(e, ui) {
      $("#time_zoom_label").text("Timeline Resolution: " + resolutions[ui.value]);
      return makeTimeline(settings.startdate, settings.enddate, settings.resolution_values[ui.value]);
    }
  }).slider("value", 2);
  $("#time_zoom_label").text("Timeline Resolution: Year");
  n = new Date();
  $("#timeline_selector").slider({
    range: true,
    min: settings.MIN_START_YEAR,
    max: n.getFullYear(),
    values: [settings.startdate, settings.enddate],
    slide: function(event, ui) {
      return $("#timeline_header").text("Timeline: " + ui.values[0] + "-" + ui.values[1]);
    },
    change: function(event, ui) {
      settings.startdate = ui.values[0];
      settings.enddate = ui.values[1];
      return makeTimeline(settings.startdate, settings.enddate, settings.resolution_values[$("#time_zoom_slider").slider("value")]);
    }
  });
  return $("#timeline_header").text("Timeline: " + settings.startdate + "-" + n.getFullYear());
};
$(function() {
  $("body").height($(window).height());
  progressBar();
  return $.getJSON("/group/mappingmilitants/cgi-bin/maps/jsondata/3", function(data) {
    $("#progress_bar").progressbar("value", 70);
    settings.startdate = parseInt(data.Map.startyear, 10);
    settings.enddate = parseInt(data.Map.endyear, 10);
    settings.zooms = data.Map.zooms;
    setUpControls(settings.zooms);
    $("#progress_bar").progressbar("value", 80);
    setUpTimeline(settings.startdate, settings.enddate);
    $("#progress_bar").progressbar("value", 90);
    setUpMapArea(data.groups, data.links, data.umbrellas, settings.startdate, settings.enddate);
    $(".toggle_checkbox.start_unchecked").prop("checked", false).each(function() {
      return $("." + $(this).attr("data-class"), "#map_container").addClass("settings_inactive").fadeOut(settings.ANIMATION_SPEED);
    });
    $("#progress_bar").progressbar("value", 100);
    $('#progress_dialog').delay(200).dialog('destroy');
    return zoomGeographic(0);
  });
});