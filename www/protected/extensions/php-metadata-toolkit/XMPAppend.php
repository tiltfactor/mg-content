<?php // -*- tab-width:2; indent-tabs-mode:nil -*-
/**
 *
 * @BEGIN_LICENSE
 *
 * Metadata Games - A FOSS Electronic Game for Archival Data Systems
 * Copyright (C) 2011 Mary Flanagan, Tiltfactor Laboratory
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public
 * License along with this program.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * This file incorporates work from Photoshop_File_Info.php, a part
 * of the PHP JPEG Metadata Toolkit (v1.12) by Evan Hunter, licensed
 * under the GNU GPLv2+.
 *
 * @END_LICENSE
 *
 */

include 'Toolkit_Version.php';          // Change: added as of version 1.11
include 'JPEG.php';
include 'XMP.php';

/************************************************************************
 * Global Variable:      Software Name
 *
 * Contents:     The string that is appended to fields which store the name of
 *               the software editor.
 *
 ************************************************************************/

// Change: Changed version numbers to reference Toolkit_Version.php -
// as of version 1.11.
$GLOBALS[ "Software Name" ] =
          "(PHP JPEG Metadata Toolkit v" . $GLOBALS['Toolkit_Version'] . ")";

/************************************************************************
 * End of Global Variable:     Software Name
 ************************************************************************/

class XMPAppend {

  // Some methods that we can call from inside Yii.
  public function get_jpeg_header_data( $filepath ) {
    return get_jpeg_header_data( $filepath );
  }

  public function put_jpeg_header_data($a, $b, $header) {
    return put_jpeg_header_data($a, $b, $header);
  }

  
  /************************************************************************
   *
   * Function:     get_xmp_dc
   *
   * Description:  Retrieves XMP Dublin Core (dc) fields, initializing missing
   *               values.
   *
   *               (NOTE: For now we only care about the dc:description field,
   *                however we can/will expand the list in the future)
   *
   * Parameters:   XMP_array - An array containing the XMP information to be
   *                 searched, as retrieved by read_XMP_array_from_text.
   *                 (saves having to parse the XMP again)
   *
   * Returns:      outputarray - An array with key-value pairs for XMP dc
   *                 fields.
   *
   *                 (NOTE: The keys are not the same as dc keys (they're
   *                  from Photoshop); I'll try to standardize them to use
   *                  the dc keys later)
   *
   ************************************************************************/
  
  public function get_xmp_dc( $XMP_array ) {
    $outputarray = array("description" => "");
    
    // XMP Processing
    
    // Retrieve the dublin core section from the XMP header
    
    // Extract the Dublin Core section from the XMP
    $dublincore_block = self::find_XMP_block( $XMP_array, "dc" );
    
    // Check that the Dublin Core section exists
    if ( $dublincore_block != FALSE ) {
      
      // Dublin Core Description Field.
      // Extract Description
      $Item = self::find_XMP_item( $dublincore_block, "dc:description" );
      
      // Check if Description Tag exists.
      if ( $Item != FALSE ) {
        // Ensure that the Description value exists and save it.
        if  ( ( array_key_exists( 'children', $Item ) ) &&
              ( $Item['children'][0]['tag'] == "rdf:Alt" ) &&
              ( array_key_exists( 'value',
                                  $Item['children'][0]['children'][0] ) ) ) {
          $outputarray =
            self::add_to_field( $outputarray, 'description' ,
                                HTML_UTF8_Escape($Item['children'][0]['children'][0]['value']),
                                $Item['children'][0]['children'][0]['value'],
                                "\n" );
        }
      }
      
      // Dublin Core Creator Field contains author
      // Extract Author
      $Item = self::find_XMP_item( $dublincore_block, "dc:creator" );
      
      // Check if Creator Tag existed
      if ( $Item != FALSE ) {
        // Ensure that the Creator value exists and save it.
        if  ( ( array_key_exists( 'children', $Item ) ) &&
              ( $Item['children'][0]['tag'] =="rdf:Seq" ) &&
              ( array_key_exists( 'value',
                                  $Item['children'][0]['children'][0] ) ) ) {
          $outputarray =
            self::add_to_field( $outputarray, 'author' ,
                                HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ),
                          "\n" );
        }
      }
      
      // Dublin Core Title Field contains title
      // Extract Title
      $Item = self::find_XMP_item( $dublincore_block, "dc:title" );
      
      // Check if Title Tag existed
      if ( $Item != FALSE ) {
        // Ensure that the Title value exists and save it.
        if  ( ( array_key_exists( 'children', $Item ) ) &&
              ( $Item['children'][0]['tag'] =="rdf:Alt" ) &&
              ( array_key_exists( 'value',
                                  $Item['children'][0]['children'][0] ) ) ) {
          $outputarray =
            self::add_to_field( $outputarray, 'title' ,
                                HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ), "," );
        }
      }
      
      // Dublin Core Rights Field contains copyrightnotice
      // Extract Rights
      $Item = self::find_XMP_item( $dublincore_block, "dc:rights" );
      
      // Check if Rights Tag existed
      if ( $Item != FALSE ) {
        // Ensure that the Rights value exists and save it.
        if  ( ( array_key_exists( 'children', $Item ) ) &&
              ( $Item['children'][0]['tag'] =="rdf:Alt" ) &&
              ( array_key_exists( 'value',
                                  $Item['children'][0]['children'][0] ) ) ) {
          $outputarray =
            self::add_to_field( $outputarray, 'copyrightnotice' ,
                                HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ),
                                "," );
        }
      }
      
      // Dublin Core Subject Field contains keywords
      // Extract Subject
      $Item = self::find_XMP_item( $dublincore_block, "dc:subject" );
      
      // Check if Subject Tag existed
      if ( $Item != FALSE ) {
        // Ensure that the Subject values exist
        if  ( ( array_key_exists( 'children', $Item ) ) &&
              ( $Item['children'][0]['tag'] =="rdf:Bag" ) ) {
          // Cycle through each Subject value and save them
          foreach ( $Item['children'][0]['children'] as $keywords ) {
            if ( ! in_array ( HTML_UTF8_Escape( $keywords['value'] ),
                              $outputarray['keywords'])) {
              if  ( array_key_exists( 'value', $keywords ) ) {
                $outputarray['keywords'][] =
                  HTML_UTF8_Escape( $keywords['value'] );
              }
            }
          }
        }
      }
    }
    
    return $outputarray;
  }
  
  
  /************************************************************************
   *
   * Function:     append_to_xmp_dc
   *
   * Description:  Retrieves XMP Dublin Core (dc) fields, appends passed-in
   *               data to them and then returns the combined values.
   *
   *               (NOTE: For now we only care about the dc:description field,
   *                and that is the *ONLY* field that we are appending to,
   *                however we can/will expand the list in the future)
   *
   * Parameters:   XMP_array - An array containing the existing XMP
   *                 information as retrieved by read_XMP_array_from_text.
   *                 (saves having to parse the XMP again)
   *
   *               dc - An array of xmp:dc fields formatted as key-value pairs:
   *                      dc = array(
   *                             "description" => "",
   *                             "creator"     => "",
   *                             "title"       => "",
   *                             "rights"      => "",
   *                             // This is formatted as an array, as it can
   *                             // have multiple 'keywords' in child elements.
   *                             "subject" => array(0 => "", 1 => "", ... )
   *                           );
   *
   * Returns:      outputarray - An array formatted like XMP_array.
   *
   ************************************************************************/
  
  public function append_to_xmp_dc( $XMP_array, $dc ) {
    // This returns a key-value pair array of Dublin Core fields.
    $existing_xmp_dc = self::get_xmp_dc($XMP_array);
    
    // -- We'll want to clean-up the values in the 'dc' array.
    //
    
    //  Create a translation table to remove carriage return characters
    $trans = array( "\x0d" => "" );

    // Cycle the fields of the passed-in data.
    foreach( $dc as $valkey => $val ) {
      // If the element is 'Keywords' or 'Supplemental Categories',
      // then it is an array, and needs to be treated as one
      if ( ( $valkey != 'supplementalcategories' ) &&
           ( $valkey != 'keywords' ) ) {
        // Not Keywords or Supplemental Categories
        // Convert escaped HTML characters to UTF8 and remove carriage returns
        $dc[ $valkey ] = strtr( HTML_UTF8_UnEscape( $val ), $trans );
      } else {
        // Either Keywords or Supplemental Categories
        // Cycle through the array,
        foreach( $val as $subvalkey => $subval ) {
          // Convert escaped HTML characters to UTF8 and remove
          // carriage returns.
          $dc[ $valkey ][ $subvalkey ] =
            strtr( HTML_UTF8_UnEscape( $subval ), $trans );
        }
      }
    }
    
    
    // -- Make sure that the XMP array is properly set up.
    
    // Set up the XMP array.
    $new_XMP_array = FALSE;
    
    // Check if XMP existed previously
    if ($XMP_array == FALSE ) {
      // XMP didn't exist - create a new one based on a blank
      // structure.
      $new_XMP_array =
        self::XMP_Check( self::new_empty_xmp_array(), array( ) );
    } else {
      // XMP does exist
      // Some old XMP processors used x:xapmeta, check for this
      if ( $XMP_array[0]['tag'] == 'x:xapmeta' ) {
        // x:xapmeta found - change it to x:xmpmeta
        $XMP_array[0]['tag'] = 'x:xmpmeta';
      }
      
      // Ensure that the existing XMP has all required fields, and add
      // any that are missing.
    }
    
    
    // -- Process the XMP Dublin Core block.
    
    // Find the Dublin Core Information within the XMP block
    $DC_block = self::find_XMP_block( $new_XMP_array, "dc" );
    
    
    // The Dublin Core description tag - Find it and
    // Update the value.
    $new_value = $existing_xmp_dc["description"] . "\n" . $dc["description"];
    $Item = & self::find_XMP_item( $DC_block, "dc:description" );
    $Item[ 'children' ][ 0 ][ 'children' ] =
      array( array(  'tag'   => "rdf:li",
                     'value' => $new_value,
                     'attributes' => array( 'xml:lang' => "x-default" ) ) );
    
    // -- Return the array.
    return $new_XMP_array;
  }
  
  
  
  
  
  /************************************************************************
   *
   *         INTERNAL FUNCTIONS
   *
   ************************************************************************/
  
  
  
  
  
  
  
  
  /************************************************************************
   *
   * Function:     XMP_Check
   *
   * Description:  Checks a given XMP array against a reference array, and
   *               adds any missing blocks and tags
   *
   *               NOTE: This is a recursive function
   *
   * Parameters:   reference_array - The standard XMP array which contains
   *                 all required tags.
   *               check_array - The XMP array to check
   *
   * Returns:      output - a string containing the timezone offset
   *
   ************************************************************************/
  
  public function XMP_Check( $reference_array, $check_array)
  {
    // Cycle through each of the elements of the reference XMP array
    foreach( $reference_array as $valkey => $val )
      {
        
        // Search for the current reference tag within the XMP array
        // to be checked.
        $tagpos = self::find_XMP_Tag( $check_array,  $val );
        
        // Check if the tag was found
        if ( $tagpos === FALSE )
          {
            // Tag not found - Add tag to array being checked
            $tagpos = count( $check_array );
            $check_array[ $tagpos ] = $val;
          }
        
        // Check if the reference tag has children
        if ( array_key_exists( 'children', $val ) )
          {
            // Reference tag has children - these need to be checked too
            
            // Determine if the array being checked has children for this tag
            if ( ! array_key_exists( 'children', $check_array[ $tagpos ] ) )
              {
                // Array being checked has no children - add a blank
                // children array.
                $check_array[ $tagpos ][ 'children' ] = array( );
              }
            
            // Recurse, checking the children tags against the
            // reference children.
            $check_array[ $tagpos ][ 'children' ] =
              self::XMP_Check( $val[ 'children' ] ,
                               $check_array[ $tagpos ][ 'children' ] );
          }
        else
          {
            // No children - don't need to check anything else
          }
      }
    
    // Return the checked XMP array
    return $check_array;
  }
  
  
  /************************************************************************
   * End of Function:     XMP_Check
   ************************************************************************/
  
  
  
  
  /************************************************************************
   *
   * Function:     find_XMP_Tag
   *
   * Description:  Searches one level of an XMP array for a specific tag, and
   *               returns the tag position. Does not descend the XMP tree.
   *
   * Parameters:   XMP_array - The XMP array which should be searched
   *               tag - The XMP tag to search for (in same format as would
   *                 be found in XMP array)
   *
   * Returns:      output - a string containing the timezone offset
   *
   ************************************************************************/
  
  public function find_XMP_Tag( $XMP_array, $tag ) {
    $namespacestr = "";
    
    // Some tags have a namespace attribute which defines them
    // (i.e. rdf:Description tags).
    
    // Check if the tag being searched for has attributs
    if ( array_key_exists( 'attributes', $tag ) ) {
      // Tag has attributes - cycle through them
      foreach( $tag['attributes'] as $key => $val ) {
        // Check if the current attribute is the namespace attribute -
        // i.e. starts with xmlns:
        if ( strcasecmp( substr($key,0,6), "xmlns:" ) == 0 ) {
          // Found a namespace attribute - save it for later.
          $namespacestr = $key;
        }
      }
    }
    
    
    
    // Cycle through the elements of the XMP array to be searched.
    foreach( $XMP_array as $valkey => $val ) {
      
      // Check if the current element is a rdf:Description tag
      if ( strcasecmp ( $tag[ 'tag' ], 'rdf:Description' ) == 0 ) {
        // Current element is a rdf:Description tag.

        // Check if the namespace attribute is the same as in the tag
        // that is being searched for.
        if ( array_key_exists( $namespacestr, $val['attributes'] ) ) {
          // Namespace is the same - this is the correct tag - return
          // it's position.
          return $valkey;
        }
      }
      // Otherwise check if the current element has the same name as
      // the tag in question.
      else if ( strcasecmp ( $val[ 'tag' ], $tag[ 'tag' ] ) == 0 ) {
        // Tags have same name - this is the correct tag - return it's
        // position.
        return $valkey;
      }
    }
    
    // Cycled through all tags without finding the correct one -
    // return error value.
    return FALSE;
  }
  
  /************************************************************************
   * End of Function:     find_XMP_Tag
   ************************************************************************/
  
  
  
  
  /************************************************************************
   *
   * Function:     create_GUID
   *
   * Description:  Creates a Globally Unique IDentifier, in the format that
   *               is used by XMP (and Windows). This value is not
   *               guaranteed to be 100% unique, but it is ridiculously
   *               unlikely that two identical values will be produced.
   *
   * Parameters:   none
   *
   * Returns:      output - a string containing the timezone offset
   *
   ************************************************************************/
  
  public function create_GUID()
  {
    // Create a md5 sum of a random number - this is a 32 character
    // hex string.
    $raw_GUID = md5( uniqid( getmypid() . rand( ) .
                             (double)microtime()*1000000, TRUE ) );
    
    // Format the string into 8-4-4-4-12 (numbers are the number of
    // characters in each block).
    return  substr($raw_GUID,0,8) . "-" .
      substr($raw_GUID,8,4) . "-" .
      substr($raw_GUID,12,4) . "-" .
      substr($raw_GUID,16,4) . "-" .
      substr($raw_GUID,20,12);
  }
  
  /************************************************************************
   * End of Function:     create_GUID
   ************************************************************************/
  
  
  
  
  
  /************************************************************************
   *
   * Function:     add_to_field
   *
   * Description:  Adds a value to a particular field in a Photoshop File
   *               Info array, first checking whether the value is already
   *               there. If the value is already in the array, it is not
   *               changed, otherwise the value is appended to whatever is
   *               already in that field of the array
   *
   * Parameters:   field_array - The Photoshop File Info array to receive
   *                 the new value
   *               field - The File Info field which the value is for
   *               value - The value to be written into the File Info
   *               separator - The string to place between values when
   *                 having to append the value
   *
   * Returns:      output - the Photoshop File Info array with the value added
   *
   ************************************************************************/
  
  public function add_to_field( $field_array, $field, $value, $separator ) {
    // Check if the value is blank
    if ( $value == "" ) {
      // Value is blank - return File Info array unchanged
      return $field_array;
    }
    
    // Check if the value can be found anywhere within the existing
    // value for this field
    if ( stristr ( $field_array[ $field ], $value ) == FALSE) {
      // Value could not be found
      // Check if the existing value for the field is blank
      if ( $field_array[$field] != "" ) {
        // Existing value for field is not blank - append a separator
        $field_array[$field] .= $separator;
      }
      // Append the value to the field
      $field_array[$field] .= $value;
    }
    
    // Return the File Info Array
    return $field_array;
  }
  
  /************************************************************************
   * End of Function:     add_to_field
   ************************************************************************/
  
  
  
  
  
  /************************************************************************
   *
   * Function:     find_XMP_item
   *
   * Description:  Searches a one level of a XMP array for a particular
   *               item by name, and returns it if found.
   *               Does not descend through the XMP array
   *
   * Parameters:   Item_Array - The XMP array to search
   *               item_name - The name of the tag to serch for
   *                 (e.g. photoshop:CaptionWriter )
   *
   * Returns:      output - the contents of the tag if found
   *               FALSE - otherwise
   *
   ************************************************************************/
  
  public function & find_XMP_item( & $Item_Array, $item_name ) {
    // Cycle through the top level of the XMP array
    foreach( $Item_Array as $Item_Key => $Item ) {
      // Check this tag name against the required tag name
      if( $Item['tag'] == $item_name ) {
        // The tag names match - return the item
        return $Item_Array[ $Item_Key ];
      }
    }
    
    // No matching tag found - return error code
    return FALSE;
  }
  
  /************************************************************************
   * End of Function:     find_XMP_item
   ************************************************************************/
  
  
  
  
  
  /************************************************************************
   *
   * Function:     find_XMP_block
   *
   * Description:  Searches a for a particular rdf:Description block within
   *               a XMP array, and returns its children if found.
   *
   * Parameters:   XMP_array - The XMP array to search as returned by
   *                 read_XMP_array_from_text
   *               block_name - The namespace of the XMP block to be found
   *                 (e.g.  photoshop or xapRights )
   *
   * Returns:      output - the children of the tag if found
   *               FALSE - otherwise
   *
   ************************************************************************/
  
  public function find_XMP_block( & $XMP_array, $block_name ) {
    // Check that the rdf:RDF section can be found (which contains
    // the rdf:Description tags.
    if ( ( $XMP_array !== FALSE ) &&
         ( ( $XMP_array[0]['tag'] ==  "x:xapmeta" ) ||
           ( $XMP_array[0]['tag'] ==  "x:xmpmeta" ) ) &&
         ( $XMP_array[0]['children'][0]['tag'] ==  "rdf:RDF" ) ) {
      // Found rdf:RDF
      // Make it's children easily accessible
      $RDF_Contents = $XMP_array[0]['children'][0]['children'];
      
      // Cycle through the children (rdf:Description tags)
      foreach ($RDF_Contents as $RDF_Key => $RDF_Item) {
        // Check if this is a rdf:description tag that has children
        if ( ( $RDF_Item['tag'] == "rdf:Description" ) &&
             ( array_key_exists( 'children', $RDF_Item ) ) ) {
          // RDF Description tag has children,
          // Cycle through it's attributes
          foreach( $RDF_Item['attributes'] as $key => $val ) {
            // Check if this attribute matches the namespace
            // block name required.
            if ( $key == "xmlns:$block_name" ) {
              // Namespace matches required block name -
              // return it's children.
              return $XMP_array[0]['children'][0]['children'][ $RDF_Key ]['children'];
            }
          }
        }
      }
    }
    
    // No matching rdf:Description block found
    return FALSE;
  }
  
  /************************************************************************
   * End of Function:     find_XMP_block
   ************************************************************************/





  /************************************************************************
   *
   *  (Turning this into a function instead!)
   * 
   * new_empty_xmp_array()
   *
   * Returns:  A template XMP array which can be used to create a new
   *           XMP segment.
   *
   ************************************************************************/
  
  function new_empty_xmp_array() {
    // Create a GUID to be used in this template array
    $new_GUID = self::create_GUID( );
    
    $the_array = 
      array (
    0 =>
    array (
      'tag' => 'x:xmpmeta',
      'attributes' =>
      array (
        'xmlns:x' => 'adobe:ns:meta/',
        'x:xmptk' => 'XMP toolkit 3.0-28, framework 1.6',
      ),
      'children' =>
        array (
        0 =>
        array (
          'tag' => 'rdf:RDF',
          'attributes' =>
          array (
            'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
            'xmlns:iX' => 'http://ns.adobe.com/iX/1.0/',
          ),
          'children' =>
          array (
            1 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'rdf:about' => "uuid:$new_GUID",
                'xmlns:pdf' => 'http://ns.adobe.com/pdf/1.3/',
              ),
            ),
            2 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'rdf:about' => "uuid:$new_GUID",
                'xmlns:photoshop' => 'http://ns.adobe.com/photoshop/1.0/',
              ),
            ),
            4 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'rdf:about' => "uuid:$new_GUID",
                'xmlns:xap' => 'http://ns.adobe.com/xap/1.0/',
              ),
              'children' =>
              array (
                0 =>
                array (
                  'tag' => 'xap:CreateDate',
                  'value' => '',
                ),
                1 =>
                array (
                  'tag' => 'xap:ModifyDate',
                  'value' => '',
                ),
                2 =>
                array (
                  'tag' => 'xap:MetadataDate',
                  'value' => '',
                ),
                3 =>
                array (
                  'tag' => 'xap:CreatorTool',
                  'value' => '',
                ),
              ),
            ),
            5 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'about' => "uuid:$new_GUID",
                'xmlns:stJob' => 'http://ns.adobe.com/xap/1.0/sType/Job#',
                'xmlns:xapBJ' => 'http://ns.adobe.com/xap/1.0/bj/',
              ),
              'children' =>
              array (
                0 =>
                array (
                  'tag' => 'xapBJ:JobRef',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Bag',
                      'children' =>
                      array (
                      ),
                    ),
                  ),
                ),
              ),
            ),
            6 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'rdf:about' => "uuid:$new_GUID",
                'xmlns:xapRights' => 'http://ns.adobe.com/xap/1.0/rights/',
              ),
              'children' =>
              array (
                1 =>
                array (
                  'tag' => 'xapRights:WebStatement',
                  'value' => '',
                ),
              ),
            ),
            7 =>
            array (
              'tag' => 'rdf:Description',
              'attributes' =>
              array (
                'rdf:about' => "uuid:$new_GUID",
                'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
              ),
              'children' =>
              array (
                0 =>
                array (
                  'tag' => 'dc:format',
                  'value' => 'image/jpeg',
                ),
                1 =>
                array (
                  'tag' => 'dc:title',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Alt',
                    ),
                  ),
                ),
                2 =>
                array (
                  'tag' => 'dc:description',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Alt',
                    ),
                  ),
                ),
                3 =>
                array (
                  'tag' => 'dc:rights',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Alt',
                    ),
                  ),
                ),
                4 =>
                array (
                  'tag' => 'dc:creator',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Seq',
                    ),
                  ),
                ),
                5 =>
                array (
                  'tag' => 'dc:subject',
                  'children' =>
                  array (
                    0 =>
                    array (
                      'tag' => 'rdf:Bag',
                    ),
                  ),
                ),
              ),
            ),

/*          0 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:exif' => 'http://ns.adobe.com/exif/1.0/',
            ),
            'children' =>
            array (

//EXIF DATA GOES HERE - Not Implemented yet
            ),
          ),
*/
/*
          2 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:tiff' => 'http://ns.adobe.com/tiff/1.0/',
            ),
            'children' =>
            array (
// TIFF DATA GOES HERE - Not Implemented yet
              0 =>
              array (
                'tag' => 'tiff:Make',
                'value' => 'NIKON CORPORATION',
              ),
            ),
          ),
*/
/*
          3 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:stRef' => 'http://ns.adobe.com/xap/1.0/sType/ResourceRef#',
              'xmlns:xapMM' => 'http://ns.adobe.com/xap/1.0/mm/',
            ),
            'children' =>
            array (
// XAPMM DATA GOES HERE - Not Implemented yet
              0 =>
              array (
                'tag' => 'xapMM:DocumentID',
                'value' => 'adobe:docid:photoshop:dceba4c2-e699-11d8-94b2-b6ec48319f2d',
              ),
              1 =>
              array (
                'tag' => 'xapMM:DerivedFrom',
                'attributes' =>
                array (
                  'rdf:parseType' => 'Resource',
                ),
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'stRef:documentID',
                    'value' => 'adobe:docid:photoshop:5144475b-e698-11d8-94b2-b6ec48319f2d',
                  ),
                  1 =>
                  array (
                    'tag' => 'stRef:instanceID',
                    'value' => "uuid:$new_GUID",
                    ),
                  ),
                ),
              ),
            ),
*/

          ),
        ),
      ),
    ),
             );

    return $the_array;
  }



  /************************************************************************
   * End of Function: new_empty_xmp_array()
   ************************************************************************/
  
}


?>
