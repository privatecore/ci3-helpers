<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('gen_rand_string'))
{
	/**
	* Generates an alphanumeric random string of given length
	*
	* @return string
	*/
	function gen_rand_string($num_chars = 8)
	{
		// [a, z] + [0, 9] = 36
		return substr(base_convert(unique_id(), 16, 36), 0, $num_chars);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('gen_rand_string_friendly'))
{
	/**
	* Generates a user-friendly alphanumeric random string of given length
	* We remove 0 and O so users cannot confuse those in passwords etc.
	*
	* @return string
	*/
	function gen_rand_string_friendly($num_chars = 8)
	{
		$rand_str = unique_id();

		// Remove Z and Y from the base_convert(), replace 0 with Z and O with Y
		// [a, z] + [0, 9] - {z, y} = [a, z] + [0, 9] - {0, o} = 34
		$rand_str = str_replace(array('0', 'O'), array('Z', 'Y'), base_convert($rand_str, 16, 34));

		return substr($rand_str, 0, $num_chars);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('gen_rand_string_crypto'))
{
	/**
	* Generate cryptographically secure random strings
	* Based on Kohana's Text::random() method and this answer: http://stackoverflow.com/a/13733588/179104
	*
	* @return string
	*/
	function gen_rand_string_crypto($num_chars = 8, $type = 'alnum')
	{
		switch ($type)
		{
			case 'alnum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyz';
				break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case 'hexdec':
				$pool = '0123456789abcdef';
				break;
			case 'numeric':
				$pool = '0123456789';
				break;
			case 'nozero':
				$pool = '123456789';
				break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
				break;
			default:
				$pool = (string) $type;
				break;
		}

		$rand_str = '';
		$max = strlen($pool);
		for ($i = 0; $i < $num_chars; $i++)
		{
			$rand_str .= $pool[crypto_rand_secure(0, $max)];
		}

		return $rand_str;
	}

	/**
	 * Helper to generate random key (integer)
	 *
	 * @return integer
	 */
	function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;

		// not so random...
		if ($range < 0)
		{
			return $min;
		}

		$log = log($range, 2);
		$bytes = (int) ($log / 8) + 1;		// length in bytes
		$bits = (int) $log + 1;				// length in bits
		$filter = (int) (1 << $bits) - 1;	// set all lower bits to 1
		do
		{
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		}
		while ($rnd >= $range);

		return $min + $rnd;
	}
}
