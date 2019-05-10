<?php
// PHP script by Ken True, webmaster@saratoga-weather.org
// rss-tropical-pacific.php  version 1.00 - 03-May-2006
//                        version 1.01 - 14-Jun-2006 minor formatting chg
//                        Version 1.02 - 15-Jun-2006 fixed <pre> format
//                        Version 1.03 - 04-Aug-2008 corrected for XHTML 1.0-Strict
//                        Version 1.04 - 18-Sep-2010 omit .SHP/.KML links
//                        Version 1.05 - 06-Jun-2013 fix NHC html text
//                        Version 1.06 - 10-May-2019 updates for PHP7+ and https for NHC site
// script available at http://saratoga-weather.org/scripts.php
//  
// you may copy/modify/use this script as you see fit,
// no warranty is expressed or implied.
//
// This script gets the current RSS Tropical Cyclone Advisories released by the NHC/TPC
// from www.nhc.noaa.gov/aboutrss.shtml and provides either
// a summary (titles only, with links) or details.  It returns 
//    'There are no active watches, warnings or advisories'
//  if there are no cited hazards in the Zone.
//
// output: creates XHTML 1.0-Strict HTML page (default)
//
// Options on URL:
//      inc=Y           -- returns only the body code for inclusion
//                         in other webpages.  Omit to return full HTML.
//      summary=Y       -- returns only the titles of the cited hazards
//                          N.N or greater.
//      zone=[A|P|AS] 		-- select the zone to use:
//							'A' for Atlantic, Caribbean, and Gulf of Mexico
//							'P' for Eastern Pacific tropical cyclones
//							'AS' for Atlantic in Spanish Language 
//
// example URL:
//  http://your.website/rss-tropical-test.php?zone=A&inc=Y
//  would return data without HTML header/footer for hazards in Atlantic 
//
// Usage:
//  you can use this webpage standalone (customize the HTML portion below)
//  or you can include it in an existing page:
//  no parms:    include("rss-tropical-test.php"); 
//  parms:    include("http://your.website/rss-tropical.test.php?inc=Y&summary=Y&zone=A");
//
//
// settings:  
//  change $Zone default(below) to your stations county zone 
//    other settings are outlined below, and are optional
// 
// settings ----------------------------------------------------------
if ( ! isset($_REQUEST['zone']) ) {
        $_REQUEST['zone']="P";  // <=== Change this default to your Zone
	}
$Zone = $_REQUEST['zone']; 

if (!isset($PHP_SELF)) {$PHP_SELF = $_SERVER['PHP_SELF'];}
$hurlURL = "$PHP_SELF"; // <=== change this default to webpage
//                                         to open for details
//  on that page, you can have the following PHP
//
//  if ( ! isset($_REQUEST['zone']) ) {
//        $_REQUEST['zone']="A";  // <=== Change this to your Zone
//	}
//  $Zone = $_REQUEST['zone']; 
//
//  include("http://your.website/rss-tropical-test.php?zone=$Zone&inc=Y");
//   
//
//
$HD = "h2";  // <=== type of heading for advisorys <$HD>...</$HD>
// end of settings -------------------------------------------------

$RSSURL = 'https://www.nhc.noaa.gov/index-at.xml';
if ($Zone == 'P') {
  $RSSURL = 'https://www.nhc.noaa.gov/index-ep.xml';
}
if ($Zone == 'AS') {
  $RSSURL = 'https://www.nhc.noaa.gov/index-at-sp.xml';
}
//  get request parameters 

// full html output or just contents (inc=Y)?
if ( ! isset($_REQUEST['inc']) ) {
        $_REQUEST['inc']="";
  }
$includeOnly = $_REQUEST['inc']; // any nonblank is ok
if ($includeOnly) {$includeOnly = "Y";}
if ( isset($_REQUEST['summary'])) {
  $doSummary = TRUE;
  } else {
  $doSummary = FALSE;
}
// the following is another way to pass the name of the page for testing
if (isset($_REQUEST['detailpage']) ) {
  $hurlURL = $_REQUEST['detailpage'];
}

if ( isset($_REQUEST['sce']) and $_REQUEST['sce'] == 'view' ) {
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
}

// begin code -----------------------------------------------------------
$insideitem = false;
$tag = "";
$title = "";
$description = "";
$link = "";
$lastBuildDate = "";
$tracking = "";
$Summary = "";
$WLink = 0;
$PageTitle = "";


// function for XML parsing .. invoked at start of XML element
function startElementPAC($parser, $name, $attrs) {
	global $insideitem, $tag, $title, $description, $link, $lastBuildDate,$doSummary,$Summary, $PageTitle;
	if ($insideitem) {
		$tag = $name;
	} elseif ($name == "ITEM") {
		$insideitem = true;
	}
	if ($name == "LASTBUILDDATE") {
	   $tag = $name;
	}
}

// function for XML parsing .. invoked at end of XML element
// bulk of the work is done here

function endElementPAC($parser, $name) {
	global $insideitem, $tag, $title, $description, $link, $lastBuildDate, $tracking,$doSummary,$Summary,$Zone,$hurlURL,$WLink,$HD, $PageTitle;
	if ($name == "ITEM") {
		if (preg_match('/\.[shp|kml]/i',$title) ) {
		  $title = "";
		  $description = "";
		  $link = "";
		  $insideitem = false;
		  return;	
		}
	    if (preg_match("|There are no tropical|i",$description)) {
		  if (! $doSummary) {
		     printf("%s",htmlspecialchars(trim($description)));
		  }
		  $Summary = htmlspecialchars(trim($description));

		} else {
			$WLink++;
		    $description = preg_replace("|<br/>|is","",$description);
//		    $description = preg_replace("|<br>|s","<br />\n",$description);
			$description = preg_replace("|href=http([^>]+)>|is","href=\"http\\1\">",$description);
	    	if (! $doSummary) {
			  printf("<$HD><a name=\"WL$WLink\"></a><a href=\"%s\">%s</a></$HD>\n",
					 trim($link),htmlspecialchars(trim($title)));
//		      printf("<p>Updated: <b>%s</b><br />\n<pre>%s</pre></p>\n",			       $lastBuildDate,trim($description)); 
			  print "<!-- len=".strlen($description)." -->\n";
			  $description = preg_replace('|&|is','&amp;',$description);
			  $description = strip_tags($description,'<br><img>');
			  $description = preg_replace('|<img ([^>]+)>|si','<img \\1><br/>',$description);
			  if (strlen($description) < 100 ) {
  			      printf("<pre><strong>%s</strong></pre>\n",trim($description)); 
			  } else {
			      $nl = strpos($description,"\n");
				  print "<!-- nl= $nl title='".htmlspecialchars(trim($title))."' -->\n";
			      if ($nl >= 120 or preg_match('|graphics|is',$title) ) {
		            printf("<p>%s</p>\n",trim($description)); 
				  } else {
		            printf("<pre>%s</pre>\n",trim($description)); 
				  }
			  }
				}
			$Summary .= "<span style=\"color: red;\"><a href=\"$hurlURL?zone=$Zone#WL$WLink\"><b>" . 
			     htmlspecialchars(trim($title)) . "</b></a></span><br />\n";
		}
		$title = "";
		$description = "";
		$link = "";
		$insideitem = false;
	} // end insideitem
	if ($name == "LASTBUILDDATE") {
	   $tag = '';
	}
	$tracking .= "<!-- tag: '$name' -->\n";
}

// extract character data from within an XML tag

function characterDataPAC($parser, $data) {
	global $insideitem, $tag, $title, $description, $link, $lastBuildDate,$doSummary,$Summary, $PageTitle;
	if ($tag == "LASTBUILDDATE") {
		$lastBuildDate .= $data;
	}
	if ($tag == 'TITLE' and (! $insideitem) ) {
	    $PageTitle .= $data;
    }
	
	if ($insideitem) {
      switch ($tag) {
		case "TITLE":
		$title .= $data;
		break;
		case "DESCRIPTION":
		$description .= $data;
		break;
		case "LINK":
		$link .= $data;
		break;
	  } // end switch
	} // end insideitem

}

//  Main Code Start

if (! $includeOnly) {  // omit HTML headers if doing inc=Y
print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RSS Tropical Cyclone Advisories from NHC</title>
</head>

<body style="background-color:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
';
print "<!-- feed source='$RSSURL' -->\n";
} // end .. only printed if full html needed

// main routine -- get RSS, parse and display
// adapted from SitePoint sample code at:
//  http://www.sitepoint.com/examples/phpxml/sitepointcover.php.txt and article at
//  http://www.sitepoint.com/article/php-xml-parsing-rss-1-0
//  by Ken True -- webmaster@saratoga-weather.org

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElementPAC", "endElementPAC");
xml_set_character_data_handler($xml_parser, "characterDataPAC");

   $STRopts = array(
	  'http'=>array(
	  'method'=>"GET",
	  'protocol_version' => 1.1,
	  'header'=>"Cache-Control: no-cache, must-revalidate\r\n" .
				"Cache-control: max-age=0\r\n" .
				"Connection: close\r\n" .
				"User-agent: Mozilla/5.0 (rss-tropical-pacific - saratoga-weather.org)\r\n" .
				"Accept: text/plain\r\n"
	  ),
	  'ssl'=>array(
	  'method'=>"GET",
	  'protocol_version' => 1.1,
		'verify_peer' => false,
	  'header'=>"Cache-Control: no-cache, must-revalidate\r\n" .
				"Cache-control: max-age=0\r\n" .
				"Connection: close\r\n" .
				"User-agent: Mozilla/5.0 (rss-tropical-pacific - saratoga-weather.org)\r\n" .
				"Accept: text/plain\r\n"
	  )
	);
	
$STRcontext = stream_context_create($STRopts);

$fp = fopen("$RSSURL","r",false,$STRcontext)
	or die("Error reading RSS data.");
while ($data = fread($fp, 4096))
	xml_parse($xml_parser, $data, feof($fp))
		or die(sprintf("%s XML error: %s at line %d: %s",
		    $PHP_SELF, 
			xml_error_string(xml_get_error_code($xml_parser)), 
			xml_get_current_line_number($xml_parser),
			htmlspecialchars(trim($data)) ));
fclose($fp);
xml_parser_free($xml_parser);

// end of main program
if ($doSummary) {
  print $Summary;
  if ($WLink) {
    print "Click on link"; 
	if ($WLink > 1) {echo "s";}
	print " above to see details on the $WLink NOAA advisor";
	if ($WLink > 1) {echo "ies"; } else { echo "y"; };
	echo ".\n";
  }
}
if (! $includeOnly) {
print "\n<!-- Zone=$Zone -->\n";
print $tracking;
print '</body>
</html>
';
} // end - only printed if full html wanted (no inc=Y)
?>