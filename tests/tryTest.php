<?php
use Log as logger;
use App\Helpers\ClientRest as R;

class tryTest extends TestCase{

	protected $isTestable = TRUE;
	protected $myBaseUrl = 'http://staging.touchiz.fr/dev/services'; //todo: use global_conf() instead

	protected function setBaseUrl()
	{
		if($this->myBaseUrl)
		{
			$this->baseUrl = $this->myBaseUrl;
		}
	}

	public function testRun()
	{
		//
		if($this->isTestable)
		{
			
			$this->setBaseUrl();
			$this->checkHttpStatus();
		}
	}

	private function checkHttpStatus($verb = 'GET')
	{
		if($verb == 'GET')
		{
			$route = [
						'supplierorders'
			     	 ];

			foreach($route as $uri)
			{
				$this->checkValidHttpResponse($uri);
			}
		}		
	}

	private function checkValidHttpResponse($route)
	{
		$response = R::call('GET', $this->baseUrl.'/'.$route);
		$this->assertEquals(200, $response->getStatusCode());
	}
}

?>