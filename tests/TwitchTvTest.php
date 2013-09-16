<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/TwitchTv.php";

class TwitchTvTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new TwitchTv();
		$res = $this->loadResource('https://api.twitch.tv/kraken/streams', array('limit'=>5), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from api.twitch.tv failed');
		}
		$this->test_channel = reset($res['streams']);
		$this->test_channels = $res['streams'];
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['channel']['name']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['channel']['name'];
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
