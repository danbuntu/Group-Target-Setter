//    jQuery.fn.dataTableExt.oSort['title-string-asc'] = function(a, b) {
//        var x = a.match(/title="(.*?)"/)[1].toLowerCase();
//        var y = b.match(/title="(.*?)"/)[1].toLowerCase();
//        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
//    };

    $(document).ready(function () {
        var oTable = $('#example').dataTable();
//        new FixedHeader(oTable);
    });

$(function() {
    $( "#accordion" ).accordion({
        collapsible: true
    });
});

$(function(){
    $('#multiOpenAccordion').multiAccordion({ active: false });
});


    // Drive the checkboxes to hide the unit detials
     $(window).load(function(){
  $("input:checkbox:not(:checked)").each(function() {
    var column = "table ." + $(this).attr("name");
    $(column).hide();
});

$("input:checkbox").click(function(){
    var column = "table ." + $(this).attr("name");
    $(column).toggle();
});
  });


// Load the script to run the pop up box
    // increase the default animation speed to exaggerate the effect
    $.fx.speeds._default = 1000;
    $(function() {
        $("#dialog").dialog({
            autoOpen: false,
            width: 600,
            height: 700
        });

        $(".addNew").click(function() {
            $("#dialog").dialog("open");
            return false;
        });
    });