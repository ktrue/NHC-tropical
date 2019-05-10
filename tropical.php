<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tropical Weather</title>
<style>
.advisorybox, .advisoryboxnoactive {
	border: 2px solid #F00;
	color: #000000;
	padding: 5px;
	background-color: #FFC;
	margin: 0.5em 0;
	width: 616px;
	text-align: left;
}
.advisoryboxnoactive {
	border: 2px solid #999;
}
pre { 
	font-family: "Courier New", Courier, monospace;
	font-size: 10pt;
	font-style: normal;
	text-indent: 0px;
	text-align: left;
}
.advisorybox pre, .advisoryboxnoactive pre {
	font-family: "Courier New", Courier, monospace;
	font-size: 9pt;
	font-style: normal;
	text-indent: 0px;
	text-align: left;
}

</style>
</head>

<body style="font-family:Arial, Helvetica, sans-serif;background-color: white;color: black;">

  <h1>Tropical Sea Temperatures</h1>
  <a href="https://www.wunderground.com/tropical/"
   title="Click to visit Weather Underground Tropical Weather Center">
   <img src="https://icons.wxug.com/data/images/sst_basin/gl_sst_mm.gif" 
     alt="Tropical Sea Temperature Map from WeatherUnderground" 
	 width="620" height="388" style="border: none"/><br/>
	 <small>Visit Weather Underground Tropical Weather Center</small></a>  
	 <p>&nbsp;</p>
	<h1>Current Atlantic Satellite Loop</h1>
	<img src="https://icons.wxug.com/data/images/blogs/atl_ir4_sat_tropicalindex_anim.gif?<?php echo time(); ?>"
	alt="Satellite loop from WeatherUnderground"
	width="456" height="342" />
	 <p>&nbsp;</p>
	<h1>Current Pacific Satellite Loop</h1>
	<img src="https://icons.wxug.com/data/images/blogs/ep_ir4_sat_tropicalindex_anim.gif?<?php echo time(); ?>"
	alt="Satellite loop from WeatherUnderground"
	width="456" height="342" />
	<p>&nbsp;</p>
    <h1>Atlantic/Gulf of Mexico/Caribbean</h1>
    <div class="advisorybox"><!-- nhcfeedparse.php -->
      <?php 
	  $_REQUEST['inc'] = 'Y';
	  $_REQUEST['zone'] = 'A';
	  include("rss-tropical-atlantic.php"); 
	  ?>
    </div>
	 <p>&nbsp;</p>
    <h1>Eastern Pacific</h1>
    <div class="advisorybox"><!-- nhcfeedparse-pac -->
    <?php 
	  $_REQUEST['inc'] = 'Y';
	  $_REQUEST['zone'] = 'P';
	  include("rss-tropical-pacific.php"); 
	?>

</body>
</html>