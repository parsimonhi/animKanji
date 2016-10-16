<?php
// animKanji project library
function errorMsg($msg)
{
	$s="";
	$s.="<svg class=\"error\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 109 109\">\n";
	$s.="<text x=\"50%\" y=\"50%\" dy=\"4\">".$msg."</text>\n";
	$s.="</svg>\n";
	return $s;
}

function kanjiFile($u)
{
    $k=mb_convert_encoding($u,'UCS-2LE','UTF-8');
    $k1=ord(substr($k,0,1));
    $k2=ord(substr($k,1,1));
    $s=dechex($k2*256+$k1);
    while (strlen($s)<5) $s="0".$s;
    return $s.".svg";
}

function createAnimatedChar($char)
{
	global $n,$t,$e,$c,$vg,$pathLengthOn,$numberingOn;
	// $n: number of strokes already drawn
	// $t: stroke drawing duration (in second)
	// $e: stroke width (in pixel)
	// extract paths from svg file corresponding to $char, add css animation-delay to each of them
	// assume svg file contains svg that describes character stroke by stroke (one path per stroke)
	// assume that svg file path order is as actual stroke order of the character
	// don't use SMIL tags such as <animate> because they are deprecated in some major browsers
	// such as chrome (in january 2016)
	if (!isset($n)) $n=0;
	if (!isset($t)) $t=1;
	if (!isset($e)) $e=5;
	if (!isset($pathLengthOn)) $pathLengthOn=1;
	if (!isset($numberingOn)) $numberingOn=0;
	$f=kanjiFile($char);
	$code=basename($f,".svg");
	$s="";
	if (isset($vg)) {if (file_exists($vg."/".$f)) $s=file_get_contents($vg."/".$f);}
	if ($s)
	{
		$m=array();
		$r=preg_match_all("#\s(d=\"[^\"]+\")#u",$s,$m); // extract path definitions
		if ($numberingOn)
		{
			$m2=array();
			$r2=preg_match_all("#(<text[^>]*>[^>]*</text>)#u",$s,$m2); // extract text definitions
		}
		$s="";
		if ($m) // build a new svg
		{
			$z="class=\"normal\" title=\"".$code."\"";
			$s.="<svg ".$z." xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 109 109\">\n";
			foreach($m[1] as $a)
			{
				$st="-webkit-animation-delay:".($n*$t+0.5)."s;animation-delay:".($n*$t+0.5)."s;";
				// 300 is the length of the longuest possible path (last stroke of hiragana nu)
				// ensure minimal compatibility with browsers that don't support pathLength
				if ($pathLengthOn) $pl=" pathLength=\"300\" ";else $pl=" ";
				$s.="<path".$pl."style=\"".$st."\"\n".$a."/>\n";
				$n++;
			}
			if ($numberingOn&&$m2) foreach($m2[1] as $a) $s.=$a."\n";
			$s.="</svg>"; // no \n here, let calling script to decide
		}
		else $s.=errorMsg($code);
	}
	else $s.=errorMsg($code);
	return $s;
}

?>