<?php

/*
 * Copyright (c) 2013 Robert Lane
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class MCServerEngine {

/*  The class containing the engine is under Matt Harzewski's Copyright
 *
 * Copyright (c) 2012 Matt Harzewski
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

	public $server;
	public $online, $motd, $online_players, $max_players;
	public $error = "OK";

	function __construct($url, $port = '25565') {

		$this->server = array(
			"url" => $url,
			"port" => $port
		);

		if ( $sock = @stream_socket_client('tcp://'.$url.':'.$port, $errno, $errstr, 1) ) {

			$this->online = true;

			fwrite($sock, "\xfe");
			$h = fread($sock, 2048);
			$h = str_replace("\x00", '', $h);
			$h = substr($h, 2);
			$data = explode("\xa7", $h);
			unset($h);
			fclose($sock);

			if (sizeof($data) == 3) {
				$this->motd = $data[0];
				$this->online_players = (int) $data[1];
				$this->max_players = (int) $data[2];
			}
			else {
				$this->error = "Cannot retrieve server info.";
			}

		}
		else {
			$this->online = false;
			$this->error = "Cannot connect to server.";
		}

	}

}

/*  End Matt Harzewski's Copyright  */

$minecraftme = new MCServerEngine('mcserver.geekgamer.tv');
//Choose the base image based on url received
$bg_image = strtolower($_GET["image"]);
switch ($bg_image) {
    case "rose":
        $image = imagecreatefrompng('images/rose.png');
        break;
    case "chase":
        $image = imagecreatefrompng('images/chase.png');
        break;
    case "joe":
        $image = imagecreatefrompng('images/joe.png');
        break;
    case "imperialphantom":
        $image = imagecreatefrompng('images/imp.png');
        break;
    default:
        $image = imagecreatefrompng('images/default.png');
}


header('content-type: image/png');

//$image = imagecreate(500, 150);
//$image = imagecreatefrompng('background_rose.png');

$blue = imagecolorallocate($image, 0, 0, 225);
$white = imagecolorallocate($image, 255, 255, 225);
$black = imagecolorallocate($image, 1, 1, 1);

$font_path = './fonts/LogoCraft.ttf';
$font_path01 = './fonts/Minecraft.ttf';

$string = 'MINECRAFTME';
$players = 'Players: '.$minecraftme->online_players.'/'.$minecraftme->max_players;

imagettftext($image,  16, 0, 12, 41, $black, $font_path, $string);
imagettftext($image,  16, 0, 10, 40, $white, $font_path, $string);

imagettftext($image,  12, 0, 13, 61, $black, $font_path01, $minecraftme->motd);
imagettftext($image,  12, 0, 11, 60, $white, $font_path01, $minecraftme->motd);

imagettftext($image,  12, 0, 13, 106, $black, $font_path01, $players);
imagettftext($image,  12, 0, 11, 105, $white, $font_path01, $players);

imagettftext($image,  12, 0, 13, 123, $black, $font_path01, 'mcserver.geekgamer.tv');
imagettftext($image,  12, 0, 11, 122, $white, $font_path01, 'mcserver.geekgamer.tv');

imagepng($image);

imagedestroy($image);

?>
