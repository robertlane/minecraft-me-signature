<?php

class MCServerEngine {

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

$minecraftme = new MCServerEngine('mcserver.geekgamer.tv');
//Choose the base image based on url received
switch ($_GET["image"]) {
    case "rose":
        $image = imagecreatefrompng('images/rose.png');
        break;
    case "chase":
        $image = imagecreatefrompng('images/chase.png');
        break;
    case "joe":
        $image = imagecreatefrompng('images/rose.png');
        break;
    default:
        $image = imagecreatefrompng('images/rose.png');
}

in

header('content-type: image/png');

//$image = imagecreate(500, 150);
//$image = imagecreatefrompng('background_rose.png');

$blue = imagecolorallocate($image, 0, 0, 225);
$white = imagecolorallocate($image, 255, 255, 225);

$font_path = './fonts/LogoCraft.ttf';
$font_path01 = './fonts/Minecraft.ttf';

$string = 'MINECRAFTME';
$players = 'Players: '.$minecraftme->online_players.'/'.$minecraftme->max_players;

imagettftext($image,  16, 0, 10, 40, $white, $font_path, $string);

imagettftext($image,  12, 0, 11, 60, $white, $font_path01, $minecraftme->motd);

imagettftext($image,  12, 0, 11, 125, $white, $font_path01, $players);

imagettftext($image,  12, 0, 11, 140, $white, $font_path01, 'mcserver.geekgamer.tv');

imagepng($image);

imagedestroy($image);

?>
