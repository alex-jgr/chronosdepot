(function ( $ ){
    $.fn.fileUpload = function( options ) {
        var files = [];
        var filesCheckInterval;
        var settings = $.extend({
            url:        "/upload.php",
            onSuccess:  function(response){
                console.log("Upload successful");
            },
            onError:    function(response){
                console.log("Upload failed");
            },
            before:     function() {},
            after:      function() {},
            responseType: "json",            
            container:  $(this).parent(),
            input:      "input[type=file]",
            uploadOn:   {
                        selector: "#file-upload", 
                        clientEvent: "click"
                    }
        }, options);
         
        $(settings.container).on("change", $(settings.input), function(event){
            var input_name = $(event.target).attr("name");
            
            $ (event.target.files).each(function(index, value) {
                files.push({data: value, name: input_name});
            });          
        });
        
        $(settings.container).on(settings.uploadOn.clientEvent, settings.uploadOn.selector, function(event){
            
            filesCheckInterval = setInterval(function() {
                if (files.length) {
                    clearInterval(filesCheckInterval);
                    var filesData = new FormData();
           
                    $(files).each(function(index, value){
                        filesData.append(value.name, value.data);
                    });
           
                    //start of ajax request
                    
                    $.ajax({
                        url:            settings.url,
                        type:           "POST",
                        data:           filesData,
                        cache:          false,
                        dataType:       settings.responseType,
                        processData:    false,
                        contentType:    false,
                        beforeSend:     settings.before,

                        success:        function(response, textStatus, jqXHR) {
                            settings.onSuccess(response, textStatus, jqXHR);
                        },

                        error:      function(response, textStatus, jqXHR) {
                            settings.onError(response, textStatus, jqXHR);
                        },
                        
                        complete:   function() {
                            $(settings.input).val(null);
                             settings.after();
                        }
                    });
                    
                    //end of ajax request
                }
            }, 300);
            
        });
    };
}(jQuery));