<?php
/**
 * @see: https://www.smashingmagazine.com/2016/05/easy-steps-to-better-logo-design/
 */

function do_icon_guides($size) {
	echo "\t<g id=\"guides\">\n";

	for ($i=1; $i < 32; $i++) {
		echo "\t\t<line x1=\"";
		echo round($size * ($i/32), 2) - 1;
		echo "\" y1=\"";
		echo 0;
		echo "\" x2=\"";
		echo round($size * ($i/32), 2) - 1;
		echo "\" y2=\"";
		echo $size;
		echo "\" stroke=\"";
		if ($i == 2 || $i == 6 || $i == 26 || $i == 30) {
			echo "rgba(0,0,0,0.4)";
		} else {
			echo "rgba(0,0,0,0.1)";
		}
		echo "\" stroke-width=\"";
		echo 2; // round($size * (1/320), 2);
		echo "\" />\n";
	}

	for ($i=1; $i < 32; $i++) {
		echo "\t\t<line x1=\"";
		echo 0;
		echo "\" y1=\"";
		echo round($size * ($i/32), 2) - 1;
		echo "\" x2=\"";
		echo $size;
		echo "\" y2=\"";
		echo round($size * ($i/32), 2) - 1;
		echo "\" stroke=\"";
		if ($i == 2 || $i == 6 || $i == 26 || $i == 30) {
			echo "rgba(0,0,0,0.4)";
		} else {
			echo "rgba(0,0,0,0.1)";
		}
		echo "\" stroke-width=\"";
		echo 2; // round($size * (1/320), 2);
		echo "\" />\n";
	}

	echo "\t\t<circle cx=\"";
	echo round($size / 2, 4);
	echo "\" cy=\"";
	echo round($size / 2, 4);
	echo "\" r=\"";
	echo round($size / 2, 4);
	echo "\" fill=\"none\" stroke=\"rgba(0,0,0,0.4)\" stroke-width=\"2\" />\n";

	echo "\t\t<circle cx=\"";
	echo round($size / 2, 4);
	echo "\" cy=\"";
	echo round($size / 2, 4);
	echo "\" r=\"";
	echo round($size * (14/32), 4);
	echo "\" fill=\"none\" stroke=\"rgba(0,0,0,0.1)\" stroke-width=\"2\" />\n";

	echo "\t\t<circle cx=\"";
	echo round($size / 2, 4);
	echo "\" cy=\"";
	echo round($size / 2, 4);
	echo "\" r=\"";
	echo round($size * (10/32), 4);
	echo "\" fill=\"none\" stroke=\"rgba(0,0,0,0.1)\" stroke-width=\"2\" />\n";

	echo "\t</g>\n";

}
