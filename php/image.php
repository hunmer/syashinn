<?php
	$s_url = urldecode($_GET['image']);

	//if(!isset($_GET['cache'])) $_GET['cache'] = 1;
	if($s_url != ''){
		$s_format = pathinfo($s_url, PATHINFO_EXTENSION);

		$md5 = md5($s_url);
		$s_file = './images/'.$md5.'.'.$s_format;
		echo "<script>document.title=".$md5.";</script>";
		if(file_exists($s_file)){
			ob_clean();
			header('Content-type:image/'.$s_format);
			exit(file_get_contents($s_file));
		}

		$ch = curl_init();
		$options =  array(
			CURLOPT_HEADER => false,
			CURLOPT_URL => $s_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
			CURLOPT_HTTPHEADER => array('X-FORWARDED-FOR:'.Rand_IP(), 'CLIENT-IP:'.Rand_IP()),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36 Edg/81.0.416.58'

		);
		if($_GET['proxy']){
			$options[CURLOPT_PROXY] = "127.0.0.1";
			$options[CURLOPT_PROXYPORT] = 1080;
		}
		if(isset($_GET['referer'])){
			$options[CURLOPT_HTTPHEADER] = [
				'origin' => urldecode($_GET['referer']),
				'referer' => urldecode($_GET['referer'])
			];
		}
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		curl_close($ch);
		//var_dump($content); exit();
		if(!is_dir('./images/')) mkdir('./images/');
		file_put_contents($s_file, $content);
		ob_clean();
		header('Content-type:image/'.$s_format);
		echo $content;
	}

function Rand_IP(){
	srand(microtime(true));
    return round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000);
}