<?php

class broker
{
	public static function run()
	{
		// allow to enable debug mode
		if(isset($_REQUEST['debug_mode']))
		{
			ini_set('display_startup_errors', 'On');
			ini_set('error_reporting'       , 'E_ALL | E_STRICT');
			ini_set('track_errors'          , 'On');
			ini_set('display_errors'        , 1);
			error_reporting(E_ALL);
		}

		$myToken  = null;
		$myMethod = null;
		$myData   = null;

		// set some settings of curl
		$myToken = null;
		if(isset($_REQUEST['token']) && $_REQUEST['token'])
		{
			$myToken = $_REQUEST['token'];
		}
		else
		{
			self::boboom('Token is not set!');
		}
		$myMethod = null;
		if(isset($_REQUEST['method']) && $_REQUEST['method'])
		{
			$myMethod = $_REQUEST['method'];
		}
		else
		{
			self::boboom('Method is not set!');
		}
		$myData = null;
		if(isset($_REQUEST['data']) && $_REQUEST['data'])
		{
			$myData = $_REQUEST['data'];
		}


		var_dump($_SERVER);

		self::send($myToken, $myMethod, $myData);
	}



	public static function send($_token, $_method = null, $_data = null)
	{
		// check method
		if(!$_method)
		{
			self::boboom('Method is not set!');
		}
		// check token
		if(strlen($_token) < 20)
		{
			self::boboom('Api key is not correct!');
		}
		// check need json
		$isJson = null;
		if($_method === 'answerInlineQuery')
		{
			$isJson = true;
		}
		$ch = curl_init();
		if ($ch === false)
		{
			self::boboom('Curl failed to initialize');
		}

		// set some settings of curl
		$apiURL = "https://api.telegram.org/bot". $_token. "/$_method";

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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);

		if (!empty($_data))
		{
			if($isJson)
			{
				$dataJson       = json_encode($_data);
				$dataJsonHeader =
				[
					'Content-Type: application/json',
					'Content-Length: ' . strlen($dataJson)
				];
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $dataJsonHeader);
			}
			else
			{
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $_data);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
			}
		}
		$result = curl_exec($ch);
		$mycode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// error on result
		if ($result === false)
		{
			self::boboom(curl_error($ch). ':'. curl_errno($ch));
		}
		// empty result
		if (empty($result) || is_null($result))
		{
			self::boboom('Empty server response');
		}
		curl_close($ch);

		// show result with jsonBoboom
		self::jsonBoboom($result);
	}


	public static function my_request($_type)
	{
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
			unset($temp['API_URL']);
			return $temp;
		}
	}


	public static function boboom($_string = null)
	{
		// change header
		exit($_string);
	}

	public static function jsonBoboom($_result = null)
	{
		if(substr($_result, 0,1) === "{")
		{
			$_result = json_decode($_result, true);
		}
		echo $_result;
		self::boboom();
	}
}

\broker::run();

?>
