<?php
require_once dirname(__FILE__).'/StreamApiService.php';

abstract class StreamService {
	
	protected $chunk_size = 100;
	/**
	 * This is stub method since many of services doesn't provide any methods to check offline channels 
	 * @param Array $channel        	
	 * @return Array or null if channel isn't exist
	 */
	public function checkChannel($channel) {
		return true;
	}
	
	/**
	 * Get stream thumbnail
	 * 
	 * @param Array $channel
	 *        	channels information
	 * @return String with thumbnail url or null if thumbnail can be obtain only
	 *         by getInfo method
	 */
	abstract public function getThumbnail($channel);
	abstract public function getEmbedPlayerCode($channel, $width, $height);
	public function renderTemplate($template, $data){
		ob_start();
		extract($data);
		require('services/templates/' . $template . '.tpl.php');
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents;
	}
	
	
	protected function getChunkSize() {
		return $this->chunk_size;
	}
	protected function splitChannels($channels=array()) {
		// TODO chunk size depends on service
		return array_chunk($channels, $this->getChunkSize());
	}
	
	
	/**
	 * Convert channel names to raw array
	 * Return unmodified argument on array of channels names
	 * 
	 * @return array
	 */
	
	public function convertChannelNames($channels) {
		$names = array();
		if(is_array($channels)) {
			$ch = reset($channels);
			if(isset($ch['name'])) {
				foreach($channels as $ch) {
					if(!isset($ch['name'])) {
						throw new InvalidArgumentException("Invalid argument for channel names");
					}
					$names[] = $ch['name'];
				}
				return $names;
			}
		}
		else if(is_scalar($channels)) {
			$channels = array($channels);
		}
		return $channels;
	}
	
	/**
	 * Get stream information in batch request
	 * Return null on error or array with channels info
	 * array(
	 *   array(
	      'name' => '',
	      'id' => '',
	      'service' => 'Livestream',
	      'live' => $stream->isLive == 'true' ? true : false,
	      'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)),
	      'title' => (string) $stream->title,
	      'description' => (string) $stream->description,
	      'viewers' => (int) $stream->currentViewerCount,
	     );
	 *
	 *
	 * @param Array $channels
	 * @return Array
	 */
	public function getInfo($channels) {
		$channels = $this->convertChannelNames($channels);
		$infos = array();
		$channel_chunks = $this->splitChannels($channels);
		foreach($channel_chunks as $chunk_channels) {
			$raw_info = $this->loadChunkInfo($chunk_channels);
			$chunk_info = $this->decodeChunkInfo($raw_info);
			if(is_array($chunk_info)) {
				foreach($chunk_info as $channel_info) {
					$infos[] = $channel_info;
				}
			}
		}
		// combine all chunks and return result
		return $infos;
	}
	
	
	/**
	 * Get stream information for chunk. Size of chunk depends on service
	 * Return null on error or raw channels info
	 */
	abstract protected function loadChunkInfo($channels);
	abstract protected function decodeChunkInfo($raw);
	
	public function nameToInfoMap($channel_info) {
		$info = array();
		foreach($channel_info as $v) {
			$info[$v['name']] = $v;
		}
		return $info;
	}
	
	protected function loadResource($url, $data = array(), $method='POST') {
		
		$url = rtrim($url, "\r\n\t &?");
		$data_q = null;
		if(!empty($data)) {
			$data_q = http_build_query($data, "", "&");
		}
		
		$curl = curl_init();
		$retries  = 3;
		$timeout = 15;
		$result = null;
		if(is_resource($curl)=== true) {
			if(strtolower($method)=='get' && !empty($data)) {
				if(strpos($url,'?')===FALSE) {
					$url .= '?'.$data_q;
				}
				else {
					$url .= '&'.$data_q;
				}
			}
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($curl, CURLOPT_COOKIESESSION, true);
			
			if(strtolower($method)=='post') {
				curl_setopt($curl, CURLOPT_POST, true);
				if($data_q){
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data_q);
				}
			}
			else if(strtolower($method)=='get') {
				curl_setopt($curl, CURLOPT_POST, false);
				// TODO add query string to URL
				
			}
			
			$result = false;
			while(($result === false)&&(--$retries > 0)) {
				$result = curl_exec($curl);
			}
			
			curl_close($curl);
		}
		return $result;
	}
	
}
