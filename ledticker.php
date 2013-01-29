<?php
class LedTicker {

	private static $ascii2iso8859_15 = array(
			0x0A => 0x20, 0x0D => 0x20, 0xC3 => 0x7F, 0xC2 => 0x80,
			0xC1 => 0x81, 0xC0 => 0x82, 0xC4 => 0x83, 0xC5 => 0x84,
			0xC6 => 0x85, 0xDF => 0x86, 0xC7 => 0x87, 0xD0 => 0x88,
			0xC9 => 0x89, 0xCA => 0x8A, 0xC8 => 0x8B, 0xCB => 0x8C,
			0xCD => 0x8D, 0xCC => 0x8E, 0xCE => 0x8F, 0xCF => 0x90,
			0xD1 => 0x91, 0xD3 => 0x92, 0xD4 => 0x93, 0xD2 => 0x94,
			0xD6 => 0x95, 0xD5 => 0x96, 0xD8 => 0x97, 0xDE => 0x98,
			0xDA => 0x99, 0xD9 => 0x9A, 0xDB => 0x9B, 0xDC => 0x9C,
			0xBE => 0x9D, 0xDD => 0x9E, 0xE3 => 0x9F, 0xE2 => 0xA0,
			0xE1 => 0xA1, 0xE0 => 0xA2, 0xE4 => 0xA3, 0xE5 => 0xA4,
			0xE6 => 0xA5, 0xE7 => 0xA6, 0xE9 => 0xA7, 0xEA => 0xA8,
			0xE8 => 0xA9, 0xEB => 0xAA, 0xED => 0xAB, 0xEC => 0xAC,
			0xEE => 0xAD, 0xEF => 0xAE, 0xF1 => 0xAF, 0xF3 => 0xB0,
			0xF4 => 0xB1, 0xF2 => 0xB2, 0xF6 => 0xB3, 0xF5 => 0xB4,
			0xF8 => 0xB5, 0xFE => 0xB6, 0xFA => 0xB7, 0xF9 => 0xB8,
			0xFB => 0xB9, 0xFC => 0xBA, 0xFF => 0xBB, 0xFD => 0xBC,
			0xA5 => 0xBD, 0xA3 => 0xBE, 0xA4 => 0xBF );

	private $ledfile;
	private $signId;

	/**
	 * Constructor.
	 *
	 * @param string text file holding messages
	 * @param integer sign id
	 */
	function __construct($ledfile = "ledticker.txt", $signId = 20) {
		$this->ledfile = $ledfile;

		// sign id between 1 and 255 (hex: 01 to FF)
		$this->signId = $signId;
	}

	/**
	 * Public function to get the current output
	 *
	 * @return string Output formatted for the ledticker
	 */
	public function getOutput() {
		// put contents of the messages file into an array
		$items = file($this->ledfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$input = $this->getNextText($items);

		return $this->createOutput($input);
	}

	/**
	 * Public function to add a new item to the messagestack
	 *
	 * Needs some sanityfu
	 * @param string $text the message
	 */
	public function addItem($text) {
		file_put_contents($this->ledfile, $text."\n", FILE_APPEND | LOCK_EX);
	}

	/**
	 * Returns number of available message items
	 *
	 * @return integer number of available messages
	 */
	public function itemsAvailable() {
		$items = file($this->ledfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return count($items);
	}

	/**
	 * Gets the next item
	 *
	 * This function deletes the current item and gets the next one
	 *
	 * @param array all items
	 * @return string the text to be displayed
	 */
	private function getNextText($items) {
		$return = array_shift($items);

		if (count($items) <= 0) {
			$put = "";
		} else {
			$put = implode("\n", $items);
			$put .= "\n";
		} 
		file_put_contents($this->ledfile, $put);
			
		return $return;
	}
	
	/**
	 * Creates a string for the led ticker
	 * 
	 * Converts a given string suitable for the hickerspace led ticker
	 * 
	 * @param string $input the input string (optional)
	 * @return string the string to be sent to the ticker display
	 */
	public static function createOutput($input = "") {
		# convert UTF-8 to ISO-8859-15
		$input = iconv('UTF-8', 'ISO-8859-15', $input);

		$text = "";

		$input = str_split($input);
		foreach ($input as $char) {
			$text .= $this->utf8Toiso8859_15($char);
		}
 
		$text = "<L1><PB><FE><MC><WC><FE>".$text;
 
		return "<ID".dechex($this->signId).">".$text.$this->calculateChecksum($text)."<E>";
	}

	/**
	 * Calculates checksum
	 * 
	 * Calculates the checksum of given text.
	 * 
	 * @param string $text the input text
	 * @return string the calculated checksum
	 */
	private static function calculateChecksum($text) {
		// this creates the checksum
		$checksum = 0;
		for ($i = 0; $i < strlen($text); $i++) {
			$checksum = $checksum ^ ord(substr($text, $i, 1));
		}
		return dechex($checksum % 256);
	}
	
	/**
	 * Converts a UTF-8 char to iso8859_15, if possible.
	 *
	 * Needs some kind of blacklist for unconvertable chars.
	 *
	 * @return string converted character, if available. Otherwise given character.
	 */
	private static function utf8Toiso8859_15($utf8Char) {
		$iso8859_15 = $this->$ascii2iso8859_15[ord($utf8Char)]
		if (isset($iso8859_15)) {
			return chr($converted);
		} else {
			return $utf8Char;
		}
	}

	/**
	 * Gives the ledticker an id.
	 *
	 * @return string the string to be sent to the ticker display
	 */
	public static function setSignId() {
		return "<ID><".dechex($this->signId)."><E>";
	}

}
?>
