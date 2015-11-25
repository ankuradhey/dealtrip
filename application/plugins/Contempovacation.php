<?php

    /*
     * Author: Ankit
     * Date: 10th March 2014
     * Version : 1
     */

    class Contempovacation
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
            $heading = $this->scrape_between($results, "<h1  id=\"h1\"  class=\"seotext seo-text-color\" >", "</h1 >");
            return $heading;
        }

        function getDescription($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }

            $description = $this->scrape_between($results, "<table ><tr ><td >", "</td >");


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

            $essentials = $this->scrape_between($results, "<li  class=\"title\" >Essentials</li >", "</td >");
            $essentials = str_replace(array("							","
", "<li >","</li >","			    		"),array("","","",","),$essentials);
            $features = $this->scrape_between($results, "<li  class=\"title\" >Amenities</li >", "</td >");
            $features = str_replace(array("			    			","<li>","</li>"),array("","",","),$features);
            return $essentials.$features;
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
            // .date-cell-booked-orig - class for booked dates
            // .date-cell-available-orig - class for available dates
            // .date-cell-unavailable-orig - class for unavailable dates

            $class = array("date-cell-booked-orig" => "booked", "date-cell-unavailable-orig" => "booked", "date-cell-available-orig" => "available");

//            $results = $this->scrape_between_all($results, "<table cellpadding=\"0\" cellspacing=\"0\" class=\"property-month-calendar\">", "</table>");
            $results = $this->scrape_between_all($results, "<td class=\"property-calendar-month\">", "</table>");
            $bookArr = array();
            $setArr = array();
            //prd($results);
            foreach ($results as $resKey => $resVal)
            {
                $date = $this->scrape_between($resVal, "<td colspan=\"7\" align=\"center\" class=\"month-heading-orig\">", "</td>");
                $date = explode(",", $date);
                $date[1] = trim($date[1]);

//                if ($date[0] != "October")
//                    continue;

                $fullDate = date('d/m/Y', strtotime("1 " . $date[0] . " " . $date[1]));

                $unavailable = $this->scrape_between_all($resVal, "<td align=\"center\" class=\"date-cell-unavailable-orig\">", "</td>");
                $booked = $this->scrape_between_all($resVal, "<td align=\"center\" class=\"date-cell-booked-orig\">", "</td>");
                //process all unavailable dates
                $start = $end = $booked[0];
                foreach ($booked as $bookKey => $bookVal)
                {
                    //checking end value
                    if ($bookVal != $end + 1 && !empty($bookVal) && $end != $booked[0])
                    {
                        $setArr[] = $start . "/" . date("m", strtotime($date[0])) . "/" . $date[1] . "|||" . $end . "/" . date("m", strtotime($date[0])) . "/" . $date[1];
                        $start = $bookVal;
                    }

                    $end = $bookVal;

                    //checking if array is ended
                    if (count($booked) == $bookKey + 1 && !empty($start))
                        $setArr[] = $start . "/" . date("m", strtotime($date[0])) . "/" . $date[1] . "|||" . $end . "/" . date("m", strtotime($date[0])) . "/" . $date[1];
                }

                //process all booked dates
                //trim
                $_count = 0;
                $unavailables = array();
                foreach ($unavailable as $unavailKey => $unavailVal)
                {
                    if ($unavailVal == "&nbsp;")
                        unset($unavailable[$unavailKey]);
                    else
                    {
                        $unavailables[$_count++] = $unavailVal;
                    }
                }
                $unavailable = $unavailables;
                $start = $end = $unavailable[0];
                foreach ($unavailable as $unavailKey => $unavailVal)
                {
                    //checking end value
                    if ($unavailVal != $end + 1 && !empty($unavailVal) && $end != $unavailable[0])
                    {
                        $setArr[] = $start . "/" . date("m", strtotime($date[0])) . "/" . $date[1] . "|||" . $end . "/" . date("m", strtotime($date[0])) . "/" . $date[1];
                        $start = $unavailVal;
                    }

                    $end = $unavailVal;

                    //checking if array is ended
                    if (count($unavailable) == $unavailKey + 1 && !empty($start))
                    {
                        $setArr[] = $start . "/" . date("m", strtotime($date[0])) . "/" . $date[1] . "|||" . $end . "/" . date("m", strtotime($date[0])) . "/" . $date[1];
                    }
                }
                //$_start = 
                
            }
//            die;

            return $setArr;
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

            $images = $this->scrape_between($results, "<div  id=\"propImgContainer\"  class=\"propertyInfoDisplayImgBox\" >", "</div >");
            //prd($images);
            $imagesArr = $this->scrape_between_all($images, "src=\"", "\"");
            unset($imagesArr[0]);
            return $imagesArr;
        }
        public function getContents($data , $start, $end)
        {
            return $this->scrape_between($data, $start, $end);
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

            while ($stop != $stopi || $stopi)
            {
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