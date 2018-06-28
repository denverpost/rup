<?php

function getYouTubeIdFromURL($url) 
{
	$pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
	preg_match($pattern, $url, $matches);
	return isset($matches[1]) ? $matches[1] : false;
}

function getYoutubeThumb($video_id) {

	$filename = $video_id.'-play';

	// CHECK IF YOUTUBE VIDEO
	$handle = curl_init("https://www.youtube.com/watch/?v=" . $video_id);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($handle);
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	if($httpCode == 404 OR !$response) {
		return false;
	}
	curl_close($handle);

	// CREATE IMAGE FROM YOUTUBE THUMB
	$image = imagecreatefromjpeg( "http://img.youtube.com/vi/" . $video_id . "/hqdefault.jpg" );

	// IF HIGH QUALITY WE CREATE A NEW CANVAS WITHOUT THE BLACK BARS
	$cleft = 0;
	$ctop = 45;
	$canvas = imagecreatetruecolor(480, 270);
	imagecopy($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);
	$image = $canvas;
	$imageWidth 	= imagesx($image);
	$imageHeight 	= imagesy($image);
	$logoImage = imagecreatefrompng( "play-hq.png" );
	imagealphablending($logoImage, true);
	$logoWidth 		= imagesx($logoImage);
	$logoHeight 	= imagesy($logoImage);
	$left = round($imageWidth / 2) - round($logoWidth / 2);
	$top = round($imageHeight / 2) - round($logoHeight / 2);
	imagecopy( $image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
	imagepng( $image, $filename .".png", 9);
	$input = imagecreatefrompng($filename .".png");
	$output = imagecreatetruecolor($imageWidth, $imageHeight);
	$white = imagecolorallocate($output,  255, 255, 255);
	imagefilledrectangle($output, 0, 0, $imageWidth, $imageHeight, $white);
	imagecopy($output, $input, 0, 0, 0, 0, $imageWidth, $imageHeight);
	imagejpeg($output, "./temp/" . $filename . ".jpg", 95);
	unlink($filename .".png");
	$filename = $filename . ".jpg";
	if (do_ftp($filename)) {
		return $filename;
	} else {
		return false;
	}

}