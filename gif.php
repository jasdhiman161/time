<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

	//Leave all this stuff as it is
	date_default_timezone_set('Asia/Calcutta');
	include 'GIFEncoder.class.php';
	include 'php52-fix.php';
	$time = $_GET['time'];
	$future_date = new DateTime(date('r',strtotime($time)));
	$time_now = time();
	$now = new DateTime(date('r', $time_now));
	$frames = [];	
	$delays = [];


	// Your image link
	$image = imagecreatefrompng('images/countdown.png');

	$delay = 100;// milliseconds

	$font = [
		'size' => 91, // Font size, in pts usually.
		'angle' => 0, // Angle of the text
		'x-offset' => 18, // The larger the number the further the distance from the left hand side, 0 to align to the left.
		'y-offset' => 135, // The vertical alignment, trial and error between 20 and 60.
		'file' => 'Futura.ttc', // Font path
		'color' => imagecolorallocate($image, 0, 0, 0), // RGB Colour of the text
	];
	for($i = 0; $i <= 60; $i++){
		
		$interval = date_diff($future_date, $now);
		
		if ($future_date < $now) {
			// Open the first source image and add the text.
			$image = imagecreatefrompng('images/end.png');
			
			if ($interval->days > 9) {
			    $text = '';
			  
			} else {
			    $text = 'Offer end';
			  
			}
			imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
			imagegif($image);
			$frames[] = ob_get_contents();
			$delays[] = $delay;
			$loops = 1;
			ob_end_clean();
			break;
		} else {
			// Open the first source image and add the text.
			$image = imagecreatefrompng('images/countdown.png');
			
			if ($interval->days > 9) {
			    $text = $interval->format('%a %H %I %S');
			} else {
			    $text = $interval->format('0%a %H %I %S');
			}
			imagettftext($image, $font['size'], $font['angle'], $font['x-offset'], $font['y-offset'], $font['color'], $font['file'], $text);
			ob_start();
			imagegif($image);
			$frames[] = ob_get_contents();
			$delays[] = $delay;
			$loops = 0;
			ob_end_clean();
		}

		$now->modify('+1 second');
	}

	//expire this image instantly
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	$gif = new AnimatedGif($frames, $delays, $loops);
	$gif->display();
