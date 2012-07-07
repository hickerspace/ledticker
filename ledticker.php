<?php
/*
This is a PHP implementation of Veit Wahlich's LED Ticker 1.1.1, which
was released under the GPLv2, too.
For more information, see http://home.ircnet.de/cru/ledticker/.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// special chars in extended ascii (see specification section 4.2.2.7)
$iso8859_15 = array(
	0x0A => 0x20, 0x0D => 0x20,
	0xC3 => 0x7F, 0xC2 => 0x80, 0xC1 => 0x81, 0xC0 => 0x82, 0xC4 => 0x83,
	0xC5 => 0x84, 0xC6 => 0x85, 0xDF => 0x86, 0xC7 => 0x87, 0xD0 => 0x88,
	0xC9 => 0x89, 0xCA => 0x8A, 0xC8 => 0x8B, 0xCB => 0x8C, 0xCD => 0x8D,
	0xCC => 0x8E, 0xCE => 0x8F, 0xCF => 0x90, 0xD1 => 0x91, 0xD3 => 0x92,
	0xD4 => 0x93, 0xD2 => 0x94, 0xD6 => 0x95, 0xD5 => 0x96, 0xD8 => 0x97,
	0xDE => 0x98, 0xDA => 0x99, 0xD9 => 0x9A, 0xDB => 0x9B, 0xDC => 0x9C,
	0xBE => 0x9D, 0xDD => 0x9E, 0xE3 => 0x9F, 0xE2 => 0xA0, 0xE1 => 0xA1,
	0xE0 => 0xA2, 0xE4 => 0xA3, 0xE5 => 0xA4, 0xE6 => 0xA5, 0xE7 => 0xA6,
	0xE9 => 0xA7, 0xEA => 0xA8, 0xE8 => 0xA9, 0xEB => 0xAA, 0xED => 0xAB,
	0xEC => 0xAC, 0xEE => 0xAD, 0xEF => 0xAE, 0xF1 => 0xAF, 0xF3 => 0xB0,
	0xF4 => 0xB1, 0xF2 => 0xB2, 0xF6 => 0xB3, 0xF5 => 0xB4, 0xF8 => 0xB5,
	0xFE => 0xB6, 0xFA => 0xB7, 0xF9 => 0xB8, 0xFB => 0xB9, 0xFC => 0xBA,
	0xFF => 0xBB, 0xFD => 0xBC, 0xA5 => 0xBD, 0xA3 => 0xBE, 0xA4 => 0xBF);

// sign id between 1 and 255 (hex: 01 to FF)
$id = 14;
$input = "SAMPLE TEXT";

/*
// set sign id (uncomment on first use)
die("<ID><".dechex($id)."><E>");
*/

// convert text to closest charset
$input = iconv('UTF-8', 'ISO-8859-15', $input);

// max text length is 420 chars for page B (<PB>)
// if text is too long, cut it and append two dots
if (strlen($input) > 420) {
	$input = substr($input, 0, 418)."..";
}

$text = "";

// replace ISO 8859-15 special characters with extended ascii
// codes (see specification section 4.2.2.7)
for ($i=0; $i < strlen($input); $i++) {
	$char = substr($input, $i, 1);
	if(isset($iso8859_15[ord($char)])) {
		$text .= chr($iso8859_15[ord($char)]);
	} else {
		$text .= $char;
	}
}


// <MX> sets the scrolling speed, A (<MA>) is fastest and
// E (<ME>) is slowest (see specification section 4.2.2.4)
$text = "<L1><PB><FE><MC><WC><FE>".$text;

// calculate checksum (see specification section 4.2)
$value = 0;
for ($i = 0; $i < strlen($text); $i++) {
	$value = $value ^ ord(substr($text, $i, 1));
}

$value = dechex($value % 256);

// finally return resulting string
echo "<ID".dechex($id).">".$text.$value."<E>";

?>
