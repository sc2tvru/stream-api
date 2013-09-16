<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/HashdTv.php";

class HashdTvTest extends StreamServiceTest {
	
	//alkoholikstv
	protected $service = null;
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new HashdTv();
		$res = $this->loadResource('http://api.hashd.tv/v1/streams/alkoholikstv,one,nalan', array(), 'GET');
		$res = json_decode($res, true);
		if(!$res) {
			$this->fail('Decode resource from api.hashd.tv failed');
		}
		$this->test_channel = reset($res);
		$this->test_channels = $res;
	}
	

	public function testCheckChannel() {
		$info = $this->service->checkChannel($this->test_channel['name_seo']);
		$this->assertNotNull($info);
	}
	
    public function testGetInfo() {
        $channels = array();
        foreach($this->test_channels as $stream) {
        	$channels[] = $stream['name_seo'];
        }
        $channels[] = $this->non_existent_channel_name;
        $info = $this->service->getInfo($channels);
        foreach($info as $value) {
        	$this->assertTrue(($value['live']===true OR $value['live']===false));
        	$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('name', $value));
        	$this->assertTrue(array_key_exists('title', $value));
        	$this->assertTrue(is_int($value['viewers']));
        }
        
    }
    
}



