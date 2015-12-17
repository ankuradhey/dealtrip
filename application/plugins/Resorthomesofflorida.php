<?php

   class Resorthomesofflorida
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

            $heading = $this->scrape_between($results, "<h2>", "</h2>");
			
			return $heading;
        }

        function getDescription($propertyId)
        {
            $results = $this->results;
            if (empty($this->results))
            {
                $results = $this->getWebsite($propertyId);
            }

            $description = $this->scrape_between($results, "<div class=\"contentFull\">", "</div>");
			
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

            $essentials = $this->scrape_between_all($results, "<li>", "</li>");
            $Villa_id = $this->scrape_between($results, "<p style=\"font-size:15px;line-height:22px;-webkit-text-size-adjust: none;\">", "</p>");
			foreach($essentials as $essent)
			{
				$essenti[] = $this->scrape_between_all($essent, "<p>", "</p>");
			}
			$linksArray = array_filter($essenti);
			
			foreach($linksArray as $arrayvalue){
				 $array .= $arrayvalue[0].$arrayvalue[1].',';
			}
			
			$essentials = rtrim($array,',');
			
			$essentials = $Villa_id.','.$essentials;
			
			return $essentials;
            return false;
        }

        //====================function for getting rates=================
        // *-Note- please provide the url in case the rate is coming from different url like iframe etc.
        function getRates($propertyId, $url = false)
        {
            //$results = $this->results;

            $results = $this->getWebsite($propertyId, $url);

	
            $rates = $this->scrape_between($results, "<div class=\"terrifChart\">", "</div>");
			//$rates = $this->scrape_between($results, "<table class=\"table table-bordered table-striped brates\">", "</table>");
            //$rates = htmlentities($rates);
            //preg_replace('\n\n','||',$rates);
			
			$rates = "<table class=\"table table-bordered table-striped brates\">" . $rates . "</table>";
            return $rates;
        }

        //================== get booked dates ============================
        function getReservations($propertyId, $url = false)
        {

             $results = $this->getWebsite($propertyId, $url);
		
			$iframes = $this->scrape_between_all($results,"<iframe","</iframe>");
			$resultss = $this->scrape_between($iframes[1], "src=\"", "\"");
			$resultss1 = $resultss.'&n=1';
			
			$calender = $this->getWebsite($propertyId, $resultss);
			$calender_data = $this->scrape_between_all($calender,"<table class=\"calendar\" >","</table>");
			$calender_month = $this->scrape_between_all($calender,"<th colspan=\"7\" class=\"current-month\">","</th>");
			$calender_data1 = array_combine($calender_month,$calender_data);
			foreach($calender_data1 as $key=>$calenders){
				
				$calender_data = $this->scrape_between_all($calenders,"<td class=\"day booked\">","</td>");
				$calender_check_in = $this->scrape_between_all($calenders,"<td class=\"day checkin\">","</td>");
				$result[$key] = array_merge($calender_data, $calender_check_in);
			}
			$bookArr1 = array();
			foreach($result as $val=>$result_val)
			{
				$year_month = $val;
				$year_and_month = explode(' ',$year_month);
				$month = $year_and_month[0];
				$year = $year_and_month[1]; 
				foreach($result_val as $date)
				{
					$result = substr($date, 0, 10);
					$str=preg_replace('/\s+/', '', $result);
					$correct_date = str_replace('<', '', $str);
					$full_date = $month.' '.$correct_date.' '.$year;
					$bookArr1[] = date('m/d/Y', strtotime($full_date));
				}
				
			}
			
			$calender1 = $this->getWebsite($propertyId, $resultss1);
			$calender_data1 = $this->scrape_between_all($calender1,"<table class=\"calendar\" >","</table>");
			$calender_month1 = $this->scrape_between_all($calender1,"<th colspan=\"7\" class=\"current-month\">","</th>");
			$calender_data2 = array_combine($calender_month1,$calender_data1);
			foreach($calender_data2 as $key1=>$calenders1){
				
				$calender_data1 = $this->scrape_between_all($calenders1,"<td class=\"day booked\">","</td>");
				$calender_check_in1 = $this->scrape_between_all($calenders1,"<td class=\"day checkin\">","</td>");
				$result1[$key1] = array_merge($calender_data1, $calender_check_in1);
			}
			$bookArr2 = array();
			foreach($result1 as $val1=>$result_val1)
			{
				$year_month1 = $val1;
				$year_and_month1 = explode(' ',$year_month1);
				$month1 = $year_and_month1[0];
				$year1 = $year_and_month1[1]; 
				foreach($result_val1 as $date1)
				{
					$result1 = substr($date1, 0, 10);
					$str1=preg_replace('/\s+/', '', $result1);
					$correct_date1 = str_replace('<', '', $str1);
					$full_date1 = $month1.' '.$correct_date1.' '.$year1;
					$bookArr2[] = date('m/d/Y', strtotime($full_date1));
				}
				
			}
			
			$bookArr = array_merge($bookArr1,$bookArr2);
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
            $imagesArr = $this->scrape_between($results, "<ul class=\"more_photos\">", "</ul>");
			$image_src = $this->scrape_between_all($imagesArr, "href=\"", "\"");
			foreach($image_src as $image_ex)
			{
				$imagesArrs = '';
				$src = explode('/',$image_ex);
				//$final_url[] = end($src);
				 $res = array_slice($src, -3, 3, true);
				foreach($res as $last_url)
				{
					$imagesArrs .= $last_url.'/';
				}
				$imagesA = rtrim($imagesArrs,'/');
				$final_url[] = $imagesA; 
			}
			$imagesArr = $final_url;
			
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
