<?php

    class Ciirus
    {

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

        public function getProperties($arr = array(), $limit = 0, $full_detail = "false", $return_quote = "false", $includePoolHeatInQuote = "false")
        {
            if (!empty($arr))
            {
                foreach ($arr as $Key => $Val)
                {
                    //if()
                    $this->$Key = $Val;
                }
            }

            $data = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetProperties xmlns="http://xml.ciirus.com/">
      <APIUsername>' . $this->apiUsername . '</APIUsername>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <ArriveDate>' . $this->arrivalDate . '</ArriveDate>
      <DepartDate>' . $this->departureDate . '</DepartDate>
      <FilterOptions>
        <ManagementCompanyID>' . $this->managementCompanyId . '</ManagementCompanyID>
        <CommunityID>' . $this->communityId . '</CommunityID>
        <PropertyID>' . $this->propertyId . '</PropertyID>
        <PropertyType>' . $this->propertyType . '</PropertyType>
        <HasPool>' . $this->hasPool . '</HasPool>
        <HasSpa>' . $this->hasSpa . '</HasSpa>
        <PrivacyFence>' . $this->privacyFence . '</PrivacyFence>
        <CommunalGym>' . $this->communalGym . '</CommunalGym>
        <HasGamesRoom>' . $this->hasGamesRoom . '</HasGamesRoom>
        <IsGasFree>' . $this->isGasFree . '</IsGasFree>
        <Sleeps>' . $this->sleeps . '</Sleeps>
        <Bedrooms>' . $this->bedrooms . '</Bedrooms>
        <PropertyClass>' . $this->propertyClass . '</PropertyClass>
        <ConservationView>' . $this->coservationView . '</ConservationView>
        <WaterView>' . $this->waterView . '</WaterView>
        <LakeView>' . $this->lakeView . '</LakeView>
        <WiFi>' . $this->wifi . '</WiFi>
        <PetsAllowed>' . $this->petsAllowed . '</PetsAllowed>
        <OnGolfCourse>' . $this->onGolfCourse . '</OnGolfCourse>
        <SouthFacingPool>' . $this->southFacingPool . '</SouthFacingPool>
      </FilterOptions>
      <SearchOptions>
        <ReturnTopX>' . $limit . '</ReturnTopX>
        <ReturnFullDetails>' . $full_detail . '</ReturnFullDetails>
        <ReturnQuote>' . $return_quote . '</ReturnQuote>
        <IncludePoolHeatInQuote>' . $includePoolHeatInQuote . '</IncludePoolHeatInQuote>
      </SearchOptions>
    </GetProperties>
  </soap12:Body>
</soap12:Envelope>';


            $headers = array(
                "Content-Type: application/soap+xml",
                "charset: utf-8",
                "Content-length: " . strlen($data) . " ");

            //$headers = "Content-Type: application/soap+xml&charset: utf-8";
            //prd($headers);
            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);


            if ($this->debug == true)
            {
                print "<xmp>" . $data . "</xmp><br />";
                print "<xmp>" . $results . "</xmp><br />";
            }

            $this->results = $results;
            return $results;

            //print "<xmp>" . $results . "</xmp><br />";
            //die;
        }

        //=======================get heading=============================================
        function getHeading($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $arr['propertyId'] = $propertyId;
                $results = $this->getProperties($arr);
            }


            preg_match('/<ManagementCompanyName>(.*?)<\/ManagementCompanyName>/', $results, $heading);
            
            return $heading[1];
        }

        //======================get community==============================================
        function getCommunity($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $arr['propertyId'] = $propertyId;
                $results = $this->getProperties($arr);
            }
            preg_match('/<Community>(.*?)<\/Community>/', $results, $community);
            return $community[1];
        }

        //======================get features===============================================
        function getFeatures($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $arr['propertyId'] = $propertyId;
                $results = $this->getProperties($arr);
            }
            preg_match('/<PropertyDetails>(.*?)<\/PropertyDetails>/', $results, $features);

            $features = $this->xml_to_array($features[0]);
            $featureArr = array();
            foreach ($features as $fKey => $fVal)
            {
                if ($fVal == 'true')
                {
                    $featureArr[] = $fKey;
                }
            }

            return implode(",", $featureArr);
        }

        //======================get property rates=============================================
        function getPropertyRates($propertyId)
        {

            $data = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetPropertyRates xmlns="http://xml.ciirus.com/">
      <APIUserName>' . $this->apiUsername . '</APIUserName>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetPropertyRates>
  </soap12:Body>
</soap12:Envelope>';

            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);


            preg_match('/<GetPropertyRatesResult>(.*)<\/GetPropertyRatesResult>/', $results, $rates);

            $rates = $this->xml_to_array($rates[0]);


            return $rates;
        }

        //====================== get property decription =============================================
        function getDescription($propertyId)
        {
            $data = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetDescriptions xmlns="http://xml.ciirus.com/">
      <APIUserName>' . $this->apiUsername . '</APIUserName>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetDescriptions>
  </soap12:Body>
</soap12:Envelope>';

            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);

            $results = str_replace(array("\n", "\r"), "", $results);
            //preg_replace('/\n/','<br>',$resuts);

            preg_match('/<GetDescriptionsResult>(.*)<\/GetDescriptionsResult>/', $results, $description);
            //preg_match('/<GetDescriptionsResponse>(.*?)<\/GetDescriptionsResponse>/', $results, $description);
            //prd($description);
            return $description[1];
        }

        //=========================== structured property rates ============================
        function getStructuredPropertyRates($propertyId)
        {

            $rates = $this->getPropertyRates($propertyId);

            //prd($rates);

            $result = '<table class="property-rating">';
            $result .= '<thead>';
            $result .= '<tr>';
            $result .= '<td>From Date</td>';
            $result .= '<td>To Date</td>';
            $result .= '<td>Minimum Nights</td>';
            $result .= '<td>Daily Rate</td>';
            $result .= '</tr>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($rates['Rate'] as $rKey => $rVal)
            {

                $from_date = explode('T',$rVal['FromDate']);
                $to_date = explode('T',$rVal['ToDate']);
                
                $result .= '<tr>';
                $result .= '<td>' . $from_date[0] . '</td>';
                $result .= '<td>' . $to_date[0] . '</td>';
                $result .= '<td>' . $rVal['MinNightsStay'] . '</td>';
                $result .= '<td>' . $rVal['DailyRate'] . '</td>';
                $result .= '</tr>';
            }


            $result .= '</tbody>';
            $result .= '</table>';

            return $result;
        }

        //========================= get images list ============================================
        function getImageList($propertyId)
        {

            $data .= '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetImageList xmlns="http://xml.ciirus.com/">
      <APIUserName>' . $this->apiUsername . '</APIUserName>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetImageList>
  </soap12:Body>
</soap12:Envelope>';

            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);



            //print "<xmp>".$results."</xmp>";
            preg_match('/<GetImageListResult>(.*)<\/GetImageListResult>/', $results, $images);

            $arr = $this->xml_to_array($images[0]);
            return $arr['string'];
        }

        //=================== get a list of book dates ================================
        function getReservations($propertyId)
        {

            $data .= '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetReservations xmlns="http://xml.ciirus.com/">
      <APIUsername>'.$this->apiUsername.'</APIUsername>
      <APIPassword>'.$this->apiPassword.'</APIPassword>
      <PropertyID>'.$propertyId.'</PropertyID>
    </GetReservations>
  </soap12:Body>
</soap12:Envelope>';
        
          
            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);
            preg_match('/<GetReservationsResult>(.*)<\/GetReservationsResult>/', $results, $reservation);
            $arr = $this->xml_to_array($reservation[0]);
            return $arr['Reservations'];
        }

        //Curl
        function curl($url, $data = NULL)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/soap+xml",
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