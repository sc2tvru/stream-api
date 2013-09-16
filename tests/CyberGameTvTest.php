<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/CyberGameTv.php";

class CyberGameTvTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new CyberGameTv();
		$res = $this->loadResource('http://api.cybergame.tv/w/streams2.php', array(), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from api.cybergame.tv failed');
		}
		$channels = array();
		foreach($res as $channel) {
			if(!empty($channel)) {
				$channels[] = $channel;
			}
		}
		$channels = array_slice($channels, 0, 5);
		$this->test_channels = $channels;
		$this->test_channel = reset($this->test_channels);
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = $this->test_channels;
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        }
        
        // Check for single channel
        $info = $this->service->getInfo($this->test_channel);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        }
        
    }
    
}

