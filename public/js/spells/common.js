var preventParentProgressUpdate = false;
$(document).ready(function(){
    // I use this to trigger a click on a page element when another one is clicked. Example: a button would trigger a click on a hidden file input (because file inputs look so bad and can't be properly styled)
    $(document.body).on("click", ".divert", function(){
        $($(this).data("divert")).trigger($(this).data("event"));
    });
   
    $(".operations .menu-item").click(function(){
        if ($("#selected-home-panel").val() !== "go-home") {
            $("#selected-home-panel").val($(this).data("selection"));;
            $(".home-panel").hide();
            $($(this).data("target")).show();
            $(".operations .menu-item").removeClass("active");
            $(".operations .menu-item." + $("#selected-home-panel").val()).addClass("active");
            if ($("#selected-home-panel").data("change_url")) {
                window.history.replaceState({"html":document.html,"pageTitle":document.pageTitle},"", "/user/home/" + $(this).data("selection"));
            }
        }
    });
    if ($("#selected-home-panel").val()) {
        $(".operations .menu-item." + $("#selected-home-panel").val()).click();
    }
    
    function createStopwatch(element) {
        $(element).stopwatch({
            onStart: function(id){
                $.post("/project/start_worklog/" + id, {}, function(response){
                    if (response.error) {
                        dialogMessage(response);
                    } else {
                        if (response.previous_time) {
                            var now = new Date();
                            ;
                            var previous_work = parseInt($("#task-timer-" + response.worklog.task_id).stopwatch("elapsed")) + (now.getTime()/1000 - parseInt(response.worklog.start_time));
                            $("#task-timer-" + response.worklog.task_id).stopwatch("elapsed", previous_work);
                        }
                        $("#page-icon").attr("href", "/public/img/timer_running.ico");
                    }
                }, "json");
            },
            onStop : function(data){
                if (!$("#task-timer-" + data.id).stopwatch("getInstanceCount")) {
                    console.log($("#task-timer-" + data.id).stopwatch("getInstanceCount"));
                    $("#page-icon").attr("href", "/public/img/timer_icon.ico");
                } else {
                    console.log($("#task-timer-" + data.id).stopwatch("getInstanceCount"));
                }
                $.post("/project/stop_worklog/" + data.id, {}, function(response){
                    if (response.error) {
                        dialogMessage(response);
                    } else {
                        response.worklog.time = secondsToTime(response.task.work);
                        response.worklog.current_time = secondsToTime(response.worklog.duration);
                        var countdown = $("#task-timer-" + response.task.id).stopwatch("countdown");
                        $("#task-timer-" + response.task.id).stopwatch("countdown", true);
                        response.worklog.remaining_time = $("#task-timer-" + response.task.id).stopwatch("getTimeValue");
                        $("#task-timer-" + response.task.id).stopwatch("countdown", countdown);
                        isTaskComplete(response);
                        $("#progress-edit").val(response.task.progress);
                        $("#task-timer-" + response.task.id).stopwatch("elapsed", response.task.work);
                        $("#timer-" + response.task.id).val(response.worklog.remaining_time);
                        $("#limit-edit").val(response.task.estimate);
                        $("#is-task-complete").css("z-index", 100001);
                    }
                }, "json");
            },
            onLimitReached: function(id){
                if (!$("#timer-" + id).hasClass("danger")) {
                    $("#timer-" + id).addClass("danger");
                }
            },
            onCreate: function(id) {
                if (parseInt($("#task-timer-" + id).data("limit")) < parseInt($("#task-timer-" + id).data("elapsed"))) {
                    $("#timer-" + id).addClass("danger");
                }
            }
        });
        
        if ($(element).data("active")) {
            $("#start-" + $(element).data("id")).click();
        }
    }
    
    $(".task-timer").each(function(){
        createStopwatch(this);
    });
    
    function isTaskComplete(worklog)
    {
        $("#task-stop-options").html(Mustache.render($("#worklog-edit-template").html(), worklog));
        $("#is-task-complete").modal('show');
    }
    
    $("#task-stop-options").on("keyup", "#duration-edit", function(){
        var time = ($(this).val()).split(":");
        if (isNumber(time[0]) && isNumber(time[1]) && isNumber(time[2])) {
            var seconds = parseInt(time[2]) + parseInt(time[1]) * 60 + parseInt(time[0]) * 3600;
            $("#time-edit").val(seconds);
            $("#remaining-time").val(secondsToTime(parseInt($("#limit-edit").val()) - seconds));
        }
    });
    
    $("#task-stop-options").on("keyup", "#worklog-time", function(){
        var value = $(this).val();
        var sign  = (value.indexOf("-") === 0);
        
        if (sign) {
            value = value.substring(1);
            sign = -1;
        } else {
            sign = 1;
        }
        
        var time = (value).split(":");
        
        if (isNumber(time[0]) && isNumber(time[1]) && isNumber(time[2])) {
            var seconds = sign * (parseInt(time[2]) + parseInt(time[1]) * 60 + parseInt(time[0]) * 3600);
            $("#worklog-time-edit").val(seconds);
            
            var timeDif         = seconds - parseInt($("#reference-worklog-time").val());
            var totalDuration   = timeDif + parseInt($("#reference-task-time").val());
            
            $("#duration-edit").val(secondsToTime(totalDuration));
            $("#time-edit").val(totalDuration);
            $("#remaining-time").val(secondsToTime(parseInt($("#limit-edit").val()) - totalDuration));
        }
    });
    
    /**
     * Handles the stopping of the worklog
     */
    $("#task-stop-options").on("click", ".stop-worklog", function(){
        var worklog_id  = $("#worklog-edit").val();
        var taskStatus  = $(this).data("status");
        var time        = ($("#duration-edit").val()).split(":");
       
        var seconds     = parseInt(time[2]) + parseInt(time[1]) * 60 + parseInt(time[0]) * 3600;
        var progress    =  $("#progress-edit").val();
        $("#time-edit").val(seconds);
        
        $.post(
                "/project/edit_worklog/" + worklog_id, 
                {
                    status  : taskStatus,
                    note    : $("#note-edit").val(),
                    duration: $("#time-edit").val(),
                    progress: progress,
                    work:     $("#worklog-time-edit").val()
                },
                function (response) {
                    if (response.success) {
                        $("#task-timer-" + response.task.id).stopwatch("elapsed", response.task.work);
                        if (response.task.status === "finished") {
                            $("#task-timer-" + response.task.id).stopwatch("remove");
                            $("#task-footer-" + response.task.id).html(Mustache.render($("#finished-task-control").html(), response.task));
                            $("#tasks-container #task-heading-" + response.task.id).addClass("finished");
                        }
                        $("#readable-task-duration-" + response.task.id).html(secondsToTime(response.task.work, true));
                        $("#is-task-complete").modal('hide');
                        if (preventParentProgressUpdate) {
                            updateProgress(response.task.id, response.task.progress);
                        } else {
                            updateProgress(response.task.id, response.task.progress, response.parent_progress);
                        }
                        
                    }
                }, "json"
        );
    });

    
    /**
     * Handles the reenabling of the tasks
     */
    $("#tasks-container").on("click", ".reenable-task", function(){
        var taskData = {
            id           :  $(this).data("id"),
            estimate     :  $(this).data("limit"),
            work         :  $(this).data("elapsed"),
            has_children :  $(this).data("has_children"),
            countdown    :  $(this).data("countdown")
        };
        $.post('/project/task_enable', taskData, function(response) {
            if (response.success) {
                $("#task-footer-" + taskData.id).html(Mustache.render($("#ongoning-task-control").html(), response.task));
                if (!parseInt(taskData.has_children)) {
                    var element = $("#tasks-container #task-timer-" + taskData.id);
                    createStopwatch(element);
                }
                $("#tasks-container #task-heading-" + taskData.id).removeClass("finished");
                $("#tasks-container #task-box-" + taskData.id).removeClass("finished");
                $("#tasks-container #task-heading-" + taskData.id).addClass("ongoing");
                $("#tasks-container #task-box-" + taskData.id).addClass("ongoing");
                
                updateProgress(taskData.id, response.task.progress, response.parent_progress);
                
            } else {
                if (response.error) {
                    dialogMessage(response);
                }
            }
        }, 'json');
        
    });
    
    
    $("#home-notifications, #header-notifications").on("click", ".pending", function() {
        var id = $(this).data("id");
        $.post("/user/view_notification",{
            id: id
        }, function(response){
            if (response.success) {
                $("#notification-" + id).attr("class", response.status);
                $("#h-notification-" + id).attr("class", response.status);
                $("#remaining-notifications").html(response.remaining);
                if (parseInt(response.remaining) > 0) {
                    $("#page-title").html("Chronos Depot ( " + response.remaining + " )");
                } else {
                    $("#page-title").html("Chronos Depot");
                }
            }
        }, "json");
    });
    
    /**
     * Setting up the off-canvas menu
     */
    $('[data-toggle="offcanvas"]').click(function () {
        var targetElement = $(this).data("target");
        var target = ".row-offcanvas";
        if (targetElement) {
            target = targetElement + target;
        }
        targetElement = targetElement.replace("#","");
        
        var activate    = !$(target).hasClass("active");
        var deactivate  = ((parseInt($(target).css("z-index")) === 99999) && $(target).hasClass("active"));
        var focus       = (parseInt($(target).css("z-index")) !== 99999) && $(target).hasClass("active");
        
        if (activate || deactivate) {
            $(target).toggleClass("active");
        }
        
        if (activate || focus) {
            var incs = 0;
            
            $(".row-offcanvas").each(function(){
                if ($(this).attr("id") !== targetElement) {
                    incs ++;
                    $(this).css("z-index", (99999 - incs));
                } else {
                    $(this).css("z-index", 99999);
                }
            });
            
            if (targetElement === 'active-tasks-container' && (!$("#tasks-container").hasClass("in"))) {
                $("#tasks-container").addClass("in");
            }
        } 
        if (deactivate) {
            var incs = 0;
            $(".row-offcanvas").each(function() {
                if ($(this).attr("id") !== targetElement) {
                    incs ++;
                    $(this).css("z-index", parseInt($(this).css("z-index")) + 1);
                }
            });
            $(target).css("z-index", (99999 - incs));
        }
    });
    
    $("#sidebar .menu-item").click(function(){
        var targetElement = $(this).data("target");
        $('[data-toggle="offcanvas"]').click();
    });
    
        
    $("select").each(function(){
        if ($(this).data("selected_value")) {
            $(this).val($(this).data("selected_value"));
        } else {
            if ($(this).data("default_value")) {
               $(this).val($(this).data("default_value"));
            }   
        }
    });
    
    $("#task-goal-filter").keyup(function(){
        var keywords = $(this).val();
        var status = $("#task-status-filter").val();
        filterTasks(keywords, status);
    });
    
    $("#task-status-filter").change(function(){
        var status = $(this).val();
        var keywords = $("#task-goal-filter").val();
        filterTasks(keywords, status);
    });
    
    function filterTasks(keywords, status) {
        if (keywords.length) {
            $("#tasks-container .task-element").hide();
            $("#tasks-container").find(".task-element").each(function(){
                if (($(this).data("goal").toLowerCase().indexOf(keywords.toLowerCase()) !== -1) && ((($(this).hasClass(status)) && status !== "all") || (status === "all"))) {
                    $(this).show();
                }
            });
        } else {
            switch (status)
            {
                case "all": $("#tasks-container .task-element").show(); break;
                case "ongoing": {
                        $("#tasks-container .task-element").hide();
                        $("#tasks-container .task-element.ongoing").show();
                } break;
                case "finished": {
                        $("#tasks-container .task-element").hide();
                        $("#tasks-container .task-element.finished").show();
                } break;
            }
        }
    }
    
    $("#search-whole-project").click(function(){
        $("#global-search-keywords").val($("#task-goal-filter").val());
        $("#global-search-status").val($("#task-status-filter").val());
        $("#global-search").submit();
    });
    
       $("#modals-bottom").append($("#modals-container").html());
       $("#modals-container").remove();
   
});
       
function updateProgress(id, progress, parent_progress) {
    if (id) {
        $("#task-box-" + id + " .progress-bar").html(progress + "%");
        $("#task-box-" + id + " .progress-bar").css("width", progress + "%");
        $("#task-box-" + id + " .progress-bar").data("valuenow", progress);
        
        $("#task-progress-" + id).html(progress);
        $("#task-" + id).data("progress", progress);
    }
    if (parent_progress) {
        $("#parent-progress .progress-bar").html(parent_progress + "%");
        $("#parent-progress .progress-bar").css("width", parent_progress + "%");
        $("#parent-progress .progress-bar").data("valuenow", parent_progress);
    }
}
    
function secondsToTime(seconds, withoutSeconds) {
    var time, days, hours, minutes, sign;
    if (seconds < 0) {
        sign = '-';
        seconds = seconds * -1;
    } else {
        sign = '';
    }
    time    = new Date(seconds * 1000);
    days    = time.getUTCDate() - 1;
    hours   = time.getUTCHours() + 24 * days;
    minutes = time.getUTCMinutes();
    seconds = time.getUTCSeconds();
    if (withoutSeconds) {
        return sign + ((hours < 10) ? ("0" + hours) : hours) + ":" + ((minutes < 10) ? ("0" + minutes) : minutes);
    } else {
        return sign + ((hours < 10) ? ("0" + hours) : hours) + ":" + ((minutes < 10) ? ("0" + minutes) : minutes) + ":" + ((seconds < 10) ? ("0" + seconds) : seconds);
    }
}
    
function dialogMessage(response) {
    if (response.error || response.success || response.warining) {
        $("#messages-container").html(Mustache.render($("#messages-template").html(), response));
        $(".message-switch").click();
    }
}

    
function isNumber(number) {
    if ((!isNaN(number)) && (number.length)) {
        return true;
    } else {
        return false;
    }
}