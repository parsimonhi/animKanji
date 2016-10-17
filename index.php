<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="initial-scale=1.0,user-scalable=yes">
<?php
if (isset($_POST["k"])&&$_POST["k"]) $data=$_POST["k"];
else $data="漢字";
$km=mb_strlen($data,'UTF-8');
$n=0; // number of stroke drawn
function isT($s) {return preg_match("#[0-9]+(\.[0-9]+)?s?#",$s);}
function isE($s) {return preg_match("#[0-9]+(\.[0-9]+)?(px)?#",$s);}
function isC($s) {return 1;}
function getT($s) {return floatval(preg_replace("#[^.0-9]#","",$s));}
function getE($s) {return floatval(preg_replace("#[^.0-9]#","",$s));}
function getC($s) {return $s;}
if (isset($_POST["t"])&&isT($_POST["t"])) $t=getT($_POST["t"]);
else $t=1; // stroke drawing duration (in second)
if (isset($_POST["e"])&&isE($_POST["e"])) $e=getE($_POST["e"]);
else $e=5; // stroke width (in pixel)
if (isset($_POST["c"])&&isC($_POST["c"])) $c=getC($_POST["c"]);
else $c="#cc0000"; // character color
include("animKanjiLib.php");
?>
<style>
body {background:#eee;text-align:center;}
em {display:block;color:#999;margin-top:0.5em;}
em a {color:#999;}
form {margin:0.5em auto;line-height:1.75em;}
input {line-height:1.125em;font-size:1em;text-align:center;vertical-align:middle;}
button {font-size:1em;vertical-align:middle;}
#k {width:3.5em;}
#t {width:2.5em;}
#e {width:2.5em;}
#c {width:4.5em;}
#a
{
	width:92%;
	margin:0 auto;
	background:#fff;
	padding:0.5em;
}

/*simpler to set svg style here instead of <svg> tag*/
svg
{
	display:inline-block;
	padding:0;
	margin:0;
	width:30%;
	fill:none;
	stroke:<?php echo $c;?>;
	stroke-width:<?php echo $e;?>px;
	stroke-linecap:round;
	stroke-linejoin:round;
}
/*svg error displays when a character is not found in kanjiVG*/
svg.error {background:#eee;}
svg.error text
{
	text-anchor:middle;
	font-family:"lucida console",monospace;
	stroke:transparent;
	fill:<?php echo $c;?>;
	font-size:16px;
}

/*here is the critical part: the css animation code*/
/*stroke-dasharray=300 was chosen to well display long paths (such as in hiragana nu)*/
/*use opacity=0 to hide path first point when animation starts (on chrome?)*/
/*use -webkit- prefix to support some old browsers*/
path {opacity:0;}
path {stroke-dasharray:300;}
path {-webkit-animation:dash <?php echo $t*0.7;?>s linear forwards;}
path {animation:dash <?php echo $t*0.7;?>s linear forwards;}
@-webkit-keyframes dash
{
	0% {opacity:1;stroke-dashoffset:300;}
	100% {opacity:1;stroke-dashoffset:0;}
}
@keyframes dash
{
	0% {opacity:1;stroke-dashoffset:300;}
	100% {opacity:1;stroke-dashoffset:0;}
}
</style>
</head>
<body>
<h1>animKanji</h1>
<form method="post" action="./">
<input id="k" name="k" type="text" value="<?php echo $data;?>" maxlength="3">
<input id="t" name="t" type="text" value="<?php echo $t."s";?>">
<input id="e" name="e" type="text" value="<?php echo $e."px";?>">
<input id="c" name="c" type="text" value="<?php echo $c;?>">
<button type="submit">OK</button>
</form>
<div id="a">
<!-- svg below are generated using a simple php function which gets svg paths from kanjiVG files -->
<!-- add animation-delay property to each path style to animate them at the right time -->
<!-- add pathLength attribute to each path to make their length fit stroke-dasharray value -->
<?php
$vg="../kanjiVG"; // path from this script to folder that contains kanji svg files
for ($k=0;$k<$km;$k++) print createAnimatedChar(mb_substr($data,$k,1,'UTF-8'))."\n";
?>
</div>
<em>
<a href="license.php">Creative Commons Attribution-Share Alike 3.0 license</a>
<!-- this work makes an extensive use of kanjiVG (see link below) -->
<a href="http://kanjivg.tagaini.net">KanjiVG</a>
</em>
</body>
</html>