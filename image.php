<?php

if ( isset($_REQUEST['url']) ) {
	$url = $_REQUEST['url'];

	if ( $url ) {
		header('Content-type: image/whatever');
		readfile($url);
	}
	exit;
}
