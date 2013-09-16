<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/JustinTv.php";

class JustinTvTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new JustinTv();
		$res = $this->loadResource('http://api.justin.tv/api/stream/list.json', array('limit'=>5), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from api.justin.tv failed');
		}
		$this->test_channel = reset($res);
		$this->test_channels = $res;
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['channel']['login']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['channel']['login'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        
        foreach($info as $value) {
        	$this->assertTrue($value['live']);
        	$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('title', $value));
        	$this->assertTrue(is_int($value['viewers']));
        }
        
    }
    
}
