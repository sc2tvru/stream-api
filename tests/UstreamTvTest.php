<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/UstreamTv.php";

class UstreamTvTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new UstreamTv();
		$res = $this->loadResource(strtr('http://api.ustream.tv/json/channel/live/search/all?key=:dev_key', array(':dev_key' => UstreamTv::DEV_KEY)), array(), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from api.ustream.tv failed');
		}
		$this->test_channels = $res['results'];
		$this->test_channel = reset($this->test_channels);
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['urlTitleName']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['urlTitleName'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        }
        
        // Check for single channel
        $info = $this->service->getInfo($this->test_channel['urlTitleName']);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        }
        
    }
    
}




