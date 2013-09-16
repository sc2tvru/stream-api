<?php

$_dirname = dirname(__FILE__).'/';
require_once $_dirname."../StreamService.php";

/**
 *  test case.
 */
class StreamServiceTest extends PHPUnit_Framework_TestCase {
	
	protected $non_existent_channel_name = 'test_non_existent_channel_name_12345';	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated Test::setUp()
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Test::tearDown()
		parent::tearDown ();
		$this->service = null;
		$this->test_channel = null;
		$this->test_channels = null;
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	public function testConvertChannelNames() {
		$mock = $this->getMockForAbstractClass('StreamService');
		$res = $mock->convertChannelNames(array());
		$this->assertEquals(array(), $res);
		$res = $mock->convertChannelNames(array("abc"));
		$this->assertEquals(array("abc"), $res);
		$res = $mock->convertChannelNames(array(array("name"=>"abc")));
		$this->assertEquals(array("abc"), $res);
		
		$res = $mock->convertChannelNames(array(array("name"=>"abc"),array("name"=>"def")));
		$this->assertEquals(array("abc","def"), $res);
		
		$this->setExpectedException('InvalidArgumentException');
		$res = $mock->convertChannelNames(array(array("name"=>"abc"),array("name-x"=>"def")));
	}
	
	
	protected function loadResource($url, $data = array(), $method='POST') {
		
		$url = rtrim($url, "\r\n\t &?");
		$data_q = null;
		if(!empty($data)) {
			$data_q = http_build_query($data, "", "&");
		}
		
		$curl = curl_init();
		$retries  = 3;
		$timeout = 15;
		$result = null;
		if(is_resource($curl)=== true) {
			if(strtolower($method)=='get' && !empty($data)) {
				if(strpos($url,'?')===FALSE) {
					$url .= '?'.$data_q;
				}
				else {
					$url .= '&'.$data_q;
				}
			}
			
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			
			if(strtolower($method)=='post') {
				curl_setopt($curl, CURLOPT_POST, true);
				if($data_q){
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data_q);
				}
			}
			else if(strtolower($method)=='get') {
				curl_setopt($curl, CURLOPT_POST, false);
				// TODO add query string to URL
				
			}
			
			$result = false;
			while(($result === false)&&(--$retries > 0)) {
				$result = curl_exec($curl);
			}
			
			curl_close($curl);
		}
		return $result;
	}
	
}

