<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/GoodGame.php";

class GoodGameTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new GoodGame();
		$res = $this->loadResource('http://goodgame.ru/api/getggchannelstatus?id=miker,pomi,abver,dian,2122', array('fmt'=>'json'), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from goodgame.ru/api failed');
		}
		$this->test_channel = reset($res);
		$this->test_channels = $res;
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['key']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['key'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	//$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        	$this->assertTrue(is_int($value['viewers']));
        }
        
    }
    
}
