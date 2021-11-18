$(document).ready(function(){
    $(".load-more, .next-page").click(function(){
        var page        = $(this).data("page");
        var clicked     = this;
        var per_page    = $("#per_page").val();
        var start_date  = $("#start-date").datepicker('getDate') / 1000;
        var end_date    = $("#end-date").datepicker('getDate') / 1000;
        
        
        $.post("/user/notifications", {
            page:       page,
            elements:   per_page,
            start_date: start_date,
            end_date:   end_date
        }, function(response){
            if (response.success) {
                if ($(clicked).hasClass("next") || $(clicked).hasClass("prev")) {
                    $("#home-notifications").html(Mustache.render($("#notifications-template").html(), response));
                } else {
                    $("#home-notifications").append(Mustache.render($("#notifications-template").html(), response));
                }
                
                updateButtons(response);
            }
        }, "json");
    });
    
    function updateButtons(response) {
        $("button.next-page.prev").data("page", response.pages.prev);
        $("button.next, button.load-more").data("page", response.pages.next);
        
        if (parseInt(response.pages.prev) === -1) {
            $("button.next-page.prev").addClass("disabled");
        } else {
            $("button.next-page.prev").removeClass("disabled");
        }
        
        if (parseInt(response.pages.max) === parseInt(response.pages.current)) {
            $("button.next, button.load-more").addClass("disabled");
        } else {
            $("button.next, button.load-more").removeClass("disabled");
        }
    }
    
    $("#start-date, #end-date").datepicker({ dateFormat: 'dd / mm - yy' });
    
    $("#per_page, #start-date, #end-date").change(function(){
        var per_page    = $("#per_page").val();
        var start_date  = $("#start-date").datepicker('getDate') / 1000;
        var end_date    = $("#end-date").datepicker('getDate') / 1000;
        
        $.post("/user/notifications", {
            elements:   per_page,
            start_date: start_date,
            end_date:   end_date
        }, function(response){
            if (response.success) {
                $("#home-notifications").html(Mustache.render($("#notifications-template").html(), response));
                updateButtons(response);
            }
        }, "json");
    });
});