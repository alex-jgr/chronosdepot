$(document).ready(function(){
    $("#email").change(function(){
        $.post("/user/check", {
            email: $(this).val()
        }, function(response) {
            if (response.error) {
                $("#email-label").html("This email already exists.");
                $("#email-label").addClass("danger");
            }
        }, "json");
    });
    $("#signup-form").validate({
        rules: {
            email: {
                    required: true,
                    email: true
            },
            password: {
                    required: true,
                    minlength: 5
            },
            confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
            },
            agree: "required"
        },
	messages: {
		password: {
			required: "Please provide a password",
			minlength: "Your password must be at least 5 characters long"
		},
		confirm_password: {
			required: "Please provide a password",
			minlength: "Your password must be at least 5 characters long",
			equalTo: "Please enter the same password as above"
		},
		email: "Please enter a valid email address",
		agree: "Please accept our policy"
	}
    });
    $("#reset-password-button").click(function() {
        $.post("/user/reset", {email: $("#email-reset-password").val()},
            function(response) {
                dialogMessage(response);
                $("#reset-password").collapse("hide");
            },"json"
        );
    });
});