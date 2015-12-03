<?php

__autoloadDB('Db');
__autoloadPlugin('Ciirus');
__autoloadPlugin('Globalresorthomes');
__autoloadPlugin('Contempovacation');
__autoloadPlugin('Fairwaysflorida');

class LibraryController extends AppController {

    public function indexAction() {
        global $mySession;
        $this->view->pageHeading = "Manage Property List";
        $db = new Db();
        $qry = "select * from " . PROPERTY . " as p
			  inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = p.property_type
                          inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = p.country_id
                          inner join " . STATE . " on " . STATE . ".state_id = p.state_id
                          inner join " . CITIES . " on " . CITIES . ".city_id = p.city_id
                          left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = p.sub_area_id
                          left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = p.local_area_id
			  inner join " . USERS . "  as u on u.user_id = p.user_id where p.status = '5'
			  order by p.property_title  ";

        $resData = $db->runQuery("$qry");
        $this->view->ResData = $db->runQuery("$qry");
    }

    public function propertyAction() {
        $res = new Ciirus("c346aeb90de54a3", "ff3a6f4e60ab4ec");
        $res->getProperties();
        die;
    }

    public function synchronizeAction() {

        global $mySession;
        $db = new Db();
        set_time_limit(0);
        $post = $this->getRequest()->getPost();
        $property_id = $this->_getParam("pptyId");

        $this->debug = false;

        if ($this->getRequest()->isPost()) {

            $xml_property_id = $post['xml_property_id'];

            $caption = $post['ref_property_caption'];
            //get data of suppliers from database
            $supplierData = $db->runQuery("select * from  subscriber ");

            $_supplierData = array();

            foreach ($supplierData as $supKey => $supVal) {
                $_supplierData[$supVal['subscriber_key']]['subscriber_name'] = $supVal['subscriber_name'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_id'] = $supVal['subscriber_id'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_url'] = $supVal['subscriber_url'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_secondary_url'] = $supVal['subscriber_secondary_url'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_base_url'] = $supVal['subscriber_base_url'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_image_url'] = $supVal['subscriber_image_url'];
                $_supplierData[$supVal['subscriber_key']]['subscriber_image_secondary_url'] = $supVal['subscriber_image_secondary_url'];

                if (!empty($supVal['subscriber_api_username'])) {
                    $_supplierData[$supVal['subscriber_key']]['subscriber_api_username'] = $supVal['subscriber_api_username'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_api_password'] = $supVal['subscriber_api_password'];
                }
            }
            //prd($_supplierData['ciirus']['subscriber_api_username']);
            //prd($post['xml_subscriber_id']);
            switch ($post['xml_subscriber_id']) {
                //ciirus subscriber synchronization code
                case 1: $res = new Ciirus($_supplierData['ciirus']['subscriber_api_username'], $_supplierData['ciirus']['subscriber_api_password']);
                    //filter parameters
                    $arr['propertyId'] = $xml_property_id;

                    $res->getProperties($arr, 0, true, true, true);

                    //subscriber name
                    $data['xml_subscriber_id'] = $post['xml_subscriber_id'];

                    //get heading
                    $heading = $res->getHeading($xml_property_id);
                    $data['xml_heading'] = $heading;

                    //get description part
                    $description = $res->getDescription($xml_property_id);
                    $data['xml_description'] = $description;

                    //get features
                    $features = $res->getFeatures($xml_property_id);
                    $data['xml_features'] = $features;

                    //get community
                    $community = $res->getCommunity($xml_property_id);
                    $data['xml_community'] = $community;

                    //get rates
                    $rates = $res->getStructuredPropertyRates($xml_property_id, $markUp);
                    $data['xml_rating'] = $rates;

                    //saving rates
                    $markUp = $post['xml_rate_markup'];
                    $rateArr = $res->getCalendarPropertyRates($xml_property_id, $markUp);

                    if (!empty($rateArr) && count($rateArr) > 0 && !$this->debug)
                        $db->delete(CAL_RATE, "property_id = " . $property_id);

                    foreach ($rateArr as $rateK => $rateV) {
                        $rateData = array();
                        $rateData['property_id'] = $property_id;
                        $rateData['date_from'] = $rateV['date_from'];
                        $rateData['date_to'] = $rateV['date_to'];
                        $rateData['nights'] = $rateV['nights'];
                        $rateData['prate'] = $rateV['prate'];

                        if (!$this->debug)
                            $db->save(CAL_RATE, $rateData);
                    }

                    //get  extras
                    $extra = $res->getExtras1($xml_property_id);

                    //poool heating extra
                    $poolExtra = $res->getExtras($xml_property_id);
                    $poolC = count($extra);


                    if ($extra['PoolHeatIncludedInPrice']) {
//                            $extra[$poolC]['FlatFeeAmount'] = 'Flat amount';    
//                            $extra[$poolC]['FlatFee'] = true;    
//                            $extra[$poolC]['DailyFee'] = false;    
                    } else {
                        $extra[$poolC]['DailyFeeAmount'] = $poolExtra['DailyCharge'];
                        $extra[$poolC]['DailyFee'] = true;
                        $extra[$poolC]['FlatFee'] = false;
                        $extra[$poolC]['ItemDescription'] = 'Pool Heating';
                        $extra[$poolC]['Mandatory'] = false;
                    }

                    if (!empty($extra)) {
                        $data['xml_extras'] = serialize($extra);

                        //delete older entries if exist
//                            if(!$this->debug)
//                            $db->delete(EXTRAS, "property_id = " . $property_id);
//                            
                        //older method commented  
//                            $dataExtra = array();
//                            $dataExtra['property_id'] = $property_id;
//                            $dataExtra['ename'] = "Pool Heating";
//                            $dataExtra['eprice'] = $extra['DailyCharge'];
//                            $dataExtra['etype'] = '0';
//                            $dataExtra['stay_type'] = '0';
//
//                            $db->save(EXTRAS, $dataExtra);
                        //saving extras
//                            foreach ($extra as $exKey => $exVal)
//                            {
//                                $dataExtra = array();
//                                $dataExtra['property_id'] = $property_id;
//                                $dataExtra['ename'] = $exVal["ItemDescription"];
//                                $dataExtra['eprice'] = $exVal['FlatFee']=='true'?$exVal['FlatFeeAmount']:$exVal['DailyFeeAmount'];
//                                $dataExtra['etype'] = $exVal['Mandatory']=='true'?'1':'0';
//                                $dataExtra['stay_type'] = $exVal['DailyFee']=='false'?'1':'0';
//                                
//                                if(!$this->debug)
//                                $db->save(EXTRAS, $dataExtra);
//                            }
                    }

                    $data['xml_property_id'] = $xml_property_id;
                    $data['xml_rate_markup'] = $post['xml_rate_markup'];
                    $condition = " id= " . $property_id;

                    if (!$this->debug)
                        $result = $db->modify(PROPERTY, $data, $condition);

                    //grab images functionality
                    $images = $res->getImageList($xml_property_id);

                    //prd($images);

                    if (count($images) > 0 && is_array($images)) {
                        //delete older images
                        $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                        foreach ($galleryArr as $galKey => $galVal) {
                            @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                            $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                        }
                    }

                    foreach ($images as $iKey => $iVal) {
                        //pr($iVal);
                        $file_name = explode("/", $iVal);
                        $file_name = urldecode(time() . "_" . end($file_name));

                        $file_name = str_replace(' ', '_', $file_name);

                        if (!$this->debug)
                            $contentOrFalseOnFailure = file_get_contents($iVal);

                        if (!$this->debug)
                            $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                        //saving data in database
                        $data_image = array();
                        $data_image['image_name'] = $file_name;
                        $data_image['property_id'] = $property_id;

                        //by default image caption - [Location][No. of Beds][Property Type]
//                        if ($iKey == 0)
//                            $data_image['image_title'] = $heading;
                        $data_image['image_title'] = $caption;

                        if (!$this->debug)
                            $db->save(GALLERY, $data_image);
                    }
                    //prd($images);
                    //save the list of booked dates
                    $reservation = $res->getReservations($xml_property_id);


                    //delete the older entries
                    if (!empty($reservation) && count($reservation))
                        $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                    foreach ($reservation as $rKey => $rVal) {
                        if ($rKey === 'ArrivalDate')
                            break;

                        $data_cal = array();
                        $data_cal['property_id'] = $property_id;
                        $date_from = explode('T', $rVal['ArrivalDate']);
                        $data_cal['date_from'] = $date_from[0];
                        $date_to = explode('T', $rVal['DepartureDate']);
                        $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                        //$data_cal['date_to'] = $date_to[0];
                        $data_cal['cal_status'] = '0';

                        if (!$this->debug)
                            $db->save(CAL_AVAIL, $data_cal);
                    }

                    if ($rKey === 'ArrivalDate') {
                        $data_cal = array();
                        $data_cal['property_id'] = $property_id;
                        $date_from = explode('T', $reservation['ArrivalDate']);
                        $data_cal['date_from'] = $date_from[0];
                        $date_to = explode('T', $reservation['DepartureDate']);
                        $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                        //$data_cal['date_to'] = $date_to[0];
                        $data_cal['cal_status'] = '0';

                        if (!$this->debug)
                            $db->save(CAL_AVAIL, $data_cal);
                    }
                    break;
                // global resort home subscriber synchronization code
                case 2:

                    $res = new Globalresorthomes($_supplierData['globalresorthomes']['subscriber_url']);
                    $res->getWebsite($xml_property_id);

                    //grab images functionality
                    $images = $res->getImageList($xml_property_id);
                    $url = $_supplierData['globalresorthomes']['subscriber_secondary_url'];

                    $data = array();
                    $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
                    $data['xml_property_id'] = $post['xml_property_id'];
                    //get heading of the property
                    $heading = $res->getHeading($xml_property_id);
                    $data['xml_heading'] = $heading;

                    //get description part of the website
                    $description = $res->getDescription($xml_property_id);
                    $data['xml_description'] = $description;

                    //get features of the property
                    $features = $res->getFeatures($xml_property_id);
                    $data['xml_features'] = $features;

                    $data['xml_extras'] = '';
                    $data['xml_community'] = '';

                    //get rates
                    $rate = $res->getRates($xml_property_id, $url);
                    $data['xml_rating'] = $rate;

                    //get booked dates
                    $reservation = $res->getReservations($xml_property_id, $url);


                    $data['xml_property_id'] = $post['xml_property_id'];
                    $condition = " id= " . $property_id;

                    if (!$this->debug)
                        $result = $db->modify(PROPERTY, $data, $condition);



                    if (!empty($reservation) && count($reservation)) {
                        $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                        foreach ($reservation as $rKey => $rVal) {
                            $data_cal = array();
                            $data_cal['property_id'] = $property_id;
                            $date_from = explode('-', $rVal);
                            // prd($date_from);

                            $data_cal['date_from'] = date('Y-m-d', strtotime($date_from[0]));

                            if (count($date_from) > 1)
                                $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[1]));
                            else
                                $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[0]));

                            //$data_cal['date_to'] = $date_to[0];
                            $data_cal['cal_status'] = '0';

                            if (!$this->debug)
                                $db->save(CAL_AVAIL, $data_cal);
                        }
                    }

                    if (count($images) > 0 && is_array($images)) {
                        //delete older images
                        $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                        foreach ($galleryArr as $galKey => $galVal) {
                            @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                            $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                        }
                    }

                    foreach ($images as $iKey => $iVal) {
                        //pr($iVal);
                        //$file_name = explode("/", $iVal);
                        $file_name = urldecode(time() . "_" . $iVal);

                        $file_name = str_replace(' ', '_', $file_name);


                        if (!$this->debug) {
                            $validImage = getimagesize($_supplierData['globalresorthomes']['subscriber_image_url'] . '/' . $iVal);

                            if (count($validImage) && is_array($validImage) && !empty($validImage))
                                $contentOrFalseOnFailure = file_get_contents($_supplierData['globalresorthomes']['subscriber_image_url'] . '/' . $iVal);
                            else {
                                $VAR = explode("_", $iVal);
                                $VAR = $VAR[0];
                                $secondary_img_url = str_replace('[VAR]', $VAR, $_supplierData['globalresorthomes']['subscriber_image_secondary_url']);
                                $contentOrFalseOnFailure = file_get_contents($secondary_img_url . '/' . $iVal);
                            }
                        }

                        if (!$this->debug)
                            $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                        //saving data in database
                        $data_image = array();
                        $data_image['image_name'] = $file_name;
                        $data_image['property_id'] = $property_id;
//                        if ($iKey == 0)
//                            $data_image['image_title'] = $heading;
                        $data_image['image_title'] = $caption;
                        if (!$this->debug)
                            $db->save(GALLERY, $data_image);
                    }

                    break;
                case 3:

                    $res = new Contempovacation($_supplierData['contempovacation']['subscriber_url']);
                    $res->getWebsite($xml_property_id);
//                        
                    $data = array();
                    $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
                    $data['xml_property_id'] = $post['xml_property_id'];

                    //get heading of the property
                    $heading = $res->getHeading($xml_property_id);
                    $data['xml_heading'] = $heading;

                    //get description part of the website
                    $description = $res->getDescription($xml_property_id);
                    $data['xml_description'] = $description;

                    //get features of the property
                    $features = $res->getFeatures($xml_property_id);
                    $data['xml_features'] = $features;

                    $data['xml_extras'] = '';
                    $data['xml_community'] = '';

                    $data['xml_rating'] = '';

                    $data['xml_property_id'] = $post['xml_property_id'];
                    $condition = " id= " . $property_id;

                    if (!$this->debug)
                        $result = $db->modify(PROPERTY, $data, $condition);

                    $images = $res->getImageList($xml_property_id);
                    if (count($images) > 0 && is_array($images) && !$this->debug) {
                        //delete older images
                        $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                        foreach ($galleryArr as $galKey => $galVal) {
                            @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                            $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                        }
                    }
                    foreach ($images as $iKey => $iVal) {
                        //$file_name = explode("/", $iVal);
                        $endfile = explode("/", $iVal);
                        $endfile = array_pop($endfile);
                        $file_name = urldecode(time() . "_" . $endfile);
                        $file_name = str_replace(' ', '_', $file_name);

                        if (!$this->debug) {
                            //$validImage = getimagesize($iVal);
                            $contentOrFalseOnFailure = file_get_contents("$iVal");
                        }

                        if (!$this->debug)
                            $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                        //saving data in database
                        $data_image = array();
                        $data_image['image_name'] = $file_name;
                        $data_image['property_id'] = $property_id;

//                        if ($iKey == 0)
//                            $data_image['image_title'] = $heading;
                        $data_image['image_title'] = $caption;

                        if (!$this->debug)
                            $db->save(GALLERY, $data_image);
                    }

                    //get booked dates
                    $reservation = $res->getReservations($xml_property_id, $url);
                    if (!empty($reservation) && count($reservation)) {
                        $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                        foreach ($reservation as $rKey => $rVal) {
                            $data_cal = array();
                            $data_cal['property_id'] = $property_id;
                            $date_from = explode('|||', $rVal);
                            $data_cal['date_from'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[0])));
                            $data_cal['date_to'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[1])));
                            //$data_cal['date_to'] = $date_to[0];
                            $data_cal['cal_status'] = '0';
                            if (!$this->debug)
                                $db->save(CAL_AVAIL, $data_cal);
                        }
                    }

                    break;
                case 4:
                    $res = new Fairwaysflorida($_supplierData['fairwaysflorida']['subscriber_url']);
                    $res->getWebsite($xml_property_id);
//                        
                    $data = array();
                    $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
                    $data['xml_property_id'] = $post['xml_property_id'];
                    //get heading of the property
                    $heading = $res->getHeading($xml_property_id);
                    $data['xml_heading'] = $heading;

                    //get description part of the website
                    $description = $res->getDescription($xml_property_id);
                    $data['xml_description'] = $description;

                    //get features of the property
                    $features = $res->getFeatures($xml_property_id);
                    $data['xml_features'] = $features;

                    $data['xml_extras'] = '';
                    $data['xml_community'] = '';

                    $data['xml_rating'] = '';

                    $data['xml_property_id'] = $post['xml_property_id'];
                    $condition = " id= " . $property_id;

                    if (!$this->debug)
                        $result = $db->modify(PROPERTY, $data, $condition);

                    //=== images process ====
                    $images = $res->getImageList($xml_property_id);
                    if (count($images) > 0 && is_array($images) && !$this->debug) {
                        //delete older images
                        $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                        foreach ($galleryArr as $galKey => $galVal) {
                            @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                            $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                        }
                    }
                    foreach ($images as $iKey => $iVal) {
                        //$file_name = explode("/", $iVal);
                        $endfile = explode("/", $iVal);
                        $endfile = array_pop($endfile);
                        $file_name = urldecode(time() . "_" . $endfile);
                        $file_name = str_replace(' ', '_', $file_name);
                        
                        if (!$this->debug) {
                            //$validImage = getimagesize($iVal);
                            $contentOrFalseOnFailure = file_get_contents("http:$iVal");
                        }

                        if (!$this->debug)
                            $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);
                        //saving data in database
                        $data_image = array();
                        $data_image['image_name'] = $file_name;
                        $data_image['property_id'] = $property_id;

//                        if ($iKey == 0)
//                            $data_image['image_title'] = $heading;
                        $data_image['image_title'] = $caption;

                        if (!$this->debug)
                            $db->save(GALLERY, $data_image);
                    }
                    //===== availability dates
                    $reservation = $res->getBookedDates($post['xml_property_id']);
                    //delete the older entries
                        if (!empty($reservation) && count($reservation)){
                            $db->delete(CAL_AVAIL, "property_id = " . $property_id);


                            foreach ($reservation as $rKey => $rVal) {
                                $data_cal = array();
                                $data_cal['property_id'] = $property_id;
                                $data_cal['date_from'] = $rVal['checkin'];
                                $data_cal['date_to'] = $rVal['checkout'];
                                $data_cal['cal_status'] = '0';
                                if (!$this->debug)
                                    $db->save(CAL_AVAIL, $data_cal);
                            }
                        }
                    break;
                default:
                    echo "Please choose proper subscriber first!!";
                    die;
            }
            $this->_redirect("property/editproperty/pptyId/" . $property_id . "/step/10");
        }
        echo "Please enter property id";
        die;
    }

    function subscriberAction() {
        global $mySession;
        $db = new Db();
        $subscriberArr = $db->runQuery("select * from subscriber");

        $this->view->subscriberArr = $subscriberArr;
        $this->view->pageHeading = "List of Subscribers";
    }

    function addsubscriberAction() {

        global $mySession;
        $db = new Db();

        $subscriber_id = $this->_getParam("subscriberId");
        $myform = new Form_Subscriber($subscriber_id);
        $this->view->pageHeading = !empty($subscriber_id) ? "Add Subscriber" : "Edit Subscriber";


        if ($this->getRequest()->isPost()) {


            if ($myform->isValid($this->getRequest()->getPost())) {
                $dataForm = $myform->getValues();
                $myObj = new Subscription();
                $result = $myObj->saveSubscription($dataForm, $subscriber_id);

                if ($result == 1) {
                    $mySession->sucessMsg = "New Subscriber added successfully.";
                    $this->_redirect('library/subscriber');
                } else {
                    $mySession->sucessMsg = "Subscriber updated successfully.";
                    $this->_redirect('library/subscriber');
                }
            }
        }

        $this->view->myform = $myform;
    }

    public function deletesubscriberAction() {
        global $mySession;
        $db = new Db();
        if ($_REQUEST['Id'] != "") {
            $arrId = explode("|", $_REQUEST['Id']);
            if (count($arrId) > 0) {
                foreach ($arrId as $key => $Id) {
                    $condition = "subscriber_id='" . $Id . "'";
                    $db->delete("subscriber", $condition);
                }
            }
        }
        exit();
    }

    public function synchronizebulkAction() {

        global $mySession;
        $db = new Db();
        set_time_limit(0);
        $post = $this->getRequest()->getPost();


        $this->debug = false;

        if ($this->getRequest()->isPost()) {
            foreach ($post['xml_property_id'] as $key => $postValue) {

                $property_id = $post["ref_property_id"][$key];
                $xml_property_id = $postValue;
                $caption = $post['ref_property_caption'][$key];

                //get data of suppliers from database
                $supplierData = $db->runQuery("select * from  subscriber ");

                $_supplierData = array();

                foreach ($supplierData as $supKey => $supVal) {
                    $_supplierData[$supVal['subscriber_key']]['subscriber_name'] = $supVal['subscriber_name'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_id'] = $supVal['subscriber_id'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_url'] = $supVal['subscriber_url'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_secondary_url'] = $supVal['subscriber_secondary_url'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_base_url'] = $supVal['subscriber_base_url'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_image_url'] = $supVal['subscriber_image_url'];
                    $_supplierData[$supVal['subscriber_key']]['subscriber_image_secondary_url'] = $supVal['subscriber_image_secondary_url'];

                    if (!empty($supVal['subscriber_api_username'])) {
                        $_supplierData[$supVal['subscriber_key']]['subscriber_api_username'] = $supVal['subscriber_api_username'];
                        $_supplierData[$supVal['subscriber_key']]['subscriber_api_password'] = $supVal['subscriber_api_password'];
                    }
                }
                //prd($_supplierData['ciirus']['subscriber_api_username']);
                //prd($post['xml_subscriber_id']);
                switch ($post['xml_subscriber_id']) {
                    //ciirus subscriber synchronization code
                    case 1:
                        $res = new Ciirus($_supplierData['ciirus']['subscriber_api_username'], $_supplierData['ciirus']['subscriber_api_password']);
                        //filter parameters
                        $arr['propertyId'] = $xml_property_id;

                        $res->getProperties($arr, 0, true, true, true);

                        //subscriber name
                        $data['xml_subscriber_id'] = $post['xml_subscriber_id'];

                        //get heading
                        $heading = $res->getHeading($xml_property_id);
                        $data['xml_heading'] = $heading;

                        //get description part
                        $description = $res->getDescription($xml_property_id);
                        $data['xml_description'] = $description;

                        //get features
                        $features = $res->getFeatures($xml_property_id);
                        $data['xml_features'] = $features;

                        //get community
                        $community = $res->getCommunity($xml_property_id);
                        $data['xml_community'] = $community;

                        //get rates
                        $rates = $res->getStructuredPropertyRates($xml_property_id, $markUp);
                        $data['xml_rating'] = $rates;
                        $markUp = $post['xml_rate_markup'][$key];
                        //saving rates
                        $rateArr = $res->getCalendarPropertyRates($xml_property_id, $markUp);

                        if (!empty($rateArr) && count($rateArr) > 0 && !$this->debug)
                            $db->delete(CAL_RATE, "property_id = " . $property_id);

                        foreach ($rateArr as $rateK => $rateV) {
                            $rateData = array();
                            $rateData['property_id'] = $property_id;
                            $rateData['date_from'] = $rateV['date_from'];
                            $rateData['date_to'] = $rateV['date_to'];
                            $rateData['nights'] = $rateV['nights'];
                            $rateData['prate'] = $rateV['prate'];

                            if (!$this->debug)
                                $db->save(CAL_RATE, $rateData);
                        }

                        //get  extras
                        $extra = $res->getExtras1($xml_property_id);

                        //poool heating extra
                        $poolExtra = $res->getExtras($xml_property_id);
                        $poolC = count($extra);


                        if ($extra['PoolHeatIncludedInPrice']) {
//                            $extra[$poolC]['FlatFeeAmount'] = 'Flat amount';    
//                            $extra[$poolC]['FlatFee'] = true;    
//                            $extra[$poolC]['DailyFee'] = false;    
                        } else {
                            $extra[$poolC]['DailyFeeAmount'] = $poolExtra['DailyCharge'];
                            $extra[$poolC]['DailyFee'] = true;
                            $extra[$poolC]['FlatFee'] = false;
                            $extra[$poolC]['ItemDescription'] = 'Pool Heating';
                            $extra[$poolC]['Mandatory'] = false;
                        }

                        if (!empty($extra)) {
                            $data['xml_extras'] = serialize($extra);

                            //delete older entries if exist
//                            if(!$this->debug)
//                            $db->delete(EXTRAS, "property_id = " . $property_id);
//                            
                            //older method commented  
//                            $dataExtra = array();
//                            $dataExtra['property_id'] = $property_id;
//                            $dataExtra['ename'] = "Pool Heating";
//                            $dataExtra['eprice'] = $extra['DailyCharge'];
//                            $dataExtra['etype'] = '0';
//                            $dataExtra['stay_type'] = '0';
//
//                            $db->save(EXTRAS, $dataExtra);
                            //saving extras
//                            foreach ($extra as $exKey => $exVal)
//                            {
//                                $dataExtra = array();
//                                $dataExtra['property_id'] = $property_id;
//                                $dataExtra['ename'] = $exVal["ItemDescription"];
//                                $dataExtra['eprice'] = $exVal['FlatFee']=='true'?$exVal['FlatFeeAmount']:$exVal['DailyFeeAmount'];
//                                $dataExtra['etype'] = $exVal['Mandatory']=='true'?'1':'0';
//                                $dataExtra['stay_type'] = $exVal['DailyFee']=='false'?'1':'0';
//                                
//                                if(!$this->debug)
//                                $db->save(EXTRAS, $dataExtra);
//                            }
                        }

                        $data['xml_property_id'] = $xml_property_id;
                        $condition = " id= " . $property_id;

                        if (!$this->debug)
                            $result = $db->modify(PROPERTY, $data, $condition);

                        //grab images functionality
                        $images = $res->getImageList($xml_property_id);

                        //prd($images);

                        if (count($images) > 0 && is_array($images)) {
                            //delete older images
                            $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                            foreach ($galleryArr as $galKey => $galVal) {
                                @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                                $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                            }
                        }

                        foreach ($images as $iKey => $iVal) {
                            //pr($iVal);
                            $file_name = explode("/", $iVal);
                            $file_name = urldecode(time() . "_" . end($file_name));

                            $file_name = str_replace(' ', '_', $file_name);

                            if (!$this->debug)
                                $contentOrFalseOnFailure = file_get_contents($iVal);

                            if (!$this->debug)
                                $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                            //saving data in database
                            $data_image = array();
                            $data_image['image_name'] = $file_name;
                            $data_image['property_id'] = $property_id;

                            //image caption for all location - bedroom - type
//                            if ($iKey == 0)
                            $data_image['image_title'] = $caption;

                            if (!$this->debug)
                                $db->save(GALLERY, $data_image);
                        }
                        //prd($images);
                        //save the list of booked dates
                        $reservation = $res->getReservations($xml_property_id);


                        //delete the older entries
                        if (!empty($reservation) && count($reservation))
                            $db->delete(CAL_AVAIL, "property_id = " . $property_id);


                        foreach ($reservation as $rKey => $rVal) {
                            if ($rKey === 'ArrivalDate')
                                break;

                            $data_cal = array();
                            $data_cal['property_id'] = $property_id;
                            $date_from = explode('T', $rVal['ArrivalDate']);
                            $data_cal['date_from'] = $date_from[0];
                            $date_to = explode('T', $rVal['DepartureDate']);
                            $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                            //$data_cal['date_to'] = $date_to[0];
                            $data_cal['cal_status'] = '0';



                            if (!$this->debug)
                                $db->save(CAL_AVAIL, $data_cal);
                        }

                        if ($rKey === 'ArrivalDate') {
                            $data_cal = array();
                            $data_cal['property_id'] = $property_id;
                            $date_from = explode('T', $reservation['ArrivalDate']);
                            $data_cal['date_from'] = $date_from[0];
                            $date_to = explode('T', $reservation['DepartureDate']);
                            $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                            //$data_cal['date_to'] = $date_to[0];
                            $data_cal['cal_status'] = '0';

                            if (!$this->debug)
                                $db->save(CAL_AVAIL, $data_cal);
                        }

                        break;
                    // global resort home subscriber synchronization code
                    case 2:

                        $res = new Globalresorthomes($_supplierData['globalresorthomes']['subscriber_url']);
                        $res->getWebsite($xml_property_id);

                        //grab images functionality
                        $images = $res->getImageList($xml_property_id);
                        $url = $_supplierData['globalresorthomes']['subscriber_secondary_url'];

                        $data = array();
                        $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
                        $data['xml_property_id'] = $post['xml_property_id'];
                        //get heading of the property
                        $heading = $res->getHeading($xml_property_id);
                        $data['xml_heading'] = $heading;

                        //get description part of the website
                        $description = $res->getDescription($xml_property_id);
                        $data['xml_description'] = $description;

                        //get features of the property
                        $features = $res->getFeatures($xml_property_id);
                        $data['xml_features'] = $features;

                        $data['xml_extras'] = '';
                        $data['xml_community'] = '';

                        //get rates
                        $rate = $res->getRates($xml_property_id, $url);
                        $data['xml_rating'] = $rate;

                        //get booked dates
                        $reservation = $res->getReservations($xml_property_id, $url);


//                        $data['xml_property_id'] = $post['xml_property_id'];
                        $data['xml_property_id'] = $xml_property_id;
                        $condition = " id= " . $property_id;

                        if (!$this->debug)
                            $result = $db->modify(PROPERTY, $data, $condition);



                        if (!empty($reservation) && count($reservation)) {
                            $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                            foreach ($reservation as $rKey => $rVal) {
                                $data_cal = array();
                                $data_cal['property_id'] = $property_id;
                                $date_from = explode('-', $rVal);
                                // prd($date_from);

                                $data_cal['date_from'] = date('Y-m-d', strtotime($date_from[0]));

                                if (count($date_from) > 1)
                                    $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[1]));
                                else
                                    $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[0]));

                                //$data_cal['date_to'] = $date_to[0];
                                $data_cal['cal_status'] = '0';

                                if (!$this->debug)
                                    $db->save(CAL_AVAIL, $data_cal);
                            }
                        }

                        if (count($images) > 0 && is_array($images)) {
                            //delete older images
                            $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                            foreach ($galleryArr as $galKey => $galVal) {
                                @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                                $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                            }
                        }

                        foreach ($images as $iKey => $iVal) {
                            //pr($iVal);
                            //$file_name = explode("/", $iVal);
                            $file_name = urldecode(time() . "_" . $iVal);

                            $file_name = str_replace(' ', '_', $file_name);


                            if (!$this->debug) {
                                $validImage = getimagesize($_supplierData['globalresorthomes']['subscriber_image_url'] . '/' . $iVal);

                                if (count($validImage) && is_array($validImage) && !empty($validImage))
                                    $contentOrFalseOnFailure = file_get_contents($_supplierData['globalresorthomes']['subscriber_image_url'] . '/' . $iVal);
                                else {
                                    $VAR = explode("_", $iVal);
                                    $VAR = $VAR[0];
                                    $secondary_img_url = str_replace('[VAR]', $VAR, $_supplierData['globalresorthomes']['subscriber_image_secondary_url']);
                                    $contentOrFalseOnFailure = file_get_contents($secondary_img_url . '/' . $iVal);
                                }
                            }

                            if (!$this->debug)
                                $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                            //saving data in database
                            $data_image = array();
                            $data_image['image_name'] = $file_name;
                            $data_image['property_id'] = $property_id;


//                            if ($iKey == 0)
                            $data_image['image_title'] = $caption;


                            if (!$this->debug)
                                $db->save(GALLERY, $data_image);
                        }

                        break;
                    case 3:

                        $res = new Contempovacation($_supplierData['contempovacation']['subscriber_url']);
                        $res->getWebsite($xml_property_id);
//                        
                        $data = array();
                        $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
//                        $data['xml_property_id'] = $post['xml_property_id'];
                        $data['xml_property_id'] = $xml_property_id;

                        //get heading of the property
                        $heading = $res->getHeading($xml_property_id);
                        $data['xml_heading'] = $heading;

                        //get description part of the website
                        $description = $res->getDescription($xml_property_id);
                        $data['xml_description'] = $description;

                        //get features of the property
                        $features = $res->getFeatures($xml_property_id);
                        $data['xml_features'] = $features;

                        $data['xml_extras'] = '';
                        $data['xml_community'] = '';

                        $data['xml_rating'] = '';

                        $data['xml_property_id'] = $post['xml_property_id'];
                        $condition = " id= " . $property_id;

                        if (!$this->debug)
                            $result = $db->modify(PROPERTY, $data, $condition);

                        $images = $res->getImageList($xml_property_id);
                        if (count($images) > 0 && is_array($images) && !$this->debug) {
                            //delete older images
                            $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                            foreach ($galleryArr as $galKey => $galVal) {
                                @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                                $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                            }
                        }
                        foreach ($images as $iKey => $iVal) {
                            //$file_name = explode("/", $iVal);
                            $endfile = explode("/", $iVal);
                            $endfile = array_pop($endfile);
                            $file_name = urldecode(time() . "_" . $endfile);
                            $file_name = str_replace(' ', '_', $file_name);

                            if (!$this->debug) {
                                //$validImage = getimagesize($iVal);
                                $contentOrFalseOnFailure = file_get_contents("$iVal");
                            }

                            if (!$this->debug)
                                $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);

                            //saving data in database
                            $data_image = array();
                            $data_image['image_name'] = $file_name;
                            $data_image['property_id'] = $property_id;
//                            if ($iKey == 0)
                            $data_image['image_title'] = $caption;

                            if (!$this->debug)
                                $db->save(GALLERY, $data_image);
                        }

                        //get booked dates
                        $reservation = $res->getReservations($xml_property_id, $url);
                        if (!empty($reservation) && count($reservation)) {
                            $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                            foreach ($reservation as $rKey => $rVal) {
                                $data_cal = array();
                                $data_cal['property_id'] = $property_id;
                                $date_from = explode('|||', $rVal);
                                $data_cal['date_from'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[0])));
                                $data_cal['date_to'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[1])));
                                //$data_cal['date_to'] = $date_to[0];
                                $data_cal['cal_status'] = '0';
                                if (!$this->debug)
                                    $db->save(CAL_AVAIL, $data_cal);
                            }
                        }

                        break;
                        case 4:
                        $res = new Fairwaysflorida($_supplierData['fairwaysflorida']['subscriber_url']);
                        $res->getWebsite($xml_property_id);
    //                        
                        $data = array();
                        $data['xml_subscriber_id'] = $post['xml_subscriber_id'];
                        $data['xml_property_id'] = $post['xml_property_id'];
                        //get heading of the property
                        $heading = $res->getHeading($xml_property_id);
                        $data['xml_heading'] = $heading;

                        //get description part of the website
                        $description = $res->getDescription($xml_property_id);
                        $data['xml_description'] = $description;

                        //get features of the property
                        $features = $res->getFeatures($xml_property_id);
                        $data['xml_features'] = $features;

                        $data['xml_extras'] = '';
                        $data['xml_community'] = '';

                        $data['xml_rating'] = '';

                        $data['xml_property_id'] = $post['xml_property_id'];
                        $condition = " id= " . $property_id;

                        if (!$this->debug)
                            $result = $db->modify(PROPERTY, $data, $condition);

                        //=== images process ====
                        $images = $res->getImageList($xml_property_id);
                        if (count($images) > 0 && is_array($images) && !$this->debug) {
                            //delete older images
                            $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id='" . $property_id . "' ");

                            foreach ($galleryArr as $galKey => $galVal) {
                                @unlink(SITE_ROOT . "images/property/" . $galVal['image_name']);
                                $db->delete(GALLERY, 'gallery_id = ' . $galVal['gallery_id']);
                            }
                        }
                        foreach ($images as $iKey => $iVal) {
                            //$file_name = explode("/", $iVal);
                            $endfile = explode("/", $iVal);
                            $endfile = array_pop($endfile);
                            $file_name = urldecode(time() . "_" . $endfile);
                            $file_name = str_replace(' ', '_', $file_name);

                            if (!$this->debug) {
                                //$validImage = getimagesize($iVal);
                                $contentOrFalseOnFailure = file_get_contents("http:$iVal");
                            }

                            if (!$this->debug)
                                $byteCountOrFalseOnFailure = file_put_contents(SITE_ROOT . "images/property/" . $file_name, $contentOrFalseOnFailure);
                            //saving data in database
                            $data_image = array();
                            $data_image['image_name'] = $file_name;
                            $data_image['property_id'] = $property_id;

    //                        if ($iKey == 0)
    //                            $data_image['image_title'] = $heading;
                            $data_image['image_title'] = $caption;

                            if (!$this->debug)
                                $db->save(GALLERY, $data_image);
                        }
                        //===== availability dates
                        $reservation = $res->getBookedDates($post['xml_property_id']);
                        //delete the older entries
                            if (!empty($reservation) && count($reservation))
                                $db->delete(CAL_AVAIL, "property_id = " . $property_id);


                            foreach ($reservation as $rKey => $rVal) {
                                $data_cal = array();
                                $data_cal['property_id'] = $property_id;
                                $data_cal['date_from'] = $rVal['checkin'];
                                $data_cal['date_to'] = $rVal['checkout'];
                                $data_cal['cal_status'] = '0';
                                if (!$this->debug)
                                    $db->save(CAL_AVAIL, $data_cal);
                            }
                    break;
                    default:
                        echo "Please choose proper subscriber first!!";
                        die;
                }
            }
            $this->_redirect("property/editproperty/pptyId/" . $property_id . "/step/10");
        }
        echo "Please enter property id";
        die;
    }

}

?>
