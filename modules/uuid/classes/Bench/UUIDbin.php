<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Kohana/Codebench
 * @category   Tests
 * @author     Geert De Deckere <geert@idoe.be>
 */
class Bench_UUIDbin extends Codebench {

	public $description = 'Converting UUIDs to binary';

	public $loops = 10000;

	public $subjects = array
	(
		// Valid callback strings
		'FFA14B9E-3AFC-3989-95B7-CD49A421EE8A',
		'209F6A77-73AD-44D9-902A-DBDCF3A1E2BE',
		'D1815512-48EC-4B62-8F17-068FBA51CBAB',
		'6D691433-05F0-4027-A5FA-5111B27CBA98',
		'00000000-0000-0000-0000-000000000000',
	);

	public function bench_for_loop($uuid)
	{
		// Get hexadecimal components of namespace
		$hex = str_replace(array('-','{','}'), '', $uuid);

		// Binary Value
		$bin = '';

		// Convert Namespace UUID to bits
		for ($i = 0, $max = strlen($hex); $i < $max; $i += 2)
		{
			$bin .= chr(hexdec($hex[$i].$hex[$i + 1]));
		}

		return base64_encode($bin);
	}
	
	public function bench_array_map($uuid)
	{
		// Get hexadecimal components of namespace
		$hex = str_replace(array('-','{','}'), '', $uuid);

		$bin = array_map('chr', array_map('hexdec', str_split($hex, 2)));

		return base64_encode(implode(NULL, $bin));
	}

}