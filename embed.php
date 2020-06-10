<?php 
/**
 * Name: Google Drive Proxy Player Advanced Script
 * Version: 1.1, Last updated: November 6, 2019
 * Website: https://apicodes.com
 * Contact: Support@apicodes.com
 */
?>
<!DOCTYPE html>
<html>
<head>
	<title>Google Drive Proxy Video Player - APICodes</title>
	<meta name="robots" content="noindex">
	<link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon" />
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://ssl.p.jwpcdn.com/player/v/8.8.6/jwplayer.js"></script>
	<script type="text/javascript">jwplayer.key="cLGMn8T20tGvW+0eXPhq4NNmLB57TrscPjd1IyJF84o=";</script>
	<style type="text/css" media="screen">html,body{padding:0;margin:0;height:100%}#apicodes-player{width:100%!important;height:100%!important;overflow:hidden;background-color:#000}.apicodes-container{position:relative;width:100%;height:0;padding-bottom:56.25%;background-color:#000;}.apicodes-video{position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;}.apicodes-frame{position:absolute;height:50px;right:8px;margin-top:8px;width:50px;z-index:1;background:url(./assets/images/logo.png);background-repeat:no-repeat;background-size:50px 50px;}</style>
</head>
<body>

<?php 
		error_reporting(0);
		
		$data = (isset($_GET['data'])) ? $_GET['data'] : '';

		if ($data != '') {
			
			include_once 'config.php';

			$data = json_decode(decode($data));

			$link = (isset($data->link)) ? $data->link : '';

			$sub = (isset($data->sub)) ? $data->sub : '';

			$poster = (isset($data->poster)) ? $data->poster : '';

			$tracks = '';
			
			foreach ($sub as $key => $value) {
			    $tracks .= '{ 
						        file: "'.$value.'", 
						        label: "'.$key.'",
						        kind: "captions"
							   },';
			}

			preg_match('/https?:\/\/(?:www\.)?(?:drive|docs)\.google\.com\/(?:file\/d\/|open\?id=)?([a-z0-9A-Z_-]+)(?:\/.+)?/is', $link, $id);

	        $cache = phpFastCache::get($id[1]);
	        if ($cache == NULL) {
	            $sources = Drive($id[1]);
	            phpFastCache::set($id[1], $sources, '7200');
	        }
	        else $sources = $cache;

			$result = '<div id="apicodes-player"></div>';

			$data = 'var player = jwplayer("apicodes-player");
						player.setup({
							sources: '.$sources.',
							aspectratio: "16:9",
							startparam: "start",
							primary: "html5",
							autostart: false,
							preload: "auto",
							image: "'.$poster.'",
						    captions: {
						        color: "#f3f368",
						        fontSize: 16,
						        backgroundOpacity: 0,
						        fontfamily: "Helvetica",
						        edgeStyle: "raised"
						    },
						    tracks: ['.$tracks.']
						});
						player.addButton(
							"./assets/images/download.svg",
							"Download Video",
							function () {
								var win = window.open(player.getPlaylistItem()["file"],"_blank");
								win.focus();
							},
							"download"
						);
			            player.on("setupError", function() {
							$("#apicodes-player").html("<div class=\"apicodes-container\"> <iframe src=\"https://drive.google.com/file/d/'.$id[1].'/preview\" width=\"100%\" height=\"100%\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" class=\"apicodes-video\"></iframe> <div class=\"apicodes-frame\"></div></div>")
			            });
						player.on("error" , function(){
							$("#apicodes-player").html("<div class=\"apicodes-container\"> <iframe src=\"https://drive.google.com/file/d/'.$id[1].'/preview\" width=\"100%\" height=\"100%\" frameborder=\"0\" allowfullscreen=\"allowfullscreen\" class=\"apicodes-video\"></iframe> <div class=\"apicodes-frame\"></div></div>")
						});';
			$packer = new Packer($data, 'Normal', true, false, true);

			$packed = $packer->pack();

			$result .= '<script type="text/javascript">' . $packed . '</script>';

			echo $result;

		} else echo 'Empty link!';

?>

</body>
</html>
