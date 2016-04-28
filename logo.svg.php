<?php
/**
 * @see: https://www.mathsisfun.com/sine-cosine-tangent.html  // Math Function
 * @see: http://svgpocketguide.com/book  // SVG Ref
 */

header('Content-type: image/svg+xml');
header('Content-Disposition: inline; filename="' . basename(__FILE__, '.php') . '"');

$req = array();
$defaults = array('icon', 'avatar', 'nofill', 'guides', 'solid');
foreach ($defaults as $value)
	$req[$value] = false;
foreach ($_REQUEST as $key => $value)
	if (in_array($key, $defaults))
		$req[$key] = ($value == '') ? true : filter_var($value, FILTER_VALIDATE_BOOLEAN);
extract($req);

$fill = false;
$color = false;
if (isset($_REQUEST['fill'])) {
	$fill = true;
	if ($_REQUEST['fill'] == '') {
		$color = '#000';
	} else {
		$color = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fill']);
	}
} else if (isset($_REQUEST['fills'])) {
	$fill = true;
	$solid = true;
	if ($_REQUEST['fills'] == '') {
		$color = '#000';
	} else {
		$color = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fills']);
	}
}


$w = $h = 1024;
$s = intval($w / 50);
$s = 40;

global $add_x, $add_y;
$add_x = $add_y = 0;

/**
 * Color helper
 */
function brighten($color, $percentage) {
	$steps = max(-10000, min(10000, $percentage*10000)) / 100;
	$steps = intval($percentage * 255);

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


/**
 *  X & Y Co-ordinates
 */
function c($x, $y, $xo = 0, $yo = 0, $end = ' ') {
	global $add_x, $add_y;
	return ($x+$xo+$add_x) . ' ' . ($y+$yo+$add_y) . $end;
}

function side_t($w, $h, $s) {
	$out = '';

	$o = intval($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o;
	$y0 = $o - intval(sin($a) * $s);

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

	$o = intval($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o + $s;
	$y0 = $o + intval(sin($a) * $s);

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

	$o = intval($s*2);
	$w = $w-($o*2);
	$h = $h-($o*2);

	$a = atan( ($w / 2) / ($h / 4) );
	$x0 = $o - $s;
	$y0 = $o + intval(sin($a) * $s);

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

function f($cs1, $cs2, $gg1, $gg2, $cst, $sld) {
	if ($sld) {
		if ($cst) {
			return $cs2;
		} else {
			return $cs1;
		}
	} else {
		if ($cst) {
			return 'url(#' . $gg2 . ')';
		} else {
			return 'url(#' . $gg1 . ')';
		}

	}
	return '#000';
}

if ($icon) {
	$width = $w;
} elseif ($avatar) {
	$width = intval($w * 1.5);
	$add_x = intval($w / 4);
} else {
	$width = $w + intval($w * 4.73);
}
if ($avatar) {
	$height = intval($h * 1.5);
	$add_y = ($h * 0.1);
} else {
	$height = $h;
}

?><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="<?= $width ?>" height="<?= $height ?>" viewBox="0 0 <?= $width ?> <?= $height ?>">

	<?php if ($fill && !$solid) { ?>
	<defs>
		<linearGradient id="grad-fill" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= brighten($color, 0.25) ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= brighten($color, -0.25) ?>; stop-opacity: 1" />
		</linearGradient>
	</defs>
	<?php } elseif (!$nofill && !$solid) { ?>
	<defs>
		<linearGradient id="grad-red" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= brighten('#f00', 0.33) ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= brighten('#f00', -0.33) ?>; stop-opacity: 1" />
		</linearGradient>
		<linearGradient id="grad-green" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= brighten('#0f0', 0.33) ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= brighten('#0f0', -0.33) ?>; stop-opacity: 1" />
		</linearGradient>
		<linearGradient id="grad-blue" x1="0%" y1="0%" x2="100%" y2="0%">
			<stop offset="0%" style="stop-color: <?= brighten('#00f', 0.33) ?>; stop-opacity: 1" />
			<stop offset="100%" style="stop-color: <?= brighten('#00f', -0.33) ?>; stop-opacity: 1" />
		</linearGradient>
	</defs>
	<?php } ?>

	<g id="background">
		<title>Background</title>
		<rect id="svgEditorBackground" x="0" y="0" width="<?= $w ?>" height="<?= $h ?>" style="fill: none; stroke: none;"/>
	</g>

	<?php if ($guides) { ?>
	<g id="guides1">
		<circle cx="<?= $w/2 ?>" cy="<?= $h/2 ?>" r="<?= $w/2 ?>" fill="rgba(255,0,0,0.1)" />
	</g>
	<?php } ?>

	<?php if (!$nofill) { ?>
	<g id="filler">
		<g id="filler-top">
			<title>Top-Frame</title>
			<polygon stroke="red" id="poly-ft" style="stroke-width: 1; fill: <?= f('red', $color, 'grad-red', 'grad-fill', $fill, $solid) ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_t($w, $h, $s) ?>"/>
		</g>
		<g id="filler-right">
			<title>Right-Frame</title>
			<polygon stroke="blue" id="poly-fr" style="stroke-width: 1; fill: <?= f('blue', $color, 'grad-blue', 'grad-fill', $fill, $solid) ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_r($w, $h, $s) ?>"/>
		</g>
		<g id="filler-left">
				<title>Left-Frame</title>
			<polygon stroke="green" id="poly-fl" style="stroke-width: 1; fill: <?= f('green', $color, 'grad-green', 'grad-fill', $fill, $solid) ?>; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_l($w, $h, $s) ?>"/>
		</g>
	</g>
	<?php } ?>

	<g id="frame">
		<g id="frame-top">
			<title>Top-Frame</title>
			<polygon stroke="black" id="poly-ft" style="stroke-width: <?= $s ?>; fill: none; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_t($w, $h, $s) ?>"/>
		</g>
		<g id="frame-right">
			<title>Right-Frame</title>
			<polygon stroke="black" id="poly-fr" style="stroke-width: <?= $s ?>; fill: none; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_r($w, $h, $s) ?>"/>
		</g>
		<g id="frame-left">
			<title>Left-Frame</title>
			<polygon stroke="black" id="poly-fl" style="stroke-width: <?= $s ?>; fill: none; stroke-linejoin: round; stroke-linecap: round;" points="<?= side_l($w, $h, $s) ?>"/>
		</g>
	</g>

	<?php if (!$icon && !$avatar) { ?>
		<g id="word">
		<?php if (!$nofill) { ?>
			<text x="<?= $w + ($w / 6) ?>" y="<?= intval($h * 0.9) ?>" fill="#f03b4d" font-size="<?= intval($h * 1.1) ?>" stroke="#f03b4d" stroke-width="<?= intval($s / 2) ?>" font-family="Ubuntu" font-weight="bold">tec<tspan fill="#565656" stroke="#565656">smith</tspan></text>
		<?php } else { ?>
			<text x="<?= $w + ($w / 6) ?>" y="<?= intval($h * 0.9) ?>" fill="none" stroke="black" stroke-width="<?= $s ?>" font-size="<?= intval($h * 1.1) ?>" font-family="Ubuntu" font-weight="bold">tecsmith</text>
		<?php }  // $nofill ?>
		</g>
	<?php } elseif ($avatar) { ?>
		<g id="word">
			<text x="<?= $s ?>" y="<?= intval($h * 1.35) ?>" fill="#f03b4d" font-size="<?= intval($h * 0.34) ?>" font-family="Ubuntu" font-weight="bold">tec<tspan fill="#565656" stroke="#565656">smith</tspan></text>
		</g>
	<?php }  ?>

	<?php if ($guides) { ?>
	<g id="guides2">
		<circle cx="<?= $w/2 ?>" cy="<?= $h/2 ?>" r="<?= $s ?>" fill="rgba(0,0,0,0.33)" />
	</g>
	<?php } ?>
</svg>
