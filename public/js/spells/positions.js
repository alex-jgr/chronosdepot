    var positions  = [];
    var task_types = [];
    var unsaved_changes = false;
    
$(document).ready(function(){   
      
    function selectedPosition()
    {
        return $("#listbox-positions-list").find(".lbjs-item.selected").data("value");
    }
    
    function initializePositions() {
        $.post("/position/get_tasks_and_positions",{},
            function(response) {
                positions  = response.positions;
                task_types = response.task_types;
                var current_position = selectedPosition();
                $("#listbox-positions-list .lbjs-item.item-" + current_position).click();
                
        }, "json");
    }    

    
    $("#listbox-task-types-list").on("click",".lbjs-item",function(){
        var task_type_id = $(this).data("value");
        
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        } else {
            $(this).addClass("selected");
        }
        
        if ($(this).hasClass("selected")) {
            positions[selectedPosition()]["task_types"].push(task_types[task_type_id]);
        } else {
            var index = positions[selectedPosition()]["task_types"].indexOf(task_types[task_type_id]);
            positions[selectedPosition()]["task_types"].splice(index, 1);
        }
        unsaved_changes = true;
        console.log(positions);
    });
    
    $("#listbox-positions-list").on("click", ".lbjs-item", function(){
        
        $("#listbox-positions-list .lbjs-item").removeClass("selected");
        $(this).addClass("selected");
               
        $("#listbox-task-types-list .lbjs-item").removeClass("selected");
        $(positions[selectedPosition()]["task_types"]).each(function(index){
            $("#listbox-task-types-list .lbjs-item.item-" + this.id).addClass("selected");
        });
    });

     
    $("#edit-position").click(function(){
        var selected_positions = $("#listbox-positions-list").find(".lbjs-item[selected]");
        console.log(selected_positions.data("value"));
    });
    
    $("#edit-task-type").click(function(){
        var selected_task_types = $("#listbox-task-types-list").find(".lbjs-item[selected]");
        console.log(selected_task_types.data("value"));
    });
   
    $("#save-task-type").click(function(){
        var task_type_id = $("#task-type-id").val();
        $.post("/position/task_type",{
            id:             task_type_id,
            name:           $("#task-type-name").val(),
            description:    $("#task-type-description").val()
        },
        function(response){
            if (!parseInt(task_type_id)) {
                task_types[response.task_type.id] = response.task_type;
                $("#listbox-task-types-list").append("<div class=\"lbjs-item item-" + response.task_type.id + " \" data-value=\"" + response.task_type.id + "\">" + response.task_type.name +"</div>");
                $("#task-type-id").val(response.task_type.id);
            } else {
                task_types[task_type_id] = response.task_type;
                $("#listbox-task-types-list .lbjs-item.item-" + task_type_id).html(response.task_type.name);
                               
                $(positions).each(function(position_index){
                   $(this.task_types).each(function(task_type_index){
                       if (parseInt(this.id) === parseInt(response.task_type.id)) {
                            var index = positions[position_index]["task_types"].indexOf(task_types[response.task_type.id]);
                            if (index >= 0) {
                                positions[selectedPosition()]["task_types"][index] = response.task_type;
                            }
                       }
                   });
                });
            }
        }, "json");
        
    });
    
    $("#save-position").click(function(){
        var position_id = $("#position-id").val();
        $.post("/position/position", {
            id:             position_id,
            name:           $("#position-name").val(),
            description:    $("#position-description").val()
        }, 
        function(response) {
            if (!parseInt(position_id)) {
                positions[response.position.id] = response.position;
                $("#listbox-positions-list").append("<div class=\"lbjs-item item-" + response.position.id + " \" data-value=\"" + response.position.id + "\">" + response.position.name +"</div>");
            } else {
                positions[response.position.id] = response.position;
                $("#listbox-positions-list .lbjs-item.item-" + position_id).html(response.position.name);                
            }
        }, "json");
    });
    
    $("#select-edit-position").change(function(){
        var id = $(this).val();
        if (parseInt(id)) {
            $("#position-id").val(id);
            $("#position-name").val(positions[id].name);
            $("#position-description").val(positions[id].description);
        } else {
            $("#position-id").val(null);
            $("#position-name").val(null);
            $("#position-description").val(null);
        }
    });
    
    $("#select-edit-task-type").change(function(){
        var id = $(this).val();
        if (parseInt(id)) {
            $("#task-type-id").val(id);
            $("#task-type-name").val(task_types[id].name);
            $("#task-type-description").val(task_types[id].description);
        } else {
            $("#task-type-id").val(null);
            $("#task-type-name").val(null);
            $("#task-type-description").val(null);
        }
    });
    
    function saveChanges(){
        $.post("/position/save_changes", 
        {positions: positions},
        function(response){
            //initializePositions();
        }, "json");
        
        unsaved_changes = false;
    }

    $("#save-changes").click(function(){
        saveChanges();
    });
    
    initializePositions();
});