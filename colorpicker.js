
window.URL || (window.URL = window.webkitURL || window.mozURL);

var url = './image.php?url=' + ( location.search.substr(1) || location.hash.substr(1) ),
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

$canvas.ondragover = function(e) {
	e.preventDefault();
	var item = e.dataTransfer.items[0];
	if ( item && item.type.indexOf('image/') == 0 ) {
		this.classList.add('dragging');
	}
};
$canvas.ondrop = function(e) {
	e.preventDefault();
	var file = e.dataTransfer.files[0];
	img.src = URL.createObjectURL(file);
	this.classList.remove('dragging');
};
$canvas.ondragleave = function(e) {
	e.preventDefault();
	this.classList.remove('dragging');
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
