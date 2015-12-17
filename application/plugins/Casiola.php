<?php

   class Casiola
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

            $heading = $this->scrape_between_all($results, "<h1>", "</h1>");
			
			$heading_full = $heading[1];
			
			$heading_explode = explode("<",$heading_full);
			
			$heading = $heading_explode[0];
			
            return $heading;
        }

        function getDescription($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }

            $description = $this->scrape_between($results, "<p>", "</p>");
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

            $essentials = $this->scrape_between($results, "<ul class=\"amenities-list\">", "</ul>");
			
			$remove = array("<li>", "</li>");
			$replace   = array("",",");

			$essentials = str_replace($remove,$replace,$essentials);
			
			return $essentials;
            return false;
        }

        //====================function for getting rates=================
        // *-Note- please provide the url in case the rate is coming from different url like iframe etc.
        function getRates($propertyId, $url = false)
        {
            //$results = $this->results;

            $results = $this->getWebsite($propertyId, $url);


            $rates = $this->scrape_between($results, "<table class=\"table table-bordered table-striped brates\">", "</table>");
            //$rates = htmlentities($rates);
            //preg_replace('\n\n','||',$rates);
            $rates = "<table class=\"table table-bordered table-striped brates\">" . $rates . "</table>";
            return $rates;
        }

        //================== get booked dates ============================
        function getReservations($propertyId, $url = false)
        {

            $results = $this->getWebsite($propertyId, $url);

			 
            $class = array("splitViewCheckin" => "booked", "booked" => "booked", "splitViewCheckout" => "available");

            $results = $this->scrape_between_all($results, "<script type=\"text/javascript\">", "</script>");
			$result_last = end($results);
			
			$matches = explode('counter = counter + 1;',$result_last);
			
			
			foreach($matches as $matches_date)
			{
				$matches_s[] = explode('myBadDates[counter] = ',$matches_date);
			}
			foreach($matches_s as $get_date)
			{
				$dates[] = $get_date[1];
			}
			array_pop($dates);
		
			$remove = array("'", ";");
			$replace   = array("","");

			$essentials = str_replace($remove,$replace,$dates);
			
			$bookArr = array();
            
			foreach($essentials as $ess){
				$pre_date = date('Y-m-d', strtotime('-1 day', strtotime($ess)));
				$date=date_create("$ess");
				$prev_date=date_create("$pre_date");
				
				$bookArr[] = date_format($prev_date,"m/d/Y");
				$bookArr[] = date_format($date,"m/d/Y");
			}
			$bookArr = array_unique($bookArr);
			
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
				
            //$images = $this->scrape_between($results, "<div class=\"galleria-thumbnails-list\">", "</div>");
            $imagesArr = $this->scrape_between($results, "<div class=\"galleria\">", "</div>");
			
			
			$essentials = str_replace("> ",">,",$imagesArr);
			
			
			$imagesArr = explode(',',$essentials);
			
			
			foreach($imagesArr as $Key=>$val){
				//echo $val;
				$image_src = $this->scrape_between($val, "src=\"", "\"");
				$image_src_array = explode('/',$image_src);
				$imagesArr[$Key] = end($image_src_array);
            }
			array_pop($imagesArr);
			
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
