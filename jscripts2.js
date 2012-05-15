$(function() {
    $('#accordion').accordion({
        collapsible: true
    });
});

$(function(){
    $('#multiOpenAccordion3').multiAccordion({ active: false });
});

$(function() {
 $("#datepicker").datepicker({
     dateFormat: 'dd-mm-yy',
     changeMonth: true,
     changeYear: true
 });
 // tl is the default so don't bother setting it's positio
 });

     $(document).ready(function () {
        var oTable = $('#example').dataTable({
            "aoColumns": [
                null,
                null,
                { "sType": 'string' },
                null,
                { "sType": 'numeric' },
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                { "sType": 'numeric' },
                { "sType": 'numeric' },
                { "sType": 'string' },
                { "sType": 'numeric' },
                { "sType": 'string' },
                { "sType": 'numeric' },
                { "sType": 'string' },
                { "sType": 'string' },
                { "sType": 'string' },
                { "sType": 'string' },
                { "sType": 'string' },
                { "sType": 'string' },
                null
            ],
            "sDom": '<"H"Rfr>t<"F"i>',

//        "sDom": '<"clear"lfr>t<ip>',
            "bJQueryUI": true,
            "bPaginate": false
        });

    });

 $(function() {
     //initially hide the textbox
     $("#target_name").hide();
     $("#target_name_title").hide();
     $("#datepicker").hide();
     $("#datepicker_title").hide();
     $("#rag").hide();
     $("#rag_title").hide();
     $("#details").hide();
     $("#details_title").hide();
     $("#checkbox").hide();
     $("#checkbox_title").hide();
     $("#medals_div").hide();
     $("#medals_title").hide();
     $("#medal").hide();
     $("#employability").hide();
     $("#save").hide();
     $('#select_review').change(function() {
         if ($(this).find('option:selected').val() == "Target") {
             $("#target_name").show();
             $("#datepicker").show();
             $("#datepicker_title").show();
             $("#target_name_title").show();
             $("#rag").hide();
             $("#rag_title").hide();
             $("#details").show();
             $("#details_title").show();
             $("#checkbox").show();
             $("#checkbox_title").show();
             $("#employability").hide();
             $("#save").show();
         } else if ($(this).find('option:selected').val() == "RAG - Traffic Light") {
             $("#rag").show();
             $("#rag_title").show();
             $("#target_name").hide();
             $("#datepicker").hide();
             $("#target_name_title").hide();
             $("#datepicker_title").hide();
             $("#details").show();
             $("#details_title").show();
             $("#checkbox").hide();
             $("#checkbox_title").hide();
             $("#medals_div").hide();
             $("#employability").hide();
             $("#save").show();

         } else if ($(this).find('option:selected').val() == "Progression Targets") {
             $("#rag").hide();
             $("#rag_title").hide();
             $("#target_name").hide();
             $("#datepicker").show();
             $("#target_name_title").hide();
             $("#datepicker_title").show();
             $("#details").show();
             $("#details_title").show();
             $("#checkbox").hide();
             $("#checkbox_title").hide();
             $("#medals_div").hide();
             $("#employability").hide();
             $("#save").show();

         }  else if ($(this).find('option:selected').val() == "Medals") {

             $("#medals_div").show();
             $("#target_name").hide();
             $("#datepicker").hide();
             $("#target_name_title").hide();
             $("#datepicker_title").hide();
             $("#details").hide();
             $("#details_title").hide();
             $("#checkbox").hide();
             $("#checkbox_title").hide();
             $("#rag").hide();
             $("#rag_title").hide()
             $("#employability").hide();
             $("#save").show();
         } else if ($(this).find('option:selected').val() == "Employability Passport") {

                      $("#medals_div").hide();
                      $("#target_name").hide();
                      $("#datepicker").hide();
                      $("#target_name_title").hide();
                      $("#datepicker_title").hide();
                      $("#details").hide();
                      $("#details_title").hide();
                      $("#checkbox").hide();
                      $("#checkbox_title").hide();
                      $("#rag").hide();
                      $("#rag_title").hide()
             $("#employability").show();
                      $("#save").show();
                  }   else {
             $("#target_name").hide();
             $("#datepicker").hide();
             $("#target_name_title").hide();
             $("#datepicker_title").hide();
             $("#rag").hide();
             $("#rag_title").hide();
             $("#details").show();
             $("#details_title").show();
             $("#checkbox").show();
             $("#checkbox_title").show();
             $("#medals_div").hide();
             $("#employability").hide();
             $("#save").show();
         }
     });

 });

    // increase the default animation speed to exaggerate the effect
    $.fx.speeds._default = 1000;
    $(function() {
        $("#dialog").dialog({
            autoOpen: false,
            width: 600,
            show: "blind",
            hide: "blind"
        });

        $("#opener").click(function() {
            $("#dialog").dialog("open");
            return false;
        });
    });

 <!-- Code to create the table that holds the student information and allows for sorting etc -->

//    jQuery.fn.dataTableExt.oSort['title-string-asc'] = function(a, b) {
//        var x = a.match(/title="(.*?)"/)[1].toLowerCase();
//        var y = b.match(/title="(.*?)"/)[1].toLowerCase();
//        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
//    };

    function toggleChecked(status) {
        $(".checkbox").each(function() {
            $(this).attr("checked", status);
        })
    }

$(document).ready(function()
{
$(".message_type").change(function()
{
var message_type=$(this).val();
var dataString = 'message_type='+ message_type;

$.ajax
({
type: "POST",
url: "ajax_message.php",
data: dataString,
cache: false,
success: function(html)
{
$(".message").html(html);
}
});

});

});
