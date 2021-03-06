<?php
/**
 * @see: https://www.mathsisfun.com/sine-cosine-tangent.html  // Math Function
 * @see: http://svgpocketguide.com/book  // SVG Ref
 */

/**
 * Color helper
 */
function brighten($color, $percentage) {
	$steps = max(-10000, min(10000, $percentage*10000)) / 100;
	$steps = round($percentage * 255);

	$color = str_replace('#', '', $color);
	if (strlen($color) == 3)
        	$color = str_repeat(substr($color, 0, 1), 2) .
        		str_repeat(substr($color, 1, 1), 2) .
        		str_repeat(substr($color, 2, 1), 2);
	if (strlen($color) != 6)
		return false;
	$RGB = str_split($color, 2);

	$output = '#';
	foreach ($RGB as $color) {
		$color = hexdec($color);
		$color = max( 0, min(255, $color + $steps) );
		$output .= str_pad( dechex($color), 2, '0', STR_PAD_LEFT );
	}
	return $output;
}

function invert($color) {
	$color = str_replace('#', '', $color);
	if (strlen($color) == 3)
        	$color = str_repeat(substr($color, 0, 1), 2) .
        		str_repeat(substr($color, 1, 1), 2) .
        		str_repeat(substr($color, 2, 1), 2);
	if (strlen($color) != 6)
		return false;
	$RGB = str_split($color, 2);

	$output = '#';
	foreach ($RGB as $color) {
		$color = hexdec($color);
		$color = 255 - $color;
		$output .= str_pad( dechex($color), 2, '0', STR_PAD_LEFT );
	}
	return $output;
}

/* ========== Starts here ================================================= */

header('Content-type: image/svg+xml');
header('Content-Disposition: inline; filename="' . basename(__FILE__, '.php') . '"');

$req = array();
$defaults = array('icon', 'avatar', 'nofill', 'guides', 'solid', 'inv');
foreach ($defaults as $value)
	$req[$value] = false;
foreach ($_REQUEST as $key => $value)
	if (in_array($key, $defaults))
		$req[$key] = ($value == '') ? true : filter_var($value, FILTER_VALIDATE_BOOLEAN);
extract($req);


// primary colors
$black = '#000';
$red = '#c30000';
$green = '#00c300';
$blue = '#0000c3';

// gradients
$red_fr = '#fb0000';
$red_to = '#8c0000';
$green_fr = '#00fb00';
$green_to = '#008c00';
$blue_fr = '#0000fb';
$blue_to = '#00008c';

// more color
$red_alt = '#f03b4d';  // "tec"
$black_alt = '#565656';  // "smith"

$fill = false;
$clr_fil = false;
if (isset($_REQUEST['fill'])) {
	$fill = true;
	if ($_REQUEST['fill'] == '') {
		$clr_fil = '#000';
	} else {
		$clr_fil = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fill']);
	}
} else if (isset($_REQUEST['fills'])) {
	$fill = true;
	$solid = true;
	if ($_REQUEST['fills'] == '') {
		$clr_fil = '#000';
	} else {
		$clr_fil = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fills']);
	}
}

$size = false;
if (isset($_REQUEST['size']) && ($_REQUEST['size'] != ''))
	$size = round($_REQUEST['size']);
if ($size !== false) {
	$size = round($size);
	if ($size <= 0) $size = false;
}
if ($size === false) $size = 1024;


$w = $h = $size;
$s = round($w / 25);


global $add_x, $add_y;
$add_x = $add_y = 0;


/**
 *  X & Y Co-ordinates
 */
function c($x, $y, $xo = 0, $yo = 0, $end = ' ') {
	global $add_x, $add_y;
	return ($x+$xo+$add_x) . ' ' . ($y+$yo+$add_y) . $end;
}

function side_t($w, $h, $s) {
	$out = '';

	$o = round($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o;
	$y0 = $o - round(sin($a) * $s);

	$iw = $w / 2;
	$ih = $h / 4;
	$x1 = $iw;
	$y1 = 0;

	$out .= c(  $x1,  $y1,  $x0,  $y0  );
	$out .= c(  $x1+$iw,  $y1+$ih,  $x0,  $y0  );
	$out .= c(  $x1,  $y1+($ih * 2),  $x0,  $y0  );
	$out .= c(  $x1-$iw,  $y1+$ih,  $x0,  $y0  );
	$out .= c(  $x1,  $y1,  $x0,  $y0,  ''  );

	return $out;
}

function side_r($w, $h, $s) {
	$out = '';

	$o = round($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o + $s;
	$y0 = $o + round(sin($a) * $s);

	$iw = $w / 2;
	$ih = $h / 4;
	$x1 = $iw;
	$y1 = ($h / 2);

	$out .= c(  $x1,  $y1,  $x0,  $y0  );
	$out .= c(  $x1+$iw,  $y1-$ih,  $x0,  $y0  );
	$out .= c(  $x1+$iw,  $y1+$ih,  $x0,  $y0  );
	$out .= c(  $x1,  $y1+($ih*2),  $x0,  $y0  );
	$out .= c(  $x1,  $y1,  $x0,  $y0,  ''  );

	return $out;
}

function side_l($w, $h, $s) {
	$out = '';

	$o = round($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o - $s;
	$y0 = $o + round(sin($a) * $s);

	$iw = $w / 2;
	$ih = $h / 4;
	$x1 = $iw;
	$y1 = ($h / 2);

	$out .= c(  $x1,  $y1,  $x0,  $y0  );
	$out .= c(  $x1-$iw,  $y1-$ih,  $x0,  $y0  );
	$out .= c(  $x1-$iw,  $y1+$ih,  $x0,  $y0  );
	$out .= c(  $x1,  $y1+($ih*2),  $x0,  $y0  );
	$out .= c(  $x1,  $y1,  $x0,  $y0,   ''  );

	return $out;
}

// figure out dimentions
if ($icon) {
	$width = $w;
} elseif ($avatar) {
	$width = round($w * 1.5);
	$add_x = round($w / 4);
} else {
	$width = $w + round($w * 4.74);
}
if ($avatar) {
	$height = round($h * 1.5);
	$add_y = ($h * 0.1);
} else {
	$height = $h;
}

// figure out colors
if ($inv) $black = invert($black);
if ($nofill) {
	$red = $green = $blue = 'none';
} else if ($solid) {
	if ($fill) $red = $green = $blue = $clr_fil;
} else {
	if ($fill) $red = $green = $blue = 'url(#grad-fill)';
	else {
		$red = 'url(#grad-red)';
		$green = 'url(#grad-green)';
		$blue = 'url(#grad-blue)';
	}
}
if ($fill) $black = brighten($clr_fil, -0.10);



// now the SVG
?><svg xmlns="http://www.w3.org/2000/svg" width="<?= $width ?>" height="<?= $height ?>" viewBox="0 0 <?= $width ?> <?= $height ?>">
<?php if ($fill && !$solid) { ?>
	<defs>
		<linearGradient id="grad-fill" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= brighten($clr_fil, 0.20) ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= brighten($clr_fil, -0.20) ?>; stop-opacity: 1" />
		</linearGradient>
	</defs>
<?php } elseif (!$nofill && !$solid) { ?>
	<defs>
		<linearGradient id="grad-red" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= $red_fr ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= $red_to ?>; stop-opacity: 1" />
		</linearGradient>
		<linearGradient id="grad-green" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= $green_fr ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= $green_to ?>; stop-opacity: 1" />
		</linearGradient>
		<linearGradient id="grad-blue" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= $blue_fr ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= $blue_to ?>; stop-opacity: 1" />
		</linearGradient>
	</defs>
<?php } ?>
<?php if (false) {  // not needed ?>
	<g id="background">
		<title>Background</title>
		<rect id="svgEditorBackground" x="0" y="0" width="<?= $w ?>" height="<?= $h ?>" style="fill: none; stroke: none;"/>
	</g>
<?php } ?>
<?php if ($guides) { ?>
	<g id="guides1">
		<circle cx="<?= $w/2 ?>" cy="<?= $h/2 ?>" r="<?= $w/2 ?>" fill="rgba(255,0,0,0.1)" />
	</g>
<?php } ?>
	<g id="frame">
		<g id="frame-top">
			<title>Top-Frame</title>
			<polygon stroke="<?= $black ?>" id="poly-ft" style="stroke-width: <?= $s ?>; fill: <?= $red ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_t($w, $h, $s) ?>"/>
		</g>
		<g id="frame-right">
			<title>Right-Frame</title>
			<polygon stroke="<?= $black ?>" id="poly-fr" style="stroke-width: <?= $s ?>; fill: <?= $blue ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_r($w, $h, $s) ?>"/>
		</g>
		<g id="frame-left">
			<title>Left-Frame</title>
			<polygon stroke="<?= $black ?>" id="poly-fl" style="stroke-width: <?= $s ?>; fill: <?= $green ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_l($w, $h, $s) ?>"/>
		</g>
	</g>
<?php if (!$icon && !$avatar) { ?>
	<g id="word">
<?php if (!$nofill) { ?>
		<text x="<?= round($w + ($w / 6)) ?>" y="<?= round($h * 0.9) ?>" fill="<?= $red_alt ?>" font-size="<?= round($h * 1.1) ?>" stroke="<?= $red_alt ?>" stroke-width="<?= round($s / 2) ?>" font-family="Ubuntu" font-weight="bold">tec<tspan fill="<?= $black_alt ?>" stroke="<?= $black_alt ?>" stroke-width="<?= round($s / 2) ?>">smith</tspan></text>
<?php } else { ?>
		<text x="<?= round($w + ($w / 6)) ?>" y="<?= round($h * 0.9) ?>" fill="none" stroke="<?= $black ?>" stroke-width="<?= $s ?>" font-size="<?= round($h * 1.1) ?>" font-family="Ubuntu" font-weight="bold">tecsmith</text>
<?php }  // $nofill ?>
	</g>
<?php } elseif ($avatar) { ?>
	<g id="word">
		<text x="<?= $s ?>" y="<?= round($h * 1.35) ?>" fill="<?= $red_alt ?>" font-size="<?= round($h * 0.34) ?>" font-family="Ubuntu" font-weight="bold">tec<tspan fill="<?= $black_alt ?>">smith</tspan></text>
	</g>
<?php }  ?>
<?php if ($guides) { ?>
	<g id="guides2">
		<circle cx="<?= $w/2 ?>" cy="<?= $h/2 ?>" r="<?= $s ?>" fill="rgba(0,0,0,0.33)" />
	</g>
<?php } ?>
</svg>
