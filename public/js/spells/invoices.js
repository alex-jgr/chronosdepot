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
    
    $("#invoice-lines-table").html(Mustache.render($("#worklogs-" + $("#template").val() + "-table-template").html(), {worklogs: worklogs, project: $("#project-id").val()}));
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