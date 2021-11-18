kohana-uploader
===============

<p>
    <strong>
        A Kohana Framework module for handling file uploads. It only works on images at the moment. 
    </strong>
</p>

<p>
    In order to have it working at least the following three modules must be enabled in bootstrap file
</p>

<pre>

    Kohana::modules(array(
	'auth'       => MODPATH.'auth',       // Basic authentication
	'image'      => MODPATH.'image',      // Image manipulation
        'Uploader'   => MODPATH . 'uploader'  // Obviously the module from this repository
    ));

</pre>

<p>
    A configuration example is located in the config folder. Looks like this 
</p>

<pre>
    return array(
        'some-image' => array(
            'path'  => DOCROOT . 'public/images/some-images',
            'adapter'  => 'File_Image',
            'extensions' =>  array('jpg', 'jpeg', 'gif'),
            'versions' => array(
                'thumb' => array(
                    'width'     => 64, 
                    'height'    => 64,
                    'crop'      => TRUE,
                    'quality'   => 100,
                    'crop_x'    => NULL,
                    'crop_y'    => NULL
                ),
                'normal' => array(
                    'width'     => 640,
                    'height'    => 480,
                    'crop'      => TRUE,
                    'quality'   => 100,
                    'crop_x'    => NULL,
                    'crop_y'    => NULL
                )
            ),
            'keep-original' => FALSE
        ),
    )
</pre>

Usage:
<pre>
    $uploader_return = Uploader::factory('some-image')->process_upload($_FILES['image']);
</pre>
<p>
    This code will look for the file type 'some-image' in the config file and will save or create (or both) the new image returning an array which contains information on the resulting image or images.
</p>
<p>
    If the original image is no longer needed, then set 'keep-original' to FALSE or don't mention it. If it is needed then it will have to be TRUE.
</p>
<p>
    For each version specified in the 'versions' a new image will be created with the given attributes and placed into a folder composed by the value in the 'path' concatenated with the array key of the version.
</p>

<p>
    A var_dump of the $uploader_return would look like this:
</p>
<pre>

array(2) {
    ["original"]=>
        array(6) {
            ["name"]=>
            string(25) "h0ecqrpfxa14175658471.jpg"
            ["path"]=>
            string(76) "DOCROOT .public/images/some-images/h0ecqrpfxa14175658471.jpg"
            ["width"]=>
            int(800)
            ["height"]=>
            int(800)
            ["created"]=>
            int(1417565847)
            ["quality"]=>
            int(100)
        }
    ["versions"]=>
        array(2) {
          ["thumb"]=>
            array(6) {
                ["name"]=>
                string(25) "h0ecqrpfxa14175658471.jpg"
                ["path"]=>
                string(83) "DOCROOT .public/images/some-images/thumb/h0ecqrpfxa14175658471.jpg"
                ["width"]=>
                int(64)
                ["height"]=>
                int(64)
                ["created"]=>
                int(1417565847)
                ["quality"]=>
                int(100)
            }
          ["normal"]=>
            array(6) {
                ["name"]=>
                string(25) "h0ecqrpfxa14175658471.jpg"
                ["path"]=>
                string(84) "DOCROOT .public/images/some-images/normal/h0ecqrpfxa14175658471.jpg"
                ["width"]=>
                int(640)
                ["height"]=>
                int(480)
                ["created"]=>
                int(1417565847)
                ["quality"]=>
                int(100)
            }
        }
}


</pre>

<p>
    Define as many image types as you need.
</p>
<p>
    Any suggestion is welcome and will be taken into consideration.
</p>