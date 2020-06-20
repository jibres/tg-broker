<?php

class broker
{
	public static function run()
	{
		$token = __DIR__.'/token.conf';

		if(!is_file($token))
		{
			self::boboom('Token file not found');
		}

		if(isset($_REQUEST['broker_token']) && $_REQUEST['broker_token'] == trim(file_get_contents($token)))
		{
			// it's ok
		}
		else
		{
			self::boboom('Hi!');
		}

		if(!isset($_REQUEST['api_url']))
		{
			self::boboom('Api url not found');
		}

		self::send($_REQUEST['api_url'], self::my_data());
	}



	public static function send($_url, $_data = null)
	{
		$ch = curl_init();

		if ($ch === false)
		{
			self::boboom('Curl failed to initialize');
		}


		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_URL, $_url. '?'. http_build_query($_data));

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);


		$result    = curl_exec($ch);
		$CurlError = curl_error($ch);
		$mycode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close ($ch);

		// error on result
		if ($result === false)
		{
			self::boboom(curl_error($ch). ':'. curl_errno($ch), true);
		}
		// empty result
		if (empty($result) || is_null($result) || !$result)
		{
			self::boboom('Empty server response', true);
		}


		// show result with jsonBoom
		self::jsonBoom($result, true);
	}


	public static function my_data()
	{
		// get all
		$allData = $_REQUEST;
		unset($allData['broker_token']);
		unset($allData['api_url']);

		// send all
		return $allData;
	}


	public static function boboom($_string = null, $_error = false)
	{
		if($_error)
		{
			@header("HTTP/1.1 504 Gateway Timeout", true, 504);
		}
		else
		{
			@header("HTTP/1.1 418 I\'m a teapot", true, 418);
		}
		// change header
		exit($_string);
	}

	public static function jsonBoom($_result = null)
	{
		if(is_array($_result))
		{
			$_result = json_encode($_result, JSON_UNESCAPED_UNICODE);
		}

		if(substr($_result, 0, 1) === "{")
		{
			@header("Content-Type: application/json; charset=utf-8");
		}
		echo $_result;
		exit();
	}
}

\broker::run();

?>
