<?php
	$json = json_decode(json_encode(simplexml_load_string(file_get_contents('web4.xml'), 'SimpleXMLElement', LIBXML_NOCDATA)), true)['url'];
	foreach ($json as $k => $v) {
		//unset($json[$k]['id']);

		switch ($json[$k]['id']) {

				case '165':
				$json[$k]['albumurl'] = "m.cgtpw.com/{0}";
				break;

				case '226':
				$json[$k]['homeurl'] = "www.9999rt.wang/html/{1}/{0}.html";
				break;
		}
	
		$url = $json[$k]['albumurl'];
		if(strpos($url, 'https://') !== false){
			$json[$k]['protocol'] = 'https://';
			$url = str_replace('https://', '', $url);
		}else{
			$json[$k]['protocol'] = 'http://';
			$url = str_replace('http://', '', $url);
			$url = str_replace('//', '', $url);
		}
		$json[$k]['host'] = explode('/', $url)[0];
		$json[$k]['homeurl'] = str_replace(['http://', 'https://', '//'], '', $json[$k]['homeurl']);
		if($json[$k]['homepager'] == '2'){
			$json[$k]['albumurl'] = $json[$k]['host'].'/{0}';
		}else{
			unset($json[$k]['albumurl']);
		}

		$arr = [];
		if(!is_array($v['labelid'])){
			$v['labelid'] = explode(';', $v['labelid']);
		}
		foreach($v['labelid'] as $v1){
			if(substr($v1, 0, 1) == '/'){
				$v1 = substr($v1, 1, strlen($v1) - 1);
			}
			$arr[] = $v1;
		}
		// if(count($arr) > 0){
		// 	var_dump($arr);
		// 	exit();
		// }
		
		$json[$k]['labelid'] = implode(';', $arr);
		switch ($json[$k]['id']) {
			case '1':
				$json[$k]['homeurl'] = 'www.ku137.net/{1}{0}.html';
				break;

			case '192':
			case '196':
				$json[$k]['proxy'] = 1;
				break;

			case '9': // www.airenzhe.com
				$json[$k]['albumurl'] = $json[$k]['albumurl'].'.html';
				break;

			case '13': // tu.heiguang.com
				$json[$k]['elearticle'] = 'div[class="tk_photo"]';
				break;

			case '63':
				$json[$k]['eletitle'] = 'em->a->text';
				break;

			case '204':
				$json[$k]['eletitle'] = '';
				break;

			case '226':
				$json[$k]['albumurl'] = 'www.9999rt.wang/html/{0}';
				break;

			case '203':
				$json[$k]['eletitle'] = '';

				$json[$k]['albumurl'] = 'pxy99.com/meinvtupian/{0}';
				break;

			case '200':
				$json[$k]['albumurl'] = 'www.hn7b.com/mntp/{0}/';
				$json[$k]['labelid'] = 'hgmv;nymv;dmmv;mnmt;rbmn;rtys;mvyh;mnxz;bjnmn;qcmn;swmn;ommn';
				break;
			case '194':
				$json[$k]['elearticle'] = '.type-post';
				$json[$k]['eletitle'] = 'h2->a->text';
				$json[$k]['eleurl'] = 'a->attr->href';

				break;

			case '14':
				$json[$k]['elearticle'] = 'div[class="cover-list"]->div[class="title-info"]';
				$json[$k]['eletitle'] = 'a->text';
				break;

			case '185':
				$json[$k]['elethumbnail'] = 'img->attr->src';
				break;

				case '19': // www.juemei.com
				case '44':
				case '82':
				case '177':
				case '189':
				case '257':
				case '263':
				case '278':

					$json[$k]['fulllalbe'] = 1;
					break;

				case '138':
					$json[$k]['albumurl'] = "www.itmtu.net/{0}.html";
					break;

				case '191':
				$json[$k]['eletitle'] = "a->text";
					break;

				case '344':
				$json[$k]['albumurl'] = "www.simei8.com/html/{0}";
					break;

				case '356':
				$json[$k]['albumurl'] = "usbitch.cc/arttypehtml/{0}.html";
					break;

				case '360':
				$json[$k]['homeurl'] = "www.chinaart8.com/{1}{0}.html";
					break;

				case '361':
				$json[$k]['albumurl'] = "t2cy.com/acg/{0}";
					break;

				case '166':
					$json[$k]['elearticle'] = "div[class=\"hytyk_list\"]->li";
					$json[$k]['eletitle'] = "dt->a->text";
					break;


				case '146':
					$json[$k]['homeurl'] = "www.zdqx.com/xzj/{0}.html";
					$json[$k]['albumurl'] = "www.zdqx.com/xzj/{0}";
					break;

				case '83':
					$json[$k]['fulllalbe'] = 1;
					$json[$k]['homeurl'] = "www.feizl.com/{1}defaultp{0}.htm";
					break;

				case '109':
					$json[$k]['homeurl'] = "www.nmtaotu.com/{1}_{0}.html";
					break;

				case '87':
					$json[$k]['homeurl'] = "www.hexuexiao.cn/{1}list-{0}.html";
					break;

				case '288':
					$json[$k]['elearticle'] = ".type-post";
					$json[$k]['eleurl'] = ".entry-title->a->text";
					break;

				case '298':
					$json[$k]['albumurl'] = "www.930tu.com/meinv/{0}";
					break;

				case '305':
					$json[$k]['elearticle'] = ".post-list-item";
					break;

				case '323':
					$json[$k]['elearticle'] = ".pic-list->li";
					break;

				case '326':
					$json[$k]['elearticle'] = "div[class=\"xxx\"]->li";
					break;

				case '290':
					$json[$k]['elearticle'] = 'div[class="lm_lbimg"]';
					$json[$k]['eletitle'] = 'parentNode->p->a->text';

					//$json[$k]['eleurl'] = ".entry-title->a->text";
					break;


				case '51':
					$json[$k]['albumurl'] = "www.girlsky.cn/mntp/{0}";
					break;

				case '24': // www.yeitu.net
					$json[$k]["elearticle"] = "div[class=\"list-box\"]->li";
					$json[$k]["eletitle"] = "a->text";
					$json[$k]["elethumbnail"] = "img->attr->data-echo";

				case '35':
					$json[$k]["elearticle"] = "div[class=\"index_pic\"]";
					break;

			default:
				break;
		}

		unset($json[$k]['homepager']);
		foreach (['elearticle', 'eletitle', 'eleurl', 'elethumbnail', 'elephtotolist', 'eleimgsrc', 'classname'] as $k1) {
			$json[$k][$k1] = str_replace('tag->', '', $json[$k][$k1]);
		}
	}
	$new = [];
	$i = 0;
	foreach ($json as $key => $value) {
		if($value['id'] == '244' && $value['webname'] == '做文章'
	 || $value['id'] == '269' && $value['webname'] == '🙂赤图网' || $value['id'] == '282' && $value['webname'] == '国美Gif'){
			continue;
		}
		$new[$i] = $value;
		$i++;
	}
	file_put_contents('web.json', json_encode($new,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));