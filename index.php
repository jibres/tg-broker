<?php

class broker
{
	public static function run()
	{
		$ch = curl_init();
		if ($ch === false)
		{
			self::end('curl not exist');
		}

		// set some settings of curl
		$apiURL = self::my_request('url');
		if($apiURL === false)
		{
			self::end('url not set');
		}

		curl_setopt($ch, CURLOPT_URL, $apiURL);
		// turn on some setting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
		// turn off some setting
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		// timeout setting
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		if(!empty(self::my_request('data')))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, self::my_request('data'));
		}

		$result = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		echo $result;
		curl_close($ch);
	}

	public static function my_request($_type)
	{
		if(isset($_REQUEST['debug_mode']))
		{
			self::debug();
		}

		if($_type === 'url')
		{
			if(isset($_REQUEST['API_URL']))
			{
				return $_REQUEST['API_URL'];
			}
			return false;
		}
		elseif($_type === 'data')
		{
			$temp = $_REQUEST;
			unset($_REQUEST['API_URL']);
			return $temp;
		}
	}


	public static function debug()
	{
		ini_set('display_startup_errors', 'On');
		ini_set('error_reporting'       , 'E_ALL | E_STRICT');
		ini_set('track_errors'          , 'On');
		ini_set('display_errors'        , 1);
		error_reporting(E_ALL);
	}


	public static function end($_string = null)
	{
		exit($_string);
	}
}

\broker::run();

?>
