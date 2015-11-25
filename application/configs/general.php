<?php

    function in_array_custom($needle, $haystack, $field)
    {

        foreach ($haystack as $key => $val)
        {

            if ($val[$field] == $needle)
                return $key;
        }

        return false;
    }

    function multi_array_key_exists($needle, $haystack)
    {

        foreach ($haystack as $key => $value) :

            if ($needle == $key)
                return $key;

            if (is_array($value)) :
                if (multi_array_key_exists($needle, $value) == true)
                    return true;
                else
                    continue;
            endif;

        endforeach;

        return false;
    }

    function CheckAndFilter($data)
    {
        $data = strip_tags($data);
        $data = str_replace("\n", "", $data);
        $data = str_replace("<br>", "", $data);
        $data = stripslashes($data);
        $data = addslashes($data);
        return get_magic_quotes_gpc() ? stripslashes($data) : $data;
    }

    function strip_magic_slashes($str)
    {
        return get_magic_quotes_gpc() ? stripslashes($str) : $str;
    }

    function degrees_difference($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);

        $distance = $dist * 60 * 1.1515;

        return $distance;
    }

    function difference_between($firstzip, $secondzip)
    {
        global $mySession;
        $db = new Db();
        $query = "select zip5, lat, lon from zipgeo where zip5 in ({$firstzip}, {$secondzip})";

//    $result = mysql_query($query) or die(mysql_error());	
        //   $firstzips = mysql_fetch_array($result);
        //  $secondzips = mysql_fetch_array($result);
        $firstzips = $db->runQuery($query);
        $secondzips = $db->runQuery($query);
        return degrees_difference($firstzips[0]['lat'], $firstzips[0]['lon'], $secondzips[0]['lat'], $secondzips[0]['lon']);
    }

    function get_zips_within($zip, $miles)
    {//echo $zip ;exit();
        global $mySession;
        $db = new Db();
        $milesperdegree = 69;
        $degreesdiff = $miles / $milesperdegree;
        $query = "select lat, lon from zipgeo where zip5={$zip}";
        $latlong = $db->runQuery($query);
        $lat1 = $latlong[0]['lat'] - $degreesdiff;
        $lat2 = $latlong[0]['lat'] + $degreesdiff;
        $lon1 = $latlong[0]['lon'] - $degreesdiff;
        $lon2 = $latlong[0]['lon'] + $degreesdiff;
        $query = "select group_concat(zip5) as matchedzipCode from zipgeo where lat between {$lat1} and {$lat2} and lon between {$lon1} and {$lon2}";
        $result = $db->runQuery($query);
        return $result;
    }

    function getDefaultUserImage($Gender = "", $UserId = "")
    {
        if ($UserId != "" && $Gender == "")
        {
            global $mySession;
            $db = new Db();
            $DataUser = $db->runQuery("select * from " . USERS . " WHERE user_id='" . $UserId . "'");
            $Gender = $DataUser[0]['sex'];
        }
        if ($Gender == '1')
        {
            return DEFAULT_MALE_USER_IMAGE;
        }
        else if ($Gender == '2')
        {
            return DEFAULT_FEMALE_USER_IMAGE;
        }
        else
        {
            return DEFAULT_USER_IMAGE;
        }
    }

    function get_distance($lat1, $long1, $lat2, $long2)
    {

//$qry = "SELECT *,(((acos(sin((".$latitude."*pi()/180)) * sin((`Latitude`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`Latitude`*pi()/180)) * cos(((".$longitude."- `Longitude`)*pi()/180))))*180/pi())*60*1.1515) as distance FROM `MyTable` WHERE distance >= ".$distance;

        $qry = "SELECT (((acos(sin((" . $lat1 . "*pi()/180)) * sin(($lat2*pi()/180))+cos((" . $lat1 . "*pi()/180)) * cos(($lat2*pi()/180)) * cos(((" . $long1 . "- $long2)*pi()/180))))*180/pi())*60*1.1515) as distance";
        echo $qry;
        exit();
        $result = $db->runQuery($qry);
        return $result;
    }

    function get_url_contents($url)
    {
        $crl = curl_init();
        $timeout = 5;
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
    }

    function getLatLongFromAddress($country, $state, $city, $address)
    {
        global $mySession;
        $db = new Db();
        $CountryData = $db->runQuery("select * from " . COUNTRIES . " where country_id='" . $country . "'");
        $StateData = $db->runQuery("select * from " . STATE . " where state_id='" . $state . "'");
        $queryString = urlencode($address . ", " . $city . ", " . $StateData[0]['state_name'] . ", " . $CountryData[0]['country_name']);
        $geocode = get_url_contents('http://maps.google.com/maps/api/geocode/json?address=' . $queryString . '&sensor=false');
        $output = json_decode($geocode);
        $lat = $output->results[0]->geometry->location->lat;
        $long = $output->results[0]->geometry->location->lng;
        return $lat . "::" . $long;
    }

    function CheckCanCreateListing()
    {
        global $mySession;
        $db = new Db();
        $businessListingsStatus = 0; // No subscription
        if ($mySession->LoggedUserType != "" && $mySession->LoggedUserId != "")
        {
            $chkPlan = $db->runQuery("select * from " . USER_SUBSCRIPTIONS . " 
		join " . SUBSCRIPTIONS . " on(" . USER_SUBSCRIPTIONS . ".plan_id=" . SUBSCRIPTIONS . ".plan_id)
		where user_id='" . $mySession->LoggedUserId . "' and is_active='1'");
            if ($chkPlan != "" and count($chkPlan) > 0)
            {
                $ListingCount = $chkPlan[0]['nof_listing'];
                $countListingCreated = 0;
                $chkUserListing = $db->runQuery("select count(business_id) as totListing from " . SERVICE_BUSINESS . " where user_id='" . $mySession->LoggedUserId . "'");
                if ($chkUserListing != "" and count($chkUserListing) > 0)
                {
                    $countListingCreated = $chkUserListing[0]['totListing'];
                }
                if ($ListingCount - $countListingCreated <= 0 && $ListingCount > 0)
                {
                    $businessListingsStatus = 1; //nof listing limti over in plan subscribe
                }
                else
                {
                    $businessListingsStatus = 2; //can create listing
                }
            }
        }
        return $businessListingsStatus;
    }

    function SetupMagicQuotes($dataArray)
    {
        //echo get_magic_quotes_gpc();
        $NewArr = array();
        if (count($dataArray) > 0)
        {
            foreach ($dataArray as $key => $valueArr)
            {
                $keyData = str_replace("'", "''", $valueArr);
                /* if (get_magic_quotes_gpc())
                  {
                  $keyData=stripslashes($keyData);
                  } */
                $NewArr[$key] = $keyData;
            }
            return $NewArr;
        }
        else
        {
            return $dataArray;
        }
    }

    function SetupMagicQuotesTrim($dataArray)
    {
        //echo get_magic_quotes_gpc();
        $NewArr = array();
        if (count($dataArray) > 0)
        {
            foreach ($dataArray as $key => $valueArr)
            {
                $keyData = str_replace("'", "''", $valueArr);
                /* if (get_magic_quotes_gpc())
                  {
                  $keyData=stripslashes($keyData);
                  } */
                $NewArr[$key] = trim($keyData);
            }
            return $NewArr;
        }
        else
        {
            return $dataArray;
        }
    }

    function changeDate($date, $IsYmd)
    {
        if ($IsYmd == '1')
        {
            //YYYY-MM-DD
            if ($date == '0000-00-00')
            {
                return "";
            }
            else
            {
                $mydate = explode("-", $date);
                return trim($mydate[1]) . "-" . trim($mydate[2]) . "-" . trim($mydate[0]);
            }
        }
        else
        {
            //MM-DD-YYYY
            if ($date == '00-00-0000')
            {
                return "";
            }
            else
            {
                $mydate = explode("-", $date);
                return trim($mydate[2]) . "-" . trim($mydate[0]) . "-" . trim($mydate[1]);
            }
        }
    }

    function SendEmail($to, $subject, $message_body, $from = ADMIN_EMAIL)
    {
//	 echo $to."<br>".$from."<br>".$message_body."<br>".$subject;exit();
        global $mySession;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//$headers .= 'To: '.$to.'' . "\r\n";
        $headers .= 'From: ' . SITE_NAME . ' <' . $from . '>' . "\r\n";
        $mailMessage = '<div style="font-family:Verdana, Geneva, sans-serif;font-size:11px;color:#1F1F1F;">' . $message_body . '</div>';
        $retvalue = mail($to, $subject, $mailMessage, $headers);
        return $retvalue;
    }

    function xmlstr_to_array($xmlstr)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        return domnode_to_array($doc->documentElement);
    }

    function domnode_to_array($node)
    {
        $output = array();
        switch ($node->nodeType)
        {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++)
                {
                    $child = $node->childNodes->item($i);
                    $v = domnode_to_array($child);
                    if (isset($child->tagName))
                    {
                        $t = $child->tagName;
                        if (!isset($output[$t]))
                        {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    }
                    elseif ($v)
                    {
                        $output = (string) $v;
                    }
                }
                if (is_array($output))
                {
                    if ($node->attributes->length)
                    {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode)
                        {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v)
                    {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes')
                        {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }

    function makeAWSUrl($parameters, $associate_tag = AMAZON_ASSOCIATE_TAG, $access_key = AMAZON_ACCESS_KEY, $secret_key = AMAZON_SCERET_KEY, $aws_version = '2009-06-01')
    {


        //print_r($parameters);
        //echo "<br>".$associate_tag."<br>".$access_key."<br>".$secret_key;
        //exit();


        $host = 'ecs.amazonaws.com';

        $path = '/onca/xml';



        $query = array(
            'Service' => 'AWSECommerceService',
            'AWSAccessKeyId' => $access_key,
            'AssociateTag' => $associate_tag,
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Version' => $aws_version,
        );



        // Merge in any options that were passed in

        if (is_array($parameters))
        {

            $query = array_merge($query, $parameters);
        }



        // Do a case-insensitive, natural order sort on the array keys.

        ksort($query);



        // create the signable string

        $temp = array();



        foreach ($query as $k => $v)
        {

            $temp[] = str_replace('%7E', '~', rawurlencode($k)) . '=' . str_replace('%7E', '~', rawurlencode($v));
        }



        $signable = implode('&', $temp);



        $stringToSign = "GET\n$host\n$path\n$signable";



        // Hash the AWS secret key and generate a signature for the request.



        $hex_str = hash_hmac('sha256', $stringToSign, $secret_key);



        $raw = '';



        for ($i = 0; $i < strlen($hex_str); $i += 2)
        {

            $raw .= chr(hexdec(substr($hex_str, $i, 2)));
        }



        $query['Signature'] = base64_encode($raw);

        ksort($query);



        $temp = array();



        foreach ($query as $k => $v)
        {

            $temp[] = rawurlencode($k) . '=' . rawurlencode($v);
        }



        $final = implode('&', $temp);



        return 'http://' . $host . $path . '?' . $final;
    }

    function getDataFromYelpbyBusiness($name, $city)
    {
        $myUrl = trim($name) . "-" . trim($city);
        $myUrl = str_replace(" ", "-", $myUrl);
        $unsigned_url = "http://api.yelp.com/v2/business/" . strtolower($myUrl);
        $response = getResponseFromYelp($unsigned_url);
        return $response;
    }

    function getReviewfromCitySearch($name, $address, $lat, $long)
    {
        $url = "http://api.citygridmedia.com/content/reviews/v2/search/where?where=" . urlencode($address) . "&what=" . urlencode($name) . "&publisher=" . PUBLISHER_CODE;
//$url="http://api.citygridmedia.com/content/reviews/v2/search/latlon?lat=".$lat."&lon=".$long."&radius=10&what=".urlencode($name)."&publisher=10000002697";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        curl_close($ch);
        return xmlstr_to_array($xml);
    }

    function getReviewfromYahooLocal($name, $lat, $long)
    {
        //$url="http://local.yahooapis.com/LocalSearchService/V3/localSearch?appid=".YAHOO_APP_ID."&query=".urlencode('Oreganos Wood Fired Pizza')."&latitude=37.401704&longitude=-122.114907";
        $url = "http://local.yahooapis.com/LocalSearchService/V3/localSearch?appid=" . YAHOO_APP_ID . "&query=" . urlencode($name) . "&latitude=" . $lat . "&longitude=" . $long;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml = curl_exec($ch);
        curl_close($ch);
        return xmlstr_to_array($xml);
    }

// We can create our custom functions here
    function getResponseFromYelp($unsigned_url)
    {
        require_once (SITE_ROOT . 'OAuth.php');
        $consumer_key = CONSUMER_KEY;
        $consumer_secret = CONSUMER_SECRET;
        $token = CONSUMER_TOKEN;
        $token_secret = TOKEN_SECRET;
        $token = new OAuthToken($token, $token_secret);
        $consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
        $oauthrequest->sign_request($signature_method, $consumer, $token);
        $signed_url = $oauthrequest->to_url();
        $ch = curl_init($signed_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($data);
        return $response;
    }

    function ResizeAndSaveImage($img, $w, $h)
    {

        $split = explode(".", $img);
        $fileExt = $split[count($split) - 1];

        $explode = explode("/", $img);
        $imageBaseName = $explode[count($explode) - 1];
        $newImageName = time() . "." . $fileExt;
        $newfilename = SITE_ROOT . "images/hairstyles/uploadedphotos/" . $newImageName;

        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2'))
        {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        $imgInfo = getimagesize($img);
        switch ($imgInfo[2])
        {
            case 1: $im = imagecreatefromgif($img);
                break;
            case 2: $im = imagecreatefromjpeg($img);
                break;
            case 3: $im = imagecreatefrompng($img);
                break;
            default: trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        //If image dimension is smaller, do not resize
        if ($imgInfo[0] <= $w && $imgInfo[1] <= $h)
        {
            $nHeight = $imgInfo[1];
            $nWidth = $imgInfo[0];
        }
        else
        {
            //yeah, resize it, but keep it proportional
            if ($w / $imgInfo[0] > $h / $imgInfo[1])
            {
                $nWidth = $w;
                $nHeight = $imgInfo[1] * ($w / $imgInfo[0]);
            }
            else
            {
                $nWidth = $imgInfo[0] * ($h / $imgInfo[1]);
                $nHeight = $h;
            }
        }
        $nWidth = round($nWidth);
        $nHeight = round($nHeight);

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent */
        if (($imgInfo[2] == 1) OR ($imgInfo[2] == 3))
        {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

        //Generate the file, and rename it to $newfilename
        switch ($imgInfo[2])
        {
            case 1: imagegif($newImg, $newfilename);
                break;
            case 2: imagejpeg($newImg, $newfilename);
                break;
            case 3: imagepng($newImg, $newfilename);
                break;
            default: trigger_error('Failed resize image!', E_USER_WARNING);
                break;
        }

        return $newfilename;
    }

    function ResizeAndSaveImage2Old($ImagePath, $ImageWidth, $ImageHeight)
    {

        $split = explode(".", $ImagePath);
        $fileExt = $split[count($split) - 1];

        $explode = explode("/", $ImagePath);
        $imageBaseName = $explode[count($explode) - 1];
        $newImageName = time() . "." . $fileExt;
        $newImagePath = SITE_ROOT . "images/hairstyles/uploadedphotos/" . $newImageName;

        list($UimgWidth, $UimgHeight) = getimagesize($ImagePath);

        $ratiow = $UimgWidth / $ImageWidth;
        $ratioh = $UimgHeight / $ImageHeight;
        if ($ratiow > $ratioh)
        {
            $ratioUimage = $ImageWidth / $UimgWidth;
        }
        else
        {
            $ratioUimage = $ImageHeight / $UimgHeight;
        }

        if ($UimgWidth > $ImageWidth || $UimgHeight > $ImageHeight)
        {
            $newWidthuImage = $UimgWidth * $ratioUimage;
            $newHeightuImage = $UimgHeight * $ratioUimage;
        }
        else
        {
            $newWidthuImage = $UimgWidth;
            $newHeightuImage = $UimgHeight;
        }
        if ($fileExt == "jpg" || $fileExt == "jpeg")
        {
            header('Content-type: image/jpeg');
            $image_p = imagecreatetruecolor($newWidthuImage, $newHeightuImage);
            $image = imagecreatefromjpeg($ImagePath);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidthuImage, $newHeightuImage, $UimgWidth, $UimgHeight);
            imagejpeg($image_p, $newImagePath, 100);
            imagedestroy($image_p);
        }
        if ($fileExt == "gif")
        {
            header('Content-type: image/gif');
            $image_p = imagecreatetruecolor($newWidthuImage, $newHeightuImage);
            $image = imagecreatefromgif($ImagePath);
            $bgc = imagecolorallocate($image_p, 255, 255, 255);
            imagefilledrectangle($image_p, 0, 0, $newWidthuImage, $newHeightuImage, $bgc);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidthuImage, $newHeightuImage, $UimgWidth, $UimgHeight);
            imagegif($image_p, $newImagePath, 100);
            imagedestroy($image_p);
        }
        if ($fileExt == "png")
        {
            header('Content-type: image/png');
            $image_p = imagecreatetruecolor($newWidthuImage, $newHeightuImage);
            $background = imagecolorallocate($image_p, 255, 255, 255);
            $image = imagecreatefrompng($ImagePath);
            imagecolortransparent($image_p, $background);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidthuImage, $newHeightuImage, $UimgWidth, $UimgHeight);
            imagepng($image_p, $newImagePath);
            imagedestroy($image_p);
        }
        return $newImagePath;
    }

    function putWaterMark($ImageUser, $ImageHair, $Left, $Top)
    {
        header('Content-type: image/jpeg');
        $imagesource = $ImageUser;

        $filetype = substr($imagesource, strlen($imagesource) - 4, 4);
        $filetype = strtolower($filetype);

        $filetype1 = substr($ImageHair, strlen($ImageHair) - 4, 4);
        $filetype1 = strtolower($filetype1);

        $newImageName = SITE_ROOT . "images/user_images/hairopinion_" . time() . $filetype;

        if ($filetype == ".gif")
            $image = @imagecreatefromgif($imagesource);
        if ($filetype == ".jpg")
            $image = @imagecreatefromjpeg($imagesource);
        if ($filetype == ".png")
            $image = @imagecreatefrompng($imagesource);
        if (!$image)
            die();

        if ($filetype1 == ".gif")
        {
            $watermark = @imagecreatefromgif($ImageHair);
        }
        if ($filetype1 == ".png")
        {
            $watermark = @imagecreatefrompng($ImageHair);
        }

        $imagewidth = imagesx($image);
        $imageheight = imagesy($image);
        $watermarkwidth = imagesx($watermark);
        $watermarkheight = imagesy($watermark);
        //$startwidth = (($imagewidth - $watermarkwidth)/2); 
        //$startheight = (($imageheight - $watermarkheight)/2); 
        imagecopy($image, $watermark, $Left, $Top, 0, 0, $watermarkwidth, $watermarkheight);
        imagejpeg($image, $newImageName);
        imagedestroy($image);
        imagedestroy($watermark);
        return $newImageName;
    }

    function getmyTime($format = true)
    {
        $sign = "+"; // Whichever direction from GMT to your timezone. + or -
        $h = "12"; // offset for time (hours)
        $dst = true; // true - use dst ; false - don't

        if ($dst == true)
        {
            $daylight_saving = date('I');
            if ($daylight_saving)
            {
                if ($sign == "-")
                {
                    $h = $h - 1;
                }
                else
                {
                    $h = $h + 1;
                }
            }
        }
        $hm = $h * 60;
        $ms = $hm * 60;
        if ($sign == "-")
        {
            $timestamp = time() - ($ms);
        }
        else
        {
            $timestamp = time() + ($ms);
        }
        $gmdate = gmdate("m-d-Y g:i A", $timestamp);
        if ($format == true)
        {
            return $gmdate;
        }
        else
        {
            return $timestamp;
        }
    }

    function formatDate($date)
    {
        //$date Y-m-d
        $explode = explode("-", $date);
        return $explode[1] . "-" . $explode[2] . "-" . $explode[0];
    }

    function array2json($arr)
    {
        if (function_exists('json_encode'))
            return json_encode($arr); //Lastest versions of PHP already has this functionality.
        $parts = array();
        $is_list = false;

        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] == 0) and ($keys[$max_length] == $max_length))
        {//See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++)
            { //See if each key correspondes to its position
                if ($i != $keys[$i])
                { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }

        foreach ($arr as $key => $value)
        {
            if (is_array($value))
            { //Custom handling for arrays
                if ($is_list)
                    $parts[] = array2json($value); /* :RECURSION: */
                else
                    $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
            } else
            {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';

                //Custom handling for multiple data types
                if (is_numeric($value))
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things







                    
// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);

        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

//MWW Functions
    function pr($string_to_print)
    {
        echo "<pre>";
        print_r($string_to_print);
        echo "</pre>";
    }

    function prd($string_to_print)
    {
        echo "<pre>";
        print_r($string_to_print);
        echo "</pre>";
        die;
    }

    function encrypt($password)
    {
        $len = strlen($password);
        if ($len > 0)
        {
            for ($i = 0; $i < $len; $i++)
            {
                $password[$i] = chr((ord($password[$i]) + $len - $i));
            }
            for ($i = 0; $i < 3; $i++)
            {
                $password .= chr(ord($password[$i]) + $len);
            }
        }

        return $password;
    }

    function decrypt($password)
    {
        $len = strlen($password) - 3;
        $passwd = "";
        for ($i = 0; $i < $len; $i++)
        {
            $temp = ord($password[$i]) - ($len - $i);
            if ($temp < 0)
                $temp += 128;
            $passwd .= chr($temp);
        }
        return $passwd;
    }

    function isLogged()
    {
        global $mySession;

        if (isset($mySession->LoggedUserId))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function ArraySearch($haystack, $needle, $index = null)
    {
        $aIt = new RecursiveArrayIterator($haystack);
        $it = new RecursiveIteratorIterator($aIt);

        while ($it->valid())
        {
            if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle))
            {
                return $aIt->key();
            }

            $it->next();
        }

        return false;
    }

    function pageauthenticate($url, $event)
    {
        //global $mySession;
//		require_once SITE_PATH . '/models/Menu.php';
//			$Menu = new Menu();	
//			$arr1=$mySession->menu;			
//			$key = ArraySearch($arr1, $url, 'page_url');			
//			
//			if ($key>0)
//			{
//				return true;
//			}else{
//				return false;
//			}
        return true;
    }

    function checkevent($url)
    {
        /* global $mySession;
          require_once SITE_PATH . '/models/Menu.php';
          $arr1=$mySession->menu;
          $key = ArraySearch($arr1, $url, 'page_url');
          $menuid=0;
          if ($key>0)
          {
          $menuid=$arr1[$key];
          }
          $arr2=$mySession->actionper;
          $key1 = ArraySearch($arr2, $menuid, 'menu_id');
          if ($key1>0)
          {
          return $key1;
          }else{
          return 0;
          } */
        return 1;
    }

    function sanisitize_input($input_string)
    {
        $san_input = trim(htmlspecialchars(stripslashes($input_string)));
        return $san_input;
    }

    function implodeData($data)
    {
        $req_value = '';
        foreach ($data as $key => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $key1 => $value1)
                {
                    if (is_array($value1))
                    {
                        foreach ($value1 as $key2 => $value2)
                        {
                            $req_value .= $key2 . '=>' . $value2 . ' , ';
                        }
                    }
                    else
                    {
                        $req_value .= $key1 . '=>' . $value1 . ' , ';
                    }
                }
            }
            else
            {
                $req_value .= $key . '=>' . $value . ' , ';
            }
        }
        return $req_value;
    }

    function addslashesInputVar($input_string = null)
    {
        if ($input_string)
        {
            $p = array();
            foreach ($input_string as $key => $value)
            {
                if (is_array($value))
                {
                    $temp = array();
                    foreach ($value as $key1 => $value1)
                    {
                        $temp[$key1] = addslashes($value1);
                    }
                    $p[$key] = $temp;
                }
                else
                    $p[$key] = addslashes($value);
            }
            return $p;
        }
    }

    function stripslashesInputVar($input_string = null)
    {
        if ($input_string)
        {
            $p = array();
            foreach ($input_string as $key => $value)
            {
                if (is_array($value))
                {
                    $temp = array();
                    foreach ($value as $key1 => $value1)
                    {
                        $temp[$key1] = stripslashes($value1);
                    }
                    $p[$key] = $temp;
                }
                else
                    $p[$key] = stripslashes($value);
            }
            return $p;
        }
    }

    function unhtmlentities($string)
    {
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
        $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
        $trans_tbl = array_flip($trans_tbl);
        return strtr($string, $trans_tbl);
    }

    function dateDiff($startDate, $endDate)
    {

        if ($startDate == '0000-00-00' || $startDate == '0000-00-00 00:00:00')
        {
            $startDate = 0;
        }
        if ($endDate == '0000-00-00' || $endDate == '0000-00-00 00:00:00')
        {
            $endDate = 0;
        }
        if (trim($startDate) != 0 && $startDate != NULL)
        {
            $startDate = str_replace("/", "-", $startDate);
            $startDate = explode(' ', $startDate);

            $startDate = strtotime($startDate[0]);
        }
        if (trim($endDate) != 0 && $endDate != NULL)
        {
            $endDate = str_replace("/", "-", $endDate);
            $endDate = explode(' ', $endDate);
            $endDate = strtotime($endDate[0]);
        }
        //return ($endDate-$startDate);
        $datediff = $endDate - $startDate;


        $day = $datediff / (60 * 60 * 24);

        return ceil($day);
    }

    function endDate($date = null, $monthToAdd = null, $yearToAdd = null)
    {

        $addDate = 0;
        $addMonth = 0;
        $addYear = 0;

        if ($date != null && !empty($date))
        {
            $currentDate = strtotime($date);
        }
        else
        {
            $currentDate = time();
        }

        if ($monthToAdd != null && !empty($monthToAdd))
        {
            $currentDate = strtotime("+" . $monthToAdd . " month", $currentDate);
        }

        if ($yearToAdd != null && !empty($yearToAdd))
        {
            $currentDate = strtotime("+" . $yearToAdd . " year", $currentDate);
            ;
        }

        //pr($currentDate);

        $expiredate = date("Y-m-d", $currentDate);

        return $expiredate;
    }

    function findSpaces($value)
    {
        if (strpos($value, " ") === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function createDirectory($dir_name)
    {
        global $CONFIG_VAR;
        if (!file_exists($dir_name))
        {
            mkdir($dir_name, 0777);
            return true;
        }
        else
            return false;
    }

    function renameDirectory($previousName, $newName)
    {
        global $CONFIG_VAR;

        if (rename($previousName, $newName))
            return true;
        else
            return false;
    }

    function removeDirectory($dir_name)
    {
        global $CONFIG_VAR;

        if ($handle = opendir($dir_name))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != '.' && $file != '..')
                    unlink($dir_name . "/" . $file);
            }
            closedir($handle);
        }
        if (rmdir($dir_name))
            return true;
        else
            return false;
    }

    function FileExtention($f)
    {
        $arr = explode(".", $f);
        return $arr[count($arr) - 1];
    }

    function createPath($path)
    {
        if (!trim($path))
            return false;
        $dir_ext = substr($path, 0, strrpos($path, '/'));
        if (!is_dir($dir_ext))
        {
            //echo "<BR/>dir_ext:".$dir_ext;
            createPath($dir_ext); //call itself means recursive loop
            if (!is_dir($path))
            {
                mkdir($path);
                chmod($path, 0777);
            }
            return true;
        }
        else
        {
            clearstatcache();
            if (!is_dir($path))
            {
                mkdir($path);
                chmod($path, 0777);
            }
            return true;
        }
    }

    function deleteAll($path)
    {
        global $mySession;


        foreach (glob($path . '*.*') as $v)
        {
            $org = strstr($v, "_org.");
            if ($org != false)
            {
                unlink($v);
            }

            $frnd = strstr($v, "_frnd.");
            if ($frnd != false)
            {
                unlink($v);
            }

            $profile = strstr($v, $mySession->user['Username'] . ".");
            if ($profile != false)
            {
                unlink($v);
            }
        }
    }

    function deleteProfileImages($path, $position)
    {
        global $mySession;


        foreach (glob($path . '*.*') as $v)
        {
            $org = strstr($v, "_" . $position . "_org.");
            if ($org != false)
            {
                unlink($v);
            }

            $pfile = strstr($v, "_" . $position . ".");
            if ($pfile != false)
            {
                unlink($v);
            }
        }
    }

    function unsetSessionVars()
    {
        global $mySession;

        unset($mySession->errmsg);
        unset($mySession->succmsg);
        unset($mySession->errMsg);
        unset($mySession->errTitle);
        unset($mySession->succTitle);
        unset($mySession->succMsg);
        unset($mySession->LoggedUserId);
        unset($mySession->property_id);
        unset($mySession->step);
        unset($mySession->LoggedUserName);
        unset($mySession->LoggedUserType);
        unset($mySession->ppty_no);
        unset($mySession->noOfNights);
        unset($mySession->arrivalDate);
        unset($mySession->partySize);
        unset($mySession->totalCost);
        unset($mySession->steps);
        unset($mySession->bookingUser);
        unset($mySession->spclOfferId);
    }

    function __autoloadModels($class_name)
    {
        require_once APPLICATION_PATH . 'application/models/' . $class_name . '.php';
    }

    //spl_autoload_register(__autoloadModels);
    function __autoloadDB($class_name)
    {
        require_once APPLICATION_PATH . 'application/models/DbTable/' . $class_name . '.php';
    }

    function __autoloadFB($class_name)
    {
        /* 		echo APPLICATION_PATH.'facebookapp/includes/'.$class_name. '.php';
          exit; */
        require_once APPLICATION_URL . 'facebookapp/includes/' . $class_name . '.php';
    }

    function __autoloadPlugin($plugin_name)
    {

        //echo APPLICATION_PATH . './../application/plugins/'.$plugin_name.'.php'; die;
        //require_once APPLICATION_PATH . './../application/plugins/'.$plugin_name.'.php';
        require_once $plugin_name . '.php';
    }

    //spl_autoload_register(__autoloadDB);


    /* function formatDate($date)
      {
      //$returndate = date_format(date_create($date),"M d, Y");
      $returndate = date_format(date_create($date),"m/d/Y");
      //echo $returndate; die;
      return $returndate;
      } */

    //function to create a random value
    function create_random_value($length, $type = 'chars')
    {
        if (($type != 'mixed') && ($type != 'chars') && ($type != 'digits'))
            return false;

        $rand_value = '';
        while (strlen($rand_value) < $length)
        {
            if ($type == 'digits')
            {
                $char = rand(0, 9);
            }
            else
            {
                $char = chr(rand(0, 255));
            }
            if ($type == 'mixed')
            {
                if (eregi('^[a-z0-9]$', $char))
                    $rand_value .= $char;
            } elseif ($type == 'chars')
            {
                if (eregi('^[a-z]$', $char))
                    $rand_value .= $char;
            } elseif ($type == 'digits')
            {
                if (ereg('^[0-9]$', $char))
                    $rand_value .= $char;
            }
        }

        return $rand_value;
    }

    //createjpg($old_image_withpath,$new_image_withpath,$new_height,$new_width);
    function createjpg($old_image, $new_image, $new_height, $new_width)
    {
        if (copy($old_image, $new_image))
        {
            
        }
        $destimgthumb = ImageCreateTrueColor($new_width, $new_height) or die("Problem In Creating image");
        //echo "<br />destimgthumb =".$destimgthumb ;
        $srcimg = imagecreatefromjpeg($old_image) or die("Problem In opening Source Image");
        //echo "<br />srcimg =".$srcimg ; 
        ImageCopyResized($destimgthumb, $srcimg, 0, 0, 0, 0, $new_width, $new_height, imagesx($srcimg), imagesy($srcimg)) or die("Problem In resizing");
        //ImageCopyResized($destimgthumb,$old_image,0,0,0,0,$new_width,$new_height,imagesx($old_image),imagesy($old_image))  or die("Problem In resizing");

        ImageJPEG($destimgthumb, $new_image) or die("Problem In saving");
        //die;
        return;
    }

    //creategif($old_image_withpath,$new_image_withpath,$new_height,$new_width);
    function creategif($old_image, $new_image, $new_height, $new_width)
    {
        if (copy($old_image, $new_image))
        {
            
        }
        $destimgthumb = ImageCreateTrueColor($new_width, $new_height) or die("Problem In Creating image");
        $srcimg = imagecreatefromgif($old_image) or die("Problem In opening Source Image");
        ImageCopyResized($destimgthumb, $srcimg, 0, 0, 0, 0, $new_width, $new_height, imagesx($srcimg), imagesy($srcimg)) or die("Problem In resizing");
        ImageGIF($destimgthumb, $new_image) or die("Problem In saving");
        return;
    }

    //createpng($old_image_withpath,$new_image_withpath,$new_height,$new_width);
    function createpng($old_image, $new_image, $new_height, $new_width)
    {
        if (copy($old_image, $new_image))
        {
            
        }
        $destimgthumb = ImageCreateTrueColor($new_width, $new_height) or die("Problem In Creating image");
        $srcimg = imagecreatefrompng($old_image) or die("Problem In opening Source Image");
        ImageCopyResized($destimgthumb, $srcimg, 0, 0, 0, 0, $new_width, $new_height, imagesx($srcimg), imagesy($srcimg)) or die("Problem In resizing");
        ImagePNG($destimgthumb, $new_image) or die("Problem In saving");
        return;
    }

    function sortWorkDate($a = NULL, $b = NULL)
    {
        return strcmp($b["DateAdded"], $a["DateAdded"]);
    }

    function showLanguageEncode($string)
    {

        return $string;
        if (mb_detect_order($string))
        {
            return utf8_decode(html_entity_decode(stripslashes($string)));
        }
        else
        {
            return stripslashes(html_entity_decode($string));
        }
    }

    function myPaging($self, $nume, $start, $limit, $sstring = '', $front)
    {
        if ($nume != 0)
        {
            $maxpage = ceil($nume / $limit);
            $current = ($start - 1) * $limit;
            $qstring = $sstring;

            $class1 = 'class="paging"';
            $class2 = 'class="pagelink"';
            $color = 'style="color:#fff;"';
            if ($front == '1')
            {
                $class1 = "";
                $class2 = "";
                $color = "";
            }
            ?>		
            <table cellpadding="0" cellspacing="0" width="100%" border="0">
                <tr <?= $class1 ?>>
                    <td align="left" valign="middle" style="padding:0px;margin:0px;border: solid 0px;"><span <?= $color ?>>Page :</span>&nbsp;&nbsp;
                        <?
                        if ($start <= 5)
                        {
                            if ($start == 1)
                                echo "<font>First</font>";
                            else
                                echo "&nbsp;<a href='$self/start/1/$qstring' " . $class2 . ">First</a>&nbsp;&nbsp;";
                        }
                        else
                            echo "&nbsp;<a href='$self/start/1/$qstring' " . $class2 . ">First</a>&nbsp;&nbsp;";

                        $starting = ((int) (($start - 1) / 5) * 5) + 1;
                        if ($starting > 5)
                        {
                            $startpoint = $starting - 1;
                            $previous = $start - 1;
                            echo "&nbsp;<a href='$self/start/$previous/$qstring' " . $class2 . ">Previous</a>&nbsp;&nbsp;&nbsp;";
                        }
                        else
                        {
                            if ($start == 1)
                                echo "<font>Previous</font>&nbsp;";
                            else
                            {
                                $previous = $start - 1;
                                echo "<a href='$self/start/$previous/$qstring' " . $class2 . ">Previous</a>&nbsp;&nbsp;&nbsp;";
                            }
                        }
                        for ($i = $starting; $i <= $starting + 4; $i++)
                        {
                            if ($start == $i)
                                echo "<font class='page'>&nbsp;$i&nbsp;</font>";
                            else
                            {
                                if ($i <= $maxpage)
                                    echo "&nbsp;<a href='$self/start/$i/$qstring' " . $class2 . ">$i</a>&nbsp;&nbsp;";
                                else
                                    break;
                            }
                        }
                        if ($starting + 4 < $maxpage)
                        {
                            $nextstart = $i / 5;
                            $next = $start + 1;
                            echo "&nbsp;&nbsp;&nbsp;<a href='$self/start/$next/$qstring' " . $class2 . ">Next</a>&nbsp;";
                        }
                        else
                        {
                            if ($start == $maxpage)
                                echo "<font>Next</font>";
                            else
                            {
                                $next = $start + 1;
                                echo "&nbsp;&nbsp;&nbsp;<a href='$self/start/$next/$qstring' " . $class2 . ">Next</a>&nbsp;";
                            }
                        }
                        if ($start > $maxpage - 4)
                        {
                            if ($start == $maxpage)
                                echo "<font>Last</font>";
                            else
                                echo "&nbsp;&nbsp;<a href='$self/start/" . $maxpage . "/$qstring' " . $class2 . ">Last</a>&nbsp;";
                        }
                        else
                            echo "&nbsp;&nbsp;<a href='$self/start/" . $maxpage . "/$qstring' " . $class2 . ">Last</a>&nbsp;";
                        ?>
                    </td>
                    <td  id= "paging_text" width="200" align="left" valign="middle" style="padding:0px;margin:0px;border: solid 0px;">
                        <div align="right">Showing
                            <?
                            if ($start * 10 > $nume)
                            {
                                $upperlimit = $nume;
                                echo ($current + 1 . " - " . $upperlimit . " of " . $nume);
                            }
                            else
                                echo ($current + 1 . " - " . $start * 10 . " of " . $nume);
                            ?>
                        </div></td>
                </tr>
            </table>
            <?
        }
    }

    function myPagingSearch($self, $nume, $start, $limit, $sstring = '', $front)
    {


        if ($nume != 0)
        {
            $maxpage = ceil($nume / $limit);
            $current = ($start - 1) * $limit;
            $qstring = $sstring;

            $class1 = 'class="paging"';
            $class2 = 'class="pagelink"';
            $color = 'style="color:#fff;"';
            if ($front == '1')
            {
                $class1 = "";
                $class2 = "";
                $color = "";
            }
            ?>		
            <table cellpadding="0" cellspacing="0" width="100%" border="0">
                <tr <?= $class1 ?>>
                    <td align="left" valign="middle" style="padding:0px;margin:0px;border: solid 0px;"><span <?= $color ?>>Page :</span>&nbsp;&nbsp;
                        <?
                        if ($start <= 5)
                        {
                            if ($start == 1)
                                echo "<font>First</font>";
                            else
                                echo "&nbsp;<a href='$self&start=1&$qstring' " . $class2 . ">First</a>&nbsp;&nbsp;";
                        }
                        else
                            echo "&nbsp;<a href='$self&start=1&$qstring' " . $class2 . ">First</a>&nbsp;&nbsp;";

                        $starting = ((int) (($start - 1) / 5) * 5) + 1;
                        if ($starting > 5)
                        {
                            $startpoint = $starting - 1;
                            $previous = $start - 1;
                            echo "&nbsp;<a href='$self&start=$previous&$qstring' " . $class2 . ">Previous</a>&nbsp;&nbsp;&nbsp;";
                        }
                        else
                        {
                            if ($start == 1)
                                echo "<font>Previous</font>&nbsp;";
                            else
                            {
                                $previous = $start - 1;
                                echo "<a href='$self&start=$previous&$qstring' " . $class2 . ">Previous</a>&nbsp;&nbsp;&nbsp;";
                            }
                        }
                        for ($i = $starting; $i <= $starting + 4; $i++)
                        {
                            if ($start == $i)
                                echo "<font class='page'>&nbsp;$i&nbsp;</font>";
                            else
                            {
                                if ($i <= $maxpage)
                                    echo "&nbsp;<a href='$self&start=$i&$qstring' " . $class2 . ">$i</a>&nbsp;&nbsp;";
                                else
                                    break;
                            }
                        }
                        if ($starting + 4 < $maxpage)
                        {
                            $nextstart = $i / 5;
                            $next = $start + 1;
                            echo "&nbsp;&nbsp;&nbsp;<a href='$self&start=$next&$qstring' " . $class2 . ">Next</a>&nbsp;";
                        }
                        else
                        {
                            if ($start == $maxpage)
                                echo "<font>Next</font>";
                            else
                            {
                                $next = $start + 1;
                                echo "&nbsp;&nbsp;&nbsp;<a href='$self&start=$next&$qstring' " . $class2 . ">Next</a>&nbsp;";
                            }
                        }
                        if ($start > $maxpage - 4)
                        {
                            if ($start == $maxpage)
                                echo "<font>Last</font>";
                            else
                                echo "&nbsp;&nbsp;<a href='$self&start=" . $maxpage . "&$qstring' " . $class2 . ">Last</a>&nbsp;";
                        }
                        else
                            echo "&nbsp;&nbsp;<a href='$self&start=" . $maxpage . "&$qstring' " . $class2 . ">Last</a>&nbsp;";
                        ?>
                    </td>
                    <td width="200" align="left" valign="middle" style="padding:0px;margin:0px;border: solid 0px;">
                        <div align="right">Showing
                            <?
                            if ($start * 10 > $nume)
                            {
                                $upperlimit = $nume;
                                echo ($current + 1 . " - " . $upperlimit . " of " . $nume);
                            }
                            else
                                echo ($current + 1 . " - " . $start * 10 . " of " . $nume);
                            ?>
                        </div></td>
                </tr>
            </table>
            <?
        }
    }

    function sendmail($to, $from, $subject, $mailtext)
    {
        $mail = new Zend_Mail();

        $mail->setBodyText($mailtext);

        $mail->setBodyHtml($mailtext);

        $mail->setFrom($to);

        $mail->addTo($from);

        $mail->setSubject($subject);

        //$mail->send(); 
        return 1;
    }

    function NVPToArray($NVPString)
    {
        $proArray = array();
        while (strlen($NVPString))
        {
            // name
            $keypos = strpos($NVPString, '=');
            $keyval = substr($NVPString, 0, $keypos);
            // value
            $valuepos = strpos($NVPString, '&') ? strpos($NVPString, '&') : strlen($NVPString);
            $valval = substr($NVPString, $keypos + 1, $valuepos - $keypos - 1);
            // decoding the respose
            $proArray[$keyval] = urldecode($valval);
            $NVPString = substr($NVPString, $valuepos + 1, strlen($NVPString));
        }
        return $proArray;
    }

    function generatePassword()
    {
        $chars = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'z', 'a', 'e', 'i', 'o', 'u');
        $rand = $chars[mt_rand(0, 24)] . $chars[mt_rand(0, 24)] . $chars[mt_rand(0, 24)] . $chars[mt_rand(0, 24)] . $chars[mt_rand(0, 24)] . $chars[mt_rand(0, 24)] . mt_rand(0, 9) . mt_rand(0, 9);

        return $rand;
    }

    function generate_property_no($user_id)
    {
        global $mySession;
        $db = new Db();
        //$userArr = $db->runQuery("select *,max(propertycode) as MAX from ".PROPERTY." where user_id = '".$user_id."' ");
        //$userArr = $db->runQuery("select max(propertycode) as maxim,(count(id)+1) as MAX from " . PROPERTY . " where user_id = '" . $user_id . "' ");
        //$userArr = $db->runQuery("select max(propertycode) as maxim from " . PROPERTY . " where user_id = '" . $user_id . "' and propertycode like '%".$user_id ."DAT%' ");
        //prd("select right(propertycode,char_length(propertycode)-sum(locate('DAT',propertycode),3)) as maxim from " . PROPERTY . " where user_id = '" . $user_id . "' and propertycode like '%".$user_id ."DAT%' ");
        $userArr = $db->runQuery("select right(propertycode,char_length(propertycode)-(locate('DAT',propertycode)+2)) as maxim from " . PROPERTY . " where user_id = '" . $user_id . "' and propertycode like '%" . $user_id . "DAT%' order by convert(maxim, decimal) desc limit 1");


        $maxim = $userArr[0]['maxim'];


        //$maxim = explode('DAT',$maxim);
        //$_ppty = strlen($userArr[0]['MAX']) == 1 ? '0' . $userArr[0]['MAX'] : $userArr[0]['MAX'];
        //prd($maxim);
        //$_ppty = $maxim[1]+1;
        $_ppty = $maxim + 1;

        $_ppty = strlen($_ppty) == 1 ? '0' . $_ppty : $_ppty;

        //prd($user_id . "DAT" . ($_ppty));

        $user_id = strlen($user_id) == 1 ? '0' . $user_id : $user_id;

        if ($userArr != "" && $userArr[0]['maxim'] != "")
            return $user_id . "DAT" . ($_ppty);


        return $user_id . "DAT01";
    }

    function rating_views($num, $class = "")
    {
        global $mySession;
        $db = new Db();

        $class = $class ? $class : "star_rank";

        $class = "class='$class'";

        switch ($num)
        {
            case '1': return '<div ' . $class . '>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '2': return '<div ' . $class . '>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '3': return '<div ' . $class . '>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '4': return '<div ' . $class . '>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '5': return '<div ' . $class . '>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
        }
    }

    function rating_view($num)
    {
        global $mySession;
        $db = new Db();
        switch ($num)
        {
            case '1': echo '<div class="star_rank"><a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '2': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '3': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '4': echo '<div class="star_rank"><a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '5': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
        }
    }

    function rating_view10($num)
    {
        global $mySession;
        $db = new Db();
        switch ($num)
        {
            case '1': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>							
						</div>';
                break;
            case '2': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '3': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '4': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '5': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '6': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '7': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '8': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '9': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
            case '10': echo '<div class="star_rank">
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
							<a class = "active" href="javascript:void(0)">&nbsp;</a>
						</div>';
                break;
        }
    }

    function ppty_image_display($img)
    {
        if ($img)
            return "<img src='" . APPLICATION_URL . "image.php?image=" . IMAGES_URL . "property/" . $img . "&width=150&height=150'>";
        else
            return "<img src='" . APPLICATION_URL . "image.php?image=uploads/generic.gif&width=150&height=150'>";
    }

//
//    function ppty_image_display($img)
//    {
//        if ($img)
//            return "<img src='" . APPLICATION_URL . "image.php?image=" . IMAGES_URL . "property/" . $img . "&width=71&height=72'>";
//        else
//            return "<img src='" . APPLICATION_URL . "image.php?image=uploads/generic.gif&width=71&height=72'>";
//    }

    function xml2array($contents, $get_attributes = 1, $priority = 'tag')
    {
        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create'))
        {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values)
            return; //Hmm...








            
//Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference
        //Go through the tags.
        $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
        foreach ($xml_values as $data)
        {
            unset($attributes, $value); //Remove existing values, or there will be trouble
            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data); //We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if (isset($value))
            {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if (isset($attributes) and $get_attributes)
            {
                foreach ($attributes as $attr => $val)
                {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if ($type == "open")
            {//The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current))))
                { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                } else
                { //There was another element with the same tag name
                    if (isset($current[$tag][0]))
                    {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr']))
                        { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            }
            elseif ($type == "complete")
            { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if (!isset($current[$tag]))
                { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                } else
                { //If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) and is_array($current[$tag]))
                    {//If it is already an array...
                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    { //If it is not an array...
                        $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes)
                        {
                            if (isset($current[$tag . '_attr']))
                            { //The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data)
                            {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($type == 'close')
            { //End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }

        return($xml_array);
    }

    function calculate_rate_after_offer($pptyId, $datefrom = "", $dateto = "")
    {
        global $mySession;
        $db = new Db();
        $discount = 0;


        $datefrom = date('Y-m-d', strtotime($mySession->arrivalDate));
        $dateto = date('Y-m-d', strtotime($datefrom . ' + ' . ($mySession->noOfNights - 1) . ' day'));

        $minrate = $db->runQuery("select round(min(prate)*exchange_rate) as PRATE from " . CAL_RATE . " 
							  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id
  							  inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
							  where property_id = '" . $pptyId . "'  
							  and 
							  ( 
							  	(" . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "')
								or
								(" . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "') 	
								or
								(" . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' )
								or
								(" . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "')
							  )	
	");

        $Total_cost = $mySession->totalCost;
        //prd(floor($spclOfferArr[0]['discount_offer']));

        foreach ($mySession->spclOfferId as $val)
        {
            $spclOfferArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS from " . SPCL_OFFERS . " inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id=" . SPCL_OFFER_TYPES . ".id 
									       where " . SPCL_OFFERS . ".spcl_offer_id = '" . $val . "' order by " . SPCL_OFFERS . ".spcl_offer_id desc");


            switch ($spclOfferArr[0]['discount_type'])
            {
                case '0': $Total_cost = round($Total_cost - (float) ($spclOfferArr[0]['discount_offer'] / 100) * $Total_cost);
                    break;
                case '1': if ($spclOfferArr[0]['free_nights_type'] == 'constant')
                        $Total_cost = round($Total_cost - (float) $minrate[0]['PRATE'] * $spclOfferArr[0]['discount_offer']);
                    else
                        $Total_cost = round($Total_cost - (float) $minrate[0]['PRATE'] * ((floor($mySession->noOfNights / $spclOfferArr[0]['discount_offer'])) > $spclOfferArr[0]['max_night'] ? $spclOfferArr[0]['max_night'] : floor($mySession->noOfNights / $spclOfferArr[0]['discount_offer'])));

                    break;
                case '3': if ($spclOfferArr[0]['id'] == '6')
                        $Total_cost = round($Total_cost - (float) 0.333 * $Total_cost);
                    elseif ($spclOfferArr[0]['id'] == '7')
                        $Total_cost = round($Total_cost - (float) 0.25 * $Total_cost);
            }
        }



        return $Total_cost;
    }

    function calc_discount($bookingId, $amount)
    {
        $db = new Db();
        global $mySession;

        $discount = 0;

        $bookingyArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS , " . BOOKING . ".property_id as PPTY_ID from " . BOOKING . " 
									 inner join " . SPCL_OFFERS . " spcl on find_in_set(spcl.spcl_offer_id, " . BOOKING . ".offer_id)
									 inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFER_TYPES . ".id = spcl.offer_id 
									 where " . BOOKING . ".booking_id =" . $bookingId);

        foreach ($bookingyArr as $val)
        {
            if ($val['discount_type'] == '0')
                $discount += ($val['discount_offer'] / 100) * $amount;
        }
        return $discount;
    }

    function calculate_optional_extras()
    {
        global $mySession;
        $db = new Db();
        $cost = 0;
        foreach ($mySession->extrasId as $values)
        {
            $extrasArr = $db->runQuery("select * from " . EXTRAS . " where eid = '" . $values . "'  ");
            if ($extrasArr[0]['stay_type'] == '0')
            {
                $cost += $extrasArr[0]['eprice'] * $mySession->noOfNights;
            }
            else
                $cost += $extrasArr[0]['eprice'];
        }
        return $cost;
    }

    function calculate_extras()
    {
        global $mySession;
        $db = new Db();

        $cost = 0;

        $currArr = $db->runQuery("select exchange_rate from " . CURRENCY . " 
							  inner join " . PROPERTY . " on " . CURRENCY . ".currency_code =  " . PROPERTY . ".currency_code
							  where " . PROPERTY . ".id = '" . $mySession->pptyId . "'
							 ");



        //prd($mySession->extrasId);

        foreach ($mySession->extrasId as $values)
        {
            $extrasArr = $db->runQuery("select * from " . EXTRAS . " where eid = '" . $values . "'  ");

            if ($extrasArr[0]['stay_type'] == '0')
            {
                $cost += round($extrasArr[0]['eprice'] * $currArr[0]['exchange_rate']) * $mySession->noOfNights;
            }
            else
                $cost += round($extrasArr[0]['eprice'] * $currArr[0]['exchange_rate']);
        }


       /* $extrasArr = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . $mySession->pptyId . "' and etype = '1' ");

        foreach ($extrasArr as $values)
        {
            if ($values['stay_type'] == '0')
            {
                $cost += round($values['eprice'] * $currArr[0]['exchange_rate']) * $mySession->noOfNights;
            }
            else
                $cost += round($values['eprice'] * $currArr[0]['exchange_rate']);
        }*/


        return $cost;
    }

    function calculate_book_extras($bookId)
    {
        global $mySession;
        $db = new Db();

        $cost = 0;


        /* 	$currArr = $db->runQuery("select exchange_rate from ".CURRENCY." 
          inner join ".PROPERTY." on ".CURRENCY.".currency_code =  ".PROPERTY.".currency_code
          inner join ".BOOKING." on ".PROPERTY.".id =  ".BOOKING.".property_id
          where ".BOOKING.".booking_id = '".$bookId."'
          ");
         */


        $bookArr = $db->runQuery("select * from " . BOOKING . " where booking_id = '" . $bookId . "' ");
        $extrasArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where booking_id = '" . $bookId . "' ");




        foreach ($extrasArr as $values)
        {


            if ($values['stay_type'] == '0')
            {
                $cost += round($values['option_price']) * dateDiff($bookArr[0]['date_from'], $bookArr[0]['date_to']);
            }
            else
                $cost += round($values['option_price']);
        }
        return $cost;
    }

//function to find if the customer is paying before 8 weeks 
    function find_payable_opt()
    {
        global $mySession;
        $db = new Db();

        $bookDate = date('Y-m-d', strtotime($mySession->arrivalDate));

        $chkQuery = $db->runQuery("SELECT case when date_Add(curdate(), interval 8 WEEK) < '" . $bookDate . "'
							   then '1'
							   else '2' end as cond");

        //1 is returned when customer is booking 8 weeks earlier than the booking date 
        //2 is returned when the customer is not booking 8 weeks earlier
        return $chkQuery[0]['cond'];
    }

    function save_calendar_stat($pptyId, $start_date, $end_date)
    {
        global $mySession;
        $db = new Db();

        $chkCalData = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $pptyId . "' ");

        //prd($chkCalData);

        if ($chkCalData != "" && count($chkCalData) > 0)
        {
            foreach ($chkCalData as $values)
            {
                //condition var to check conflict
                //condition for  |'| (!')
                if ($start_date > $values['date_from'] && $start_date < $values['date_to'])//checking for start date	
                {
                    $calendar_flag = 0;

                    $dataForm['property_id'] = $pptyId;

                    $dataForm['date_from'] = $values['date_from'];
                    $dataForm['date_to'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($start_date)), date('d', strtotime($start_date)) - 1, date('Y', strtotime($start_date))));
                    $dataForm['cal_status'] = $values['cal_status'];
                    $db->save(CAL_AVAIL, $dataForm);

                    //(child)condition for |''|
                    if ($end_date > $values['date_from'] && $end_date < $values['date_to'])//checking for start date	
                    {
                        //$dataUpdate['property_id'] = $mySession->property_id;
                        $dataUpdate = array();
                        $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                        $condition = 'cal_id=' . $values['cal_id'];
                        $db->modify(CAL_AVAIL, $dataUpdate, $condition);

                        $calendar_flag = 1;  //condition for checking
                    }


                    if ($calendar_flag != 1)
                    {
                        $condition = 'cal_id=' . $values['cal_id'];
                        $db->delete(CAL_AVAIL, $condition);
                    }



                    //saving ''
                    $dataForm = array();

                    $dataForm['property_id'] = $pptyId;
                    $dataForm['date_from'] = $start_date;
                    $dataForm['date_to'] = $end_date;
                    $dataForm['cal_status'] = '0';
                    $db->save(CAL_AVAIL, $dataForm);

                    $calendar_flag_total = 1;
                }

                //condition for (!') |'|   [! |''|]
                if ($end_date > $values['date_from'] && $end_date < $values['date_to'] && $calendar_flag == 0)
                {
                    $dataUpdate = array();
                    //updating  when  '|'| => ''||
                    $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                    $condition = 'cal_id=' . $values['cal_id'];
                    $db->modify(CAL_AVAIL, $dataUpdate, $condition);



                    $dataForm = array();
                    //saving when '|'| =>  ''||
                    $dataForm['property_id'] = $pptyId;

                    $dataForm['date_to'] = $end_date;
                    $dataForm['date_from'] = $start_date;
                    $dataForm['cal_status'] = '0';
                    $db->save(CAL_AVAIL, $dataForm);
                    $calendar_flag_total = 1;
                }


                //conditon for checking if there is no conflicts on the date
                if ($calendar_flag_total == 1)
                    return;
            }

            if ($calendar_flag_total == 0)
            {
                //if($start_date <= $values['date_from'] && $end_date >= $values['date_to'])//checking for duplicate 	
                {
                    $condition = " date_from >= '" . $start_date . "' and date_to  <=  '" . $end_date . "'  and  property_id = '" . $pptyId . "' ";
                    $db->delete(CAL_AVAIL, $condition);
                }
                $dataForm = array();
                $dataForm['property_id'] = $pptyId;
                $dataForm['date_to'] = $end_date;
                $dataForm['date_from'] = $start_date;
                $dataForm['cal_status'] = '0';
                $db->save(CAL_AVAIL, $dataForm);
            }
        }
        else
        {
            $dataForm['property_id'] = $pptyId;
            $dataForm['date_from'] = $start_date; //date('Y-m-d',strtotime($_REQUEST['Datef']));
            $dataForm['date_to'] = $end_date; //date('Y-m-d',strtotime($_REQUEST['Datet']));
            $dataForm['cal_status'] = '0';
            $db->save(CAL_AVAIL, $dataForm);
        }
    }

    function __bookSessionClear()
    {
        global $mySession;
        unset($mySession->noOfNights);
        unset($mySession->arrivalDate);
        unset($mySession->partySize);
        unset($mySession->totalCost);
        unset($mySession->Adults);
        unset($mySession->Children);
        unset($mySession->Infants);
        unset($mySession->steps);
        unset($mySession->bookingUser);
        unset($mySession->pptyId);
        unset($mySession->spclOfferId);
        unset($mySession->extrasId);
        unset($mySession->totalCost);
        unset($mySession->finalAmt);
        unset($mySession->paymentType);
        unset($mySession->bookingUser);
        unset($mySession->minrate);
    }

    function cal_availability_old($pptyId, $dateFrom, $dateTo, $title = 0) //function for calculation whether the property is available or on request within the dates
    {




        $dateFrom = empty($dateFrom) ? "" : date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo . " -1 day"));


        global $mySession;
        $db = new Db();



        //1 on request
        //2 Available
        //0 unknown





        if ($pptyId == "" || strlen($dateFrom) <= 0 || $dateTo == "")
        {
            $availArr = $db->runQuery("select cal_default from  " . PROPERTY . " where id = '" . $pptyId . "' ");

            if ($title === 0)
                return $availArr[0]['cal_default'] == '0' ? 2 : 1;
            else
                return $availArr[0]['cal_default'] == '0' ? "Book Now (&radic;) - This Property is Available to be booked by you, subject to owner acceptance" : "Book Now (?) - Bookings for this Property are 'On Request' as the up-to-date Availability status has to be checked, please allow up to 24 hours for a confirmation.";
        }


        $chkQuery = $db->runQuery("select case
										 when cal_status = '1'
										 then '1'
										 when  cal_status = '2' and  date_from <= '" . $dateFrom . "' and date_to >= '" . $dateTo . "' 
										 then '2'
										 else '0' end as STATUS
										 from " . CAL_AVAIL . " where property_id = '" . $pptyId . "' and (( date_from <= '" . $dateFrom . "' and date_to >= '" . $dateFrom . "') or ( date_from <= '" . $dateTo . "' and date_to >= '" . $dateTo . "' ))  ");

        if ($chkQuery != "" and count($chkQuery) > 0)
            return $chkQuery[0]['STATUS'];
        else
            return 0;
    }

    function cal_availability($pptyId, $title = 0) //function for calculation whether the property is available or on request within the dates
    {
        global $mySession;
        $db = new Db();
        $chkQuery = $db->runQuery("select cal_default from " . PROPERTY . " where  id = '" . $pptyId . "' ");



        if ($chkQuery != "" and count($chkQuery) > 0)
        {
            if ($title === 0)
                return $chkQuery[0]['cal_default'] == '0' ? '2' : '0';
            else
            //return ($chkQuery[0]['cal_default'] == '0') ? 'Book Now (&radic;) - This Property is Available to be booked by you, subject to owner acceptance' : 'Book Now (?) - Bookings for this Property are On Request as the up-to-date Availability status has to be checked, please allow up to 24 hours for a confirmation';
                return ($chkQuery[0]['cal_default'] == '0') ? 'Instant Online Booking with payment protection' : 'Contact us to check availability';
        }
        else
        {
            if ($title === 0)
                return '0';
            else
            //  return 'Book Now (?) - Bookings for this Property are On Request as the up-to-date Availability status has to be checked, please allow up to 24 hours for a confirmation';
                return 'Contact us to check availability';
        }
    }

    function rate_extras($pptyId, $tot = 1, $minNights = "")  //1 for all extras,  2 for compulosry, 3 for optional [not used]
    {

        $db = new Db();
        global $mySession;

        $amount = 0;

        if ($tot == '1')
            $ppty = $db->runQuery("select * from " . EXTRAS . "	 where property_id = '" . $pptyId . "' ");
        elseif ($tot == '2') //compulsory extras
            $ppty = $db->runQuery("select * from " . EXTRAS . "	 where property_id = '" . $pptyId . "' and etype = '1' ");
        else
            $ppty = $db->runQuery("select * from " . EXTRAS . "	 where property_id = '" . $pptyId . "' and etype = '0' ");

        foreach ($ppty as $val)
        {
            if ($val['stay_type'] == '0')
                $amount += $val['eprice'] * $minNights;
            else
                $amount += $val['eprice'];
        }
        return $amount;
    }

    function accommodation_rate($datefrom, $dateto, $minNights, $pptyId)
    {

        global $mySession;
        $db = new Db();
        $totalCost = 0;
        //echo "select  from ".CAL_RATE." where date_from >= '".date('Y-m-d',strtotime($datefrom))."' and date_to <= '".date('Y-m-d',strtotime($dateto))."' and property_id = '".$pptyId."' "; exit;
        $sql = $db->runQuery("select prate,nights from " . CAL_RATE . " where date_from >= '" . $datefrom . "' and date_to <= '" . $dateto . "' and property_id = '" . $pptyId . "' ");



        if (count($sql) > 0)
        {
            if ($minNights < $sql[0]['nights'])
                $totalCost = 0;
            else
                $totalCost = $sql['prate'] * $minNights;
        }
        else //find the minimum rate
        {
            $minrate = $db->runQuery("select min(prate) as MIN from " . CAL_RATE . " where property_id = '" . $pptyId . "' ");
            $totalCost = $minrate[0]['MIN'] * $minNights;
        }

        //code for fetching the extras


        $actcurrArr = $db->runQuery("select exchange_rate*" . ($totalCost != "" ? $totalCost : 0) . " as mul from " . CURRENCY . " inner join " . PROPERTY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code where " . PROPERTY . ".id = '" . $pptyId . "'  ");

        if (count($actcurrArr) > 0)
            $totalCost = $actcurrArr[0]['mul'];




        $extraArr = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . $pptyId . "' and etype = '1' ");



        foreach ($extraArr as $values)
        {
            if ($values['stay_type'] == '0') //per night
            {
                $totalCost = $totalCost + $values['eprice'] * $minNights;
            }
            else //per stay
                $totalCost = $totalCost + $values['eprice'];
        }

        return $totalCost;
    }

    function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item)
        {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
            {
                return true;
            }
        }
        return false;
    }

    function is_special_offer($pptyId, $datefrom = "", $dateto = "")
    {
        $db = new Db();
        global $mySession;



        $chkQuery = $db->runQuery("select * from " . SPCL_OFFERS . " 
							   where property_id = '" . $pptyId . "' 
							   and activate = '1'
							   and book_by >= curdate()
							   ");

        if ($chkQuery != "" && count($chkQuery) > 0)
        {
            return 1;
        }
        else
            return 0;
    }

    function review_count($pptyId)
    {
        $db = new Db();
        global $mySession;

        $reviewArr = $db->runQuery("select review_id from " . OWNER_REVIEW . " where property_id = '" . $pptyId . "' and parent_id = '0' ");

        return count($reviewArr);
    }

    function avg_customer_rating($pptyId)
    {
        $db = new Db();
        global $mySession;

        $reviewArr = $db->runQuery("select rating from  " . OWNER_REVIEW . " where property_id = '" . $pptyId . "' and parent_id = '0' ");

        $counter = 0;
        $total = 0;
        foreach ($reviewArr as $val)
        {
            $total += $val['rating'];
            $counter++;
        }

        return ($total / $counter) ? round($total / $counter, 2) . "/10" : "0/10";
    }

    function breadcrumb_trail($country, $state = "", $city = "", $sub_area = "", $local_area = "", $property_no = "")
    {
        $bread = "<ul class='breadcrumb-ul'>";
        $bread .= "<li class='plain-text'>Holiday Rentals: </li>";
        $bread .= "<li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "search/property/" . trim($country) . "'> " . $country . "</a>&raquo;</li>";
        $bread .= "<li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "search/property/" . trim($country) . "/" . $state . "'>" . $state . "</a>&raquo;</li>";
        $bread .= "<li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "search/property/" . trim($country) . "/" . $state . "/" . $city . "'>" . $city . "<a>";

        $bread .= ($sub_area != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "search/property/" . $country . "/" . $state . "/" . $city . "/" . $sub_area . "'>" . $sub_area . "</a>") : "";

        $bread .= ($local_area != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "search/property/" . $country . "/" . $state . "/" . $city . "/" . $sub_area . "/" . $local_area . "'>" . $local_area . "</a></li>") : "";
        $bread .= "</ul>";

        if (!empty($property_no))
        {
            $bread .="-  Property no: <span style='color:#009933;font-weight:bold;'>$property_no</span>";
        }

        return $bread;
    }

    function breadcrumb_location($country, $state = "", $city = "", $sub_area = "", $local_area = "", $information)
    {
        $bread = "<ul class='breadcrumb-ul'>";
        $bread .= "<li class='plain-text'>Holiday Rentals: </li>";
        $bread .= "<li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "holiday-rentals/" . trim($country) . "'> " . $country . "</a>";
        $bread .= ($state != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "holiday-rentals/$country/$state'>$state</a>") : "";
        $bread .= ($city != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "holiday-rentals/$country/$state/$city'>$city</a>") : "";

        $bread .= ($sub_area != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "holiday-rentals/" . $country . "/" . $state . "/" . $city . "/" . $sub_area . "'>" . $sub_area . "</a>") : "";

        $bread .= ($local_area != "") ? ("&raquo;</li><li><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='" . APPLICATION_URL . "holiday-rentals/" . $country . "/" . $state . "/" . $city . "/" . $sub_area . "/" . $local_area . "'>" . $local_area . "</a></li>") : "";
        $bread .= "</ul>";

        $bread .= " - ";
        if (!empty($local_area))
            $bread .= $local_area;
        elseif (!empty($sub_area))
            $bread .= $local_area;
        elseif (!empty($city))
            $bread .= $city;
        elseif (!empty($state))
            $bread .= $state;
        else
            $bread .= $country;


        $bread .=" Information";
        return $bread;
    }

    function breadcrumb_trail_text($country, $state = "", $city = "", $sub_area = "", $local_area = "")
    {
        $bread = "<p style='display:none;'>";
        $bread .= $country . ",";
        $bread .= $state . ",";
        $bread .= $city;
        $bread .= ($sub_area != "") ? (", $sub_area ") : "";

        $bread .= ($local_area != "") ? (", $local_area ") : "";
        $bread .= "</p>";
        return $bread;
    }

//
//function breadcrumb_trail($country,$state="",$city="",$sub_area="",$local_area="")
//{
//	$bread = "<label><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='".APPLICATION_URL."search/property/".$country."'> ".$country."</a>&raquo;</label>";
//	$bread .= "<label><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='".APPLICATION_URL."search/property/".$country."/".$state."'>".$state."</a>&raquo;</label>";
//	$bread .= "<label><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='".APPLICATION_URL."search/property/".$country."/".$state."/".$city."'>".$city."<a>";
//	
//	$bread .= ($sub_area!="")?("&raquo;</label><label><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='".APPLICATION_URL."search/property/".$country."/".$state."/".$city."/".$sub_area."'>".$sub_area."</a>"):"";
//	
//	$bread .= ($local_area!="")?("&raquo;</label><label><a class='left_panel_heading_ppty_search' style='width:auto;float:none;' href='".APPLICATION_URL."search/property/".$country."/".$state."/".$city."/".$sub_area."/".$local_area."'>".$local_area."</a></label>"):"";
//	
//	return $bread;
//}


    /*
      Re Captcha functions
      ------------------------------------- */

    /**
     * Encodes the given data into a query string format
     * @param $data - array of string elements to be encoded
     * @return string - encoded request
     */
    function _recaptcha_qsencode($data)
    {
        $req = "";
        foreach ($data as $key => $value)
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';

        // Cut the last '&'
        $req = substr($req, 0, strlen($req) - 1);
        return $req;
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * @return array response
     */
    function _recaptcha_http_post($host, $path, $data, $port = 80)
    {

        $req = _recaptcha_qsencode($data);

        $http_request = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;

        $response = '';
        if (false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ))
        {
            die('Could not open socket');
        }

        fwrite($fs, $http_request);

        while (!feof($fs))
            $response .= fgets($fs, 1160); // One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);

        return $response;
    }

    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * This is called from the browser, and the resulting reCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     * @param string $pubkey A public key for reCAPTCHA
     * @param string $error The error given by reCAPTCHA (optional, default is null)
     * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

     * @return string - The HTML to be embedded in the user's form.
     */
    function recaptcha_get_html($pubkey, $error = null, $use_ssl = false)
    {
        if ($pubkey == null || $pubkey == '')
        {
            die("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
        }

        if ($use_ssl)
        {
            $server = RECAPTCHA_API_SECURE_SERVER;
        }
        else
        {
            $server = RECAPTCHA_API_SERVER;
        }

        $errorpart = "";
        if ($error)
        {
            $errorpart = "&amp;error=" . $error;
        }
        return '<script type="text/javascript" src="' . $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

	<noscript>
  		<iframe src="' . $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>';
    }

    /**
     * A ReCaptchaResponse is returned from recaptcha_check_answer()
     */
    class ReCaptchaResponse
    {

        var $is_valid;
        var $error;

    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct
     * @param string $privkey
     * @param string $remoteip
     * @param string $challenge
     * @param string $response
     * @param array $extra_params an array of extra variables to post to the server
     * @return ReCaptchaResponse
     */
    function recaptcha_check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array())
    {
        if ($privkey == null || $privkey == '')
        {
            die("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
        }

        if ($remoteip == null || $remoteip == '')
        {
            die("For security reasons, you must pass the remote ip to reCAPTCHA");
        }



        //discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0)
        {
            $recaptcha_response = new ReCaptchaResponse();
            $recaptcha_response->is_valid = false;
            $recaptcha_response->error = 'incorrect-captcha-sol';
            return $recaptcha_response;
        }

        $response = _recaptcha_http_post(RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify", array(
            'privatekey' => $privkey,
            'remoteip' => $remoteip,
            'challenge' => $challenge,
            'response' => $response
                ) + $extra_params
        );

        $answers = explode("\n", $response [1]);
        $recaptcha_response = new ReCaptchaResponse();

        if (trim($answers [0]) == 'true')
        {
            $recaptcha_response->is_valid = true;
        }
        else
        {
            $recaptcha_response->is_valid = false;
            $recaptcha_response->error = $answers [1];
        }
        return $recaptcha_response;
    }

    /**
     * gets a URL where the user can sign up for reCAPTCHA. If your application
     * has a configuration page where you enter a key, you should provide a link
     * using this function.
     * @param string $domain The domain where the page is hosted
     * @param string $appname The name of your application
     */
    function recaptcha_get_signup_url($domain = null, $appname = null)
    {
        return "https://www.google.com/recaptcha/admin/create?" . _recaptcha_qsencode(array('domains' => $domain, 'app' => $appname));
    }

    function _recaptcha_aes_pad($val)
    {
        $block_size = 16;
        $numpad = $block_size - (strlen($val) % $block_size);
        return str_pad($val, strlen($val) + $numpad, chr($numpad));
    }

    /* Mailhide related code */

    function _recaptcha_aes_encrypt($val, $ky)
    {
        if (!function_exists("mcrypt_encrypt"))
        {
            die("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
        }
        $mode = MCRYPT_MODE_CBC;
        $enc = MCRYPT_RIJNDAEL_128;
        $val = _recaptcha_aes_pad($val);
        return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    function _recaptcha_mailhide_urlbase64($x)
    {
        return strtr(base64_encode($x), '+/', '-_');
    }

    /* gets the reCAPTCHA Mailhide url for a given email, public key and private key */

    function recaptcha_mailhide_url($pubkey, $privkey, $email)
    {
        if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null)
        {
            die("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
                    "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
        }


        $ky = pack('H*', $privkey);
        $cryptmail = _recaptcha_aes_encrypt($email, $ky);

        return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64($cryptmail);
    }

    /**
     * gets the parts of the email to expose to the user.
     * eg, given johndoe@example,com return ["john", "example.com"].
     * the email is then displayed as john...@example.com
     */
    function _recaptcha_mailhide_email_parts($email)
    {
        $arr = preg_split("/@/", $email);

        if (strlen($arr[0]) <= 4)
        {
            $arr[0] = substr($arr[0], 0, 1);
        }
        else if (strlen($arr[0]) <= 6)
        {
            $arr[0] = substr($arr[0], 0, 3);
        }
        else
        {
            $arr[0] = substr($arr[0], 0, 4);
        }
        return $arr;
    }

    /**
     * Gets html to display an email address given a public an private key.
     * to get a key, go to:
     *
     * http://www.google.com/recaptcha/mailhide/apikey
     */
    function recaptcha_mailhide_html($pubkey, $privkey, $email)
    {
        $emailparts = _recaptcha_mailhide_email_parts($email);
        $url = recaptcha_mailhide_url($pubkey, $privkey, $email);

        return htmlentities($emailparts[0]) . "<a href='" . htmlentities($url) .
                "' onclick=\"window.open('" . htmlentities($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities($emailparts [1]);
    }

    function addQueryString($qvar, $qvalue)
    {

        $qa = explode("&", $_SERVER['QUERY_STRING']);

        $query_string = array();



        foreach ($qa as $v)
        {

            $vars = explode("=", $v);
            if ($vars[0] != $qvar && $vars[0] != "")
            {
                $query_string[] = @$vars[0] . "=" . @$vars[1];
            }
        }


//        if ($qvalue != '0')// additional condition for paging done here
//        {
//            $query_string[] = $qvar . "=" . $qvalue;
//        }
        //prd($query_string);
        $qstr = implode("&", $query_string);
        return $qstr;
    }

    function optimize_url($url)
    {

        $url = urldecode($url);
        $url = strtolower($url);
        // $url = str_replace(" ","-",$url);
        return $url;
    }

    function objectToArray($d)
    {
        if (is_object($d))
        {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d))
        {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return array_map(__FUNCTION__, $d);
        }
        else
        {
            // Return array
            return $d;
        }
    }

    //    This is a function to get current page url
    function curPageURL()
    {
        $pageURL = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] != "off")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }


        return $pageURL;
    }

    function canonicalUrl()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $filter = new Zend_Filter_Alnum(true);
        $params = array();
        foreach ($request->getParams() as $key => $value)
        {
            if (in_array($key, array("controller", "action", "module")))
            {
                continue;
            }
            array_push($params, $key . "/" . $filter->filter($value));
        }
        return implode("/", $params);
    }

    function twitterify($ret)
    {

        //
        // Replace all text that precedes a URL with an HTML anchor
        // that hyperlinks the URL and shows the preceding text as
        // the anchor text.
        // 
        // e.g., "hello world www.test.com" becomes
        // <a href="www.test.com" target="_blank">hello world</a>
        //
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $ret);
    }

    function removeElementsByTagName($tagName, $document)
    {
        $nodeList = $document->getElementsByTagName($tagName);
        for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0;)
        {
            $node = $nodeList->item($nodeIdx);
            $node->parentNode->removeChild($node);
        }
    }
    
    ?>