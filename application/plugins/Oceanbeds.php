<?php 
Class Oceanbeds{
	
	private $apiUsername, $apiPassword, $results;
        public $arrivalDate, $departureDate;

        public function __construct($apiUsername, $apiPassword)
        {

            //mandatory fields
            $this->apiUsername = $apiUsername;
            $this->apiPassword = $apiPassword;

            //other secondary mandatory fields
            $this->arrivalDate = $this->departureDate = "";

            //other fields
            $this->managementCompanyId = $this->communityId = $this->propertyId = $this->propertyType = $this->propertyClass = $this->sleeps = $this->bedrooms = 0;
            $this->hasPool = $this->hasSpa = $this->privacyFence = $this->communalGym = $this->hasGamesRoom =
                    $this->coservationView =
                    $this->waterView = $this->lakeView = $this->wifi = $this->petsAllowed = $this->onGolfCourse =
                    $this->southFacingPool = 2;
            $this->isGasFree = "false";
        }
		
		//-------------------------------------------------------get property Heading---------------------------------------
		 function getHeading($propertyId)
        {
            $data .= '<?xml version="1.0" encoding="utf-8"?>
			<PropertyDetailRQ xmlns="http://oceanbeds.com/2014/10">
					  <Credential>
						<User>'. $this->apiUsername .'</User>
						<Password>'. $this->apiPassword .'</Password>
					  </Credential>
					  <Id>'. $propertyId . '</Id>
					  </PropertyDetailRQ>';
			$results = $this->curl('http://oceanbeds.com/service.svc/GetPropertyDetail', $data);
			
			preg_match('/<Name>(.*?)<\/Name>/', $results, $heading);
			
			return $heading[1];
        }
		
		//======================get features===============================================
        function getFeatures($propertyId)
        {
			$data .= '<?xml version="1.0" encoding="utf-8"?>
			<PropertyDetailRQ xmlns="http://oceanbeds.com/2014/10">
					  <Credential>
						<User>'. $this->apiUsername .'</User>
						<Password>'. $this->apiPassword .'</Password>
					  </Credential>
					  <Id>'. $propertyId . '</Id>
					  </PropertyDetailRQ>';
			$results = $this->curl('http://oceanbeds.com/service.svc/GetPropertyDetail', $data);
			$xml=simplexml_load_string($results);
			$featureArr = $xml->Response->Property->Facility->KeyFeatures;
			return $featureArr;
		}
		
		//---------------------------------------------------------get property Images-------------------------------------------------
		function getImageList($propertyId)
        {
			$data .= '<?xml version="1.0" encoding="utf-8"?>
			<PropertyDetailRQ xmlns="http://oceanbeds.com/2014/10">
					  <Credential>
						<User>'. $this->apiUsername .'</User>
						<Password>'. $this->apiPassword .'</Password>
					  </Credential>
					  <Id>'. $propertyId . '</Id>
					  </PropertyDetailRQ>';
			$results = $this->curl('http://oceanbeds.com/service.svc/GetPropertyDetail', $data);
			preg_match('/<ImageList>(.*?)<\/ImageList>/', $results, $ImageList);
			$arr = $this->xml_to_array($ImageList[0]);
			$imagereturn =array();
			foreach($arr as $imageurl){
				foreach($imageurl as $images_url)
				$imagereturn[] = $images_url['Url'];
			}
			return $imagereturn;
		} 
		
		//====================== get property decription =============================================
        function getDescription($propertyId)
        {
			$data .= '<?xml version="1.0" encoding="utf-8"?>
			<PropertyDetailRQ xmlns="http://oceanbeds.com/2014/10">
					  <Credential>
						<User>'. $this->apiUsername .'</User>
						<Password>'. $this->apiPassword .'</Password>
					  </Credential>
					  <Id>'. $propertyId . '</Id>
					  </PropertyDetailRQ>';
			$results = $this->curl('http://oceanbeds.com/service.svc/GetPropertyDetail', $data);
			
			preg_match('/<SettingScene>(.*?)<\/SettingScene>/', $results, $features);
			preg_match('/<Location>(.*?)<\/Location>/', $results, $location);
			preg_match('/<FacilitiesAmenity>(.*?)<\/FacilitiesAmenity>/', $results, $facilities);
			preg_match('/<Dining>(.*?)<\/Dining>/', $results, $dinning);
			preg_match('/<ForKids>(.*?)<\/ForKids>/', $results, $forkids);
			preg_match('/<RoomInfo>(.*?)<\/RoomInfo>/', $results, $roominfo);
			preg_match('/<ThingsYouShouldKnow>(.*?)<\/ThingsYouShouldKnow>/', $results, $things);
			preg_match('/<HotelAddress>(.*?)<\/HotelAddress>/', $results, $hoteladdress);
			
			
		
			if($features[1] != ''){
				$description .= "<b>Setting the scene</b></br>".$features[1]."</br>";
			}
			if($location[1] != '')
			{
				$description .= "<b>Location</b></br>".$location[1]."</br>";
			}
			if($facilities[1] != '')
			{
				$description .= "<b>Facilities & amenities</b></br>".$facilities[1]."</br>";
			}
			if($dinning[1] != '')
			{
				$description .= "<b>Dining</b></br>".$dinning[1]."</br>";
			}
			if($forkids[1] != '')
			{
				$description .= "<b>For Kids</b></br>".$forkids[1]."</br>";
			}
			if($roominfo[1] != '')
			{
				$description .= "<b>Room Info</b></br>".$roominfo[1]."</br>";
			}
			if($things[1] != '')
			{
				$description .= "<b>Things you should know</b></br>".$things[1]."</br>";
			}
			if($hoteladdress[1] != '')
			{
				$description .= "<b>Hotel Address</b></br>".$hoteladdress[1]."</br>";
			}
			
			return $description;
			
		}
	
	//=================== get a list of book dates ================================
        function getReservations($propertyId)
        {
			$property_code = "CLNYCLB";
			$data .= '<?xml version="1.0" encoding="utf-8"?>
			<PropertyAvailabilityRQ xmlns="http://oceanbeds.com/2014/10">
			<Credential>
				<User>'.$this->apiUsername.'</User>
				<Password>'.$this->apiPassword.'</Password>
			</Credential>
			<CheckInDate>11-dec-2015</CheckInDate>
			<CheckOutDate>25-dec-2015</CheckOutDate>
			<RoomList>
				<RequestRoom>
					<Adults>2</Adults>
					<Children>0</Children>
				</RequestRoom>
				<RequestRoom>
					<Adults>2</Adults>
					<Children>0</Children>
				</RequestRoom>
			</RoomList>
			<PropertyCode>'.$property_code.'</PropertyCode>
			<StateId>0</StateId>
			<CityId>0</CityId>
			<Region></Region>
			<CommunityId>0</CommunityId>
			<BedRoom>0</BedRoom>
			<HomeType>Hotel</HomeType>
			<IsAvailable>true</IsAvailable>
			<HotelSearch>true</HotelSearch>
			<GenericSearch>true</GenericSearch>
			<VillaSearch>true</VillaSearch>
		</PropertyAvailabilityRQ>';
			$results = $this->curl('http://oceanbeds.com/service.svc/GetPropertyAvailability', $data);
			
			echo "<pre>"; print_r($results); echo "</pre>";
			//echo "<pre>"; print_r($KeyFeatures); echo "</pre>";
			die('fh');
		//	return $results;
		}
	
	//Curl
        function curl($url, $data = NULL)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/xml",
                "charset: utf-8",
                "Content-length: " . strlen($data) . " "));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            return $results = curl_exec($ch);
        }

        function xml_to_array($xml, $main_heading = '')
        {
            $deXml = simplexml_load_string($xml);
            $deJson = json_encode($deXml);
            $xml_array = json_decode($deJson, TRUE);
            if (!empty($main_heading))
            {
                $returned = $xml_array[$main_heading];
                return $returned;
            }
            else
            {
                return $xml_array;
            }
        }
}
?>