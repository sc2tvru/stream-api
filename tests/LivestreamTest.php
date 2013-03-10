<?php

require "../StreamService.php";
require "../services/Livestream.php";

class LivestreamTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        @$xml = simplexml_load_file(strtr(Livestream::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => 'proshowcase,livestream')));

        $dom=new DOMDocument;
        //Something wrong with namespaces in this xml
        $dom->loadXML(str_replace('ls:', '', $xml->asXML()));

        //Check xml scheme
        $this->assertTrue($dom->schemaValidate('./schema/livestream_v1.0_info.xsd'));

        $streamService = new Livestream();
        $channels = array(
            array('name' => 'proshowcase', 'id' => 'proshowcase'),
            array('name' => 'livestream', 'id' => 'livestream'),
        );

        $info = $streamService->getInfo($channels);

        foreach($info as $value) {
            $this->assertTrue(is_bool($value['live']));
            $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(array_key_exists('description', $value));
            $this->assertTrue(is_int($value['viewers']));
        }
    }
}
