<?php // -*- tab-width:2; indent-tabs-mode:nil -*-


include("../protected/extensions/php-metadata-toolkit/XMPAppend.php");

class Tester {

  public function work() {
    list($version, $format, $date, $system) =
      array("10", "myformat", "Today date", "super system");
    
    // Extract the filename of the image from the query results array.
    //$filename = "pizza.jpg";
    //$filename = "output2.jpg";
    //$filename = "stem226_063.jpg";
    //$filename = "image-with-xmp.jpg";
    $filename = "test.jpg";

    $output_filename = "output.jpg";
    
    // Copy this image into our output directory.
    
    copy($filename, $output_filename) or die("Couldn't copy image");
    
    print "Image copied successfully<br />\n";
    

    // Get the embedded XMP data from our image.
    $header_data = XMPAppend::get_jpeg_header_data( $output_filename );

    //Yii::log("Got the data", "Error");    
    print "Got the embedded data: " . print_r($header_data, true);

    print "<br /><br />--------------------------------------------<br /><br />\n";
    
    $xmp_array =  read_XMP_array_from_text(get_XMP_text($header_data));
    //print_r($xmp_array);

    print "read the embedded XMP data: " . print_r($xmp_array, true);

    print "<br /><br />--------------------------------------------<br /><br />\n";


    //Yii::log("read xmp array", "Error");    

    $existing_dc_metadata = XMPAppend::get_xmp_dc($xmp_array);

    //Yii::log("got the xmp dc", "Error");

    print "<br /><br />got the existing XMP dc data: " . print_r($existing_dc_metadata, true);

    print "<br /><br />--------------------------------------------<br /><br />\n";

    $tags = array("dog", "cat", "hamburger", "chicken", "rat");


    // Following the formatting guidelines, create a string that
    // embeds not ony the tags, but also includes key information such
    // as a datestamp, version of mg, and installation location of the
    // mg server software.
    $description_blurb =
      "[org.tiltfactor.metadatagames_$version f$format ($date) " .
      "(" . implode(", ", $tags) . ") installation:$system]";

    // FOR debugging, add --nothing-- to the XMP metadata.
    //$description_blurb = "";

    //Yii::log("Description is: $description_blurb ----", "Error");

    print "<br /><br />Description blurb is: $description_blurb";

    print "<br /><br />--------------------------------------------<br /><br />\n";


    // TESTING THIS HERE:
    $existing_xmp_dc = XMPAppend::get_xmp_dc($xmp_array);
   
    print "<br /><br />Existing dublin core XMP data is: " . print_r($existing_xmp_dc, true);

    print "<br /><br />--------------------------------------------<br /><br />\n"; 

    // Append the new metadata to the old array (filling-in/creating
    // any missing metadata contents/structure necessary along the
    // way).
    $updated_dc_metadata =
      XMPAppend::append_to_xmp_dc($xmp_array,
                                  array( "description" => $description_blurb ));

    // TO DEBUG, write the same metadata back (i.e. don't use the
    // updated metadata):
    //$updated_dc_metadata = $xmp_array;

    // GOOD NEWS!
    //
    // If I don't change the metadata and just write it back as-is,
    // the XMP metadata *is* recognized.
    //
    // This is a starting point we can work from!

    //Yii::log("appended the metadata: " . print_r($updated_dc_metadata, true) , "Error");

    print "<br /><br />appending new metadata to old array: " . print_r($updated_dc_metadata, true);

    print "<br /><br />--------------------------------------------<br /><br />\n";


    // Put the tweaked XMP metadata back into the full metadata array.
    $updated_header_data =
      put_XMP_text($header_data, write_XMP_array_to_text($updated_dc_metadata));

    //Yii::log("update header data array", "Error");

    print "<br /><br />PUtting tweaked XMP metadat back into the full array (this line re-extracts the data after it's been put in): " .
      print_r(read_XMP_array_from_text(get_XMP_text($updated_header_data)),
              true);
    
    print "<br />---</br />";

    print "Header is: " ;
    print "<br /><br />Length of updated header data: " . count($updated_header_data);

    print "<br /><br />--------------------------------------------<br /><br />\n";
    $rp = realpath("$output_filename");
    print "real path is '$rp'<br />\n";
    
    // Load the new metadata into the image.
    $result = XMPAppend::put_jpeg_header_data($rp, $rp, $updated_header_data);

    //Yii::log("Putting the JPEG header back was a " .
    //         $result ?
    //         "Success." :
    //         "Failure.", "Error");

    print "Putting the JPEG header back was a " . ($result ?
      "Success." :
                                                   "Failure.");

    print "<br /><br />Back to the beginning. File: $rp <br />";
    $newfile_xmp_array =
      read_XMP_array_from_text(get_XMP_text(XMPAppend::get_jpeg_header_data($rp)));

    print "XMP array is now: " . print_r($newfile_xmp_array, true);

    print "<br /><br />--------------------------------------------<br /><br />\n";


    // Okay -- something is going wonky with the example images from
    // Rauner.
    //
    // We'll want to do 2 things to test this:
    //   1) We should compare the before/after XMP data (to see what it
    //      thinks the differences are).
    //
    //   2) We should find some non-Rauner images with XMP data and see
    //      if the bug is present in those images as well.
    //
    //       DONE -- I used the test image from PHP Metadata Toolkit,
    //            and I saw the same problem. Darn!

    // Diff 
    print "<br /><br />This is the diff between the XMP arrays:<br /><br />\n";

    print_r(array_diff($xmp_array, $newfile_xmp_array));

    print "<br /><br />--------------------------------------------<br /><br />\n";


    // Diff  (new way)
    print "<br /><br />This is the diff between the XMP arrays, done across text:<br /><br />\n";

    print "<br /><br />--------------------------------------------<br /><br />\n";

  }
}

$t = new Tester();
$t->work();

?>