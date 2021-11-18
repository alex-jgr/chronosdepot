$(document).ready(function(){
    $("#image").fileUpload({
        url: "/user/save_picture",
        input : "#image",
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
            $("#image-preview").html("<img class=\"thumbnail\" src=\"/public/images/users/normal/" + response.versions.normal.name + "\" alt/>");
            $("#image_id").val(response.image_id);
        }
    });
    
    $("#image").change(function(event){
        if (event.target.files.length) {
            $("#start-upload").removeClass("disabled");
            $("#image-file").html(event.target.files[0].name);
        } else {
            $("#image-file").html(null);
        }
    });
    
    $("#theme").change(function(){
        var color;
        switch ($(this).val()) {
            case "hack": color = "#222222"; break;
            default: color = "#ffffff"; break;
        }
        if (color) {
            $("#background").val(color);
            $("body").attr("style", "background: " + color);
            $("#background").attr("style", "background: " + color);
        }
        $("#selected-user-theme").attr("href","/public/css/themes/" + $(this).val() + ".css");
    });
    
    $("#user-settings-form").validate({
        rules: {
            username:{
                required: true,
                minlength: 1
            },
            password: {
                minlength: 5
            },
            re_password: {
                minlength: 5,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            username: {
                required: "Please provide a username",
                minlength: "Must be at least 1 character long"
            },
            password: {
                minlength: "Your password must be at least 5 characters long"
            },
            re_password: {
                equalTo: "Passwords do not match."
            },
            email: {
                required: "Please enter a valid email address",
                email: "It must be an email"
            }
        },
        errorClass: "error input-group-addon"
    });
    
    $("#background").colorpicker().on('changeColor.colorpicker', function(event){
        $("body").attr("style", "background: " +  event.color.toHex());
        $("#background").attr("style", "background: " +  event.color.toHex());
    });
    
    $("#date-format").find("option").each(function(){
        var format = $(this).data("format");
        $(this).html(moment().format(format)+" (" + format + ")");
    });
});