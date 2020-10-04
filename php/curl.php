<?php
//echo file_get_contents('http://www.xgrtys.com/riben/');
exit();
$ch = curl_init();
$options =  array(
	CURLOPT_HEADER => false,
	CURLOPT_URL => 'http://www.xgrtys.com/riben/',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
	CURLOPT_FOLLOWLOCATION => TRUE,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36 Edg/81.0.416.58'
);
if($_GET['proxy']){
	$options[CURLOPT_PROXY] = "127.0.0.1";
	$options[CURLOPT_PROXYPORT] = 1080;
}
$options[CURLOPT_HTTPHEADER] = [
	// 'origin' => $_GET['referer'],
	'referer' => $j['albumurl']
];
curl_setopt_array($ch, $options);
$html = curl_exec($ch);
var_dump($html);