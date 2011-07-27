<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mosogeocoder_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'mosoGeocoder',
		'version'	=> '1.2'
	);
	
	var $api_address = 'http://maps.googleapis.com/maps/api/geocode/json?address=';
	
	// --------------------------------------------------------------------
	
	function __constructor()
	{
		$this->EE =& get_instance();
	}
	
	// =========================================
	// = Display the field in the publish form =
	// =========================================
	function display_field($data)
	{
		$value = "";
		
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$value = $arr[0];
			}
		}
		
		return form_input(array(
			'name'	=> $this->field_name,
			'id'	=> $this->field_id,
			'value'	=> $value
		));
	}
	
	/*
		TODO Make this return Lat & Lng value independantly
	*/
	
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		$value = "";
		
		//Check we have valid data before exploding
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$value = $arr[1] . ', ' . $arr[2];
					
			}//if $count($arr) > 0
		}//if $data
		
		return $value;
	}
	
	
	public function replace_lat($data, $params = array(), $tagdata = FALSE)
	{
		$value = "";
		
		//Check we have valid data before exploding
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$value = $arr[1];
					
			}//if $count($arr) > 0
		}//if $data
		
		return $value;
	}
	
	
	public function replace_lng($data, $params = array(), $tagdata = FALSE)
	{
		$value = "";
		
		//Check we have valid data before exploding
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$value = $arr[2];
					
			}//if $count($arr) > 0
		}//if $data
		
		return $value;
	}
	
	public function replace_address($data, $params = array(), $tagdata = FALSE)
	{
		$value = "";
		
		//Check we have valid data before exploding
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$value = $arr[0];
					
			}//if $count($arr) > 0
		}//if $data
		
		return $value;
	}
	
	
	// ===========================
	// = Returns map with marker =
	// ===========================
	public function replace_map($data, $params = array(), $tagdata = FALSE)
	{
		if($data){
			$arr = explode("::", $data);
			if(count($arr) > 0){
				$lat = $arr[1];
				$lng = $arr[2];
					
			}//if $count($arr) > 0
		}//if $data
		
		$script = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>';
		$script .= "
		<div id='gmap'></div>
		<script type='text/javascript'>
			(function(){
				var map = document.getElementById('gmap');
				var gmap = new google.maps.Map(map, {
					zoom : 8,
					center : new google.maps.LatLng($lat, $lng),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				})
			})()
		</script>";
	}
	
	
	
	// =============================================
	// = Process the address and return an address =
	// =============================================
	
	/*
		TODO Add marker position and config options for map
	*/
	public function save($str)
	{
		if(!$str) return NULL;
		
		$url_str = urlencode($str);

		//If you want an extended data set, change the output to "xml" instead of csv
		$url = $this->api_address.$url_str.'&sensor=false';
		//Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$data = curl_exec($ch);
		curl_close($ch);
		$json = json_decode($data);
		
		if($json->results != "ZERO_RESULTS"){
			return $str . '::' . $json->results[0]->geometry->location->lat . "::" . $json->results[0]->geometry->location->lng;
		}else{
			return false;
		}
		
	}
}
// END Google_maps_ft class

/* End of file ft.google_maps.php */
/* Location: ./system/expressionengine/third_party/google_maps/ft.google_maps.php */