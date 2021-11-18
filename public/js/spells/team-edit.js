$(document).ready(function(){
    $("#image").fileUpload({
        url:    "/team/save_picture",
        input:  "#image",
        uploadOn: {
            selector: "#start-upload",
            clientEvent: "click"
        },
        before: function() {
            $("#upload-status").addClass("uploading");
            $("#start-upload").addClass("disabled");
        },
        after: function() {
            $("#upload-status").removeClass("uploading");
        },
        onSuccess: function(response) {
            dialogMessage(response);
            $("#image-preview").html("<img class=\"thumbnail\" src=\"/public/images/teams/normal/" + response.versions.normal.name + "\" alt/>");
            $("#image_id").val(response.image_id);
        }
    });
    
    $("#image").change(function(event){
        if (event.target.files.length) {
            $("#start-upload").removeClass("disabled");
        }
    });
});