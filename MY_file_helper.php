<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_remote_file_size'))
{
	/**
	 * Get remote file size in bytes
	 *
	 * @param  string $url
	 * @return mixed
	 */
	function get_remote_file_size($url)
	{
		// get CI class instance
		$CI =& get_instance();

		$CI->load->library('curl');

		// start session (also wipes existing/previous sessions)
		$CI->curl->create($url);

		// human looking options
		$CI->curl->option('returntransfer', TRUE);
		$CI->curl->option('header', TRUE);
		$CI->curl->option('nobody', TRUE);

		$CI->curl->execute();

		if ($responce = $CI->curl->info)
		{
			return $responce['download_content_length'];
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('save_remote_file'))
{
	/**
	 * Save remote file to the specified path
	 *
	 * @param  string $url
	 * @param  string $path
	 * @return mixed
	 */
	function save_remote_file($url, $path)
	{
		if ( ! @is_dir($path) && ! @mkdir($path, 0700, TRUE))
		{
			return FALSE;
		}
		elseif ( ! is_writable($path))
		{
			return FALSE;
		}

		if ( ! ($src = @fopen($url, 'r')))
		{
			return FALSE;
		}
		elseif ( ! ($filesize = get_remote_file_size($url)))
		{
			return FALSE;
		}

		if (substr($path, -1, 1) != '/')
		{
			$path .= '/';
		}
		$filename = basename(clean_remote_file_url($url));
		$dest = $path . $filename;

		if ( ! file_put_contents($dest, $src, LOCK_EX))
		{
			return FALSE;
		}

		return $dest;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_remote_file_exists'))
{
	/**
	 * Check if remote file exists
	 *
	 * @param  string  $url
	 * @return boolean
	 */
	function is_remote_file_exists($url)
	{
		$headers = @get_headers($url);

		return stripos($headers[0], '200 OK') !== FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('clean_remote_file_url'))
{
	/**
	 * Remove query string parameters from an URL
	 *
	 * @param  string $str
	 * @return string
	 */
	function clean_remote_file_url($str)
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

// ------------------------------------------------------------------------

if ( ! function_exists('filesize_format'))
{
	/**
	 * Format file's size with human readable format
	 *
	 * @param  string $path
	 * @return string
	 */
	function filesize_format($path)
	{
		$size = filesize($path);
		$units = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
		$power = $size > 0 ? floor(log($size, 1024)) : 0;

		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}
}
