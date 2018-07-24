<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('human_to_mysql'))
{
	/**
	 * Converts "human" to MySQL's datetime format
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function human_to_mysql($str = '')
	{
		if (empty($str))
		{
			return '0000-00-00 00:00:00';
		}

		// We don't check specified format for compatibility
		// Result: YYYY-MM-DD HH:MM:SS
		return date('Y-m-d H:i:s', strtotime($str));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mysql_to_human'))
{
	/**
	 * Converts MySQL's datetime format to "human"
	 *
	 * @param  string  $str
	 * @param  boolean $format
	 * @return mixed
	 */
	function mysql_to_human($str = '', $format = FALSE)
	{
		if (empty($str))
		{
			return 'Unknown';
		}
		elseif (empty($format))
		{
			$format = 'U';
		}

		return date($format, strtotime($str));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('unix_to_mysql'))
{
	/**
	 * Converts unix to MySQL's datetime format
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function unix_to_mysql($str = '')
	{
		if (empty($str))
		{
			return '0000-00-00 00:00:00';
		}

		return date('Y-m-d H:i:s', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('zero_date'))
{
	/**
	 * Check for the "zero date" (0000-00-00 00:00:00)
	 *
	 * @param  string  $str
	 * @return boolean
	 */
	function zero_date($str = '')
	{
		if (empty($str))
		{
			return TRUE;
		}

		$time = strtotime($str);

		return ( ! $time OR $time < 0) ? TRUE : FALSE;
	}
}
