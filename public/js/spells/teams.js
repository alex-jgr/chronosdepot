var data_container  = [];

$(document).ready(function(){
    $("#team-members").on("click", ".task-types", function(){
        var user_id = $(this).data("user");
        $("#task-types-user-name").html($(this).data("name"));
        $("#manage-users-task-id").val(user_id);
        
        $("#listbox-assigned-task-types").html(Mustache.render($("#task-types-template").html(), data_container.members[findMemberIndex(user_id)]));
        $("#listbox-task-types-list").html(Mustache.render($("#task-types-template").html(), data_container));
        
        $(data_container.task_types).each(function(){
            if (!$("#listbox-task-types-list .item-" + this.id).hasClass("taken")) {
                $("#listbox-task-types-list .item-" + this.id).addClass("available");
            }
        });
        
        $(data_container.members[findMemberIndex(user_id)].task_types).each(function(index){
            $("#listbox-task-types-list .item-" + this.id).removeClass("available");
            $("#listbox-task-types-list .item-" + this.id).addClass("taken");
        });
        
        $("#listbox-task-types-list .taken").hide();
    });
    
    if ($("#team-id").val()) {
        $.post("/team/users_task_types",
                {
                    team_id : $("#team-id").val()
                },
                function(response){
                    data_container = response;
                    $("#listbox-task-types-list").html(Mustache.render($("#task-types-template").html(), response));
                }
                ,"json"
        );
    } else {
        console.log("No team id present.");
    }
            
    $("#listbox-task-types-list, #listbox-assigned-task-types").on("click", ".lbjs-item", function(){
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
    });
    
    $("#filter-task-types").change(function(){
        if ($(this).val() !== "all") {
            $("#listbox-task-types-list .lbjs-item").hide();
            $("#listbox-task-types-list .available.lbjs-item.position-" + $(this).val()).show();
        } else {
            $("#listbox-task-types-list .available.lbjs-item").show();
        }
    });
    
    function findTaskTypeIndex(id) {
        var task_type_index = -1;
        $(data_container.task_types).each(function(index){
            if (parseInt(this.id) === parseInt(id)) {
                task_type_index = index;
            }
        });
        return task_type_index;
    }
    
    function releaseMemberTaskType(member_index, task_type_id) {
        var member_task_type_index = -1;
        $(data_container.members[member_index].task_types).each(function(index){
            if (parseInt(this.id) === parseInt(task_type_id)) {
                data_container.members[member_index].task_types.splice(index, 1);
            }
        });
    }
    
     function findMemberIndex(id) {
        var member_index = -1;
        $(data_container.members).each(function(index){
            if (parseInt(this.id) === parseInt(id)) {
                member_index = index;
            }
        });
        return member_index;
    }
    
    $("#assign-task-type").click(function(){
        var memberIndex = findMemberIndex($("#manage-users-task-id").val());
        data_container.members[memberIndex].task_types = [];
        
        ($("#listbox-task-types-list").find(".selected")).each(function(index){
            var id = $(this).data("value");
            var name = $(this).html();
            
            var task_type_index = findTaskTypeIndex(id);
            
            data_container.members[memberIndex].task_types.push(data_container.task_types[task_type_index]);
            $("#listbox-assigned-task-types").append("<div class=\"lbjs-item item-" + id + "\" data-value=\"" + id + "\">" + name + "</div>");
            $("#listbox-task-types-list .item-" + id).removeClass("selected");
            $("#listbox-task-types-list .item-" + id).removeClass("available");
            $("#listbox-task-types-list .item-" + id).addClass("taken");
            $("#listbox-task-types-list .item-" + id).hide();
        });
    });
    
    $("#release-task-type").click(function(){
        var memberIndex = findMemberIndex($("#manage-users-task-id").val());
        
        ($("#listbox-assigned-task-types").find(".selected")).each(function(index){
            var id = $(this).data("value");
            var name = $(this).html();
            
            releaseMemberTaskType(memberIndex, id);
            
            $("#listbox-assigned-task-types .item-" + id).remove();
            $("#listbox-task-types-list .item-" + id).addClass("selected");
            $("#listbox-task-types-list .item-" + id).addClass("available");
            $("#listbox-task-types-list .item-" + id).removeClass("taken");
            $("#listbox-task-types-list .item-" + id).show();
            $("#filter-task-types").change();
        });
    });
    
    $("#save-task-types").click(function(){
        $("#save-task-types").hide();
        $.post("/team/save_user_task_types",
            {
                user_task_types: data_container.members[findMemberIndex($("#manage-users-task-id").val())]
            },
            function (response) {
                $("#save-task-types").show();
            }, "json"
        );
    });
    
    $("#team-members").on("click", ".kick", function(){
        $("#kick-user-name").html($(this).data("name"));
        $("#kick-user-id").val($(this).data("user"));
    });
    
    $(".kick-user").on("click", ".kick-user-confirm").click(function(){
        $.post("/team/exclude", {
            user_id : $("#kick-user-id").val(),
            team_id : $("#team-id").val()
        }, function(response) {
            if (response.success) {
                $("#team-member-" + $("#kick-user-id").val()).replaceWith("");
                dialogMessage(response);
                $(".kick-user").modal("hide");
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
                $("#team-invites").append(Mustache.render($("#team-request-template").html(), response));
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
    $(".team-requests").on("click", ".action", function(){
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
    
    $("#team-invites").on("click", ".cancel", function(){
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