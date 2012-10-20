<?php
/**
 * Parsing XML format (for jwplayer).
 */

 //http://www.w3.org/TR/2010/REC-ttaf1-dfxp-20101118/
 //http://documentation.hwdmediashare.co.uk/wiki/Making_video_accessible
 //http://www.codemiles.com/php/php-srt-to-xml-subtitle-converter-t1347.html
 
die('to implement');
class XmlSubtitle extends Subtitle{
  protected function _parse(&$f){
  }
  public function get(){
  }
  public function decodeTime($v){
  }
  public function encodeTime($t){
  }
  
  public static function getVersion(){
    //return '$Id: $';
  }
}


?>
<?php
/**
http://www.codemiles.com/php/php-srt-to-xml-subtitle-converter-t1347.html 

<script type="text/javascript">
      var s1 = new SWFObject("player.swf","ply","328","200","9","#FFFFFF");
      s1.addParam("allowfullscreen","true");
      s1.addParam("allowscriptaccess","always");
      s1.addParam("flashvars","file=bunny.flv&captions=subtitles/bunny.xml");
      s1.write("container");
   </script>

*/


// script to convert multi-line srt caption files to new-format (02-05-08) tt XML caption files
$use_cdata_tags = true;
$debug_output = true;

// the directory to write the new files in
// it must exist, be writeable, and be outside of the directory that is being scanned
$new_directory = '../temp/';

/////////////////////////////////// no user configuration below this \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

// get filename or scan directory if it's a directory
$filename = (isset($_GET["filename"])) ? strval($_GET["filename"]) : "./";

// read each file into an array
$it = new RecursiveDirectoryIterator("$filename");

foreach(new RecursiveIteratorIterator($it) as $file)
{

// debug('Filename', $file); exit;
// debug('Ext', substr(strtolower($file), (strlen($file) - 4), 4));// exit;

// debug - only use test file
// if($file == '.\multi-line_test_file.srt')

  // check for .srt extension
  if(substr(strtolower($file), (strlen($file) - 4), 4) == '.srt')
  {
    $ttxml     = '';
    $full_line = '';

    if($file_array = file($file))
    {
      // write tt , head, and div elements for the new file
     $ttxml .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
      $ttxml .= "<tt xml:lang='en' xmlns='http://www.w3.org/2006/10/ttaf1' xmlns:tts='http://www.w3.org/2006/10/ttaf1#style'>\n";
      $ttxml .= "  <head>\n";
      $ttxml .= "  </head>\n";
      $ttxml .= "  <body>\n";
      $ttxml .= "    <div xml:id=\"captions\">\n";

      foreach($file_array as $line)
      {
        $line = rtrim($line);

// debug('Line', $line);

        // get begin and end
        //                00  :  00  :  32  ,   000   -->   00  :  00  :  37  ,   000
        if(preg_match('/(\d\d):(\d\d):(\d\d),(\d\d\d) --> (\d\d):(\d\d):(\d\d),(\d\d\d)/', $line, $match))
        {
          $begin = $match[1] . ":" . $match[2] . ":" . $match[3] . "." . $match[4];
          $end   = $match[5] . ":" . $match[6] . ":" . $match[7] . "." . $match[8];
          $full_line = '';
        }
        // if the next line is not blank, get the text
        elseif($line != '')
        {
          if($full_line != '')
          {
            $full_line .= '<br />' . $line;
          }
          else
          {
            $full_line .= $line;
          }
        }

        // if the next line is blank, write the paragraph
        if($line == '')
        {
          // write new paragraph
          //                 <p begin="00:08:01.50" end="00:08:07.50">Nothing is going on.</p>
          if($use_cdata_tags)
          {
            $ttxml .= "      <p begin=\"" . $begin . "\" end=\"" . $end . "\"><![CDATA[" . $full_line . "]]></p>\n";
          }
          else
          {
            $ttxml .= "      <p begin=\"" . $begin . "\" end=\"" . $end . "\">" . $full_line . "</p>\n";
          }

// debug('Text', $line);
// debug('ttxml', $ttxml); exit;

          $full_line = '';
        }
      }

// write ending tags
$ttxml .= " </div>\n";
$ttxml .= " </body>\n";
$ttxml .= "</tt>\n";

      // write new file
      $new_file = $new_directory . substr($file, 0, (strlen($file) - 4)) . '.xml';
      $fh = fopen($new_file, 'w') or die('Can\'t open: ' . $new_file);
      fwrite($fh, $ttxml) or die('Can\'t write to: ' . $new_file);
      fclose($fh);
    }
  }
}


function debug($title, $value)
{
  global $debug_output;

  if ($debug_output)
  {
    print "<pre>";
    if (is_array($value))
    {
      print $title . ":\n";
      print_r($value);
    }
    else
    {
      print $title . ": " . $value;
    }
    print "</pre>\n";
  }
}

?>
