<?php

function barcode( $text="0", $size="20", $orientation="horizontal", $code_type="code128", $print=false, $SizeFactor=1 ) {
	$code_string = "";
	// Translate the $text into barcode the correct $code_type
	if ( in_array(strtolower($code_type), array("code128")) ) {
		$chksum = 64;
		// Must not change order of array elements as the checksum depends on the array's key to validate final code
		$code_array = array("@"=>"121223","."=>"122231","0"=>"123122","1"=>"123221","2"=>"223211",
			"3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122","A"=>"111323",
			"B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113",
			"I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121",
			"P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123",
			"W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221",
			"e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211",
			"l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241",
			"s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141",
			"z"=>"214121");
		$code_keys = array_keys($code_array);
		$code_values = array_flip($code_keys);
		for ( $X = 1; $X <= strlen($text); $X++ ) {
			$activeKey = substr( $text, ($X-1), 1);
			$code_string .= $code_array[$activeKey];
			$chksum=($chksum + ($code_values[$activeKey] * $X));
		}
		$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 64) * 64))]];
		$code_string = "211214" . $code_string . "2331112";
	}
	// Pad the edges of the barcode
	$code_length = 10;
	if ($print) {
		$text_height = 30;
	} else {
		$text_height = 0;
	}

	for ( $i=1; $i <= strlen($code_string); $i++ ){
		$code_length = $code_length + (integer)(substr($code_string,($i-1),1));
	}

		$img_width = $code_length*$SizeFactor;
		$img_height = $size;

	$image = imagecreate($img_width, $img_height + $text_height);
	$black = imagecolorallocate ($image, 0, 0, 0);
	$white = imagecolorallocate ($image, 255, 255, 255);
	imagefill( $image, 0, 0, $white );

	$location = 10;
	for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
		$cur_size = $location + ( substr($code_string, ($position-1), 1) );
		if ( strtolower($orientation) == "horizontal" ) {
			imagefilledrectangle($image, $location * $SizeFactor, 0, $cur_size * $SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black));
		}else
			imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
		$location = $cur_size;
	}

	header("Content-Transfer-Encoding: binary");
	header('Content-Description: File Transfer');
	header ('Content-type: image/png');
	header("Content-Disposition: inline; filename= ". $text);
	imagepng($image);





}

$text = $_GET["text"];
$size = "20";
$orientation = "horizontal";
$code_type = "code128";
$print = false;
$sizefactor = "1";

barcode($text, $size, $orientation, $code_type, $print, $sizefactor );
?>




