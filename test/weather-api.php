<?php

	/**
	 * Grabs weather data from Google.com's weather API and return a nicely formatted array
	 *
	 * @author Ashwin Surajbali
	 * @package Redink Design
	 * @version 0.9.2
	 *
	 * @example
	 * $w = new googleWeather();
	 * $w->enable_cache = 1;
	 * $w->cache_path = '/var/www/mysite.com/cache';
	 * $ar_data = $w->get_weather_data(10027);
	 * print_r($ar_data);
	 * echo $ar_data['forecast'][0]['day_of_week'];
	 *
	 * Requires PHP 5 or greater
	 *
	 */

	class googleWeather{

		/**
		 * Zipcode
		 *
		 * @var int
		 */
		public $zip;

		/**
		 * Disable or enable caching
		 *
		 * @var boolean
		 */
		public $enable_cache = 0;

		/**
		 * Path to your cache directory
		 * eg. /www/website.com/cache
		 *
		 * @var string
		 */
		public $cache_path = '';

		/**
		 * Cache expiration time in seconds
		 * Default: 3600 = 1 Hour
		 * If the cached file is older than 1 hour, new data is fetched
		 *
		 * @var int
		 */
		public $cache_time = 3600; // 1 hour

		/**
		 * Full location of the cache file
		 *
		 * @var string
		 */
		private $cache_file;

		/**
		 * Location of the google weather api
		 *
		 * @var string
		 */
		private $gweather_api_url = 'http://www.google.com/ig/api?weather=';

		/**
		 * Storage var for data returned from curl request to the google api
		 *
		 * @var string
		 */
		private $raw_data;

		/**
		 * Pull weather information for 'Zipcode' passed in
		 * If enable_cache = true, data is cached and refreshed every hour
		 * Weather data is returned in an associative array
		 *
		 * @param int $zip
		 * @return array
		 */
		public function get_weather_data($zip = 'larissa'){


			$this->zip = $zip;


			if ($this->enable_cache && !empty($this->cache_path)){
				$this->cache_file = $this->cache_path . '/' . $this->zip;
				return $this->load_from_cache();
			}

			$return_array = array();
			// build the url
			$this->gweather_api_url = $this->gweather_api_url . $this->zip;
			//$this->gweather_api_url = "http://www.unblock-web.biz/index.php?q=aHR0cDovL3d3dy5nb29nbGUuY29tL2lnL2FwaT93ZWF0aGVyPWxhcmlzc2EsZ3ImaGw9ZWw%3D&hl=3ed"; 			

															
			if ($this->make_request()){


				try {
				    $xml = new SimpleXMLElement(stripslashes($this->raw_data));
				} catch (Exception $e) { 
					return $return_array;
				}
				
				
				
				

				$return_array['forecast_info']['city'] = $xml->weather->forecast_information->city['data'];
				$return_array['forecast_info']['zip'] = $xml->weather->forecast_information->postal_code['data'];
				$return_array['forecast_info']['date'] = $xml->weather->forecast_information->forecast_date['data'];
				$return_array['forecast_info']['date_time'] = $xml->weather->forecast_information->current_date_time['data'];

				$return_array['current_conditions']['condition'] = $xml->weather->current_conditions->condition['data'];
				$return_array['current_conditions']['temp_f'] = $xml->weather->current_conditions->temp_f['data'];
				$return_array['current_conditions']['temp_c'] = $xml->weather->current_conditions->temp_c['data'];
				$return_array['current_conditions']['humidity'] = $xml->weather->current_conditions->humidity['data'];
				$return_array['current_conditions']['icon'] = 'http://www.google.com' . $xml->weather->current_conditions->icon['data'];
				$return_array['current_conditions']['wind'] = $xml->weather->current_conditions->wind_condition['data'];

				for ($i = 0; $i < count($xml->weather->forecast_conditions); $i++){
					$data = $xml->weather->forecast_conditions[$i];
					$return_array['forecast'][$i]['day_of_week'] = $data->day_of_week['data'];
					$return_array['forecast'][$i]['low'] = $data->low['data'];
					$return_array['forecast'][$i]['high'] = $data->high['data'];
					//$return_array['forecast'][$i]['icon'] = 'http://img0.gmodules.com/' . $data->icon['data'];
					$pos = strrpos($data->icon['data'], '/', -1);
					$return_array['forecast'][$i]['icon'] = substr($data->icon['data'], $pos+1 );					
					$return_array['forecast'][$i]['condition'] = $data->condition['data'];
				}

			}

			if ($this->enable_cache && !empty($this->cache_path)){
				$this->write_to_cache();
			}

			return $return_array;

		}

		private function load_from_cache(){

			if (file_exists($this->cache_file)){

				$file_time = filectime($this->cache_file);
				$now = time();
				$diff = ($now-$file_time);

				if ($diff <= 3600){
					return unserialize(file_get_contents($this->cache_file));
				}
			}

		}

		private function write_to_cache(){

			if (!file_exists($this->cache_path)){
				// attempt to make the dir
				mkdir($this->cache_path, 0777);
			}

			if (!file_put_contents($this->cache_file, serialize($return_array))){
				echo "<br />Could not save data to cache. Please make sure your cache directory exists and is writable.<br />";
			}
		}

		private function make_request(){

			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_URL, $this->gweather_api_url);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$this->raw_data = curl_exec ($ch);
			curl_close ($ch);

			if (empty($this->raw_data)){
				return false;
			}else{
				return true;
			}

		}

	}



					
                        function selectIcon($dayforecast) {

                            global $ar_data;


                    		switch  ($ar_data['forecast'][$dayforecast]['icon']) {
                    			case 'sunny.gif' : 
								case 'weather_sunny-40.gif' :								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/32.png"> <br>'; break;
										
										
                                case 'mostly_sunny.gif' :
								case 'weather_overcast-40.gif' :								
										 echo '<br><img src="/templates/classic/img/weathericons/small_icons/30.png"> <br>'; break;
                                
								case 'partly_cloudy.gif':
								case 'weather_partlycloudy-40.gif' : 
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/28.png"> <br>'; break;
								
                                case 'mostly_cloudy.gif' : 								
								case 'weather_mostlycloudy-40.gif' :								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/28.png"> <br>'; break;
										
										
                                case 'chance_of_storm.gif' : 
								case 'weather_scatteredshowers-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/37.png"> <br>'; break;
										
										
                                case 'rain.gif' : 
								case 'weather_rain-40.gif':								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/12.png"> <br>'; break;
										
                                case 'chance_of_rain.gif' :								
								case 'weather_windy-40.gif' :
								case 'weather_drizzle-40.gif' :
										 echo '<br><img src="/templates/classic/img/weathericons/small_icons/39.png"> <br>'; break;
										
										
                                case 'chance_of_snow.gif' :
								case 'weather_rainsnow-40.gif' :		
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/42.png"> <br>'; break;
										
                                case 'cloudy.gif' : 
								case 'weather_cloudy-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/26.png"> <br>'; break;
										
										
                                case 'mist.gif' : 
								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/22.png"> <br>'; break;
										
                                case 'storm.gif' :
								case 'weather_heavyrain-40.gif' :								
								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/12.png"> <br>'; break;
										
										
                                case 'thunderstorm.gif' : 
								case 'weather_thunderstorms-40.gif' :								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/17.png"> <br>'; break;
										
										
                                case 'chance_of_tstorm.gif' : 
								case 'weather_scatteredthunderstorms-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/37.png"> <br>'; break;
										
										
                                case 'sleet.gif' : 								
								case 'weather_sleet-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/5.png"> <br>'; break;
										
                                case 'snow.gif' : 
								case 'weather_snow-40.gif' :
								case 'weather_heavysnow-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/14.png"> <br>'; break;
										
										
                                case 'icy.gif' : 
								case 'weather_icy-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/25.png"> <br>'; break;
										
										
                                case 'dust.gif' : 
								case 'weather_dust-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/21.png"> <br>'; break;
										
										
                                case 'fog.gif' : 
								case 'weather_fog-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/22.png"> <br>'; break;
										
										
                                case 'smoke.gif' :
								case 'weather_smoke-40.gif' :								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/30.png"> <br>'; break;
										
										
                                case 'haze.gif' : 
								case 'weather_overcast-40.gif' :								
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/30.png"> <br>'; break;
										
										
                                case 'flurries.gif' : 								
								case 'weather_snowflurries-40.gif' :
										echo '<br><img src="/templates/classic/img/weathericons/small_icons/30.png"> <br>'; break;

                    			default : echo '<span id="missingicon" class='.$ar_data['forecast'][0]['icon'].'></span>';
                    		}

                        }
                        global $ar_data;

                		$w = new googleWeather();

                		$w->enable_cache = 0;
                		$w->cache_path = '/var/www/mysite.com/cache';
                		$ar_data = $w->get_weather_data('larissa,gr&hl=el');

                     ?>


               
