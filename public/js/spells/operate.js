var teams;
var task = {};
preventParentProgressUpdate = true;
$(document).ready(function(){
    /**
     * In case the last child task of a certain parent has sub tasks and is open, then the new task will have to placed after the tree of this last task
     * This is why a position will have to be searched for recursively
     * 
     * @param {type} parent_id
     * @returns {new relative position to place the task in}
     */
    function getNewPosition(parent_id) {
        var tasks = $("#project-tasks").find(".child-of-" + parent_id);
        var last  = tasks.length;
        if (!$(tasks[last-1]).hasClass("open")) {
            return last;
        } else {
            return last + getNewPosition($(tasks[last-1]).data("id"));
        }
    }
    
    $("#post-controls").on("click", "#save", function(){
        
        var task_id = $("#task-id").val();
        
        var postData = {
            task_id:            task_id,
            project_id:         $("#project-id").val(),
            parent_id:          $("#parent-id").val(),
            grandparent_id:     $("#grandparent-id").val(),
            goal:               $("#goal").val(),
            task_type_id:       $("#task-type-id").val(),
            estimate_hours:     $("#estimate-hours").val(),
            estimate_minutes:   $("#estimate-minutes").val(),
            budget:             $("#budget").val(),
            description:        $("#description").val(),
            status:             $("#status").val()
        };
        
        $.post("/project/task", postData,
        function(response){
            if (response.success) {
                if (response.tasks.parent_id) {
                    if (!parseInt(postData.task_id)) {
                        if (!$("#task-"+ response.tasks.parent_id).find("button.expand-collapse").length || ($("#task-"+ response.tasks.parent_id + " button.expand-collapse").hasClass("open"))) {
                            var button= "<button class=\"btn btn-default pull-left expand-collapse open\" data-id=\"" + response.tasks.parent_id + "\" data-project_id=\"" + postData.project_id + "\" data-parent=\"" + postData.grandparent_id + "\"><i class=\"glyphicon glyphicon-chevron-down visible\"></i><i class=\"glyphicon hidden\">!</i><i class=\"loading\"></i></button>";
                            $("#task-" + response.tasks.parent_id + " .collapse-control").html(button);

                            var task_view = $.parseHTML(Mustache.render($("#task-row").html(), response));

                            var position_in_task = getNewPosition(response.tasks.parent_id);//parseInt($("#project-tasks").find(".child-of-" +response.tasks.parent_id).length);
                            var tasks            = $("#project-tasks").find(".task-item");
                            var parent_position  = parseInt(tasks.index($("#task-" + response.tasks.parent_id)));
                            
                            tasks.splice(parent_position + position_in_task +1, 0, task_view[1]);
                            $("#project-tasks").html(tasks);
                        } else {                            
                            $("#task-"+ response.tasks.parent_id + " button.expand-collapse").click();
                        }
                        
                    } else {
                        $("#project-tasks #task-" + postData.task_id).replaceWith(Mustache.render($("#task-row").html(), response));
                    }
                } else {
                    if (parseInt(postData.task_id)) {
                        if ($("#project-tasks #task-" + postData.task_id).hasClass("open")) {
                            response.tasks.is_open = true;
                        }
                        $("#project-tasks #task-" + postData.task_id).replaceWith(Mustache.render($("#task-row").html(), response));
                    } else {
                        $("#project-tasks").append(Mustache.render($("#task-row").html(), response));
                    }
                }
                updateTasksRecurring(response.tasks);
                updateRecurringProgress(response.tasks.id);
                $("#total-estimated-work").val(response.totals.total_estimate);
                $("#total-project-work").val(response.totals.total_work);
                $("#total-project-spendings").val(response.totals.total_spendings);
                $("#total-project-expenses").val(response.totals.total_expenses);
            }
            $(".create-task").modal("hide");;
            
            if (task_id) {
                $("#messages-container").html(Mustache.render($("#messages-template").html(), response));
                $(".message-switch").click();
            } else {
                $("#users-" + response.tasks.id).click();
            }
        }, "json");
    });
    
    function addFrontZeros(number){
        if (number < 10) {
            number = "0" + number;
        }
        return number;
    }
    
    function updateTasksRecurring(task) {
        if (task.id){
            var estimate_date_object    = new Date(parseInt(task.estimate) * 1000);
            task.estimate_hours         = addFrontZeros(estimate_date_object.getUTCHours());
            task.estimate_minutes       = addFrontZeros(estimate_date_object.getUTCMinutes());
            task.estimate               = task.estimate_hours + ":" + task.estimate_minutes;
            
            var duration            = new Date(parseInt(task.work) * 1000);
            task.duration_hours     = addFrontZeros(duration.getUTCHours());
            task.duration_minutes   = addFrontZeros(duration.getUTCMinutes());
            task.duration           = task.duration_hours + ":" + task.duration_minutes;
            
            switch (task.status) {
                case "ongoing":  task.icon = "eye-open";  break;
                case "disabled": task.icon = "eye-close"; break;
                case "finished": task.icon = "thumbs-up"; break;
            }
            
            if (($("#project-tasks #task-" + task.id).find("button.expand-collapse")).length) {
                task.has_children = true;
            }
            if (($("#project-tasks #task-" + task.id).find("button.expand-collapse.open")).length) {
                task.is_open = true;
            }
            
            var tasks = {tasks: [task]};

            $("#project-tasks #task-" + task.id).replaceWith(Mustache.render($("#task-row").html(), tasks));
            
            if (parseInt(task.parent_id)){
                updateTasksRecurring(task.parent);
            }
        }
    }
    
    $("#project-tasks").on("click", ".edit-task", function(){
        var data = $(this).data();
        $("#task-id").val(data.id);
        
        $.get("/project/task_data/" + data.id, function(data){
            $("#project-id").val(data.project_id);
            $("#parent-id").val(data.parent_id);
            $("#goal").val(data.goal);
            $("#task-type-id").val(data.task_type_id);
            $("#estimate-hours").val(data.estimate_hours);
            $("#estimate-minutes").val(data.estimate_minutes);
            $("#budget").val(data.budget);
            $("#description").val(data.description);
            $("#status").val(data.status);
            $(".create-task .panel-heading").html("Edit task: " + data.goal);
            $("#edit-task-users").data("id", data.id);
            $("#edit-task-users").data("goal", data.goal);
            $("#edit-users-button").show();
            $("#delete").removeClass("hidden");
        },"json");
    });
    
    $("#project-tasks").on("click", ".add-subtask", function(){
        var data = $(this).data();
        $("#task-id").val(null);
        $("#project-id").val(data.project_id);
        $("#parent-id").val(data.id);
        $("#grandparent-id").val(data.parent_id);
        $("#task-type-id").val(data.task_type_id);
        $("#goal").val(null);
        $("#estimate-hours").val(null);
        $("#estimate-minutes").val(null);
        $("#budget").val(null);
        $("#description").val(null);
        $(".create-task .panel-heading").html("Add a subtask to: " + data.goal);
        $("#delete").addClass("hidden");
        $("#status").val("ongoing");
        $("#edit-users-button").hide();
    });
    
    $("#post-controls").on("click", "#cancel", function(){
        $(".create-task").modal("hide");;
    });
    
    $("#add-task").click(function(){
        $("#task-id").val(null);
        $("#parent-id").val(null);
        $("#goal").val(null);
        $("#task-type-id").val(null);
        $("#estimate-hours").val(null);
        $("#estimate-minutes").val(null);
        $("#budget").val(null);
        $("#description").val(null);
        $("#status").val("ongoing");
        $(".create-task .panel-heading").html("Add a task");
        $("#delete").addClass("hidden");
        $("#edit-users-button").hide();
    });
    
    $("#project-tasks").on("click", ".expand-collapse", function(){
        var parent      = $(this).data("parent");
        var id          = $(this).data("id");
        var glyph       = $(this).find(".glyphicon.visible");
        var progress    = $("#task-" + id).data("progress");
        if ($(this).hasClass("open")) {
            
            var multiChildren = $("#project-tasks").find(".child-of-" + id + " button.open");
            
            $(multiChildren).each(function(index){
                $(this).click();
            });
            
            $(".child-of-" + id).replaceWith(null);
            $(this).removeClass("open");
            $("#task-" + id).removeClass("open");
            $(glyph).removeClass("glyphicon-chevron-down");
            $(glyph).addClass("glyphicon-chevron-right");
        } else {
            $(this).addClass("open");
            
            $(glyph).hide();
            $("#task-" + id + " .collapse-control .loading").show();
            
            $(glyph).removeClass("glyphicon-chevron-right");
            $(glyph).addClass("glyphicon-chevron-down");
            
            var childOf = "";
            if (parent) {
                childOf = " child-of-" +parent;
            }
            $("#task-" + id + " .collapse-control button i.visible").hide();
            $("#task-" + id + " .collapse-control button i.hidden").show();
            $.post("/project/subtasks",
                {
                    id: $(this).data("id"),
                    project_id: $(this).data("project_id")
                },
                function(response){
                    var status = $("#task-" + id).data("status");
                    var oldContent = "<div class=\"task-item" + childOf + " " + status + " open\" id=\"task-" + response.tasks[0].parent_id + "\" data-id=\"" + response.tasks[0].parent_id + "\" data-status=\"" + status + "\" data-progress=\"" + progress + "\" data-parent=\"" + parent + "\">" + $("#task-" + response.tasks[0].parent_id).html() + "</div>";
                    $(".child-of-" + response.tasks[0].parent_id).remove();
                    $("#task-" + response.tasks[0].parent_id).replaceWith(oldContent + Mustache.render($("#task-row").html(), response));
                    
                    $("#task-" + id + " .collapse-control button i.hidden").hide();
                    $("#task-" + id + " .collapse-control button i.visible").show();
                    $("#task-" + id + " .collapse-control .loading").hide();
                    $(glyph).show();
                },"json");
        }
    });
    
    $("#delete").click(function(){
        $("#delete-message").html("Are you sure that you want to delete this task?");
    });
    
    $("#project-tasks").on("click", ".delete-task-button", function(){
        $("#task-id").val($(this).data("id"));
        $("#delete-message").html("Are you sure that you want to delete this task?");
        $(".confirm-delete").modal("show");
    });
    
    $("#confirm-delete-button").click(function(){
        var task_id = $("#task-id").val();
        $.post("/project/delete_task", 
                {
                    id: task_id
                },
                function(response) {
                    $("#messages-container").html(Mustache.render($("#messages-template").html(), response));
                    $(".create-task").modal("hide");;
                    $(".message-switch").click();
                    if (response.success) {
                        updateTasksRecurring(response.task);
                        $("#total-estimated-work").val(response.totals.total_estimate);
                        $("#total-project-work").val(response.totals.total_work);
                        $("#total-project-spendings").val(response.totals.total_spendings);
                        $("#total-project-expenses").val(response.totals.total_expenses);
                
                        var parent_id = $("#task-" + task_id + " button.add-subtask").data("parent_id");

                        $("#task-" + task_id).remove();
                        
                        var has_children = $("#project-tasks").find(".child-of-" + parent_id);
                        
                        if (!(has_children.length)) {
                            $("#task-" + parent_id + " .collapse-control").html(null);
                        }
                        updateRecurringProgress(response.parent.id);
                        updateProgress(response.parent.id, response.parent.progress, response.totals.project_progress);
                    }
                    $(".confirm-delete").modal("hide");
                }, "json"
        );
    });
    $("#cancel-delete").click(function(){
        $(".confirm-delete").modal("hide");
    });
    
    function updateRecurringProgress(task_id) {
        var parent = $("#task-" + task_id).data("parent");
        if (parseInt(parent)) {
            var sum     = 0;
            var count   = 0;
            $(".child-of-" + parent).each(function(){
                sum += parseInt($(this).data("progress"));
                count ++;
            });
            var progress = Math.round(sum/count);
            $("#task-" + parent).data("progress", progress);
            $("#task-progress-" + parent).html(progress);
//            if (progress === 100) {
//                $("#task-" + parent).removeClass("ongoing");
//                $("#task-" + parent).removeClass("finished");
//                $("#task-" + parent).removeClass("disabled");
//                $("#task-" + parent).data("status", "finished");
//                $("#task-" + parent).addClass("finished");
//                $("#task-" + parent + " .change-status i").attr("class", "glyphicon glyphicon-thumbs-up");
//            }
            updateRecurringProgress(parent);
        }
    }
    
    /**********************************************************************************************************************
     * Change task status functionality
     */
    $("#project-tasks").on("click", ".change-status", function(){
        var id = $(this).data("id");
        $("#change-status-message").html("Change status for: #" + id + " " + $("#task-" + id + " .edit-task").data("goal"));
        $("#change-status-id").val(id);
    });
    
    $(".change-status-btn").click(function(){
        var id = $("#change-status-id").val();
        var status = $(this).data("status");
        $.post(
                "/project/task/change-status",
                {
                    task_id: id,
                    task_status: status
                },
                function(response) {
                    $("#messages-container").html(Mustache.render($("#messages-template").html(), response));
                    $(".change-status-modal").modal("hide");
                    $(".message-switch").click();
                    
                    if (response.icon && $("#task-" + id).hasClass("open")) {
                        $("#task-" + id + " .collapse-control button.expand-collapse").click();
                        $("#task-" + id).data("status", status);
                        $("#task-" + id + " .collapse-control button.expand-collapse").click();
                    } else {
                        $("#task-" + id).removeClass("ongoing");
                        $("#task-" + id).removeClass("finished");
                        $("#task-" + id).removeClass("disabled");
                        $("#task-" + id).addClass(status);
                    }
                    $("#task-" + id + " .change-status i").attr("class", "glyphicon glyphicon-" + response.icon);
                    updateProgress(id, response.task.progress, response.totals.project_progress);
                    updateRecurringProgress(response.task.id);
                }, "json"
        );
    });
    
    /*********************************************************************************************************************
     * Manage users
     */
    function updateUsersAssignmentsModal(id, goal) {
        $.get(
            "/project/task/users/" + id,
            function(response) {
                if (response.success) {
                    task.id     = id;
                    task.users = [];
                    $("#listbox-assigned-users-list").html(null);
                    
                    $("#listbox-available-users-list .lbjs-item").removeClass("taken");
                    $("#listbox-available-users-list .lbjs-item").addClass("available");
                    $("#listbox-available-users-list .lbjs-item").show();
                    $("#listbox-available-users-list .lbjs-item.available").show();
                    
                    if (response.users.length) {
                        $(response.users).each(function(index){
                            $("#listbox-assigned-users-list").append("<div class=\"lbjs-item item-" + this.id + "\" data-value=\"" + this.id + "\">" + this.username + "</div>");
                            task.users.push(parseInt(this.id));
                            $("#listbox-available-users-list .lbjs-item.item-" + this.id).removeClass("available");
                            $("#listbox-available-users-list .lbjs-item.item-" + this.id).addClass("taken");
                            $("#listbox-available-users-list .lbjs-item.item-" + this.id).hide();
                        });
                    }
                    $("#select-team").change(); //updating visibility based on selected team if any
                    $("#assign-users-task-id").html(id);
                    $("#assign-users-task-name").html(goal);
                }
            }, "json"
        );
    }
    
     $("#edit-task-users").click(function() {
        var id      = $(this).data("id");
        var goal    = $(this).data("goal");
        updateUsersAssignmentsModal(id, goal);
        $(".create-task").modal("hide");
        $(".manage-users").modal("show");
     });
    
    $("#project-tasks").on("click", "button.users", function() {
        var id      = $(this).data("id");
        var goal    = $(this).data("goal");
        updateUsersAssignmentsModal(id, goal);
    });

    $("#listbox-available-users-list, #listbox-assigned-users-list").on("click", ".lbjs-item", function(){
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
    });
    
    $("#assign-user").click(function(){
        $($("#listbox-available-users-list").find(".selected")).each(function(index){
            var id = parseInt($(this).data("value"));
            if (task.users.indexOf(id) === -1) {
                $(this).removeClass("selected");
                $(this).removeClass("available");
                $(this).addClass("taken");
                
                var username = $(this).html();
                $("#listbox-assigned-users-list").append("<div class=\"lbjs-item item-" + id +  " available\" data-value=\"" + id + "\">" + username + "</div>");
                $(this).hide();
                task.users.push(id);
            }
        });
    });
    
    $("#select-team").change(function(){
        if ($(this).val() !== 'none') {
            $("#listbox-available-users-list .lbjs-item").hide();
            $("#listbox-available-users-list .lbjs-item.available.team-" + $(this).val()).show();
        } else {
            $("#listbox-available-users-list .lbjs-item.available").show();
        }
    });
    
    $("#release-user").click(function(){
        var selected_team = $("#select-team").val();
        $($("#listbox-assigned-users-list").find(".selected")).each(function(index){
            var id = parseInt($(this).data("value"));
            task.users.splice(task.users.indexOf(id), 1);
            
            $("#listbox-available-users-list .item-" + id).removeClass("taken");
            $("#listbox-available-users-list .item-" + id).addClass("available");
            if (selected_team === 'none') {
                $("#listbox-available-users-list .item-" + id).show();
            } else {
                $("#listbox-available-users-list .team-" + selected_team + ".item-" + id).show();
            }
            $(this).replaceWith(null);
        });
    });
    
    $("#save-task-users").click(function(){
        $.post("/project/task/users/assign", {
                task_id: task.id,
                users:   task.users
            },
            function(response){
                if (response.success) {
                    dialogMessage(response);
                    $(".manage-users").modal("hide");
                } else {
                    dialogMessage(response);
                }
            },"json");
    });
    $("#confirm-default-assignments").click(function(){
        var project_id = $(this).data("project_id");
        $.post("/project/default_assignments",{
                project_id: project_id
            }, function(response) {
                if (response.success) {
                    $(".default-assignments").modal("hide");
                }
            },"json"
        );
    });
});