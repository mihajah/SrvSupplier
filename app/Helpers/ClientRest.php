<?php 

namespace App\Helpers;

class ClientRest
{
	public static function get($url)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->get($url);
		return $response;
	}

	public static function post($url, $data)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->post($url, ['body' => $data]);
		return $response;
	}

	public static function put($url, $data)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->put($url, ['body' => $data]);
		return $response;
	}

	public static function delete($url, $data)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->delete($url, ['body' => $data]);
		return $response;
	}

	public static function call($method, $uri, $data = [])
	{
		$client   = new \GuzzleHttp\Client();
	    $request  = $client->createRequest($method, $uri, $data);
	    $response = $client->send($request);
	    return  [
	    			'response' => $response, 
	    			'request'  => $request
	    		];
	}
}