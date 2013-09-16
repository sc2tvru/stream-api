<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/MotionCreds.php";

class MotionCredsTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new MotionCreds();
		$res = $this->loadResource(strtr(MotionCreds::CHECK_STREAM_STATUS_WITH_OFFLINE_URL, array(':channels' => implode(',',array('1o7lofv9jemne','of14eqp75q8a')))), array(), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource http://www.gamecreds.com/api failed');
		}
		
		foreach($res['result'] as $k=>$v) {
			$v['id'] = $k;
			$this->test_channels[] = $v;
		}
		$this->test_channel = reset($this->test_channels);
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['id']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['id'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue($value['live']===true || $value['live']===false);
        	$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('title', $value));
        	$this->assertTrue(is_int($value['viewers']));
        }
        
    }
    
}
