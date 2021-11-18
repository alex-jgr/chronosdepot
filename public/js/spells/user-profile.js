$(document).ready(function(){
    $(".listbox-teams").on("click", ".add-to-team", function(){
        $.post("/team/invite_request", {
            team_id : $(this).data("team_id"),
            user_id : $("#user-id").val()            
        }, function(response){
            if (response.success) {                
                dialogMessage(response);
            }
        }, "json");
    });
});