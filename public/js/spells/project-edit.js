$(document).ready(function() {

    
    ($("#listbox-positions-list").find(".lbjs-item.selected")).each(function(index){
        positions.push($(this).data("value"));
    });
    
    $("#listbox-positions-list").on("click",".lbjs-item",function(){
        var task_type_id = $(this).data("value");
        
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
        
        if ($(this).hasClass("selected")) {
            positions.push($(this).data("value"));
        } else {
            var index = positions.indexOf($(this).data("value"));
            positions.splice(index, 1);
        }
    });
    
    $("#save-positions").click(function(){
        $.post("/project/positions", {
            positions: positions
        },
        function(response){
            
        });
    });
    
    $(".project-teams-list").on("click", ".lbjs-item", function(){
                
        if ($(this).hasClass("selected")) {
                $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
        updateTeamInputs();
    });
    
    function updateTeamInputs() {
        var teams = [];
        var selectedTeams = $(".project-teams-list").find(".selected");
        if (selectedTeams.length) {
            $(selectedTeams).each(function(index){
                var team_id     = parseInt($(this).data("team_id"));
                var team        = {id: team_id};
                teams.push(team);
                $("#hidden-teams-inputs").html(Mustache.render($("#assigned-teams-template").html(), {teams: teams}));
            });
        } else {
            $("#hidden-teams-inputs").html(null);
        }
    }
    
    $("#currency_id").change(function(){
        $(".wage-currency").html($("#currency_id option[value='" + $(this).val() + "']").text());
    });
    $(".wage-currency").html($("#currency_id option[value='" + $("#currency_id").val() + "']").text());
    
    $("#update-user-wages").click(function() {
        var wages = [];
       
        $(".user-wage").each(function(){
            var object = {
                wage_id     : $(this).data("wage_id"),
                wage        : $(this).val(),
                user_id     : $(this).data("user_id")
            };
            wages.push(object);
        });
        
        var data = {
            project_id: $("#project-id").val(),
            wages: wages
        };
        
        $.post(
                "/project/set_wages",
                data,
                function(response){
                    $(".hourly-wages").modal("hide");
                    dialogMessage(response);
                }, 
                "json"
        );
    });
    
    $("#project-invites").on("click", ".cancel", function(){
        $("#exclude-project-contact-name").html($(this).data("user"));
        $("#exclude-project-contact-id").val($(this).data("id"));
        $(".exclude-contact-modal").modal("show");
    });
    
    $("#project-contacts").on("click", ".cancel", function(){
        $("#exclude-project-contact-name").html($(this).data("user"));
        $("#exclude-project-contact-id").val($(this).data("id"));
        $(".exclude-contact-modal").modal("show");
    });
    
    $("#confirm-exclude-project-contact").click(function(){
        var contact_id = $("#exclude-project-contact-id").val();
        $.get("/project/cancel_contact/" + contact_id,
            function(response){
                if (response.success) {
                    $("#" + response.type + "-" + contact_id).replaceWith(null);
                    $(".exclude-contact-modal").modal("hide");
                }
            },
            "json"
        );
    });
    
    $("#invite-contact-button").click(function(){
        $.post("/project/invite_contact/" + $("#project-id").val(), 
            {email: $("#invite-contact-email").val()},
            function(response){
                if (response.success) {
                    $("#project-invites").html(Mustache.render($("#project-invite-template").html(), response));
                }
            },
            "json"
        );
    });
    $("#project-wage").change(function(){
        $("#past-inpact").show();
    });
});
