<?php 
use App\Helpers\ClientRest as R;

class SupplierTest extends TestCase {

	protected $isTestable 	= true;
	protected $debug 		= false;

	protected function setBaseUrl()
	{
		$this->baseUrl = config('constants.baseurl');
	}

	public function testRun()
	{
		//
		if($this->isTestable)
		{	
			$this->setBaseUrl();
			$this->checkHttpStatus('GET');
			$this->checkHttpStatus('POST');
			$this->checkHttpStatus('PUT');
		}
	}


	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'supplierorders',
						'supplierorders/open',
						'supplierorders/1'
			     	 ];
			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		

		if($verb == 'POST')
		{
			$uri  	 = 'supplierorders';
			$data 	 = 	[
							'id_supplier' 	 	=> 100,
							'id_transporter' 	=> 4,
							'created_by' 	 	=> 1,
							'test_unit'			=> true
						]; 
			$this->checkValidHttpResponse($uri, $verb, $this->setHttpHeaders($data));
		}

		if($verb == 'PUT')
		{
			$uri  	 = 'supplierorders/1';
			$data 	 = 	[
							'id_supplier' 	 		=> 100,
							'id_transporter' 		=> 4,
							'created_by' 	 		=> 1,
						    'transporter_price' 	=> 0,
						    'totalweight' 			=> 0,
						    'tracking' 				=> '',
						    'step' 					=> 1,
						    'status'				=> 1,
						    'processed' 			=> 0,
						    'refund' 				=> 0,
						    'packaging' 			=> 0,
						    'full_cost_computed' 	=> 0,
							'test_unit'				=> true
						];
			$this->checkValidHttpResponse($uri, $verb, $this->setHttpHeaders($data));
		}
	}

	
	private function setHttpHeaders($data)
	{ 
		$options =  [
						'headers' => [ 'Content-type' => 'application/x-www-form-urlencoded' ],
						'body' 	  => $data
					];
		return $options; 
	}

	private function checkValidHttpResponse($route, $verb = 'GET', $options = [])
	{
		$url 				= $this->baseUrl . $route;
		$response 			= R::call($verb, $url, $options);
		$status 			= $response['response']->getStatusCode();
		$result 			= $response['response']->getBody();
		$neededResponse 	= '{"success":true';
		$extrait 			= substr($result, 0, strlen($neededResponse));
		$info 				= '[' . $verb . '] ' . $result . '    ---------    ';
		switch ($verb) {
			case 'GET':
				$this->assertEquals(200, $status);
				break;
			case 'POST':
			case 'PUT':
				$this->assertEquals($neededResponse, $extrait);
				if($this->debug)
				{
					echo $info;
				}
				break;
			default:
				break;
		}
	}
}