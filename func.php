<?php

function encrypt($string) {
	$key = '69&3jV39sA!H#uZC33';
	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	}
	$results = base64_encode($result);
	$results = strtr($results, '+/', '-_');
	$results = str_replace('=', '', $results);
	return $results;
}

function curl($url) {
	$ch = @curl_init();
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

function Drive($id) {
	$domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) .'/proxy.php';
	$get = curl('https://docs.google.com/file/d/'.$id.'/view');
	preg_match('#DRIVE_STREAM=(.*?);#i', $get, $cookie);
	$data = explode('"fmt_stream_map","', $get);
	$data = explode('"]', $data[1]);
	$data = explode(',', $data[0]);
	$data = str_replace(array('\u003d', '\u0026'), array('=', '&'), $data);
	foreach($data as $list) {
		$data = explode('|', $list);
		if($data[0] == 37) {$s[1080] = '{"file":"'.$domain.'?data='.encrypt($data[1]).'&key='.$cookie[1].'","type":"mp4","label":"1080p"}';}
		if($data[0] == 22) {$s[720] = '{"file":"'.$domain.'?data='.encrypt($data[1]).'&key='.$cookie[1].'","type":"mp4","label":"720p"}';}
		if($data[0] == 59) {$s[480] = '{"file":"'.$domain.'?data='.encrypt($data[1]).'&key='.$cookie[1].'","type":"mp4","label":"480p","default":"true"}';}
		if($data[0] == 18) {$s[360] = '{"file":"'.$domain.'?data='.encrypt($data[1]).'&key='.$cookie[1].'","type":"mp4","label":"360p"}';}
	}
	krsort($s);
	$js = implode(',',$s);
	return '['.$js.']';
}