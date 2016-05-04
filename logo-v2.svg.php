<?php
/**
 * @see: http://svgpocketguide.com/book  // SVG Ref
 */

// enums
define('T', "\t");
define('N', "\n");

define('GEN_LOGO', 0);
define('GEN_ICON', 1);
define('GEN_AVATAR', 2);

// globals
global $dim, $stroke_width, $offset_x, $offset_y;

$dim = false;
if (isset($_REQUEST['size']) && ($_REQUEST['size'] != '')) $dim = round($_REQUEST['size']);
if (!$dim) $dim = 1024;
$stroke_width = round($dim / 28.5, 0);
$offset_x = $offset_y = 0;

// primary colors
$pen_black     = '#000';
$pen_white     = '#FFF';
$pen_red       = '#c30000';
$pen_green     = '#00c300';
$pen_blue      = '#0000c3';
// more color
$pen_red_alt   = '#f03b4d';  // "tec"
$pen_black_alt = '#565656';  // "smith"

// gradients
$pen_red_fr    = '#fb0000';
$pen_red_to    = '#8c0000';
$pen_green_fr  = '#00fb00';
$pen_green_to  = '#008c00';
$pen_blue_fr   = '#0000fb';
$pen_blue_to   = '#00008c';

/* ----- The Cube --------------------------------------------------------- */

// @see: logo-v2.wireframe.inkscape.svg
$cube_original = array(
	'0 408,4368 2554,0 4543,-4368 2554',
	'361 -225,4806 2009,4338 -2524,326 -5028',
	'-361 -225,-4806 2009,-4338 -2524,-326 -5028',
	);

// generate cube
$cube = array();
foreach ($cube_original as $i => $co) {
	$cube[$i] = array();
	$co = explode(',', $co);
	foreach ($co as $pt)
		$cube[$i][] = explode(' ', $pt);
}
// convert string to float
foreach ($cube as $i => $co)
	foreach ($co as $j => $pt) {
		$cube[$i][$j][0] = floatval($cube[$i][$j][0]);
		$cube[$i][$j][1] = floatval($cube[$i][$j][1]);
	}
// apply offsets the flip Y
$xo = 5000;
$yo = 5250;
foreach ($cube as $i => $co)
	foreach ($co as $j => $pt) {
		$cube[$i][$j][0] = $cube[$i][$j][0] + $xo;
		$cube[$i][$j][1] = -($cube[$i][$j][1] + $yo) + 10000;
	}
// $cube as percentage
foreach ($cube as $i => $co)
	foreach ($co as $j => $pt) {
		$cube[$i][$j][0] = ($cube[$i][$j][0] ) / 10000;
		$cube[$i][$j][1] = ($cube[$i][$j][1] ) / 10000;
	}

/* ----- SVG Drawing ------------------------------------------------------ */

function brighter($hex, $percent) {
	// Work out if hash given
	$hash = '';
	if (stristr($hex,'#')) {
		$hex = str_replace('#','',$hex);
		$hash = '#';
	}
	if (strlen($hex) == 3)
		$hex = str_repeat(substr($hex,0,1), 2) .
			str_repeat(substr($hex,1,1), 2) .
			str_repeat(substr($hex,2,1), 2);

	$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
	$new_hex = '';

	// convert to decimal and change luminosity
	for ($i = 0; $i < 3; $i++) {
		$dec = hexdec( substr( $hex, $i*2, 2 ) );
		$dec = min( max( 0, $dec + $dec * $percent ), 255 );
		$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
	}

	return $hash . $new_hex;
}

function pth($co_ords, $stroke, $s_width, $fill) {
	global  $offset_x, $offset_y;
	$out = T . '<path stroke="' . $stroke . '" stroke-width="' . $s_width .
		'" fill="' . $fill . '" d="';
	foreach ($co_ords as $j => $pt) {
		$out .= (($j == 0) ? 'M' : 'L') . ' ';
		$out .= round($pt[0] + $offset_x, 4) . ' ';
		$out .= round($pt[1] + $offset_y, 4) . ' ';
	}
	$out .= 'Z" stroke-linecap="round" stroke-linejoin="round" />' . N;
	return $out;
}

function lgr($name, $s_color, $e_color, $opacity = 1) {
	$out = T.T.'<linearGradient id="' . $name . '" x1="0%" y1="0%" x2="100%" y2="50%">'.N;
	$out .= T.T.T.'<stop offset="0%" style="stop-color: '.$s_color.'; stop-opacity: '.$opacity.'" />'.N;
	$out .= T.T.T.'<stop offset="100%" style="stop-color: '.$e_color.'; stop-opacity: '.$opacity.'" />'.N;
	$out .= T.T. '</linearGradient>'.N;
	return $out;
}

/* ======================================================================== */
/* ----- EXECUTION STARTS HERE -------------------------------------------- */
/* ======================================================================== */

$req = array();
$defaults = array('icon', 'avatar', 'nofill', 'guides', 'solid', 'inv');
foreach ($defaults as $value)
	$req[$value] = false;
foreach ($_REQUEST as $key => $value)
	if (in_array($key, $defaults))
		$req[$key] = ($value == '') ? true : filter_var($value, FILTER_VALIDATE_BOOLEAN);
extract($req);

$fill = false;
$fill_alt = false;
if (isset($_REQUEST['fill'])) {
	$fill = true;
	if ($_REQUEST['fill'] == '') {
		$fill_alt = '#000';
	} else {
		$fill_alt = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fill']);
	}
} else if (isset($_REQUEST['fills'])) {
	$fill = true;
	$solid = true;
	if ($_REQUEST['fills'] == '') {
		$fill_alt = '#000';
	} else {
		$fill_alt = '#' . str_replace(array(' ', '#'), '', $_REQUEST['fills']);
	}
}

$size = false;
if (isset($_REQUEST['size']) && ($_REQUEST['size'] != ''))
	$size = round($_REQUEST['size']);
if ($size !== false) {
	$size = round($size);
	if ($size <= 0) $size = false;
}
if ($size === false) $size = $dim;

// Adjust cube
foreach ($cube as $i => $co)
	foreach ($co as $j => $pt) {
		$cube[$i][$j][0] = ($cube[$i][$j][0] * $size);
		$cube[$i][$j][1] = ($cube[$i][$j][1] * $size);
	}

// figure out dimentions
$generating = GEN_LOGO;
if ($icon) $generating = GEN_ICON;
elseif ($avatar) $generating = GEN_AVATAR;

switch ($generating) {
	case GEN_ICON:
		$width = $dim;
		$height = $dim;
		break;
	case GEN_AVATAR:
		$width = round($dim * 1.5, 2);
		$height = round($dim * 1.5, 2);
		$offset_x = round($dim / 4, 2);
		$offset_y = round($dim * 0.12, 2);
		break;
	default:  // GEN_LOGO
		$width = round($dim * 5.74, 0);
		$height = $dim;
		$offset_x += round($dim / 25, 0);
}

header('Content-type: image/svg+xml');
header('Content-Disposition: inline; filename="' . basename(__FILE__, '.php') . '"');

?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="<?= $width ?>" height="<?= $height ?>">
	<defs>
<?php

if (!$nofill) {
	if (!$solid) {
		if (!$fill) {
			$fill_red = 'url(#grad-red)';
			$fill_green = 'url(#grad-green)';
			$fill_blue = 'url(#grad-blue)';

			echo lgr('grad-red', $pen_red_fr, $pen_red_to, 0.8);
			echo lgr('grad-green', $pen_green_fr, $pen_green_to, 0.8);
			echo lgr('grad-blue', $pen_blue_fr, $pen_blue_to, 0.8);
		} else {
			$fill_red = $fill_green = $fill_blue = 'url(#grad-fill)';
			echo lgr('grad-fill', brighter($fill_alt, 0.2), brighter($fill_alt, -0.3), 0.8);
		}

	} else {
		if (!$fill) {
			$fill_red = $pen_red;
			$fill_green = $pen_green;
			$fill_blue = $pen_blue;
		} else
			$fill_red = $fill_green = $fill_blue = $fill_alt;
	}
	$pen_red = $pen_green = $pen_blue = $pen_black;
	$fill_red_alt = $pen_red_alt;
	$fill_black_alt = $pen_black_alt;
} else
	$fill_red = $fill_green = $fill_blue = $fill_red_alt = $fill_black_alt = 'none';

if ($inv) $pen_red = $pen_green = $pen_blue = $pen_white;

if ($fill && ($generating == GEN_ICON))
	$pen_red = $pen_green = $pen_blue = ($solid ? brighter($fill_alt, -0.2) : $fill_alt);

$text_strk = $nofill ? $stroke_width : round($stroke_width/2, 2);

// if ($nofill && ($generating == GEN_LOGO) && !$inv) $pen_red = $pen_green = $pen_blue = $pen_black;

?>
	</defs>
<?php

echo pth($cube[0], $pen_red, $stroke_width, $fill_red);
echo pth($cube[2], $pen_green, $stroke_width, $fill_green);
echo pth($cube[1], $pen_blue, $stroke_width, $fill_blue);

if ($generating == GEN_LOGO) {
?>
	<text
		x="<?= round(($width / 2) + ($dim / 2) + ($offset_x * 2), 4) ?>"
		y="<?= round($dim * 0.92, 4) ?>"
		text-anchor="middle"
		fill="<?= $fill_red_alt ?>"
		fill-opacity="1"
		stroke="<?= $pen_red_alt ?>"
		stroke-width="<?= $text_strk ?>"
		stroke-opacity="1"
		font-size="<?= round($dim * 1.1, 0) ?>"
		font-family="Ubuntu"
		font-weight="bold">tec<tspan
			fill="<?= $fill_black_alt ?>"
			stroke="<?= $pen_black_alt ?>"
			stroke-width="<?= $text_strk ?>">smith</tspan></text>
<?php
} elseif ($generating == GEN_AVATAR) {
?>
	<text
		x="<?= round($width / 2, 0) ?>"
		y="<?= round($height * 0.925, 2) ?>"
		text-anchor="middle"
		fill="<?= $pen_red_alt ?>"
		fill-opacity="1"
		font-size="<?= round($height * 0.23, 2) ?>"
		font-family="Ubuntu"
		font-weight="bold">tec<tspan
			fill="<?= $pen_black_alt ?>">smith</tspan></text>
<?php
}
?>
</svg>
