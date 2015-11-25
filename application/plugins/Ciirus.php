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

        public function getProperties($arr = array(), $limit = 0, $full_detail = "true", $return_quote = "false", $includePoolHeatInQuote = "false")
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
        <ReturnFullDetails>true</ReturnFullDetails>
        <ReturnQuote>true</ReturnQuote>
        <IncludePoolHeatInQuote>true</IncludePoolHeatInQuote>
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

        //============================ property rates for save (calendar rates) ============================

        function getCalendarPropertyRates($propertyId, $rateMarkup = 31)
        {
            $rateArr = array();
            $rates = $this->getPropertyRates($propertyId);

            foreach ($rates['Rate'] as $rKey => $rVal)
            {
                $from_date = explode('T', $rVal['FromDate']);
                $to_date = explode('T', $rVal['ToDate']);

                $daily_rate = $rVal['DailyRate'];

//                $_13_percent = ($daily_rate * 13) / 100;
//                $_18_percent = ($daily_rate * 18) / 100;
                $markupPercent = ($daily_rate * $rateMarkup) / 100;

                $daily_rate = $daily_rate + $markupPercent;

                $rateArr[$rKey]['date_from'] = $from_date[0];
                $rateArr[$rKey]['date_to'] = $to_date[0];
//                $rateArr[$rKey]['nights'] = $rVal['MinNightsStay'];
                $rateArr[$rKey]['nights'] = '7';
                $rateArr[$rKey]['prate'] = round($daily_rate,2);
                
            }
            return $rateArr;
        }

        //=========================== structured property rates (work page) ============================
        function getStructuredPropertyRates($propertyId, $rateMarkup = 31)
        {

            $rates = $this->getPropertyRates($propertyId);


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
            
            //check multi dimensional
            if(count($rates['Rate']) == count($rates['Rate'], COUNT_RECURSIVE)){
                $rates['Rate'] = array($rates['Rate']);
            }
            
            foreach ($rates['Rate'] as $rKey => $rVal)
            {
                $from_date = explode('T', $rVal['FromDate']);
                $to_date = explode('T', $rVal['ToDate']);

                $daily_rate = $rVal['DailyRate'];

//                $_13_percent = ($daily_rate * 13) / 100;
//                $_18_percent = ($daily_rate * 18) / 100;
                $markUp = ($daily_rate * $rateMarkup) / 100;

                $daily_rate = $daily_rate + $markUp;

                $result .= '<tr>';
                $result .= '<td>' . $from_date[0] . '</td>';
                $result .= '<td>' . $to_date[0] . '</td>';
                $result .= '<td>' . $rVal['MinNightsStay'] . '</td>';
                $result .= '<td>' . round($daily_rate,2) . '</td>';
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
      <APIUsername>' . $this->apiUsername . '</APIUsername>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetReservations>
  </soap12:Body>
</soap12:Envelope>';


            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);
            preg_match('/<GetReservationsResult>(.*)<\/GetReservationsResult>/', $results, $reservation);
            $arr = $this->xml_to_array($reservation[0]);

            //prd($arr);
            return $arr['Reservations'];
        }

        //=================== get a list of book dates ================================
        function isPropertyAvailable($propertyId, $arrivalDate, $departureDate)
        {

            $data .= '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <IsPropertyAvailable xmlns="http://xml.ciirus.com/">
      <APIUsername>' . $this->apiUsername . '</APIUsername>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
      <ArrivalDate>' . $arrivalDate . '</ArrivalDate>
      <DepartureDate>' . $departureDate . '</DepartureDate>
    </IsPropertyAvailable>
  </soap12:Body>
</soap12:Envelope>';


            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);

            //print "<xmp>".$results."</xmp>";
            preg_match('/<IsPropertyAvailableResult>(.*)<\/IsPropertyAvailableResult>/', $results, $reservation);
            $arr = $this->xml_to_array($reservation[0]);
            return $arr[0];
        }

        //========================== function to make booking ==============================
        function makeBooking($propertyId, $arrivalDate, $departureDate, $guestArr)
        {

            $data .= '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <MakeBooking xmlns="http://xml.ciirus.com/">
      <APIUsername>' . $this->apiUsername . '</APIUsername>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <BD>
        <ArrivalDate>' . $arrivalDate . '</ArrivalDate>
        <DepartureDate>' . $departureDate . '</DepartureDate>
        <PropertyID>' . $propertyId . '</PropertyID>
        <GuestName>' . $guestArr['guest_name'] . '</GuestName>
        <GuestEmailAddress>' . $guestArr['guest_email'] . '</GuestEmailAddress>
        <GuestTelephone>' . $guestArr['guest_telephone'] . '</GuestTelephone>
        <GuestAddress>' . $guestArr['guest_address'] . '</GuestAddress>
        <PoolHeatRequired>' . $guestArr['pool_heat'] . '</PoolHeatRequired>
      </BD>
    </MakeBooking>
  </soap12:Body>
</soap12:Envelope>';

            $results = $this->curl('http://xml.ciirus.com/CiirusXML.12.017.asmx', $data);

            preg_match('/<BookingPlaced>(.*)<\/BookingPlaced>/', $results, $booking);
            $booking = $this->xml_to_array($booking[0]);

            preg_match('/<BookingID>(.*)<\/BookingID>/', $results, $bookingId);
            $bookingId = $this->xml_to_array($bookingId[0]);

            preg_match('/<ErrorMessage>(.*)<\/ErrorMessage>/', $results, $error);
            $error = $this->xml_to_array($error[0]);

            $arr['booking_placed'] = $booking[0];
            $arr['booking_id'] = $bookingId[0];
            $arr['error_msg'] = $error[0];

            return $arr;
        }

        //replacing the older getExtra function as it returned few details. 
        function getExtras1($propertyId)
        {
            $data = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetExtras xmlns="http://xml.ciirus.com/">
      <APIUserName>' . $this->apiUsername . '</APIUserName>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetExtras>
  </soap12:Body>
</soap12:Envelope>';



            $results = $this->curl('http://xml.ciirus.com/XMLAdditionalFunctions1.asmx', $data);

            preg_match('/<GetExtrasResult>(.*)<\/GetExtrasResult>/', $results, $extra);
            $extra = $this->xml_to_array($extra[0]);

            if (isset($extra['RowCount']) && $extra['RowCount'] > 0)
            {
                return $extra['Extras']['PropertyExtras'];
            }
            else
                return false;
        }

        //======= list of extras =======================
        function getExtras($propertyId)
        {

            $data = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <GetPoolHeatSettings xmlns="http://xml.ciirus.com/">
      <APIUserName>' . $this->apiUsername . '</APIUserName>
      <APIPassword>' . $this->apiPassword . '</APIPassword>
      <PropertyID>' . $propertyId . '</PropertyID>
    </GetPoolHeatSettings>
  </soap12:Body>
</soap12:Envelope>';

            $results = $this->curl('http://xml.ciirus.com/XMLAdditionalFunctions1.asmx', $data);

            print "<xmp>" . $results . "</xmp>";
//            die;

            preg_match('/<GetPoolHeatSettingsResult>(.*)<\/GetPoolHeatSettingsResult>/', $results, $extra);
            $extra = $this->xml_to_array($extra[0]);

            if ($extra['HasPrivatePool'] && $extra['PoolHeatable'])
                return $extra;
            else
                return false;
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