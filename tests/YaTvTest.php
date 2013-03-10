<?php

require "../StreamService.php";
require "../services/YaTv.php";

class YaTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        $json_string = file_get_contents(strtr(YaTv::CHECK_STREAM_STATUS_URL, array(':channel_name' => 'test')));
        $info = json_decode($json_string);

        //Validate json structure
        $this->assertTrue(is_object($info));
        $this->assertTrue(property_exists($info, 'code'));
        $this->assertTrue(property_exists($info, 'data'));
        $this->assertTrue(property_exists($info->data, 'cid'));
        $this->assertTrue(property_exists($info->data, 'type'));
        $this->assertTrue(property_exists($info->data, 'attributes'));
        $this->assertTrue(property_exists($info->data->attributes, 'title'));
        $this->assertTrue(property_exists($info->data->attributes, 'description'));
    }
}
