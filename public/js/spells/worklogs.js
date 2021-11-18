var worklogs;
var totalDuration = 0;
var date_format = $("#user-date-format").val();

$(document).ready(function() {
    
    worklogs = $.parseJSON($("#worklogs-json").val());
    
    updateTotals();
    
    $("#start-time, #end-time").datepicker({
        dateFormat: date_format
    });
    
    $("#date-filters").on("change","#start-time, #end-time",function(){
        var date = $(this).datepicker("getDate").getTime()/1000;
        switch($(this).attr("id")) {
            case "start-time": $("#start_date").val(date); break;
            case "end-time": $("#end_date").val(date); break;
        }
    });
    
    var start = $("#start_date").val();
    
    if (start) {
        $("#start-time").datepicker("setDate", moment(start * 1000).format(date_format));
    }
    
    var end = $("#end_date").val();
    
    if (end) {
        $("#end-time").datepicker("setDate", moment(end * 1000).format(date_format));
    }

    $(".selected-users").click(function(){
        var users = [];
        $("#users-filter").find(".selected-users").each(function(){
            if ($(this).prop("checked")) {
                users.push({user_id: parseInt($(this).val())});
            }
        });
        
        if (users.length) {
            $("#worklogs-table tr").hide();
            $(users).each(function(){
                $("#worklogs-table tr.user-" + this.user_id).show();
            });
        } else {
            $("#worklogs-table tr").show();
        }
        recalculateTotals();
    });
    
    $("#worklogs-table").on("click", ".update-worklog", function(){
        var id = $(this).data("id");
        
        if ($(this).data("state") === "open") {
            var value   = $("#edit-duration-" + id).val();
            var sign    = (value.indexOf("-") === 0);
            
            if (sign) {
                value = value.substring(1);
                sign = "-";
            } else {
                sign = "";
            }
            console.log(sign);
            var time    = (value).split(":");
            
            if (isNumber(time[0]) && isNumber(time[1]) && isNumber(time[2])) {
                var note    = $("#edit-note-" + id).val();
                var seconds = parseInt(time[2]) + parseInt(time[1]) * 60 + parseInt(time[0]) * 3600;
                
                $.post("/worklogs/update/"+id,{
                    duration: sign + seconds,
                    note: note
                },
                function(response) {
                    if (response.success) {
                        var index = findWorklogIndex(id);
                        worklogs[index].duration        = sign + seconds;
                        worklogs[index].human_duration  = sign + value;
                        worklogs[index].note            = note;
                        updateTotals();
                        //closeWorklog(id);
                    }
                }, "json");
            } else {
                $("#messages-container").html(Mustache.render($("#messages-template").html(), {
                    error: {
                        message: "Please use a valid format for the duration!"
                    }
                }));
                $("#error-message").modal("show");
            }
        } else {
            $(this).data("state", "open");
            $("#readonly-duration-" + id + ", #edit-duration-" + id + ", #readonly-note-" + id + ", #edit-note-" + id + ", #cancel-control-" + id).toggle();
            $("#edit-control-icon-" + id).removeClass("glyphicon-pencil");
            $("#edit-control-icon-" + id).addClass("glyphicon-floppy-save");
        }
    });
    
    function closeWorklog(id) {
        $("#edit-control-icon-" + id).removeClass("glyphicon-floppy-save");
        $("#edit-control-icon-" + id).addClass("glyphicon-pencil");
        $("#edit-control-" + id).data("state", "closed");
        $("#readonly-duration-" + id + ", #edit-duration-" + id + ", #readonly-note-" + id + ", #edit-note-" + id + ", #cancel-control-" + id).toggle();
    }
    
    $("#worklogs-table").on("click", ".cancel-worklog", function(){
        var id = $(this).data("id");
        closeWorklog(id);
    });
    
    $("#worklogs-table").on("click", ".delete-worklog", function(){
        var id = $(this).data("id");
        $("#confirm-worklog-delete-id").val(id);
        $("#delete-message").html("Are you sure that you want to delete the worklog for the task <strong>" + $(this).data("goal") + "</strong> from <i>" + $(this).data("started") + "</i> ?");
        $(".confirm-delete").modal("show");
    });
    
    $(".confirm-delete").on("click", "#confirm-worklog-delete-button", function(){
        var worklogId = $("#confirm-worklog-delete-id").val();
        $.get("/worklogs/delete/" + worklogId,
        function(response){
            if (response.success) {
                var index = findWorklogIndex(worklogId);
                worklogs.splice(index, 1);
                updateTotals();
                $(".confirm-delete").modal("hide");
            }
        }, "json");
    });
    
    $(".sum").click(function(){
        var sum = $(this).val();
        console.log(sum);
        if (sum !== "none") {
            if (sum === "task-types") {
                var taskTypes = [];
                $(worlogs).each(function(){
                    if (this.task_type) {
                        var task_type_name = this.task_type;
                        var taskTypeIndex = -1;
                        
                        /**
                         * TODO: CONTINUE THIS BLOCK!!! <<<=================================================================== Continue that block!!!!!
                         */
                        $(taskTypes).each(function(index) {
                            if (this.name == task_type_name) {
                                var taskTypeIndex = index;
                            }
                        });
                        if (taskTypeIndex >= 0) {
                            taskTypes[taskTypeIndex].duration += parseInt(this.duration);
                        } else {
                            taskTypes.push({});
                        }
                    }
                });
                $("#worklogs-table").html(Mustache.render($("#task-types-sum-table-template").html(), {}));
            } else {
                var tasks = [];
                $(worlogs).each(function(){
                    
                });
                $("#worklogs-table").html(Mustache.render($("#tasks-sum-table-template").html(), {}));
            }
        } else {
            $("#total-duration").html(secondsToTime(totalDuration));    
            $("#total-pay").html(Math.round(($("#project-wage").val()*100) * totalDuration/3600)/100);
            $("#worklogs-table").html(Mustache.render($("#worklogs-" + $("#template").val() +"-table-template").html(), {worklogs: worklogs, project: $("#project-id").val()}));
        }
    });
});

function findWorklogIndex(id) {
    var worklogIndex = -1;
    $(worklogs).each(function(index) {
        if (parseInt(worklogs[index].id) === parseInt(id)) {
            worklogIndex = index;
        }
    });
    return worklogIndex;
}

function updateTotals() {
    totalDuration = 0;
    $(worklogs).each(function(index){
        if ($("#worklog-" + this.id).css("display") !== "none") {
            var date = moment(new Date(parseInt(this.start_time) * 1000));
            var formatted = date.format(date_format + ", HH:mm");
            worklogs[index].human_start = formatted;
            totalDuration += parseInt(this.duration);
        }
    });
    
    $("#total-duration").html(secondsToTime(totalDuration));
    
    $("#total-pay").html(Math.round(($("#project-wage").val()*100) * totalDuration/3600)/100);
    
    $("#worklogs-table").html(Mustache.render($("#worklogs-" + $("#template").val() + "-table-template").html(), {worklogs: worklogs, project: $("#project-id").val()}));
}

function recalculateTotals() {
    totalDuration = 0;
    $(worklogs).each(function(index){
        if ($("#worklog-" + this.id).css("display") !== "none") {
            var date = moment(new Date(parseInt(this.start_time) * 1000));
            var formatted = date.format(date_format + ", HH:mm");
            worklogs[index].human_start = formatted;
            totalDuration += parseInt(this.duration);
        }
    });
    
    $("#total-duration").html(secondsToTime(totalDuration));
    
    $("#total-pay").html(Math.round(($("#project-wage").val()*100) * totalDuration/3600)/100);
}