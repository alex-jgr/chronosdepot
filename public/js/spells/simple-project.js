$(document).ready(function(){
   $(".kick-user-confirm").click(function(){
        $.post("/team/exclude", {
            user_id : $("#kick-user-id").val(),
            team_id : $("#team-id").val()
        }, function(response) {
            if (response.success) {
                $("#team-member-" + $("#kick-user-id").val()).replaceWith("");
                dialogMessage(response);
                $("#close-kick-modal").click();
            }
        }, "json");
    });
    $("#find-user").autocomplete({
        minLength: 3, 
        source: function(request, response) { 
            $.post("/user/ajax_search", { 
                email: request.term
            }, function(data) {
                response(data); 
            }, "json"); },
            select: function( event, ui ) {
                if (ui.item) {
                    $("#selected-user").attr("href", "/user/profile/" + ui.item.id);
                    $("#selected-user").html("User: #" + ui.item.id + ": " + ui.item.value);
                    $("#invite-user-id").val(ui.item.id);
                }
            },
            response: function(event, ui) {
                if (!ui.content.length) {
                    $("#selected-user").val("User not found. An email invitation will be sent if you add the user anyway.");
                    $("#invite-email").val($("#find-user").val());
                }
            }
    });
        
    $("#add-user-to-team").click(function(){
        $.post("/team/invite_request", {
            team_id : $("#team-id").val(),
            user_id : $("#invite-user-id").val(),
            email   : $("#invite-email").val()
        }, function(response){
            if (response.success) {
                $("#team-requests").append(Mustache.render($("#team-request-template").html(), response));
                dialogMessage(response);
            }
        }, "json");
    });
    
    $("#request-team-join").click(function(){
        $.post('/team/join_request', {
            team_id: $(this).data("team_id")
        }, function(response){
            if (response.success) {
                dialogMessage(response);
                $("#request-team-join").replaceWith("Your request is pending");
            }
        }, "json");
    });
    $("#team-requests").on("click", ".action", function(){
        $.get("/team/accept_join/" + $(this).data("request"), 
        function(response) {
            if (response.success) {
                $("#team-request-" + response.request_id).replaceWith(null);
                $("#team-members-table").append(Mustache.render($("#team-member-template").html(), response));
            } else {
                dialogMessage(response);
            }
        }, "json");
    });
    
    $("#team-requests").on("click", ".cancel", function(){
        $.get("/team/cancel_request/" + $(this).data("request"),
        function(response){
            if (response.success) {
                $("#team-request-" + response.request_id).replaceWith(null);
            } else {
                dialogMessage(response);
            }
        },"json");
    }); 
});