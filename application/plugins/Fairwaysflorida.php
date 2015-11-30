<?php

/*
 * @author - ankit
 * @date - 20 September 2015
 * Version - 1
 */

class Fairwaysflorida {

    private $results;

    public function __construct($url) {
        if (empty($url))
            exit("Please input url of the website!!");

        $this->url = $url;
    }

    public function getWebsite($property_id, $url = "") {
        if (empty($url))
            $url = $this->url;
        $result = $this->curl($url . $property_id);
        $this->results = $result;

        return $result;
    }

    function getHeading($propertyId) {
        $results = $this->results;
        if (empty($this->results)) {
            $results = $this->getWebsite($propertyId);
        }
        $heading = $this->scrape_between($results, "<h1>", "</h1>");
        return $heading;
    }

    function getDescription($propertyId) {
        $results = $this->results;
        if (empty($this->results)) {
            $results = $this->getWebsite($propertyId);
        }
        $description = $this->scrape_between($results, "<p id=\"longDescription\">", "</p>");
        return $description;
    }

    function getFeatures($propertyId) {
        $results = $this->results;
        if (empty($this->results)) {
            $results = $this->getWebsite($propertyId);
        }
        $featureArr = array();

//        $pool = $this->scrape_between($results, " | POOL: <span class=\"subTitle\">", "</span>");
        //============ Amenities ====================
        $amenities = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Standard Amenities\r
        </td>
        <td>\r
                ", "</td>");
        $amenities = explode(",",trim($amenities));
        
        //============ kitchen facility ===============
        $kitchen = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Kitchen\r
        </td>\r
        <td>\r
                ", "</td>");
        $kitchen = explode(",",trim($kitchen));
        
        //============ living facility =================
        $living = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Living\r
        </td>\r
        <td>\r
                ", "</td>");
        $living = explode(",",trim($living));
        
        //============ convenience facility ============
        $convenience = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Convenience\r
        </td>\r
        <td>\r
                ", "</td>");
        $convenience = explode(",",trim($convenience));
        
        //============ outdoor amenitites ==============
        $outdoor = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Outdoor\r
        </td>\r
        <td>\r
                ", "</td>");
        $outdoor = explode(",",trim($outdoor));
        
        //=========== Geographic =======================
        $geographic = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Geographic\r
        </td>\r
        <td>\r
                ", "</td>");
        $geographic = explode(",",trim($geographic));
        
        //=========== Entertainment =====================
        $entertainment = $this->scrape_between($results, "<td class=\"amenity-grouping\">\r
            Entertainment\r
        </td>\r
        <td>\r
                ", "</td>");
        $entertainment = explode(",",trim($entertainment));
        
        
        $amenities = array_merge($amenities, $kitchen, $living, $convenience, $outdoor, $geographic, $entertainment);
        
        if (is_array($amenities) && !empty($amenities))
            return implode(",", $amenities);

        return false;
    }
    
    //==================== get booked dates ==========================
    function getBookedDates($propertyId){
        $availabilityUrl = 'http://www.fairwaysflorida.com/Unit/Availability/'.$propertyId.'?startDate='.  urlencode(date('m/d/Y')).'&endDate='.  urlencode(date('m/d/Y',strtotime('+2 year ')));
        $data = file_get_contents($availabilityUrl);
        $data = json_decode($data, true);
        $checkoutDate = false;
        $checkinDate = false;
        $nonAvailArr = array();
        foreach($data as $key=>$value){
            $currDate = date('Y-m-d',strtotime("+$key day"));
            $status = $value['S'];
            if( ($status == 'I' || $status == 'U' || $status == 'O') && $checkoutDate == false ){
                $checkinDate = $currDate;
                $checkoutDate = $currDate;
            }
            if(($status == 'A' || $status == 'I' ) && $checkoutDate != false && $checkinDate != false){
                $checkoutDate = date('Y-m-d',strtotime("+".($key-1)." day"));
                $nonAvailArr[] = array('checkin'=>$checkinDate,'checkout'=>$checkoutDate);
                $checkinDate = $checkoutDate = false;
                
            }
        }
        return $nonAvailArr;
//        http://www.fairwaysflorida.com/Unit/Availability/73069?startDate=11%2F25%2F2015&endDate=11%2F25%2F2017
//        http://www.fairwaysflorida.com/Unit/Availability/73069?startDate=26%2F11%2F2015&endDate=26%2F11%2F2017
        
    }

    //==================== function for downloading images =====================
    function getImageList($propertyId) {
        $results = $this->results;
        if (empty($this->results)) {
            $results = $this->getWebsite($propertyId);
        }
        //$imagesArr = array();
        $images = $this->scrape_between($results, "<div class=\"carousel-inner\" role=\"listbox\">", "</div>\r
                \r
</div>");
        $imagesArr = $this->scrape_between_all($images, "<div class=\"item\">\r
                      ", "<h4 id=\"title\"> </h4>");
        $imagesArr[] = $this->scrape_between($images, "<div class=\"item active\">", "<h4 id=\"title\"></h4>\r
                    </div>");
        
        foreach ($imagesArr as $Key => $val) {
            $imagesArr[$Key] = $this->scrape_between($val, "src=\"", "\"");
        }

        return $imagesArr;
    }

    //==================== Defining the basic scraping function ================
    private function scrape_between($data, $start, $end) {

        $data = stristr($data, $start); // Stripping all data from before $start
        $data = substr($data, strlen($start));  // Stripping $start
        $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
        $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
        return $data;   // Returning the scraped data from the function
    }

    private function scrape_between_all($data, $start, $end) {
        $data = stristr($data, $start);
        $data = substr($data, strlen($start));
        $stop = strripos($data, $end); //stop is last occurrence of the $end of the data
        $stopi = stripos($data, $end); //stopi is the first occurrence of data

        $scrape = array();

        while ($stop != $stopi || $stopi) {
            $datai = substr($data, 0, $stopi);
            $scrape[] = $datai;
            $data = substr($data, strlen($datai));
            $data = stristr($data, $start);
            $data = substr($data, strlen($start));
            $stop = strripos($data, $end);
            $stopi = stripos($data, $end);
        }
        return $scrape;
    }

    private function curl($url) {
        $ch = curl_init();  // Initialising cURL
        curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
        $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
        curl_close($ch);    // Closing cURL
        return $data;   // Returning the data from the function
    }

}