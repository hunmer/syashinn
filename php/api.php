<?php
	// error_reporting(0);
			
	//echo file_get_contents('test.json');
	 //exit();

	/*$_GET = [
		'id' => 8,
		'sid' => 0,
		//'url' => 'https://www.rtys8.cc/zgrtys/ZG1.html', // 是否使用子级规则
		'page' => 1
	];*/
	$_GET['debug'] = false;
	if(!isset($_GET['cache'])) $_GET['cache'] = false;
	$_POST = [];
	$_POST['max'] = 10;
	// echo file_get_contents('https://www.girlsky.cn/mntp/xgmn/');
	// exit();
	header("Access-Control-Allow-Origin: *");
	set_time_limit(-1);
		$_POST = array_merge_recursive($_POST, $_GET);
		if(isset($_GET['id']) && isset($_GET['sid'])){

			$j = [];
			foreach (json_decode(file_get_contents('web.json'), true) as $key => $value) {
				if(intval($value['id']) == $_GET['id']){
					$j = $value;
					break;
				}
			}
			if(count($j) == 0){
				exit();
			}

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$_GET['data'] = $j;
			if($_GET['debug']) echo '"id": "'.$j['id'].'",'."\r\n";
			//var_dump($j);
			//exit();
			//var_dump($j);

			$sid = $_GET['sid'];
			$_GET['tid'] = 0;
			$labelid = [];
			$labelname = [];
			$id = 0;
			while(isset($j['labelid-'.$id])){
				$ids = explode(';', $j['labelid-'.$id]);
				//if($ids[count($ids)-1] == '') array_pop($ids); // 去除空

				$labelid = array_merge($labelid, $ids);
				$names = explode(';', $j['label-'.$id]);
				for($i=0;$i<count($ids);$i++){
					if($cnt == $sid){ // 分类id
						$_GET['tid'] = $id;
					}
					$cnt++;
					if(!isset($names[$i])){
						$names[$i] = '';
					}
				}
				$labelname = array_merge($labelname, $names);
				$id++;
			}


			$_GET['labelname'] = $labelname;
			$_GET['labelid'] = $labelid;
			// var_dump($labelid, $labelname);
			// exit();
			//if(isset($labelid[$sid])){
				loadPage();
			//}

		}

		function loadPage(){
			$cnt = 0;
			$j = $_GET['data'];
			$sid = $_GET['sid'];
			$list = $_GET['labelid'][$sid];
			if($_GET['debug']) echo $_GET['labelname'][$sid]."\r\n";
			// http://www.mm131.net/xinggan/list_6_1.html (错误)
			//  http://www.mm131.net/xinggan (正确)
			if($_GET['url'] != ''){
				$url = $_GET['url'];
			}else{
				if($_GET['page'] == 1 && isset($j['albumurl-'.$_GET['tid']])){
					if(!$j['fulllalbe']){
						$list = explode('/', $list)[0]; // 取第一个目录
					}
					$url = str_replace('{0}', $list, $j['albumurl-'.$_GET['tid']]);
				}else{
					$url = str_replace('{0}', $_GET['page'], $j['homeurl-'.$_GET['tid']]);
					$url = str_replace('{1}', $list, $url);
				}
				$url = $j['protocol'].$url;
			}

			$_GET['url'] = $url;
			if($_GET['cache'] && file_exists('./cache/'.md5($_GET['url']).'.json')){
				echo file_get_contents('./cache/'.md5($_GET['url']).'.json');
				exit();
			}

			$_GET['lastUrl'] = $url;
			if(isset($_GET['lastUrl']) && $_GET['lastUrl'] == $url){
				logError();
			}
			$_GET['lastUrl'] = $url;
			if($_GET['debug']) echo '* -> ' . $url."\r\n";
			//exit();

			$file = './cache/'.md5($url).'.html';
			$exists = file_exists($file);
			if(0 && $exists){
				$content = getFile($file);
				// if($j['charset'] != 'utf-8'){
					// 	$html = mb_convert_encoding($html, "utf-8", $j['charset']);
					// }
			}else{
				if(isset($j['curl']) && $j['curl']['enable'] === false){
					$content = file_get_contents($url);
				}else{
					$ch = curl_init();
					$options =  array(
						CURLOPT_HEADER => false,
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						 CURLOPT_ENCODING => 'gzip',
						CURLOPT_TIMEOUT => 30,
						CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
						CURLOPT_HTTPHEADER => array('X-FORWARDED-FOR:'.Rand_IP(), 'CLIENT-IP:'.Rand_IP()),
						CURLOPT_FOLLOWLOCATION => TRUE,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_SSL_VERIFYHOST => false,
						CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36 Edg/81.0.416.58'
					);
					if($j['curl']['proxy']){
						$options[CURLOPT_PROXY] = "127.0.0.1";
						$options[CURLOPT_PROXYPORT] = 1080;
					}
					// $options[CURLOPT_HTTPHEADER] = [
					// 	// 'origin' => $_GET['referer'],
					// 	'referer' => $j['albumurl']
					// ];
					curl_setopt_array($ch, $options);
					$content = curl_exec($ch);
				}
			}
			// if($j['charset'] != 'utf-8'){
			// 	$content = mb_convert_encoding($content, "utf-8", $j['charset']);
			// }
			//var_dump($content);
			// if(!$exists){
			// 	if(!is_dir('./cache/')){
			// 		mkdir('./cache/', 0777);
			// 	}
			// 	// if($j['charset'] != 'utf-8'){
			// 	// 	$html = mb_convert_encoding($html, "utf-8", $j['charset']);
			// 	// }
				file_put_contents($file, $content);
			//}
			if($_GET['debug']) var_dump(strlen($content));
			if(strlen($content) == 0){
				logError();
			}

			//var_dump($content);
			include_once 'simple_html_dom.php';
			$html = str_get_html($content);
			if($html){
				$doms = $html->find(str_replace('->', ' ', _g('elearticle')));
				if(count($doms) == 0){
					if($_GET['debug']) var_dump($content);
					die('没有找到图片!');
				}
				if($_GET['debug']) var_dump('dom ->'.count($doms));

				$a_title = explode('->', _g('eletitle'));
				$a_url = explode('->', _g('eleurl'));
				$a_cover = explode('->',_g('elethumbnail'));
				foreach ($doms as $k => $dom) {
					$lastTag = strtolower($dom->tag);
					$title = getText(parseDom($lastTag, $dom, $a_title));
					$url = checkUrl($j['host'], parseDom($lastTag, $dom, $a_url));
					$cover = checkUrl($j['host'], parseDom($lastTag, $dom, $a_cover));
					if(strpos($cover, 'background-image:url(') !== false){
						$cover = str_replace(['background-image:url(', ');'], '', $cover);
					}

					$_POST['res'][] = [
						'title' => $title,
						'url' => $url,
						'cover' => $cover
					];
					$cnt++;
					// var_dump($dom->find(str_replace('->', ' ', $j['eletitle'])));
					if($_GET['debug'] && $k == 1){
						var_dump($title);
						var_dump($url);
						var_dump($cover);
					}
				}
			}
			if($cnt == 0){
				logError();
			}
			checkEcho();
		}

		function checkEcho(){
			if(
				(isset($_GET['url']) && isset($_GET['data']['children']['max']) && count($_POST['res']) >= $_GET['data']['children']['max']) || count($_POST['res']) >= $_POST['max']){
				echoJson();
			}else{
				$_GET['page']++;
				loadPage();
			}
		}

		function logError(){
			$_GET['cnt']++;
			if($_GET['cnt'] >= 3){
				echoJson();
			}
		}

		function echoJson(){
			if(isset($_GET['data']['res_album'])){
				foreach ($_GET['data']['res_album'] as $key => $value) {
					switch($key){
						case 'url':
						case 'title':
						case 'cover':
							if(!is_array($value)){
								$value = [$value];
							}
							foreach($value as $v){
								if(is_array($v['f'])){
									foreach($v['f'] as $v_k => $v_v){
										if(strpos($v_v, '{requst_url}') !== false){
											$v['f'][$v_k] = str_replace('{requst_url}', $_GET['url'], $v['f'][$v_k]); 
										}
									}
								}
								
								foreach($_POST['res'] as $r_k => $r_v){
									switch($v['t']){
										case 'replace':
											$_POST['res'][$r_k][$key] = str_replace($v['f'][0], $v['f'][1], $_POST['res'][$r_k][$key]);
											break;

										case "proxy":
											$_POST['res'][$r_k][$key] = './php/image.php?image='.urlencode($_POST['res'][$r_k][$key]).'&referer='.urlencode($_GET['url']);
											break;
									}
								}
							}
							break;
					}
				}
			}
			$res = json_encode($_POST);
			file_put_contents('./cache/'.md5($_GET['url']).'.json', $res);
			echo $res;
			//echo '"id": "'.$_GET['data']['id'].'",'."\r\n";

			exit();
		}


		function _g($s){
			if($_GET['url'] != '' && isset($_GET['data']['children'][$s])){
				return $_GET['data']['children'][$s];
			}
			return $_GET['data'][$s];
		}

		function getText($text){
			return trim(explode("\n", $text)[0]);
		}

		function checkUrl($host, $url){
			if(is_null($url) || $url == '') return '';
			if(strpos($url, '/') === 0){
					$url = $host.$url;
			}else
			if(strpos($url, '//') === FALSE){
				$url = $host.'/'.$url;
			}
			return trim($url);
		}

	function parseDom($lastTag, $d, $arr){
		$skip = false;
		foreach ($arr as $k => $s) {
			$s = strtolower($s);
			switch($s){
				case 'text':
					return $d->plaintext;

				case 'attr':
					return isset($arr[$k+1]) ? $d->$arr[$k+1] : '';

				case 'parentnode':
					$d = $d->parent();
					$skip = true;
					break;

				// case 'children':
				// 	$d = $d->children ()
				// 	break;
			}
			if(!$skip){
					if($s != 'div' && $lastTag !== $s){ // 如果是同个元素则不获取.但div可以互相覆盖
					$d = $d->find($s, 0);
				}
			}
			$skip = false;
		}
		return '';
	}

	function getFile($filePath){
		$text = file_get_contents($filePath);  
        //$encodType = mb_detect_encoding($text);  
        define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));  
        define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));  
        define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));  
        define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));  
        define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));  
        $first2 = substr($text, 0, 2);  
        $first3 = substr($text, 0, 3);  
        $first4 = substr($text, 0, 3);  
        $encodType = "";  
        if ($first3 == UTF8_BOM)  
            $encodType = 'UTF-8 BOM';  
        else if ($first4 == UTF32_BIG_ENDIAN_BOM)  
            $encodType = 'UTF-32BE';  
        else if ($first4 == UTF32_LITTLE_ENDIAN_BOM)  
            $encodType = 'UTF-32LE';  
        else if ($first2 == UTF16_BIG_ENDIAN_BOM)  
            $encodType = 'UTF-16BE';  
        else if ($first2 == UTF16_LITTLE_ENDIAN_BOM)  
            $encodType = 'UTF-16LE';  

        //下面的判断主要还是判断ANSI编码的·  
        if ($encodType == '') {//即默认创建的txt文本-ANSI编码的  
            $content = iconv("GBK", "UTF-8", $text);  
        } else if ($encodType == 'UTF-8 BOM') {//本来就是UTF-8不用转换  
            $content = $text;  
        } else {//其他的格式都转化为UTF-8就可以了  
            $content = iconv($encodType, "UTF-8", $text);  
        }  
        return $text;
	}
function Rand_IP(){
	srand(microtime(true));
    return round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000).".".round(rand(600000, 2550000) / 10000);
}