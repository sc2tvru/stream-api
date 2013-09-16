<?php
$_dirname = dirname(__FILE__).'/';
require_once $_dirname."StreamServiceTest.php";
require_once $_dirname."../services/Livestream.php";

class LivestreamTest extends StreamServiceTest {
	
	protected $service = null;
	protected $xml_string = "";
	protected $test_channel = null;
	protected $test_channels = null;
	
	public function setUp() {
		$this->service = new Livestream();
		$res = $this->loadResource(strtr(Livestream::CHECK_STREAM_V1_0_STATUS_URL, array(':channels' => 'proshowcase,livestream')), array(), 'GET');
		if(!$res) {
			$this->fail('Load resource from '.Livestream::CHECK_STREAM_V1_0_STATUS_URL.' failed');
		}
		// Hack - removing xml namespace
		$res = str_replace('ls:', '', $res);
		$xml = simplexml_load_string($res);
		if(!$xml) {
			$this->fail('Decode resource from '.Livestream::CHECK_STREAM_V1_0_STATUS_URL.' failed');
		}
		
		$this->test_channels = array();
		foreach($xml as $v) {
			$this->test_channels[] = array(
				'name'=>(string)$v->attributes()->name,					
			);
		}
		$this->xml_string = $res;
		$this->test_channel = reset($this->test_channels);
	}
	
	public function testSchema() {
		$xml = simplexml_load_string($this->xml_string);
		$dom = new DOMDocument;
		//Something wrong with namespaces in this xml
		$dom->loadXML($xml->asXML());
		//Check xml scheme
		$this->assertTrue($dom->schemaValidate(dirname(__FILE__).'/schema/livestream_v1.0_info.xsd'));
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
        	$this->assertTrue($value['live']===true || $value['live']===false);
        	$this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
        	$this->assertTrue(array_key_exists('title', $value));
        	$this->assertTrue(is_int($value['viewers']));
        }
        
    }
    
}

