<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('set_hashpath'))
{
	/**
	 * Generate and create path by unique identifier
	 *
	 * @param integer $id
	 * @param string  $path
	 * @param boolean $multiple
	 */
	function set_hashpath($id, $path, $multiple = FALSE)
	{
		// make sure we have a numeric id and type specified
		if (is_null($id) OR ! is_numeric($id))
		{
			return config_item('temp_path');
		}
		// check if specified path is empty
		if (empty($path))
		{
			return config_item('temp_path');
		}

		$hash = md5((string)$id);
		$hash = implode(DIRECTORY_SEPARATOR, str_split(
			substr($hash, 0, 9), 3 // 3 levels with length of 3 symbols
		));

		if ($multiple)
		{
			$hash .= DIRECTORY_SEPARATOR.$id;
		}

		$hash_path = $path.$hash.DIRECTORY_SEPARATOR;
		if ( ! @is_dir($hash_path) && ! @mkdir($hash_path, 0700, TRUE))
		{
			return config_item('temp_path');
		}

		return $hash_path;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_relative_path'))
{
	/**
	 * Return a relative path to a file or directory using base directory.
	 * When you set $base to /website and $path to /website/store/library.php
	 * this function will return /store/library.php
	 *
	 * Remember: All paths have to start from "/" or "\" this is not Windows compatible.
	 *
	 * @return string
	 */
	function get_relative_path($base, $path)
	{
		// Detect directory separator
		$separator = substr($base, 0, 1);
		$_base = array_slice(explode($separator, rtrim($base,$separator)),1);
		$_path = array_slice(explode($separator, rtrim($path,$separator)),1);

		return $separator.implode($separator, array_slice($_path, count($_base))).(is_dir($path) ? $separator : '');
	}
}
