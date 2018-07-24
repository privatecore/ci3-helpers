<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('is_home'))
{
	/**
	 * Check whenever current page is home/index
	 *
	 * @return boolean
	 */
	function is_home()
	{
		$CI =& get_instance();

		$class = strtolower($CI->router->fetch_class());
		$method = strtolower($CI->router->fetch_method());

		return ($class === $CI->router->default_controller && $method === 'index');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('is_valid_timestamp'))
{
	/**
	 * Check is valid timestamp
	 *
	 * Determines if specified string is a valid unix timestamp.
	 * If integer is passed, function will return FALSE, because
	 * of variable conversions.
	 *
	 * @param  string  $str
	 * @return boolean
	 */
	function is_valid_timestamp($str)
	{
		return ((string) (int) $str === $str)
			&& ($str <= PHP_INT_MAX)
			&& ($str >= ~PHP_INT_MAX);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('display_json'))
{
	/**
	 * Outputs an array in a user-readable JSON format
	 *
	 * @param array $data
	 */
	function display_json($data)
	{
		$json = json_indent($data);

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		echo $json;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('json_indent'))
{
	/**
	 * Convert an array to a user-readable JSON string
	 *
	 * @param  array  $data the original array to convert to JSON
	 * @return string
	 */
	function json_indent($data = array())
	{
		// make sure array is provided
		if (empty($data))
		{
			return NULL;
		}

		// Encode the string
		$json = json_encode($data);

		$result = '';
		$pos = 0;
		$str_len = strlen($json);
		$indent_str = '  ';
		$new_line = "\n";
		$prev_char = '';
		$out_of_quotes = true;

		for ($i = 0; $i <= $str_len; $i++)
		{
			// grab the next character in the string
			$char = substr($json, $i, 1);

			// are we inside a quoted string?
			if ($char == '"' && $prev_char != '\\')
			{
				$out_of_quotes = !$out_of_quotes;
			}
			// if this character is the end of an element, output a new line and indent the next line
			elseif (($char == '}' OR $char == ']') && $out_of_quotes)
			{
				$result .= $new_line;
				$pos--;

				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indent_str;
				}
			}

			// add the character to the result string
			$result .= $char;

			// if the last character was the beginning of an element, output a new line and indent the next line
			if (($char == ',' OR $char == '{' OR $char == '[') && $out_of_quotes)
			{
				$result .= $new_line;

				if ($char == '{' OR $char == '[')
				{
					$pos++;
				}

				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indent_str;
				}
			}

			$prev_char = $char;
		}

		// return result
		return $result . $new_line;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_to_csv'))
{
	/**
	 * Save data to a CSV file
	 *
	 * @param  array
	 * @param  string
	 * @return boolean
	 */
	function array_to_csv($data = array(), $filename = "export.csv")
	{
		$CI =& get_instance();

		// disable the profiler otherwise header errors will occur
		$CI->output->enable_profiler(FALSE);

		if ( ! empty($data))
		{
			// ensure proper file extension is used
			if ( ! substr(strrchr($filename, '.csv'), 1))
			{
				$filename .= '.csv';
			}

			try
			{
				// set the headers for file download
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
				header("Cache-Control: no-cache, must-revalidate");
				header("Pragma: no-cache");
				header("Content-type: text/csv");
				header("Content-Description: File Transfer");
				header("Content-Disposition: attachment; filename={$filename}");

				$output = @fopen('php://output', 'w');

				// used to determine header row
				$header_displayed = FALSE;

				foreach ($data as $row)
				{
					if ( ! $header_displayed)
					{
						// use the array keys as the header row
						fputcsv($output, array_keys($row));
						$header_displayed = TRUE;
					}

					// clean the data
					$allowed = '/[^a-zA-Z0-9_ %\|\[\]\.\(\)%&-]/s';
					foreach ($row as $key => $value)
					{
						$row[$key] = preg_replace($allowed, '', $value);
					}

					// insert the data
					fputcsv($output, $row);
				}

				fclose($output);

			}
			catch (Exception $e) {}
		}

		exit;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('active_link'))
{
	/**
	 * Check if link is active and set proper CSS class
	 *
	 * @return string
	 */
	function active_link($uri, $strict = FALSE)
	{
		if ( ! is_array($uri))
		{
			$uri = array($uri);
		}

		$css_class = '';

		// get CI class instance
		$CI =& get_instance();

		// get current uri string
		$current = $CI->uri->uri_string();
		if ( ! $current)
		{
			// default router method name
			$current = 'index';
		}

		// for strict checks use strcmp (binary safe)
		// others - checks if string is occur at the begining of uri
		$func = $strict ? 'strcmp' : 'strpos';

		foreach ($uri as $value)
		{
			// remove leading and trailing slashes
			$value = trim($value, '/');

			// if only parent folder/controller of uri specified
			if (strstr($value, '/') === FALSE)
			{
				// check only against first uri's segment and break if comparison failed
				if ($value === $CI->uri->segment(1))
				{
					$css_class = 'active';
				}
				break;
			}

			if ($func($current, $value) === 0)
			{
				$css_class = 'active';
				break;
			}
		}

		return $css_class;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('sprintf_assoc'))
{
	/**
	 * Functions vsprintf, sprintf, and printf do not allow for associative arrays to perform replacements,
	 * `sprintf_assoc` resolves this by using the key of the array in the lookup for string replacement.
	 * http://php.net/manual/en/function.vsprintf.php
	 *
	 * @param  string $str Value of the string needs to be formatted
	 * @param  array  $replacement_vars Associative array with values needs to be replaced
	 * @param  string $prefix Prefix in string that helps to format
	 *
	 * @return string Return formatted string
	 */
	function sprintf_assoc($str = '', $vars = array(), $prefix = '%')
	{
		if ( ! $str)
		{
			return '';
		}

		if (count($vars))
		{
			foreach ($vars as $key => $value)
			{
				$str = str_replace($prefix . $key . $prefix, $value, $str);
			}
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('clean_phone'))
{
	/**
	 * Cleanup telephone number from all symbols except numbers and +
	 * ex.: +7 (123) 456-78-90 -> +71234567890
	 *
	 * @param  string $str
	 * @return string
	 */
	function clean_phone($str)
	{
		return preg_replace('/[^0-9\+]/', '', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('format_phone'))
{
	/**
	 * Format telephone number to fit desired format
	 * ex.: +71234567890 -> +7 (123) 456-78-90
	 *
	 * @param  string $str
	 * @return string
	 */
	function format_phone($str)
	{
		// cleanup number first
		$str = preg_replace('/[^0-9\+]/', '', $str);
		return preg_replace("/(\+?\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/", "$1 ($2) $3-$4-$5", $str);
	}
}
