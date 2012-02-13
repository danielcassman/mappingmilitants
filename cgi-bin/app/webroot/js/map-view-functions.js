var groupClickFunction, linkClickFunction;

linkClickFunction = function(link) {
  return function(e) {
    var opener;
    opener = this;
    if ($(opener).data("dialog_open")) return false;
    $(opener).data("dialog_open", true);
    return $("<div/>", {
      html: processDate(link.date, "e") + ": " + link.description
    }).dialog({
      modal: false,
      title: $("#group-" + link.group1).attr("data-shortname") + " and " + $("#group-" + link.group2).attr("data-shortname") + " " + getLinkType(link.type),
      width: 300,
      "min-height": 100,
      position: [e.pageX - $(window).scrollLeft() - 150, e.pageY - $(window).scrollTop() - 70],
      beforeClose: function() {
        return $(opener).data("dialog_open", false);
      }
    });
  };
};

groupClickFunction = function(group, buttons) {
  return function(e) {
    return $("<div/>", {
      html: group.description
    }).dialog({
      title: group.name,
      modal: true,
      resizable: false,
      draggable: false,
      width: 400,
      buttons: buttons
    });
  };
};
