<?php
	https://img1.mmmw.net/pic/5631/75.jpg
	https://img1.mmmw.net/pic/5631/75.jpg

	https://img28.dsjiejiu.com/wp-content/uploads/m2/5618/80.jpg
	https://img28.dsjiejiu.com/wp-content/uploads/m2/5618/80.jpg
	exit();
	include_once 'simple_html_dom.php';
	$html = str_get_html('<p>a</p><p>b</p><p>c</p><p>d</p>');
	if($html){
		var_dump(count($html->find('p')));
		var_dump($html->find('p', -1)->plaintext);

	}