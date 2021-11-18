$(document).ready(function(){
    $(".clone-template").click(function(){
        var data = $.parseJSON($("#template-" + $(this).data("id")).val());
        $(Object.keys(data)).each(function(){
            if (this.indexOf("id") === -1) {
                $("#" + this).val(data[this]);
            }
        });
        window.history.replaceState({"html":document.html,"pageTitle":document.pageTitle},"", "/invoice/template");
        $("#action-heading").html("Create new invoice template");
    });
    
    $(".edit-template").click(function(){
        var data = $.parseJSON($("#template-" + $(this).data("id")).val());
        $(Object.keys(data)).each(function(){
            $("#" + this).val(data[this]);
        });
        window.history.replaceState({"html":document.html,"pageTitle":document.pageTitle},"", "/invoice/template/" + data["id"]);
        $("#action-heading").html("Edit template: " + data["name"]);
    });
});