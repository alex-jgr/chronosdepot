JQuery ajax upload plugin
=========================

<h2>Usage example:</h2>
<p>
    <strong>HTML</strong>
</p>

<pre>
    &lt;div class="input-group"&gt;
        &lt;label class="input-group-addon"&gt;Image&lt;/label&gt;
        &lt;input class="form-control" type="file" name="image" id="image"/&gt;
    &lt;/div&gt;
</pre>

<p>
    <strong>JS<strong>
</p>

<pre>
    $(document).ready(function(){
       $("#image").fileUpload({
            url:        "/upload.php",
            onSuccess:  function(response){
                        //your code here
                    },
            onError:    function(response){
                        //your code here
                    },
            before:     function() {
                        //your code here
                    },
            after:      function() {
                        //your code here
                    },
            responseType: "json",
            container:  $(this).parent(),
            uploadOn: {
                    selector: "#image",
                    clientEvent: "change"
            }
       });
    });
</pre>


<p>
    The reason why I made this was just because I wanted to define on what element and event would the image be uploaded
</p>
