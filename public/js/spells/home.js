$(document).ready(function(){
    $(".leave-team-trigger").click(function(){
         //href="/team/leave/{{id}}"
         $("#leave-team-name").html($(this).data("name"));
         $("#leave-team-id").attr("href", "/team/leave/" + $(this).data("id"));
    });
});