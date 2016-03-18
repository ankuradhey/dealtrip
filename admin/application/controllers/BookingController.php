<?php

__autoloadDB('Db');

class BookingController extends AppController {
    #-----------------------------------------------------------#
    # Booking Index Action Function
    #-----------------------------------------------------------#

    public function indexAction() {
        global $mySession;
        $db = new Db();
        $this->view->pageHeading = "Manage Booking";


        $bookingyArr = $db->runQuery("select *, " . BOOKING . ".booking_id as booking_id from " . BOOKING . "
									left join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id  
									inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code 
									inner join " . USERS . " on " . USERS . ".user_id=" . BOOKING . ".user_id  
									order by " . BOOKING . ".booking_id desc");




        $this->view->bookingData = $bookingyArr;
    }
    
    public function adhocAction() {
        global $mySession;
        $db = new Db();
        $this->view->pageHeading = "Manage Ad hoc Booking";


        $bookingyArr = $db->runQuery("select *, " . BOOKING . ".booking_id as booking_id from " . BOOKING . "
                                        left join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id  
                                        inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
                                        inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code 
                                        inner join " . USERS . " on " . USERS . ".user_id=" . BOOKING . ".user_id  
                                        where ad_hoc = '1' 
                                        order by " . BOOKING . ".booking_id desc");




        $this->view->bookingData = $bookingyArr;
    }

    #-----------------------------------------------------------#
    # Booking View Action Function
    #-----------------------------------------------------------#

    public function viewbookingAction() {
        global $mySession;
        $db = new Db();
        $this->view->pageHeading = "View Booking Detail";
        $bookingId = $this->getRequest()->getParam("bookingId");

        $bookingyArr = $db->runQuery("select *, " . BOOKING . ".booking_id as booking_id from " . BOOKING . "
									left join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id  
									inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code 
									inner join " . USERS . " on " . USERS . ".user_id=" . BOOKING . ".user_id  
									inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . USERS . ".country_id
									left join " . SPCL_OFFERS . " ON " . SPCL_OFFERS . ".spcl_offer_id = " . BOOKING . ".offer_id
									left join " . SPCL_OFFER_TYPES . " ON " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
     							    where  " . BOOKING . ".booking_id =" . $bookingId);




        $datefrom = date('Y-m-d', strtotime($bookingyArr[0]['date_from']));
        $dateto = date('Y-m-d', strtotime($bookingyArr[0]['date_to']));


        $bookingycomplusoryArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='1' and booking_id =" . $bookingId);


        $extraArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where booking_id = " . $bookingId);




        /* $usersenderinfoArr= $db->runQuery("select * from ".BOOKING." inner join ".USERS." on ".USERS.".user_id=".BOOKING.".user_id inner join ".COUNTRIES." on ".COUNTRIES.".country_id=".USERS.".country_id  where ".BOOKING.".booking_id =".$bookingId);
          $userreceiverinfoArr= $db->runQuery("select * from ".PROPERTY." inner join ".USERS." on ".USERS.".user_id=".PROPERTY.".user_id inner join ".COUNTRIES." on ".COUNTRIES.".country_id=".USERS.".country_id  where ".PROPERTY.".Id IN (select property_id from ".BOOKING." where booking_id=".$bookingId.")"); */
        $this->view->bookingData = $bookingyArr;
        $this->view->optionComplusoryData = $bookingycomplusoryArr;
        $this->view->extraData = $extraArr;
        $this->view->usersenderData = $usersenderinfoArr;
        $this->view->userreceiverData = $userreceiverinfoArr;
        $this->view->minrate = $bookingyArr[0]['min_rate'];


        //code to change the notification values for the booking notifications
        if ($bookingyArr[0]['booking_notify'] == '0') {
            $dataupdate['booking_notify'] = '1';
            $condition = "booking_id = " . $bookingId;
            $db->modify(BOOKING, $dataupdate, $condition);
        }
    }

    /*
     * Author: ankit
     * Description: used for telephone booking
     */

    public function manageAction() {
        global $mySession;
        $db = new Db();
        $myform = new Form_User();
        $form = new Form_Booking();
        $this->view->form = $form;
        $this->view->myform = $myform;
        $userForm = new Form_User();
        $this->view->userForm = $userForm;
    }

    #-----------------------------------------------------------#
    # Delete booking Action Function
    #-----------------------------------------------------------#

    public function deletebookingAction() {
        global $mySession;
        $db = new Db();
        if ($_REQUEST['Id'] != "") {
            $arrId = explode("|", $_REQUEST['Id']);
            if (count($arrId) > 0) {
                foreach ($arrId as $key => $Id) {
                    $condition1 = "booking_id='" . $Id . "'";
                    $db->delete(BOOKING, $condition1);
                    $db->delete(BOOKING_EXTRA, $condition1);
                }
            }
        }
        exit();
    }

    #-----------------------------------------------------------#
    #  Change Status Booking Action Function
    #-----------------------------------------------------------#

    public function changebookingstatusAction() {
        global $mySession;
        $db = new Db();
        $BcID = $_REQUEST['Id'];
        $status = $_REQUEST['Status'];


        $dataForm = array();

        $bookArr = $db->runQuery("select * from " . BOOKING . " where booking_id = '" . $BcID . "' ");



        if ($bookArr[0]['booking_status'] == '2' && $status != '2') {

            $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $bookArr[0]['property_id'] . "'
		  						   and date_from = '" . date('Y-m-d', strtotime($bookArr[0]['date_from'])) . "'
								   and date_to = '" . date('Y-m-d', strtotime($bookArr[0]['date_to'] . " -1 day")) . "'		
								   and cal_status = '0'
								  ");


            if (count($calArr) > 0) {
                $condition = ' cal_id = ' . $calArr[0]['cal_id'];
                $db->delete(CAL_AVAIL, $condition);
            }
            /* $dataForm['property_id'] = $bookArr[0]['property_id'];
              $dataForm['date_from'] = date('Y-m-d',strtotime($bookArr[0]['date_from']));
              $dataForm['date_to'] = date('Y-m-d',strtotime($bookArr[0]['date_to']." -1 day"));
              $dataForm['cal_status'] = '1';
              save_calendar_stat($dataForm['property_id'],$dataForm['date_from'],$dataForm['date_to']); */
        }



        $data_update['booking_status'] = $status;
        $condition = "booking_id= " . $BcID;
        $db->modify(BOOKING, $data_update, $condition);


        if ($status == '2') {
            $dataForm['property_id'] = $bookArr[0]['property_id'];
            $dataForm['date_from'] = date('Y-m-d', strtotime($bookArr[0]['date_from']));
            $dataForm['date_to'] = date('Y-m-d', strtotime($bookArr[0]['date_to'] . " -1 day"));
            $dataForm['cal_status'] = '0';
            save_calendar_stat($dataForm['property_id'], $dataForm['date_from'], $dataForm['date_to']);
        }
        exit();
    }

    public function editAction() {
        global $mySession;
        $db = new Db();
        $BcID = $_REQUEST['Id'];
        $status = $_REQUEST['Status'];


        $dataForm = array();

        $bookArr = $db->runQuery("select * from " . BOOKING . " where booking_id = '" . $BcID . "' ");
    }

    /*
     * Description: used to find the maximum members allowed in a property - for telephone booking
     */

    public function getmemberlimitAction() {
        global $mySession;
        $db = new Db();
        $retArr = array('error' => 'true', 'success' => 'false', 'message' => "Oops! some error occurred. Please refresh");
        if ($this->getRequest()->isPost()) {
            $pptycode = $this->_getParam('pptycode');
            if ($pptycode) {
                $pptyArr = $db->runQuery("select maximum_occupancy,id,cal_default from property where propertycode = '$pptycode' ");
                if (!empty($pptyArr[0]['maximum_occupancy']) && count($pptyArr))
                    $retArr = array('maximumOccupancy' => $pptyArr[0]['maximum_occupancy'], 'propertyId' => $pptyArr[0]['id'], 'error' => 'false', 'success' => 'true');
                else
                    $retArr['message'] = 'Please enter proper code to book a property';
            }
        }
        return $this->_helper->json($retArr);
    }

    /*
     * getVoucher - used while booking to get applied special offers
     */

    public function getvoucherAction() {
        global $mySession;
        $db = new Db();

        $this->_helper->layout()->disableLayout();
        $ppty = $_REQUEST['ppty'];
        $code = $_REQUEST['code'];
        $noOfNights = $_REQUEST['nights'];
        $dateTo = $_REQUEST['nights'] - 1; //$mySession->noOfNights - 1;
        $arrivalDate = str_replace('/', '-', $_REQUEST['arrivalDate']); //$mySession->arrivalDate;
        //echo $mySession->arrivalDate;
        $dateFrom = date('Y-m-d', strtotime($arrivalDate));
        $dateTo = date('Y-m-d', strtotime($dateFrom . " + $dateTo day"));
        $Total_cost = $_REQUEST['totalAmount'];
        $special = $db->runQuery("select * from " . SPCL_OFFERS . " inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id=" . SPCL_OFFER_TYPES . ".id 
                                where " . SPCL_OFFER_TYPES . ".promo_code='" . trim($code) . "' and " . SPCL_OFFERS . ".activate='1' 
                                and  " . SPCL_OFFERS . ".property_id='" . $ppty . "' 
                                and " . SPCL_OFFERS . ".valid_from <='" . $dateFrom . "' 
                                and  " . SPCL_OFFERS . ".valid_to >='" . $dateTo . "'
                                and  case 
                                                        when " . SPCL_OFFER_TYPES . ".min_nights_type = '1' 
                                                        then " . SPCL_OFFER_TYPES . ".min_nights <= '" . $noOfNights . "'
                                                        else " . SPCL_OFFERS . ".min_night <= '" . $noOfNights . "' end
                                and  " . SPCL_OFFERS . ".book_by >= curdate()
                                order by " . SPCL_OFFERS . ".spcl_offer_id desc");
        $resultArr = array('error' => 'true', 'success' => 'false');
        if (count($special) > 0) {
            $resultArr['error'] = 'false';
            $resultArr['success'] = 'true';
            $resultArr['spcl_offer_id'] = $special[0]['spcl_offer_id'];
            $spclOfferArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS from " . SPCL_OFFERS . "
                                            inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id=" . SPCL_OFFER_TYPES . ".id 
                                            where " . SPCL_OFFERS . ".spcl_offer_id = '" . $special[0]['spcl_offer_id'] . "' 
                                            order by " . SPCL_OFFERS . ".spcl_offer_id desc");
            $resultArr['type_name'] = $spclOfferArr[0]['type_name'];
            switch ($spclOfferArr[0]['discount_type']) {
                case '0': $resultArr['promo_code'] = "(" . $spclOfferArr[0]['promo_code'] . ") " . $spclOfferArr[0]['discount_offer'] . "%";
                    $Total_cost = round($Total_cost - (float) ($spclOfferArr[0]['discount_offer'] / 100) * $Total_cost);
                    break;
                case '1': if ($spclOfferArr[0]['free_nights_type'] == 'constant') {
                        $resultArr['promo_code'] = "(" . $spclOfferArr[0]['promo_code'] . ") " . $this->minrate . " x " . $spclOfferArr[0]['discount_offer'];
                        $Total_cost = round($Total_cost - (float) $minrate[0]['PRATE'] * $spclOfferArr[0]['discount_offer']);
                    } else {
                        $resultArr['promo_code'] = "(" . $spclOfferArr[0]['promo_code'] . ") " . $this->minrate . " x " . floor($mySession->noOfNights / $spclOfferArr[0]['discount_offer']);
                        $Total_cost = round($Total_cost - (float) $minrate[0]['PRATE'] * $spclOfferArr[0]['discount_offer']);
                    }
                    break;
                case '2': $resultArr['promo_code'] = "(" . $spclOfferArr[0]['promo_code'] . ") " . "Free Pool Heating";
                    break;
                case '3': $resultArr['promo_code'] = "(" . $spclOfferArr[0]['promo_code'] . ") " . $spclOfferArr[0]['id'] == '6' ? "33.3%" : "25%";
                    if ($spclOfferArr[0]['id'] == '6')
                        $Total_cost = round($Total_cost - (float) 0.333 * $Total_cost);
                    elseif ($spclOfferArr[0]['id'] == '7')
                        $Total_cost = round($Total_cost - (float) 0.25 * $Total_cost);
                    break;
            }
            $resultArr['reducedCost'] = $Total_cost;
        }
        echo $this->_helper->json($resultArr);
    }

    /*
     *  check availability of property
     *  Received params in post - Adults, Children, date_from (d-m-y format), date_to, 
     */

    public function checkavailabilityAction() {
        global $mySession;
        $db = new Db();
        $res = array("error" => "true", "success" => "true", "message" => "no posted data");
        $book = new Booking();
        if ($this->getRequest()->isPost()) {
            $dataForm = $this->getRequest()->getParams();
            $result = $book->checkAvailablity($dataForm, $dataForm['pptyId']);

            if ($result["output"]) {
                $res["cost"] = $result["cost"] ? $result["cost"] : 0;
                $res["error"] = "false";
                $res["success"] = "true";
            } else {
                $res["message"] = $result["message"];
                $res["error"] = "true";
                $res["success"] = "false";
            }
        }
        echo $this->_helper->json($res);
    }

    /*
     * Step after booking done - saving in database
     */

    public function processbookAction() {
        global $mySession;
        $db = new Db();
        $dataForm = array();
        $dataextraForm = array();
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            $post = $request->getPost();
          
            $dateFrom = explode("/", $post['date_from']);
            $dateFrom = $dateFrom[1] . "/" . $dateFrom[0] . "/" . $dateFrom[2];
            $dateTo = explode("/", $post['departureDates']);
            $dateTo = $dateTo[1] . "/" . $dateTo[0] . "/" . $dateTo[2];
           

            $spclOffer = $post['spclOffrId'];
            $extras = implode(",", $post['extras']);
            $dataForm['property_id'] = $post['propertyId'];
            //$dataForm['property_id'] = $mySession->bookingUser['property_id'];
            $dataForm['user_id'] = $post['userId'];
            $dataForm['date_from'] = date('Y-m-d', strtotime($dateFrom));
            $dataForm['date_to'] = date('Y-m-d', strtotime($dateTo));
            $dataForm['offer_id'] = $spclOffer;
            $dataForm['min_rate'] = $post['totalAmount'];
            //$dataForm['extras_id'] = $extras;
            $dataForm['children'] = $post['Children'];
            $dataForm['adult'] = $post['Adults'];
            $dataForm['infants'] = $post['Infants'];
//            $dataForm['total'] = $mySession->Infants;
//            $dataForm['rental_amt'] = $post['finalAmount'];
            $dataForm['booking_date'] = date('Y-m-d');
            //TO DO
            $dataForm['booking_type'] = '0';

//            if (!empty($dataForm['depositAmount'])) {
                $dataForm['paid_status'] = '2';
                $dataForm['payment_status'] = 'success';
//            } else {
//                $dataForm['paid_status'] = '0';
//            }
            $dataForm['rental_amt'] = $post['totalAmount'];
            $dataForm['telephonic'] = '1';
            $db->save(BOOKING, $dataForm);
            $bookingId = $db->lastInsertId();
            
            $dataForm = array();
            //code to save in payment table
            $dataForm['user_id'] = $post['userId'];
            $dataForm['property_id'] = $post['propertyId'];
            $dataForm['amount_paid'] = $post['finalAmount'];
            $dataForm['booking_id'] = $bookingId;
            $dataForm['payment_date'] = date('Y-m-d');
            $dataForm['card_amount'] = $post['cardFees'];
           $db->save(PAYMENT, $dataForm);



                $Url = '<a href="' . APPLICATION_URL . '">' . APPLICATION_URL . '</a>';
                $templateData = $db->runQuery("select * from " . EMAIL_TEMPLATES . " where template_id='7'");
               $usernewData = $db->runQuery("select * from users where user_id=".$post['userId']);
                $messageText = $templateData[0]['email_body'];
                $subject = $templateData[0]['email_subject'];

                //userId

                $messageText = str_replace("[NAME]", $usernewData[0]['first_name'].' '.$usernewData[0]['last_name'], $messageText);
                $messageText = str_replace("[SITENAME]", SITE_NAME, $messageText);
                $messageText = str_replace("[SITEURL]", APPLICATION_URL, $messageText);
                $messageText = str_replace("[PROPERTYNO]", $post['propertyCode'], $messageText);
               SendEmail($post['emailAddress'], $subject, $messageText);
            

            
            
            //code to save data in calendar table
			/*if($post['finalupdatecalendar']=='yes'){
             $dataForm = array();
              $dataForm['property_id'] = $mySession->pptyId;
              $dataForm['date_from'] = date('Y-m-d',strtotime($mySession->arrivalDate));
              $dataForm['date_to'] = date('Y-m-d',strtotime($mySession->arrivalDate." + ".$mySession->noOfNights." day"));
              $dataForm['cal_status'] = '0';
              save_calendar_stat($mySession->pptyId,$dataForm['date_from'],$dataForm['date_to']); 
			}*/
             if($post['finalupdatecalendar']=='yes'){
                $updatecalendar=array();
                $updatecalendar['property_id']=$post['propertyId'];
                $updatecalendar['date_from']=date('Y-m-d', strtotime($dateFrom));
                $updatecalendar['date_to']=date('Y-m-d', (strtotime($dateTo)-86400));
                $updatecalendar['cal_status']='0';
                 $db->save('cal_avail', $updatecalendar);


            }
            //saving in the booking extra table

            foreach ($post['extras'] as $values) {
                if(!empty($values)){
                $extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  " . EXTRAS . " 
			                    inner join " . PROPERTY . " on " . PROPERTY . ".id = " . EXTRAS . ".property_id
					    inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
                                            where eid = '" . $values . "'	 ");

                $dataForm = array();
                $dataForm['booking_id'] = $bookingId;
                $dataForm['option_name'] = $extrasArr[0]['ename'];
                $dataForm['option_price'] = $extrasArr[0]['eprice'];
                $dataForm['option_status'] = $extrasArr[0]['etype'];
                $dataForm['stay_type'] = $extrasArr[0]['stay_type'];
                $db->save(BOOKING_EXTRA, $dataForm);
            }
            }

            $extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  " . EXTRAS . " 
                                        inner join " . PROPERTY . " on " . PROPERTY . ".id = " . EXTRAS . ".property_id
   					inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
					where property_id = '" . $post['propertyId'] . "' and etype = '1' ");

            //saving for the compulosry extras table
            foreach ($extrasArr as $values) {
                $dataForm = array();
                $dataForm['booking_id'] = $bookingId;
                $dataForm['option_name'] = $values['ename'];
                $dataForm['option_price'] = $values['eprice'];
                $dataForm['option_status'] = $values['etype'];
                $dataForm['stay_type'] = $values['stay_type'];
                $db->save(BOOKING_EXTRA, $dataForm);
            }

            $mySession->sucessMsg = "Thank you.. Property has been Booked Successfully";
            $pptyno = $db->runQuery("select propertycode from  " . PROPERTY . " where id = '" . $post['propertyId'] . "' ");

//            $fullName = $mySession->LoggedUserName;
//
//            $Url = '<a href="' . APPLICATION_URL . '">' . APPLICATION_URL . '</a>';
//            $templateData = $db->runQuery("select * from " . EMAIL_TEMPLATES . " where template_id='7'");
//            $messageText = $templateData[0]['email_body'];
//            $subject = $templateData[0]['email_subject'];
//
//            $messageText = str_replace("[NAME]", $fullName, $messageText);
//            $messageText = str_replace("[SITENAME]", SITE_NAME, $messageText);
//            $messageText = str_replace("[SITEURL]", APPLICATION_URL, $messageText);
//            $messageText = str_replace("[PROPERTYNO]", $pptyno[0]['propertycode'], $messageText);
//
//            SendEmail($dataForm['email_address'], $subject, $messageText);
            //===== code for adding popular properties
            //two cases
            //1. when booked property is already in the list of slides property
            //2. when booked property is not in the list of slides property

            $identifyArr = $db->runQuery("select * from " . SLIDES_PROPERTY . " where lppty_property_id = '" . $post['propertyId'] . "' and lppty_type = '1' ");

            if ($identifyArr != "" and count($identifyArr) > 0) {

                $db->delete(SLIDES_PROPERTY, 'lppty_id= "' . $identifyArr[0]['lppty_id'] . '" ');

                $updateData = array();
                $updateData['lppty_order'] = new Zend_Db_Expr('lppty_order-1');
                $updateData['lppty_status'] = '1';

                $db->modify(SLIDES_PROPERTY, $updateData, "lppty_type='1' and lppty_order > '" . $identifyArr[0]['lppty_order'] . "' ");
            } else {

                $updateData = array();
                $updateData['lppty_order'] = new Zend_Db_Expr('lppty_order+1');
                $updateData['lppty_status'] = '1';

                $db->modify(SLIDES_PROPERTY, $updateData, "lppty_type='1'");

                $saveData = array();
                $saveData['lppty_property_id'] = $post['propertyId'];
                $saveData['lppty_type'] = '1';
                $saveData['lppty_order'] = '1';
                $db->save(SLIDES_PROPERTY, $saveData);
            }
            //---------------------------------------
            //__bookSessionClear();
            $this->_redirect("booking");
        }
        else
            $this->_redirect("booking/manage");
    }

    public function getextrabypropertyidAction(){
        $dataForm = $this->getRequest()->getParams();
        $db = new Db();
        $extrasArr = $db->runQuery("select eid,ename,eprice*exchange_rate as eprice,etype,stay_type from  " . EXTRAS . " 
                                        inner join " . PROPERTY . " on " . PROPERTY . ".id = " . EXTRAS . ".property_id
   					inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
					where property_id = '" . $dataForm['propertyId'] . "' and etype = '1' ");
            print_r(json_encode($extrasArr));die;
    }
    
    public function editbookingAction() {
        global $mySession;
        $db = new Db();
        $bookingId = $this->getRequest()->getParam("Id");
        $dataForm = array();

        $bookingyArr = $db->runQuery("select *, " . BOOKING . ".booking_id as booking_id from " . BOOKING . "
         left join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id  
         inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
         inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code 
         inner join " . USERS . " on " . USERS . ".user_id=" . BOOKING . ".user_id  
         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . USERS . ".country_id
         left join " . SPCL_OFFERS . " ON " . SPCL_OFFERS . ".spcl_offer_id = " . BOOKING . ".offer_id
         left join " . SPCL_OFFER_TYPES . " ON " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
                where  " . BOOKING . ".booking_id =" . $bookingId);

        $datefrom = date('Y-m-d', strtotime($bookingyArr[0]['date_from']));
        $dateto = date('Y-m-d', strtotime($bookingyArr[0]['date_to']));

        $bookingycomplusoryArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='1' and booking_id =" . $bookingId);
		//echo "<pre>"; print_r($bookingyArr);
        $extraArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where booking_id = " . $bookingId);
		//echo "<pre>"; print_r($extraArr);
		//die;
        $propertyArr = $db->runQuery("select id,propertycode from " . PROPERTY . " ");

        if ($this->getRequest()->isPost()) {
            $postDataArray = $this->getRequest()->getPost();
            $a = explode('/', $postDataArray['date_from']);
            $dateTo = date('Y-m-d', mktime(0, 0, 0, $a[1], $a[0] + $postDataArray['no_of_night'], $a[2]));
            $dataForm = array();
            $dataForm['property_id'] = $postDataArray['propertyId'];
            $dataForm['rental_amt'] = $postDataArray['holiday_rental_cost'];
            $dataForm['date_from'] = $a[2] . "-" . $a[1] . "-" . $a[0];
            $dataForm['date_to'] = $dateTo;
            $dataForm['adult'] = $postDataArray['no_adult'];
            $dataForm['children'] = $postDataArray['no_child'];
            $dataForm['infants'] = $postDataArray['no_infants'];
            $condition = "booking_id =" . $bookingId;
            $db->modify(BOOKING, $dataForm, $condition);
            
            if(count($postDataArray['extras'])){
            $db->delete(BOOKING_EXTRA, "booking_extra_id in (".implode(',',$postDataArray['booking_extra_id']).") ");
            }
//            echo '<pre>'; print_r($postDataArray['extras']); exit('Macro die');
            //saving in the booking extra table
            foreach ($postDataArray['extras'] as $values) {
                $extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  " . EXTRAS . " 
			                    inner join " . PROPERTY . " on " . PROPERTY . ".id = " . EXTRAS . ".property_id
					    inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
                                            where eid = '" . $values . "'	 ");
                
                $dataForm = array();
                $dataForm['booking_id'] = $bookingId;
                $dataForm['option_name'] = $extrasArr[0]['ename'];
                $dataForm['option_price'] = $extrasArr[0]['eprice'];
                $dataForm['option_status'] = $extrasArr[0]['etype'];
                $dataForm['stay_type'] = $extrasArr[0]['stay_type'];
                $db->save(BOOKING_EXTRA, $dataForm);
            }
            

            $propertyArr = $db->runQuery("select user_id,propertycode from " . PROPERTY . " where id='" . $postDataArray['propertyId'] . "'");
            
            $userArr = $db->runQuery("select email_address from " . USERS . " where user_id='" . $propertyArr[0]['user_id'] . "'");
            $html = "<table>
                        <tr><td>Property: </td><td>" . $propertyArr[0]['propertycode'] . "</td></tr>
                        <tr><td>Cost of Holiday Rental: </td><td>" . $postDataArray['holiday_rental_cost'] . "</td></tr>
                        <tr><td>Cost of Property Extras: </td><td>" . $postDataArray['extra_cost'] . "</td></tr>
                        <tr><td>Booking Form:</td><td>" . $a[2] . "-" . $a[1] . "-" . $a[0] . "</td></tr>
                        <tr><td>No Of Nights:</td><td>" . $postDataArray['no_of_night'] . "</td></tr>
                    </table>";

//            $mail = new Zend_Mail();
//            $mail->setFrom('');
//            $mail->addTo($bookingyArr[0]['email_address']);
//            $mail->setSubject('Modify Booking Details');
//            $mail->setBodyHtml($html);
//            $mail->send();
//            $mail = new Zend_Mail();
//            $mail->setFrom('');
//            $mail->addTo($userArr[0]['email_address']);
//            $mail->setSubject('Modify Booking Details');
//            $mail->setBodyHtml($html);
//            $mail->send();
            $this->_redirect("booking/index");
        }

        $this->view->bookingData = $bookingyArr;
        $this->view->optionComplusoryData = $bookingycomplusoryArr;
        $this->view->extraData = $extraArr;
        $this->view->usersenderData = $usersenderinfoArr;
        $this->view->userreceiverData = $userreceiverinfoArr;
        $this->view->minrate = $bookingyArr[0]['min_rate'];
        $this->view->propertyData = $propertyArr;
        //echo '<pre>';print_r($propertyArr);
    }

}

?>