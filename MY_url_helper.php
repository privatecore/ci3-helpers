<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('url_path'))
{
	/**
	 * Parse URL string by PHP_URL_PATH and return path array
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function url_path($str)
	{
		if (empty($str))
		{
			return FALSE;
		}

		if (($path = @parse_url($str, PHP_URL_PATH)) !== FALSE)
		{
			$path = trim($path, '/');
			$path = explode('/', $path);
		}

		return $path;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('clean_url'))
{
	/**
	 * Remove query string parameters from an URL
	 *
	 * @param  string $str
	 * @return string
	 */
	function clean_url($str)
	{
		if (empty($str))
		{
			return '';
		}

		$parts = parse_url($str);
		$uri = isset($parts['path']) ? $parts['path'] : '';

		return $parts['scheme'] . '://' . $parts['host'] . $uri;
	}
}
