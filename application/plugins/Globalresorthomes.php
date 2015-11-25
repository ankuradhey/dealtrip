<?php

    /*
     * Author: Ankit
     * Date: 10th March 2014
     * Version : 1
     */

    class Globalresorthomes
    {

        private $results;

        public function __construct($url)
        {
            if (empty($url))
                exit("Please input url of the website!!");

            $this->url = $url;
        }

        public function getWebsite($property_id, $url = "")
        {
            if (empty($url))
                $url = $this->url;

            $result = $this->curl($url . $property_id);
            $this->results = $result;

            return $result;
        }

        function getHeading($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }

            $heading = $this->scrape_between($results, "<h1 class=\"titleText\">", "</h1>");


            return $heading;
        }

        function getDescription($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }

            $description = $this->scrape_between($results, "<div class=\"fullBump\" id=\"infoText\">", "</div>");


            return $description;
        }

        function getFeatures($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }
            $featureArr = array();

            $pool = $this->scrape_between($results, " | POOL: <span class=\"subTitle\">", "</span>");
            $spa = $this->scrape_between($results, " | SPA: <span class=\"subTitle\">", "</span>");
            $gameroom = $this->scrape_between($results, " | GAMEROOM: <span class=\"subTitle\">", "</span>");

            if ($pool == 'YES')
                $featureArr[] = 'pool';
            if ($spa == 'YES')
                $featureArr[] = 'spa';
            if ($gameroom == 'YES')
                $featureArr[] = 'gameroom';

            if (is_array($featureArr) && !empty($featureArr))
                return implode(",", $featureArr);

            return false;
        }

        //====================function for getting rates=================
        // *-Note- please provide the url in case the rate is coming from different url like iframe etc.
        function getRates($propertyId, $url = false)
        {
            //$results = $this->results;

            $results = $this->getWebsite($propertyId, $url);


            $rates = $this->scrape_between($results, "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">", "</table>");
            //$rates = htmlentities($rates);
            //preg_replace('\n\n','||',$rates);
            $rates = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">" . $rates . "</table>";
            return $rates;
        }

        //================== get booked dates ============================
        function getReservations($propertyId, $url = false)
        {

            $results = $this->getWebsite($propertyId, $url);

            //prd($results);
            //$results = $this->scrape_between($results, "<table width=\"920\" border=\"0\" cellspacing=\"0\" cellpadding=\"4\">", "</table>");
            // :- Note -: 
            // .highlight1 - class for booked dates
            // .highlight3 - class for checkin date
            // .calcell - class for available dates
            // .highlight2 - class for checkout date

            $class = array("Highlight1" => "booked", "Highlight3" => "booked", "Highlight2" => "available", "Highlight4"=>"booked");

            $results = $this->scrape_between($results, "<script type=\"text/javascript\">", "</div>");
            preg_match_all("|YAHOO.example.calendar.cal1.addRenderer\(\"(.*)\", YAHOO.example.calendar.cal1.renderCellStyle(.*)\);|", $results, $matches);
            //preg_match_all("|YAHOO.example.calendar.cal1.addRenderer\((.*)|", $results, $matches);

            $bookArr = array();
            foreach ($matches[1] as $key => $dates)
            {
                if ($matches[2][$key] == 'Highlight1' || $matches[2][$key] == 'Highlight3' || $matches[2][$key] == 'Highlight4')
                    $bookArr[] = $dates;
            }

            return $bookArr;
        }

        //==================== function for downloading images =====================
        function getImageList($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }
            //$imagesArr = array();

            $images = $this->scrape_between($results, "<ul id=\"carousel\" class=\"elastislide-list\">", "</ul>");
            $imagesArr = $this->scrape_between_all($images, "<a href=\"javascript:void(0);\">", "</a>");
            foreach($imagesArr as $Key=>$val){
                $imagesArr[$Key] = $this->scrape_between($val, "id=\"", "\"");
            }
            return $imagesArr;
        }

        //==================== Defining the basic scraping function ================
        private function scrape_between($data, $start, $end)
        {

            $data = stristr($data, $start); // Stripping all data from before $start
            $data = substr($data, strlen($start));  // Stripping $start
            $stop = stripos($data, $end);   // Getting the position of the $end of the data to scrape
            $data = substr($data, 0, $stop);    // Stripping all data from after and including the $end of the data to scrape
            return $data;   // Returning the scraped data from the function
        }

        private function scrape_between_all($data, $start, $end)
        {
            $data = stristr($data, $start);
            $data = substr($data, strlen($start));
            $stop = strripos($data, $end); //stop is last occurrence of the $end of the data
            $stopi = stripos($data, $end); //stopi is the first occurrence of data
            
            $scrape = array();
            
            while($stop != $stopi || $stopi){
                $datai = substr($data,0,$stopi);
                $scrape[] = $datai;
                $data = substr($data,strlen($datai));
                $data = stristr($data, $start);
                $data = substr($data, strlen($start));
                $stop = strripos($data, $end);
                $stopi = stripos($data, $end);
            }
            return $scrape;
        }

        private function curl($url)
        {
            $ch = curl_init();  // Initialising cURL
            curl_setopt($ch, CURLOPT_URL, $url);    // Setting cURL's URL option with the $url variable passed into the function
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Setting cURL's option to return the webpage data
            $data = curl_exec($ch); // Executing the cURL request and assigning the returned data to the $data variable
            curl_close($ch);    // Closing cURL
            return $data;   // Returning the data from the function
        }

    }
?>
