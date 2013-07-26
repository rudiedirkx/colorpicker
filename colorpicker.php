<?php

if ( isset($_REQUEST['_image']) ) {
	$url = $_REQUEST['_image'];

	if ( $url ) {
		header('Content-type: image/whatever');
		readfile($url);
	}
	exit;
}

?>
<!doctype html>
<html>

<head>
<title>Color picker</title>
<style>
.dark {
	color: white;
}
html {
	margin: 0;
	padding: 0;
}
body {
	margin: 20px;
	padding: 0;
}
div,
canvas,
code {
	display: inline-block;
}
canvas {
	background: #eee;
	border: solid 20px #ccc;
	cursor: pointer;
	position: relative;
}
code {
	min-width: 150px;
	font-size: 30px;
	margin-left: 20px;
	font-weight: bold;
}
span {
	margin-right: 2em;
}
input {
	width: 15em;
	background: rgba(0,0,0,0.1);
	border: 0;
	font-family: inherit;
	font-size: inherit;
	font-weight: inherit;
	color: inherit;
	padding: 3px;
}
.dark input {
	background: rgba(255,255,255,0.1);
}
a {
	color: #000;
	background: #bbb;
	box-shadow: 0 0 20px #000;
	font-weight: bold;
	text-decoration: none;
	padding: 6px 10px;
	border-radius: 20px;
	margin-right: 6px;
}
</style>
</head>

<body>

<canvas></canvas>
<code>
	RGBA: <input id="rgba"></input><br />
	HEX: <input id="hex"></input>
</code>

<p><a href="javascript:location='http://webblocks.nl/tests/colorpicker.php?url=' + encodeURIComponent(location)">Colorpick</a> &lt;&lt; drag to your bookmarks</p>

<script>
var url = '?_image=<?= @$_GET['url'] ?>',
	picking = false;

var $html = document.documentElement,
	$canvas = document.querySelector('canvas'),
	$rgba = document.querySelector('#rgba'),
	$hex = document.querySelector('#hex'),
	ctx = $canvas.getContext('2d'),
	img = new Image;
img.onload = function(e) {
	$canvas.width = this.width;
	$canvas.height = this.height;
	ctx.drawImage(this, 0, 0, this.width, this.height);
};
img.src = url;

$canvas.onselectstart = function(e) {
	e.preventDefault();
};
$canvas.onmousedown = function(e) {
	e.preventDefault();
	picking = true;
	$canvas.onmousemove(e);
};
$html.onmouseup = function(e) {
	picking = false;
};
$canvas.onmousemove = function(e) {
	if ( picking ) {
		var s = getComputedStyle(this),
			x = e.layerX - parseFloat(s.getPropertyValue('border-left-width')),
			y = e.layerY - parseFloat(s.getPropertyValue('border-top-width')),
			imageData = ctx.getImageData(x, y, 1, 1),
			pixel = imageData.data,
			dark = darkColor.apply(null, pixel);

		dark &= pixel[3] != 0;

		pixel[3] /= 255;
		var rgba = 'rgba(' + [].join.call(pixel, ', ') + ')',
			hex = '#' + toHex(pixel[0]) + toHex(pixel[1]) + toHex(pixel[2]);

		$html.style.backgroundColor = rgba;
		$html.classList[ dark ? 'add' : 'remove' ]('dark');

		$rgba.value = rgba;
		$hex.value = hex;
	}
};

function toHex(dec) {
	var hex = dec.toString(16);
	hex.length < 2 && (hex = '0' + hex);
	return hex;
}

function darkColor(r, g, b) {
	var yiq = (r*299 + g*587 + b*114) / 1000;
	return yiq < 128;
}
</script>

<script>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-40956114-1']);
_gaq.push(['_setDomainName', 'webblocks.nl']);
_gaq.push(['_setAllowLinker', true]);
_gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>

</body>

</html>
