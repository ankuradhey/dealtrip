<?php 
Class Floridatravelnetwork{
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
			$u = "destam1";
			$p = "davidkym";
            $data .= '<?xml version="1.0" encoding="utf-8" ?>
						<request id="11">
						<LoginId>'. $u .'</LoginId>
						<Password>'. $p .'</Password>
						<HotelId>'. $propertyId .'</HotelId>
						</request>';
			$results = $this->curl('http://wds2.projectstatus.co.uk/floridaTN/ftnwebservice.asmx', $data);
			
			
			echo "<pre>"; print_r($results); echo "</pre>"; die('fh');
			
			preg_match('/<Name>(.*?)<\/Name>/', $results, $heading);
			
			return $heading[1];
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