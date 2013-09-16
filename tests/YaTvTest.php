<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/YaTv.php";

class YaTvTest extends StreamServiceTest {
	
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new YaTv();
		$this->test_channels = array(array("name"=>"pervoesetevoe"),array("name"=>"test1"));
		$this->test_channel = reset($this->test_channels);
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['name']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['name'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        }
        
    }
    
}
