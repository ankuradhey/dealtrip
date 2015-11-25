<?php

    __autoloadDB('Db');

    class MyaccountController extends AppController
    {

        public function indexAction()
        {
            global $mySession;
            $db = new Db();
            $userArr = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");

            if ($mySession->LoggedUserType == '2')
            {
                $pageArr = $db->runQuery("select * from " . PAGES1 . " where page_id = '82' ");
                $title = $pageArr[0]['page_title'];
                $title = str_replace('[OWNER_NAME]', strtoupper((substr(trim($userArr[0]['first_name']), 0, 1))) . substr(trim($userArr[0]['first_name']), 1), $title);
                $this->view->pageTitle = $title;
                $this->view->welcomemessage = $pageArr[0]['page_content'];

                // news Updates
                $newArr = $db->runQuery("select * from " . NEWS . " where status = '1' order by news_id desc");


                foreach ($newArr as $keys => $values)
                {

                    $tmp = explode(",", $values['black_viewers']);

                    if (!in_array($mySession->LoggedUserId, $tmp)):

                        foreach ($values as $key => $val)
                        {
                            $newsArr[$keys][$key] = $val;
                        }
                    endif;
                }

                $this->view->newsArr = $newsArr;
            }
            else
            {

                $pageArr = $db->runQuery("select * from " . PAGES1 . " where page_id = '88' ");
                $title = $pageArr[0]['page_title'];


                switch ($userArr[0]['title'])
                {
                    case '0': $Title = "Mr.";
                        break;
                    case '1': $Title = "Mrs.";
                        break;
                    case '2': $Title = "Miss.";
                        break;
                    case '3': $Title = "Ms.";
                        break;
                    case '4': $Title = "Dr.";
                        break;
                }

                $title = str_replace('[TITLE]', $Title, $title);
                $title = str_replace('[CUSTOMER_NAME]', strtoupper((substr(trim($userArr[0]['first_name']), 0, 1))) . substr(trim($userArr[0]['first_name']), 1), $title);
                $this->view->pageTitle = $title;
                $this->view->welcomemessage = $pageArr[0]['page_content'];

                /* $this->view->pageTitle = "Welcome ".$userArr[0]['username'].""; 
                  $adminArr = $db->runQuery("select * from ".ADMINISTRATOR." ");
                  if($msgArr[0]['welcomemessage'] != "")
                  $this->view->welcomemessage = $userArr[0]['welcomemessage'];
                  else
                  $this->view->welcomemessage = $adminArr[0]['welcomemessage'];
                 */
            }
        }

        public function addpropertyAction()
        {
            global $mySession;
            $db = new Db();

            $this->view->pageTitle = "Add a Property";

            $ppty_no = generate_property_no($mySession->LoggedUserId);
            $this->view->ppty_no = $ppty_no;

            //code for checking the status of each step
            if (isset($mySession->property_id))
            {
                $statusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                $this->view->status_2 = $status_step2 = $statusArr[0]['status_2'];
                $this->view->status_3 = $status_step3 = $statusArr[0]['status_3'];
                $this->view->status_4 = $status_step4 = $statusArr[0]['status_4'];
                $this->view->status_5 = $status_step5 = $statusArr[0]['status_5'];
                $this->view->status_6 = $status_step6 = $statusArr[0]['status_6'];
                $this->view->status_7 = $status_step7 = $statusArr[0]['status_7'];
                $this->view->status_8 = $status_step8 = $statusArr[0]['status_8'];
                $this->view->status_9 = $status_step9 = $statusArr[0]['status_9'];

                if ($status_step2 && $status_step3 && $status_step4 && $status_step5 && $status_step6 && $status_step7 && $status_step8 && $status_step9)
                {
                    $status_data['status'] = '2';
                    $condition = "id=" . $mySession->property_id;
                    $db->modify(PROPERTY, $status_data, $condition);
                }
            }
            else
            {
                $this->view->status_2 = 0;
                $this->view->status_3 = 0;
                $this->view->status_4 = 0;
                $this->view->status_5 = 0;
                $this->view->status_6 = 0;
                $this->view->status_7 = 0;
                $this->view->status_8 = 0;
                $this->view->status_9 = 0;
            }

            if ($mySession->ppty_no != "")
            {
                $pId = $mySession->ppty_no;
            }
            else
            {
                $pId = "";
                $mySession->step = '0';
            }

            $this->view->pId = $pId;

            $stepId = $this->getRequest()->getParam("step");

            if ($stepId != "")
                $mySession->step = $stepId - 1;


            switch ($mySession->step)
            {
                case '0': $myform = new Form_Ownproperty($mySession->property_id);
                    $this->view->step = '1';
                    $step = '1';

                    if (isset($mySession->property_id))
                    {
                        $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                        $this->view->rating = $ratingArr[0]['star_rating'];
                    }

                    break;
                case '1': $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");
                    $this->view->specData = $specArr;
                    $myform = new Form_Propertyspec($mySession->property_id);
                    $this->view->stepId = $stepId;

                    $this->view->step = '2';
                    $step = '2';

                    //bathroom no. 
                    $bathroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->no_bedrooms = $bathroomArr[0]['bedrooms'];
                    break;

                case '2': $amenityArr = $db->runQuery("select * from " . AMENITY_PAGE . " ");
                    $this->view->amenityData = $amenityArr;

                    $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
                    $this->view->amenityArr = $amenity;

                    if ($mySession->property_id != '')
                        $myform = new Form_Amenity($mySession->property_id);
                    else
                        $myform = new Form_Amenity();

                    $this->view->step = '3';
                    $step = '3';

                    break;

                case '3': if ($stepId == '4')
                        $myform = new Form_Location($mySession->property_id);
                    else
                        $myform = new Form_Location();
                    $this->view->step = '4';
                    $step = '4';

                    break;

                case '4': $this->view->step = '5';

                    $Arr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->floorPlan = $Arr[0]['floor_plan'];
                    //$myform = new Form_Photo();
                    $step = '5';

                    //submit photo action
                    //submit photo action ends


                    break;

                case '5': $myform = new Form_Cal();
                    $this->view->myform = $myform;
                    $next = $this->getRequest()->getParam("cal");

                    if ($next != "")
                        $this->view->nexts = $next;
                    else
                        $this->view->nexts = 0;

                    $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $mySession->property_id . "' ");
                    $this->view->calArr = $calArr;

                    $this->view->step = '6';
                    $step = '6';

                    //passing default value of calendar to view
                    $pptyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->cal_default = $pptyArr[0]['cal_default'];


                    break;

                case '6': $this->view->step = '7';
                    $step = '7';

                    $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id where so.property_id = '" . $mySession->property_id . "' order by so.spcl_offer_id asc
								   ");


                    if (count($offerArr) == 0)
                    {
                        $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . "");
                        foreach ($offerArr as $values)
                        {
                            $dataForm['offer_id'] = $values['id'];
                            //$dataForm['date_from'] = date('Y-m-d', strtotime($_REQUEST['Date_f']));
                            //$dataForm['date_to'] = date('Y-m-d', strtotime($_REQUEST['Date_t']));
                            //$dataForm['nights'] = $_REQUEST['Nights'];
                            //$dataForm['prate'] = $_REQUEST['Rate'];
                            $dataForm["property_id"] = $mySession->property_id;
                            $db->save(SPCL_OFFERS, $dataForm);
                        }
                    }


                    $this->view->offerArr = $offerArr;
                    //echo $mySession->step; exit;
                    //rental question
                    $rentalQues = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

                    if ($rentalQues != "" && count($rentalQues) > 0)
                        $this->view->rental_ques = $rentalQues[0]['rental_ques'];


                    //currency data
                    $currData = $db->runQuery("select * from  " . CURRENCY . " order by currency_order asc ");
                    $this->view->currencyData = $currData;

                    //checking for currency
                    if ($rentalQues[0]['currency_code'] != NULL)
                    {
                        $this->view->currency_set = '1';
                        $this->view->currency_val = $rentalQues[0]['currency_code'];
                    }
                    else
                        $this->view->currency_set = '0';

                    break;

                case '7': $myform = new Form_Oreview($mySession->property_id);
                    //query for getting image
                    $this->view->step = '8';
                    //$uData = $db->runQuery("select * from ".OWNER_REVIEW." where property_id = '".$mySession->property_id."' ");

                    /* if($uData[0]['owner_image'] == "")
                      $this->view->image = "no_owner_pic.jpg";
                      else
                      $this->view->image = $uData[0]['owner_image'];
                     */

                    if (!isset($mySession->reviewImage))
                        $mySession->reviewImage = "no_owner_pic.jpg";

                    $this->view->myform = $myform;


                    $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " as r
													inner join " . PROPERTY . " as p on p.id = r.property_id
													inner join " . USERS . " as u on u.user_id = p.user_id
													where r.property_id = '" . $mySession->property_id . "' and r.review_status = '1' order by r.review_id desc ");



                    $i = 0;
                    foreach ($reviewArr as $val)
                    {

                        if ($val['parent_id'] == 0)
                        {

                            $childArr = $db->runQuery("select * from " . OWNER_REVIEW . " where parent_id = '" . $val['review_id'] . "'  ");

                            $reviewData[$i]['review_id'] = $val['review_id'];
                            $reviewData[$i]['uType'] = $val['uType'];
                            $reviewData[$i]['guest_name'] = $val['guest_name'];
                            $reviewData[$i]['owner_image'] = $val['guest_image'];
                            $reviewData[$i]['headline'] = $val['headline'];
                            $reviewData[$i]['review'] = $val['review'];
                            $reviewData[$i]['comment'] = $val['comment'];
                            $reviewData[$i]['location'] = $val['location'];
                            $reviewData[$i]['image'] = $val['image'];
                            $reviewData[$i]['review_date'] = $val['review_date'];
                            $reviewData[$i]['check_in'] = $val['check_in'];
                            $reviewData[$i]['rating'] = $val['rating'];

                            $k = 0;
                            foreach ($childArr as $val1)
                            {
                                $reviewData[$i]['child'][$k]['guest_name'] = $val1['guest_name'];
                                $reviewData[$i]['child'][$k]['owner_image'] = $val1['guest_image'];
                                $reviewData[$i]['child'][$k]['comment'] = $val1['comment'];
                                $reviewData[$i]['child'][$k]['review_date'] = $val1['review_date'];
                                $k++;
                            }
                        }
                        $i++;
                    }
                    //prd($reviewData);
                    $this->view->reviewArr = $reviewData;

                    break;

                case '8': if (isset($mySession->property_id))
                        $myform = new Form_Arrival($mySession->property_id);
                    else
                        $myform = new Form_Arrival();
                    $this->view->step = '9';
                    $this->view->myform = $myform;

                    $Arrival = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->arrival = $Arrival[0]['arrival_instruction'];
                    $this->view->arrival1 = $Arrival[0]['arrival_instruction1'];
                    $this->view->arrival2 = $Arrival[0]['arrival_instruction2'];
                    if ($this->getRequest()->isPost())
                        $mySession->step = '9';
                    break;

                case '9': $this->view->step = '10';
                    $dataUpdate = array();
                    $dataUpdate['status'] = '2';
                    $condition = " id = " . $mySession->property_id;
                    $db->modify(PROPERTY, $dataUpdate, $condition);

                    break;
            } //switch case ends		


            /* code used for step 2 */
            if ($this->getRequest()->isPost() && $_REQUEST['copy_ppty'] == "")
            {
                if ($_REQUEST['next'] != "")
                    $mySession->step = '2';
            }

            if ($this->getRequest()->isPost() && $_REQUEST['copy_ppty'] != "")
            {

                $chkQuery = $db->runQuery("select * from " . PROPERTY . " where propertycode = '" . trim($_REQUEST['copy_ppty']) . "' ");

                if ($chkQuery != "" && count($chkQuery) > 0)
                {
                    $param1 = $_REQUEST['pricing'];
                    $param2 = $_REQUEST['available'];
                    $param3 = $_REQUEST['offers'];
                    $param4 = $_REQUEST['images'];

                    $obj = new Propertyintro();
                    $obj->copyProperty($_REQUEST['copy_ppty'], $param1, $param2, $param3, $param4);
                }
                else
                    $mySession->errorMsg = "Please enter proper property code in the box";


                $this->_redirect("myaccount/addproperty");
            }





            /*             * * checking that step 2 is there then fetch property specification from the table * */


            $this->view->myform = $myform;
            $this->view->varsuccess = $varsuccess;


            /*             * *  Image fetch from database  ** */
            /*             * *                            ** */
        }

        public function processcalAction()
        {

            global $mySession;
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $mySession->step = '6';

                $ppty_id = $this->getRequest()->getParam("ppty");
                if ($ppty_id != "")
                    $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
                else
                    $this->_redirect("myaccount/addproperty");
            }
        }

        public function processphotoAction()
        {
            global $mySession;
            $db = new Db();
            if ($this->getRequest()->isPost())
            {

                if ($_REQUEST['order_image'] == "")
                    $mySession->errorMsg = "Images not chosen";
                else
                {
                    $sort_images = explode('&', $_REQUEST['order_image']);
                    //pr($sort_images);

                    for ($i = 0; $i < count($sort_images); $i++)
                        $sort_image[] = explode('=', $sort_images[$i]);
                    //prd($sort_image);
                    //update the order of records
                    $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $mySession->property_id . "' ");

                    $dataUpdate = array();
                    $i = 0;

                    foreach ($imgArr as $key => $values)
                    {
                        $temp[$i] = $sort_image[$i][1];

                        $tmp = "blck" . $i;
                        $dataUpdate['property_id'][$tmp] = $values['property_id'];
                        $dataUpdate['image_name'][$tmp] = $values['image_name'];
                        $dataUpdate['image_title'][$tmp] = $values['image_title'];
                        $i++;
                    }




                    $conditionUpdate = " property_id = " . $mySession->property_id;
                    //pr($temp);
                    //prd($dataUpdate);
                    //echo $i;
                    //prd($dataUpdate);
                    //prd($temp);
                    $db->delete(GALLERY, $conditionUpdate);

                    for ($k = 0; $k < $i; $k++)
                    {
                        $pos = $temp[$k];
                        $dataInsert['property_id'] = $mySession->property_id;
                        $dataInsert['image_name'] = $dataUpdate['image_name'][$pos];
                        $dataInsert['image_title'] = $dataUpdate['image_title'][$pos];
                        $db->save(GALLERY, $dataInsert);
                    }

                    //code for changing the active status 
                    $data_status['status_5'] = '1';
                    $condition = "id=" . $mySession->property_id;
                    $db->modify(PROPERTY, $data_status, $condition);


                    $mySession->step = '5';
                }
            }
            $ppty_id = $this->getRequest()->getParam("ppty");
            if ($ppty_id != "")
                $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
            else
                $this->_redirect("myaccount/addproperty");
        }

        public function floorplanAction()
        {
            global $mySession;
            $db = new Db();
            $flag_status = 0;



            $pptyId = $this->getRequest()->getParam("ppty");
            $data = $_FILES['floor_plan']['name'];
            $extnsn = explode(".", $data);
            $extnsns = array_pop($extnsn);

            $allowed_extnsn = explode(",", FLOORPLAN_EXTNSN);


            if (!in_array($extnsns, $allowed_extnsn))
            {
                $msg['error'] = "Only image or pdf uploadable";
                //$mySession->errorMsg = "Only image or pdf uploadable";
            }
            else
            {

                if ($_FILES['floor_plan']['name'] != "")
                {
                    $chkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' and floor_plan != '' ");

                    if ($chkQuery != "" && count($chkQuery) > 0)
                    {
                        @unlink(SITE_ROOT . "images/floorplan/" . $chkQuery[0]['floor_plan']);
                        $flag_status = 1;
                    }
                    copy($_FILES['floor_plan']['tmp_name'], SITE_ROOT . "images/floorplan/" . $_FILES['floor_plan']['name']);
                    $imageNewName = time() . "_" . $_FILES['floor_plan']['name'];
                    @rename(SITE_ROOT . 'images/floorplan/' . $_FILES['floor_plan']['name'], SITE_ROOT . 'images/floorplan/' . $imageNewName);
                    $data_update['floor_plan'] = $imageNewName;
                    $condition = "id = " . $pptyId;
                    $db->modify(PROPERTY, $data_update, $condition);
                    if (!$flag_status)
                        $mySession->sucessMsg = "Floorplan uploaded sucessfully";
                    else
                        $mySession->sucessMsg = "Floorplan uploaded and modified sucessfully";
                }
                $mySession->sucessMsg = "Floorplan uploaded sucessfully";

                $ppty_id = $this->getRequest()->getParam("ppty");

                $msg['success'] = '1';
                /* if($ppty_id != "")
                  $this->_redirect("myaccount/editproperty/ppty/".$ppty_id);
                  else
                  $this->_redirect("myaccount/addproperty"); */
            }
            echo json_encode($msg);

            exit;
        }

        public function processrentalAction()
        {
            global $mySession;
            $db = new Db();
            if ($this->getRequest()->isPost() && isset($_REQUEST['step']) && $_REQUEST['step'] == '7')
            {
                if ($_REQUEST['rental_ques'] != "")
                {
                    $dataForm = array();
                    $dataForm['rental_ques'] = $_REQUEST['rental_ques'];
                    $condition = "id = " . $mySession->property_id;
                    $db->modify(PROPERTY, $dataForm, $condition);
                }
                $mySession->step = '7';
            }

            $ppty_id = $this->getRequest()->getParam("ppty");
            if ($ppty_id != "")
                $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
            else
                $this->_redirect("myaccount/addproperty");
        }

        public function processpageAction()
        {
            global $mySession;
            $db = new Db();
            /*  echo $mySession->step;
              exit; */

            $pptyId = $this->getRequest()->getParam("ppty");
            $stepId = $this->getRequest()->getParam("step");


            $mySession->step = $stepId - 1;


            switch ($mySession->step)
            {
                case '0': $myform = new Form_Ownproperty();
                    if (isset($mySession->property_id))
                    {
                        $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                        $this->view->rating = $ratingArr[0]['star_rating'];
                    }
                    break;
                case '1': if ($stepId != "")
                        $myform = new Form_Propertyspec($mySession->property_id);
                    elseif ($mySession->property_id != "")
                        $myform = new Form_Propertyspec($mySession->property_id);
                    else
                        $myform = new Form_Propertyspec();
                    break;

                case '2': if ($stepId != "")
                        $myform = new Form_Amenity($mySession->property_id);
                    elseif ($mySession->property_id != "")
                        $myform = new Form_Amenity($mySession->property_id);
                    else
                        $myform = new Form_Amenity();
                    break;
                case '3': if ($stepId != "")
                        $myform = new Form_Location($mySession->property_id);
                    elseif ($mySession->property_id != "")
                        $myform = new Form_Location($mySession->property_id);
                    else
                        $myform = new Form_Location();
                    break;
                case '7': $mySession->step = '8';
                    $ppty_id = $this->getRequest()->getParam("ppty");
                    if ($ppty_id != "")
                        $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
                    else
                        $this->_redirect("myaccount/addproperty");

                    $myform = new Form_Oreview();
                    break;
                case '8': $myform = new Form_Arrival();
                    break;
            }


            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();

                    $myObj = new Propertyintro();

                    $dataForm['rating'] = $_REQUEST['rating'];

                    if ($mySession->property_id == "")
                        $mySession->property_id = $pptyId;

                    $Result = $myObj->savePropertyintro($dataForm, $mySession->property_id);
                    if ($Result > 0)
                    {
                        $mySession->sucessMsg = "Successfully Saved";
                        $varsuccess = 1;
                    }
                    else
                    {
                        $mySession->errorMsg = "Title you entered for the property already exists";
                        $this->view->myform = $myform;
                        //$this->render("addproperty");
                    }
                }
                else
                {
                    if ($step == '1')
                        $mySession->errorMsg = "Instructions File not valid (upload pdf or doc)";
                    else
                        $mySession->errorMsg = "Fill up the details first";

                    $this->view->myform = $myform;
                    $this->render("addproperty");
                }
            }

            $ppty_id = $this->getRequest()->getParam("ppty");
            if ($ppty_id != "")
                $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
            else
                $this->_redirect("myaccount/addproperty");
        }

        public function uploadAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();


            $pptyId = $this->getRequest()->getParam("pptyId");

            if ($pptyId != "")
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $mySession->property_id . "' ");
            else
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $pptyId . "' ");

            //echo $_REQUEST['value']; exit;
            if (count($imgArr) <= 24)
            {

                $data = $_REQUEST['name'];

                $spacetrim = str_replace(" ", "_", $_REQUEST['name']);


                $randomName = time() . "_" . $spacetrim;

                if (copy(SITE_ROOT . "ankit_ups/" . $data, SITE_ROOT . "images/property/" . $randomName))
                {

                    unlink(SITE_ROOT . "ankit_ups/" . $data);

                    // process of saving in database //
                    $dataForm['step'] = '5';
                    $dataForm['image'] = $randomName;

                    if ($pptyId != "")
                        $dataForm['property_id'] = $pptyId;
                    else
                        $dataForm['property_id'] = $mySession->property_id;
                    //$myObj = new Propertyintro();
                    //$Result = $myObj->savePropertyintro($dataForm, $mySession->property_id);
                    //check whether the image uploaded is the first one

                    $orderQuery = $db->runQuery("select count(*) as _counter from " . GALLERY . " where property_id = '" . ($pptyId != "" ? $pptyId : $mySession->property_id) . "' ");
                    $resultQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . ($pptyId != "" ? $pptyId : $mySession->property_id) . "' ");


                    $data_update = array();

                    if ($orderQuery[0]['_counter'] == "0")
                        $data_update['image_title'] = $resultQuery[0]['property_title'];


                    $data_update['property_id'] = $dataForm['property_id'];
                    $data_update['image_name'] = $dataForm['image'];
                    $db->save(GALLERY, $data_update);


                    if ($orderQuery[0]['_counter'] >= 5)
                    {
                        $data_update1['status_5'] = '1';
                        $condition = "id=" . $dataForm['property_id'];
                        $db->modify(PROPERTY, $data_update1, $condition);
                    }
                    else
                    {
                        $data_update1['status_5'] = '0';
                        $condition = "id=" . $dataForm['property_id'];
                        $db->modify(PROPERTY, $data_update1, $condition);
                    }


                    echo $randomName . ":uploaded successfully";
                }
                else
                {
                    // Show an error message should something go wrong.
                    echo "Something went wrong. Check that the file isn't corrupted";
                }
            }
            else
            {
                echo "Maximum limit reached";
            }
            exit;
        }

        public function uploadedAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $this->getRequest()->getParam("pptyId");

            if ($pptyId != "")
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $pptyId . "' ");
            else
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $mySession->property_id . "' ");

            $str = "";
            if ($imgArr != "" && count($imgArr) > 0)
            {
                foreach ($imgArr as $values)
                {
                    $str .= $values['gallery_id'] . "|" . $values['image_name'] . "|" . $values['image_title'] . "+++";
                }
                echo $str = substr($str, 0, strlen($str) - 3);
            }
            exit;
        }

        public function deleteimgAction()
        {
            global $mySession;
            $db = new Db();

            $chkQuery = $db->runQuery("select * from " . GALLERY . " where gallery_id = '" . $_REQUEST['Id'] . "' ");

            if (count($chkQuery) > 0) //if any image found then follow below operation
            {

                @unlink(SITE_ROOT . "images/property/" . $chkQuery[0]['image_name']);
                $condition = " gallery_id = " . $_REQUEST['Id'];
                $db->delete(GALLERY, $condition);
                exit("s");
            }
            else
                exit("f");
        }

        public function experimentAction()
        {
            global $mySession;
            $db = new Db();

            $mySession->property_id = 44;
            $mySession->step = '4';

            if ($this->getRequest()->isPost())
            {

                $sort_images = explode('&', $_REQUEST['order_image']);
                //pr($sort_images);
                for ($i = 0; $i < count($sort_images); $i++)
                    $sort_image[] = explode('=', $sort_images[$i]);


                //prd($sort_image);
                //update the order of records
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $mySession->property_id . "' ");

                $dataUpdate = array();
                $i = 0;
                foreach ($imgArr as $key => $values)
                {
                    $temp[$i] = $sort_image[$i][1];
                    $dataUpdate['property_id'][$temp[$i]] = $values['property_id'];
                    $dataUpdate['image_name'][$temp[$i]] = $values['image_name'];
                    $i++;
                }


                $conditionUpdate = " property_id = " . $mySession->property_id;
                //pr($temp);
                //prd($dataUpdate);
                //echo $i;
                //prd($dataUpdate);
                //prd($temp);
                $db->delete(GALLERY, $conditionUpdate);

                for ($k = 0; $k < $i; $k++)
                {
                    $pos = $temp[$k];
                    $dataInsert['property_id'] = $mySession->property_id;
                    $dataInsert['image_name'] = $dataUpdate['image_name'][$pos];
                    $db->save(GALLERY, $dataInsert);
                }
            }
        }

        public function sortimagesAction()
        {
            global $mySession;
            $db = new Db();
            //$mySession->property_id = 40;
            //$mySession->step = '4';
            $sort_images = explode('&', $_REQUEST['Id']);
            //pr($sort_images);
            for ($i = 0; $i < count($sort_images); $i++)
                $sort_image[] = explode('=', $sort_images[$i]);


            //prd($sort_image);
            //update the order of records
            $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $mySession->property_id . "' ");

            $dataUpdate = array();
            $i = 0;
            foreach ($imgArr as $key => $values)
            {
                $dataUpdate['property_id'][$i] = $values['property_id'];
                $dataUpdate['image_name'][$i] = $values['image_name'];


                $temp[$i] = $sort_image[$i][1];
                $i++;
            }


            $conditionUpdate = " property_id = " . $mySession->property_id;

            //echo $i;
            //prd($dataUpdate);
            //prd($temp);
            $db->delete(GALLERY, $conditionUpdate);

            for ($k = 0; $k < $i; $k++)
            {
                $pos = $temp[$k];
                $dataInsert['property_id'] = $mySession->property_id;
                $dataInsert['image_name'] = $dataUpdate['image_name'][$pos];
                $db->save(GALLERY, $dataInsert);
            }

            exit;
        }

        // temporary made function//

        public function updatecalendarAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Cal();
            $this->view->myform = $myform;

            $property_id = $this->getRequest()->getParam("ppty");
            if ($property_id != "")
            {
                $mySession->property_id = $property_id;
            }

            $next = $this->getRequest()->getParam("cal");
            if ($next != "")
                $this->view->nexts = $next;
            else
                $this->view->nexts = 0;


            $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $mySession->property_id . "' ");

            $this->view->calArr = $calArr;

            //passing default value of calendar to view
            $pptyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
            $this->view->cal_default = $pptyArr[0]['cal_default'];
        }

        public function savecalendarstatAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("ppty");
            /* echo $_REQUEST['Datef'];
              echo $_REQUEST['Datet'];
              echo "<br>";
             */

            $ddt = explode("/", $_REQUEST['Datef']);
            $ddt1 = explode("/", $_REQUEST['Datet']);

            $start_date = date('Y-m-d', strtotime($ddt[0] . "-" . $ddt[1] . "-" . $ddt[2]));
            $end_date = date('Y-m-d', strtotime($ddt1[0] . "-" . $ddt1[1] . "-" . $ddt1[2]));

            $calendar_flag = 0;  //condition for checking
            $calendar_flag_total = 0;
            //query for checking earlier records related to calendar

            if ($pptyId != "")
                $chkCalData = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $pptyId . "' ");
            else
                $chkCalData = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $mySession->property_id . "' ");

            if ($chkCalData != "" && count($chkCalData) > 0)
            {
                foreach ($chkCalData as $values)
                {
                    //condition var to check conflict
                    //condition for  |'| (!')
                    if ($start_date > $values['date_from'] && $start_date < $values['date_to'])//checking for start date	
                    {
                        $calendar_flag = 0;

                        if ($pptyId != "")
                            $dataForm['property_id'] = $pptyId;
                        else
                            $dataForm['property_id'] = $mySession->property_id;

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

                        if ($pptyId != "")
                            $dataForm['property_id'] = $pptyId;
                        else
                            $dataForm['property_id'] = $mySession->property_id;

                        $dataForm['date_from'] = $start_date;
                        $dataForm['date_to'] = $end_date;
                        $dataForm['cal_status'] = $_REQUEST['Status'];
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
                        if ($pptyId != "")
                            $dataForm['property_id'] = $pptyId;
                        else
                            $dataForm['property_id'] = $mySession->property_id;

                        $dataForm['date_to'] = $end_date;
                        $dataForm['date_from'] = $start_date;
                        $dataForm['cal_status'] = $_REQUEST['Status'];
                        $db->save(CAL_AVAIL, $dataForm);
                        $calendar_flag_total = 1;
                    }


                    //conditon for checking if there is no conflicts on the date
                    if ($calendar_flag_total == 1)
                        exit;
                }

                if ($calendar_flag_total == 0)
                {
                    //if($start_date <= $values['date_from'] && $end_date >= $values['date_to'])//checking for duplicate 	
                    {
                        if ($pptyId != "")
                            $condition = " date_from >= '" . $start_date . "' and date_to  <=  '" . $end_date . "'  and  property_id = '" . $pptyId . "' ";
                        else
                            $condition = " date_from >= '" . $start_date . "' and date_to  <=  '" . $end_date . "'  and  property_id = '" . $mySession->property_id . "' ";
                        $db->delete(CAL_AVAIL, $condition);
                    }
                    $dataForm = array();
                    if ($pptyId != "")
                        $dataForm['property_id'] = $pptyId;
                    else
                        $dataForm['property_id'] = $mySession->property_id;
                    $dataForm['date_to'] = $end_date;
                    $dataForm['date_from'] = $start_date;
                    $dataForm['cal_status'] = $_REQUEST['Status'];
                    $db->save(CAL_AVAIL, $dataForm);
                }
            }
            else
            {
                if ($pptyId != "")
                    $dataForm['property_id'] = $pptyId;
                else
                    $dataForm['property_id'] = $mySession->property_id;

                $dataForm['date_from'] = $start_date; //date('Y-m-d',strtotime($_REQUEST['Datef']));
                $dataForm['date_to'] = $end_date; //date('Y-m-d',strtotime($_REQUEST['Datet']));
                $dataForm['cal_status'] = $_REQUEST['Status'];
                $db->save(CAL_AVAIL, $dataForm);
            }
            exit;
        }

        /*         * * function for set session variable only*** */

        public function setsessionAction()
        {
            global $mySession;
            $db = new Db();
            $pId = $this->getRequest()->getParam('pId');
            echo $mySession->ppty_no = $pId;
            $mySession->step = '0';
            exit;
        }

        /*         * temporary page * */

        public function gmapAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Location();
            $this->view->myform = $myform;
        }

        public function setcaptionAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();
            $adminId = $this->getRequest()->getParam("id");
            $this->view->sucessfull = 0;
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $data_update['image_title'] = $_REQUEST['caption'];
                $condition = "gallery_id=" . $adminId;
                $db->modify(GALLERY, $data_update, $condition);
                $this->view->sucessfull = 1;
            }
        }

        //new function for rental rates and special offers

        public function rentalAction()
        {
            global $mySession;
            $db = new Db();
            $mySession->property_id = '60';

            $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id where so.property_id = '" . $mySession->property_id . "' 
								   union
								   select * from " . SPCL_OFFER_TYPES . "  as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id");

            $this->view->offerArr = $offerArr;
        }

        //ajax function for step 7 (rental rates) - for getting the rates list

        public function getratesAction()
        {
            global $mySession;
            $db = new Db();
            $rate_string = "";

            $pptyId = $this->getRequest()->getParam("ppty");

            $rateData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . ($pptyId ? $pptyId : $mySession->property_id) . "' order by date_from asc ");

            if ($rateData != "" && count($rateData) > 0)
            {
                foreach ($rateData as $values)
                {
                    $rate_string .= date('d-m-Y', strtotime($values['date_from'])) . "," . date('d-m-Y', strtotime($values['date_to'])) . "," . $values['nights'] . "," . $values['prate'] . "," . $values['id'] . "|";
                }
                echo $rate_string = substr($rate_string, 0, strlen($rate_string) - 1);
            }
            else
            {
                exit(0);
            }

            exit;
        }

        //ajax function for step 7 (rental rates) - for saving the rates list

        public function setratesAction()
        {

            global $mySession;
            $db = new Db();
            $rate_string = "";
            if ($_REQUEST['Date_f'] != "")
            {
                /* $dataForm['date_from'] = date('Y-m-d', strtotime($_REQUEST['Date_f']));
                  $dataForm['date_to'] = date('Y-m-d', strtotime($_REQUEST['Date_t']));
                  $dataForm["property_id"] = $mySession->property_id;
                  $db->save(CAL_RATE,$dataForm);

                  //code to change the status of step7
                  $status_data['status_7'] = '1';
                  $condition = "id=".$mySession->property_id;
                  $db->modify(PROPERTY,$status_data,$condition); */

                /* $dataForm['nights'] = $_REQUEST['Nights']; */



                $tmp = explode(".", $_REQUEST['Rate']);


                $ddt = explode("/", $_REQUEST['Date_f']);
                $ddt1 = explode("/", $_REQUEST['Date_t']);

                $start_date = date('Y-m-d', strtotime($ddt[0] . "-" . $ddt[1] . "-" . $ddt[2]));
                $end_date = date('Y-m-d', strtotime($ddt1[0] . "-" . $ddt1[1] . "-" . $ddt1[2]));

                /* 			$start_date = date('Y-m-d', strtotime($_REQUEST['Date_f']));
                  $end_date = date('Y-m-d', strtotime($_REQUEST['Date_t'])); */

                $chkCalData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $mySession->property_id . "' ");



                $flag_check = 0;

                foreach ($chkCalData as $val)
                {
                    if ($start_date <= date('Y-m-d', strtotime($val['date_from'])) && $end_date >= date('Y-m-d', strtotime($val['date_to'])))
                    {

                        $flag_check = 1;
                        $condition = "id=" . $val['id'];
                        $db->delete(CAL_RATE, $condition);
                    }

                    /* 	if($start_date > date('Y-m-d',strtotime($val['date_from'])))
                      {
                      foreach($chkCalData as $tal)
                      {
                      if($val['id'] != $tal['id'])
                      {
                      if($end_date < $tal['date_to'])
                      {

                      $condition = "id=".$val['id'];
                      $db->delete(CAL_RATE,$condtion);

                      $condition = "id=".$tal['id'];
                      $db->delete(CAL_RATE,$condtion);

                      break;
                      }

                      }
                      }

                      }
                      if($end_date < date('Y-m-d',strtotime($val['date_to'])))
                      {

                      $flag_smart = 0;

                      foreach($chkCalData as $tal)
                      {
                      if($val['id'] != $tal['id'])
                      {
                      if($start_date > $tal['date_from'])
                      {


                      $condition = "id=".$val['id'];
                      $db->delete(CAL_RATE,$condtion);

                      $condition = "id=".$tal['id'];
                      $db->delete(CAL_RATE,$condtion);

                      break;
                      }

                      }
                      }



                      }
                     */
                }


                /* if($flag_check == 1)
                  {
                  $dataForm['property_id'] = $mySession->property_id;
                  $dataForm['date_from'] = $start_date;
                  $dataForm['date_to'] = $end_date;
                  $dataForm['prate'] = $tmp[0];
                  $dataForm['nights'] = $_REQUEST['Nights'];
                  $db->save(CAL_RATE,$dataForm);
                  } */



                //exit;

                $chkCalData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $mySession->property_id . "' ");

                $calendar_flag = 1;
                $flag_check = 1;
                if ($chkCalData != "" && count($chkCalData) > 0)
                {

                    foreach ($chkCalData as $values)
                    {

                        //condition var to check conflict
                        //condition for  |'| (!')

                        if ($start_date >= $values['date_from'] && $start_date <= $values['date_to'])//checking for start date	
                        {
                            $calendar_flag = 0;


                            $dataForm = array();
                            $dataForm['property_id'] = $mySession->property_id;
                            $dataForm['date_from'] = $values['date_from'];
                            $dataForm['date_to'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($start_date)), date('d', strtotime($start_date)) - 1, date('Y', strtotime($start_date))));
                            $dataForm['prate'] = $values['prate'];
                            $dataForm['nights'] = $values['nights'];

                            $db->save(CAL_RATE, $dataForm);

                            //(child)condition for |''|
                            if ($end_date >= $values['date_from'] && $end_date <= $values['date_to'])//checking for start date	
                            {
                                //$dataUpdate['property_id'] = $mySession->property_id;
                                $dataUpdate = array();
                                $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                                $condition = 'id=' . $values['id'];
                                $db->modify(CAL_RATE, $dataUpdate, $condition);

                                $calendar_flag = 1;  //condition for checking
                            }

                            //saving ''
                            $dataForm = array();

                            $dataForm['property_id'] = $mySession->property_id;
                            $dataForm['date_from'] = $start_date;
                            $dataForm['date_to'] = $end_date;
                            $dataForm['nights'] = $_REQUEST['Nights'];
                            $dataForm['prate'] = $tmp[0];
                            $db->save(CAL_RATE, $dataForm);

                            $calendar_flag_total = 1;


                            $calendar_flag_total1 = 2;
                            $calendar_flag_total1_cond = $values['id'];
                            $flag_check = 0;



                            if ($calendar_flag != 1)
                            {
                                foreach ($chkCalData as $calues)
                                {

                                    if ($end_date >= $calues['date_from'] && $end_date <= $calues['date_to'])
                                    {

                                        $dataUpdate = array();
                                        //updating  when  '|'| => ''||
                                        $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                                        $condition = 'id=' . $calues['id'];
                                        $db->modify(CAL_RATE, $dataUpdate, $condition);

                                        $condition = 'id=' . $calendar_flag_total1_cond;
                                        $db->delete(CAL_RATE, $condition);

                                        exit;
                                    }
                                }
                            }
                        }

                        //condition for (!') |'|   [! |''|]
                        if ($end_date >= $values['date_from'] && $end_date <= $values['date_to'] && $calendar_flag == 0)
                        {
                            $dataUpdate = array();
                            //updating  when  '|'| => ''||
                            $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                            $condition = 'id=' . $values['id'];
                            $db->modify(CAL_RATE, $dataUpdate, $condition);



                            $dataForm = array();
                            //saving when '|'| =>  ''||
                            $dataForm['property_id'] = $mySession->property_id;
                            $dataForm['date_to'] = $end_date;
                            $dataForm['date_from'] = $start_date;
                            $dataForm['nights'] = $_REQUEST['Nights'];
                            $dataForm['prate'] = $tmp[0];
                            $db->save(CAL_RATE, $dataForm);

                            if ($calendar_flag_total != 1)
                            /* $calendar_flag_total1 = 2;
                              else */
                                $calendar_flag_total1 = 0;

                            $calendar_flag_total = 1;
                            $calendar_flag_total1_cond = $values['id'];
                        }
                        else if ($end_date >= $values['date_from'] && $end_date <= $values['date_to'] && $flag_check != 0)
                        {
                            $dataUpdate = array();
                            //updating  when  '|'| => ''||
                            $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                            $condition = 'id=' . $values['id'];
                            $db->modify(CAL_RATE, $dataUpdate, $condition);



                            $dataForm = array();
                            //saving when '|'| =>  ''||
                            $dataForm['property_id'] = $mySession->property_id;
                            $dataForm['date_to'] = $end_date;
                            $dataForm['date_from'] = $start_date;
                            $dataForm['nights'] = $_REQUEST['Nights'];
                            $dataForm['prate'] = $tmp[0];
                            $db->save(CAL_RATE, $dataForm);

                            if ($calendar_flag_total != 1)
                                $calendar_flag_total1 = 2;
                            else
                                $calendar_flag_total1 = 0;

                            $calendar_flag_total = 1;
                            $calendar_flag_total1_cond = $values['id'];
                        }

                        //conditon for checking if there is no conflicts on the date
                        /* if($calendar_flag_total == 1)
                          exit; */
                    }


                    if ($calendar_flag_total == 0)
                    {
                        /* if($start_date <= $values['date_from'] && $end_date >= $values['date_to'])//checking for duplicate 	
                          {
                          $condition = "id=".$values['id'];
                          $db->delete(CAL_RATE,$condition);
                          } */

                        $dataForm = array();
                        $dataForm['property_id'] = $mySession->property_id;
                        $dataForm['date_to'] = $end_date;
                        $dataForm['date_from'] = $start_date;
                        $dataForm['nights'] = $_REQUEST['Nights'];
                        $dataForm['prate'] = $tmp[0];
                        $db->save(CAL_RATE, $dataForm);
                    }

                    if ($calendar_flag_total1 == 2 && $calendar_flag == 0)
                    {
                        $cond = "id=" . $calendar_flag_total1_cond;
                        $db->delete(CAL_RATE, $cond);
                    }
                }
                else
                {
                    $dataForm['property_id'] = $mySession->property_id;
                    $dataForm['date_from'] = date('Y-m-d', strtotime($start_date));
                    $dataForm['date_to'] = date('Y-m-d', strtotime($end_date));
                    $dataForm['nights'] = $_REQUEST['Nights'];
                    $dataForm['prate'] = $tmp[0];
                    $db->save(CAL_RATE, $dataForm);
                }
            }
            exit;
        }

        //ajax function for step 7 (rental rates) - for deleting the rates list

        public function deleterentalrateAction()
        {
            global $mySession;
            $db = new Db();
            $adminId = $this->getRequest()->getParam("id");

            if ($adminId > 0)
            {
                $condition = "id=" . $adminId;
                $db->delete(CAL_RATE, $condition);
                exit("1");
            }

            //code for changing the status of the step 7
            $chkStat = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $mySession->property_id . "' ");

            if (count($chkStat) == 0)
            {
                $status_data['status_7'] = '0';
                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_data, $condition);
            }

            exit;
        }

        //ajax function for step 7 (rental rates) - for getting extras list
        public function getextrasAction()
        {
            global $mySession;
            $db = new Db();
            $extra_string = "";
            $pptyId = $this->getRequest()->getParam("ppty");
            $extraData = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . ($pptyId ? $pptyId : $mySession->property_id) . "' ");
            if ($extraData != "" && count($extraData) > 0)
            {
                foreach ($extraData as $values)
                {
                    $extra_string .= $values['ename'] . "," . $values['eprice'] . "," . $values['etype'] . "," . $values['eid'] . "," . $values['stay_type'] . "|";
                }
                echo $extra_string = substr($extra_string, 0, strlen($extra_string) - 1);
            }
            else
            {
                exit(0);
            }

            exit;
        }

        //ajax function for step 7 (rental rates) - for saving extras list
        public function saveextrasAction()
        {
            global $mySession;
            $db = new Db();
            $rate_string = "";
            $pptyId = $this->getRequest()->getParam("ppty");
            if ($_REQUEST['extra_name'] != "")
            {
                $dataForm['ename'] = $_REQUEST['extra_name'];
                $tmp = explode(".", $_REQUEST['extra_price']);
                $dataForm['eprice'] = $tmp[0];
                $dataForm['etype'] = $_REQUEST['extra_type'];
                $dataForm['stay_type'] = $_REQUEST['stay_type'];
                $dataForm["property_id"] = $mySession->property_id;
                $db->save(EXTRAS, $dataForm);
            }
            exit;
        }

        //ajax function for step 7 (rental rates) - for deleting extras list
        public function deleteextrasAction()
        {
            global $mySession;
            $db = new Db();
            $adminId = $this->getRequest()->getParam("id");

            if ($adminId > 0)
            {
                $condition = "eid=" . $adminId;
                $db->delete(EXTRAS, $condition);
                exit("1");
            }
            exit;
        }

        public function saveoffersAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $this->getRequest()->getParam("pptyId");
            //check that if value exist already
            if ($pptyId == "")
                $chkQuery = $db->runQuery("select * from " . SPCL_OFFERS . " as so 
								   where so.property_id = '" . $mySession->property_id . "' 
								   and  so.spcl_offer_id = '" . $_REQUEST['Spcl_offer_id'] . "' ");
            else
                $chkQuery = $db->runQuery("select * from " . SPCL_OFFERS . " as so 
								   where so.property_id = '" . $pptyId . "' 
								   and  so.spcl_offer_id = '" . $_REQUEST['Spcl_offer_id'] . "' ");


            if ($chkQuery != "" && count($chkQuery) > 0)
            {
                //update
                //query for checking limit exceeding 3
                if ($pptyId == "")
                    $countQuery = $db->runQuery("select * from " . SPCL_OFFERS . "  where property_id = '" . $mySession->property_id . "' 
								   and  offer_id = '" . $chkQuery[0]['offer_id'] . "' ");
                else
                    $countQuery = $db->runQuery("select * from " . SPCL_OFFERS . "  where property_id = '" . $pptyId . "' 
								   and  offer_id = '" . $chkQuery[0]['offer_id'] . "' ");

                if ($_REQUEST['Valid_f'] != "")
                {

                    if ($chkQuery[0]['discount_offer'] == NULL && count($countQuery) <= 3)
                    {
                        $dataForm['offer_id'] = $chkQuery[0]['offer_id'];
                        if ($pptyId == "")
                            $dataForm["property_id"] = $mySession->property_id;
                        else
                            $dataForm["property_id"] = $pptyId;
                        $db->save(SPCL_OFFERS, $dataForm);
                    }
                    $dataForm = array();


                    $ddt = explode("/", $_REQUEST['Valid_f']);
                    $ddt1 = explode("/", $_REQUEST['Valid_t']);
                    $ddt2 = explode("/", $_REQUEST['Book_by']);

                    if (count($ddt) < 2)
                        $ddt = explode("-", $_REQUEST['Valid_f']);

                    if (count($ddt1) < 2)
                        $ddt1 = explode("-", $_REQUEST['Valid_t']);

                    if (count($ddt2) < 2)
                        $ddt2 = explode("-", $_REQUEST['Book_by']);


                    $start_date = date('Y-m-d', strtotime($ddt[0] . "-" . $ddt[1] . "-" . $ddt[2]));
                    $end_date = date('Y-m-d', strtotime($ddt1[0] . "-" . $ddt1[1] . "-" . $ddt1[2]));
                    $book_date = date('Y-m-d', strtotime($ddt2[0] . "-" . $ddt2[1] . "-" . $ddt2[2]));

                    $dataForm['offer_id'] = $chkQuery[0]['offer_id'];
                    $dataForm['discount_offer'] = $_REQUEST['Discount'];
                    $dataForm['valid_from'] = $start_date;
                    $dataForm['valid_to'] = $end_date;
                    $dataForm['min_night'] = $_REQUEST['Nights'];
                    $dataForm['book_by'] = $book_date;
                    $dataForm['activate'] = '1';

                    if ($pptyId == "")
                        $condition = "property_id=" . $mySession->property_id . " and spcl_offer_id = " . $_REQUEST['Spcl_offer_id'];
                    else
                        $condition = "property_id=" . $pptyId . " and spcl_offer_id = " . $_REQUEST['Spcl_offer_id'];
                    $db->modify(SPCL_OFFERS, $dataForm, $condition);
                }
            }
            /* else
              {
              if($_REQUEST['Valid_f'] != "")
              {
              $dataForm['offer_id'] = $_REQUEST['Offer_id'];
              $dataForm['discount_offer'] = $_REQUEST['Discount'];
              $dataForm['valid_from'] = date('Y-m-d', strtotime($_REQUEST['Valid_f']));
              $dataForm['valid_to'] = date('Y-m-d', strtotime($_REQUEST['Valid_t']));
              $dataForm['min_night'] = $_REQUEST['Nights'];
              $dataForm['book_by'] = $_REQUEST['Book_by'];
              $dataForm['activate'] = '1';
              $dataForm["property_id"] = $mySession->property_id;

              $db->save(SPCL_OFFERS,$dataForm);

              $dataForm = array();
              $dataForm['offer_id'] = $_REQUEST['Offer_id'];
              $dataForm["property_id"] = $mySession->property_id;
              $db->save(SPCL_OFFERS,$dataForm);
              }

              } */
            exit;
        }

        public function deactivateoffersAction()
        {
            global $mySession;
            $db = new Db();
            $adminId = $this->getRequest()->getParam("id");
            $dataForm['activate'] = '0';
            $condition = "spcl_offer_id=" . $adminId;
            $db->modify(SPCL_OFFERS, $dataForm, $condition);
            exit;
        }

        // new temporary page for owner review

        public function reviewAction()
        {
            global $mySession;
            $db = new Db();

            $ppty_id = $this->getRequest()->getParam("ppty");

            if ($ppty_id != "")
                $mySession->property_id = $ppty_id;

            $myform = new Form_Oreview($mySession->property_id);
            //query for getting image

            if (!isset($mySession->reviewImage))
                $mySession->reviewImage = "no_owner_pic.jpg";

            $this->view->myform = $myform;
        }

        public function ownerimageuploadAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();

            $data = $_FILES['image']['name'];

            $extnsn = explode(".", $data);
            $extnsn = array_pop($extnsn);

            $allowed_extnsn = explode(",", IMAGE_EXTNSN);

            if (!in_array($extnsn, $allowed_extnsn))
            {

                $msg['error'] = "Only Images ";
            }
            else
            {


                $randomName = time() . "_" . $_FILES['image']['name'];


                if (move_uploaded_file($_FILES['image']['tmp_name'], SITE_ROOT . "images/profile/" . $randomName))
                {

                    $mySession->reviewImage = $randomName;

                    $msg['name'] = $randomName;

                    //echo "image uploaded sucessfully";
                }
                else
                {
                    $msg['error'] = "error in uploading image";
                }
            }
            echo json_encode($msg);
            exit;
        }

        //edit property
        public function editpropertyAction()
        {
            global $mySession;
            $db = new Db();

            $property_id = $this->getRequest()->getParam("ppty");


            $this->view->ppty_id = $property_id ? $property_id : $mySession->property_id;
            if (!isset($mySession->property_id))
            {
                $mySession->step = '0';
                $mySession->property_id = $property_id;
                $propertyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                $mySession->ppty_no = $propertyArr[0]['propertycode'];
            }

            if (isset($mySession->property_id))
            {

                if ($property_id != $mySession->property_id)
                    ;
                $mySession->property_id = $property_id;

                $statusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

                $this->view->status_2 = $status_step2 = $statusArr[0]['status_2'];
                $this->view->status_3 = $status_step3 = $statusArr[0]['status_3'];
                $this->view->status_4 = $status_step4 = $statusArr[0]['status_4'];
                $this->view->status_5 = $status_step5 = $statusArr[0]['status_5'];
                $this->view->status_6 = $status_step6 = $statusArr[0]['status_6'];
                $this->view->status_7 = $status_step7 = $statusArr[0]['status_7'];
                $this->view->status_8 = $status_step8 = $statusArr[0]['status_8'];
                $this->view->status_9 = $status_step9 = $statusArr[0]['status_9'];

                if ($status_step2 && $status_step3 && $status_step4 && $status_step5 && $status_step6 && $status_step7 && $status_step8 && $status_step9)
                {
                    if ($statusArr[0]['status'] != '3')
                    {
                        $status_data['status'] = '2';
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $status_data, $condition);
                    }
                }
            }

            $this->view->pId = $mySession->ppty_no;

            $stepId = $this->getRequest()->getParam("step");

            if ($stepId != "")
                $mySession->step = $stepId - 1;


            switch ($mySession->step)
            {
                case '0': $myform = new Form_Ownproperty($mySession->property_id);
                    $this->view->step = '1';
                    $step = '1';
                    if (isset($mySession->property_id))
                    {
                        $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                        $this->view->rating = $ratingArr[0]['star_rating'];
                    }

                    break;
                case '1': $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");
                    $this->view->specData = $specArr;

                    $myform = new Form_Propertyspec($mySession->property_id);
                    $this->view->step = '2';
                    $step = '2';

                    $mySession->step = '1';

                    $bathroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->no_bedrooms = $bathroomArr[0]['bedrooms'];

                    break;

                case '2': $amenityArr = $db->runQuery("select * from " . AMENITY_PAGE . " ");
                    $this->view->amenityData = $amenityArr;
                    $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
                    $this->view->amenityArr = $amenity;
                    $myform = new Form_Amenity($mySession->property_id);
                    $this->view->step = '3';
                    $step = '3';
                    break;

                case '3': $myform = new Form_Location($mySession->property_id);
                    $this->view->step = '4';
                    $step = '4';
                    break;

                case '4': $this->view->step = '5';
                    $Arr = $db->runQuery("select floor_plan from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->floorPlan = $Arr[0]['floor_plan'];
                    //$myform = new Form_Photo();
                    $step = '5';

                    //submit photo action
                    //submit photo action ends


                    break;

                case '5': $myform = new Form_Cal();
                    $this->view->myform = $myform;
                    $next = $this->getRequest()->getParam("cal");

                    if ($next != "")
                        $this->view->nexts = $next;
                    else
                        $this->view->nexts = 0;

                    $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $mySession->property_id . "' ");

                    $this->view->calArr = $calArr;

                    $this->view->step = '6';
                    $step = '6';

                    //passing default value of calendar to view
                    $pptyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");
                    $this->view->cal_default = $pptyArr[0]['cal_default'];


                    break;

                case '6': $this->view->step = '7';
                    $step = '7';

                    $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id where so.property_id = '" . $mySession->property_id . "' order by so.spcl_offer_id asc
								   ");


                    if (count($offerArr) == 0)
                    {
                        $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . "");
                        foreach ($offerArr as $values)
                        {
                            $dataForm['offer_id'] = $values['id'];
                            //$dataForm['date_from'] = date('Y-m-d', strtotime($_REQUEST['Date_f']));
                            //$dataForm['date_to'] = date('Y-m-d', strtotime($_REQUEST['Date_t']));
                            //$dataForm['nights'] = $_REQUEST['Nights'];
                            //$dataForm['prate'] = $_REQUEST['Rate'];
                            $dataForm["property_id"] = $mySession->property_id;
                            $db->save(SPCL_OFFERS, $dataForm);
                        }
                    }


                    $this->view->offerArr = $offerArr;
                    //echo $mySession->step; exit;
                    //rental question
                    $rentalQues = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

                    if ($rentalQues != "" && count($rentalQues) > 0)
                        $this->view->rental_ques = $rentalQues[0]['rental_ques'];


                    //currency data
                    $currData = $db->runQuery("select * from  " . CURRENCY . " order by currency_order asc ");
                    $this->view->currencyData = $currData;

                    //checking for currency
                    if ($rentalQues[0]['currency_code'] != NULL)
                    {
                        $this->view->currency_set = '1';
                        $this->view->currency_val = $rentalQues[0]['currency_code'];
                    }
                    else
                        $this->view->currency_set = '0';

                    break;

                case '7': $myform = new Form_Oreview($mySession->property_id);
                    //query for getting image
                    $this->view->step = '8';
                    $uData = $db->runQuery("select * from " . OWNER_REVIEW . " where property_id = '" . $mySession->property_id . "' ");


                    $this->view->myform = $myform;

                    if (!isset($mySession->reviewImage))
                        $mySession->reviewImage = "no_owner_pic.jpg";

                    $this->view->myform = $myform;


                    $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " as r
													inner join " . PROPERTY . " as p on p.id = r.property_id
													inner join " . USERS . " as u on u.user_id = p.user_id
													where r.property_id = '" . $mySession->property_id . "' and r.review_status = '1' order by r.review_id desc ");



                    $i = 0;
                    foreach ($reviewArr as $val)
                    {

                        if ($val['parent_id'] == 0)
                        {

                            $childArr = $db->runQuery("select * from " . OWNER_REVIEW . " where parent_id = '" . $val['review_id'] . "' ");

                            $reviewData[$i]['review_id'] = $val['review_id'];
                            $reviewData[$i]['uType'] = $val['uType'];
                            $reviewData[$i]['guest_name'] = $val['guest_name'];
                            $reviewData[$i]['owner_image'] = $val['guest_image'];
                            $reviewData[$i]['headline'] = $val['headline'];
                            $reviewData[$i]['review'] = $val['review'];
                            $reviewData[$i]['comment'] = $val['comment'];
                            $reviewData[$i]['location'] = $val['location'];
                            $reviewData[$i]['image'] = $val['image'];
                            $reviewData[$i]['review_date'] = $val['review_date'];
                            $reviewData[$i]['check_in'] = $val['check_in'];
                            $reviewData[$i]['rating'] = $val['rating'];

                            $k = 0;
                            foreach ($childArr as $val1)
                            {
                                $reviewData[$i]['child'][$k]['guest_name'] = $val1['guest_name'];
                                $reviewData[$i]['child'][$k]['owner_image'] = $val1['guest_image'];
                                $reviewData[$i]['child'][$k]['comment'] = $val1['comment'];
                                $reviewData[$i]['child'][$k]['review_date'] = $val1['review_date'];
                                $k++;
                            }
                        }
                        $i++;
                    }
                    //prd($reviewData);
                    $this->view->reviewArr = $reviewData;
                    break;

                case '8': if (isset($mySession->property_id))
                        $myform = new Form_Arrival($mySession->property_id);
                    else
                        $myform = new Form_Arrival();
                    $this->view->step = '9';
                    $this->view->myform = $myform;

                    $Arrival = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");


                    $this->view->arrival = $Arrival[0]['arrival_instruction'];
                    $this->view->arrival1 = $Arrival[0]['arrival_instruction1'];
                    $this->view->arrival2 = $Arrival[0]['arrival_instruction2'];

                    if ($this->getRequest()->isPost())
                        $mySession->step = '9';
                    break;

                case '9': $this->view->step = '10';
                    $mySession->activate = '1';
                    break;
            } //switch case ends		








            /*             * * checking that step 2 is there then fetch property specification from the table * */


            $this->view->myform = $myform;
            $this->view->varsuccess = $varsuccess;


            /*             * *  Image fetch from database  ** */
            /*             * *                            ** */
        }

        public function propertyportfolioAction()
        {
            global $mySession;
            $db = new Db();
            $limit = 10; //5 records per page

            $start = $this->getRequest()->getParam("start");
            if ($start == "")
            {
                $start = 1;
            }

            $starti = ($start - 1) * 10;

            $totalRecords = $db->runQuery("select * from " . PROPERTY . " as p
									  left join " . GALLERY . " as g on g.property_id = p.id	
									 where user_id = '" . $mySession->LoggedUserId . "' and status != '0' group by p.id ");

            $propertyArr = $db->runQuery("select * from " . PROPERTY . " as p
									  left join " . GALLERY . " as g on g.property_id = p.id	
									 where user_id = '" . $mySession->LoggedUserId . "' and status != '0' group by p.id order by p.id desc limit $starti,$limit");

            $this->view->propertyData = $propertyArr;
            $this->view->total = count($totalRecords);
            $this->view->start = $start;
            $this->view->now = APPLICATION_URL . "myaccount/propertyportfolio";
            $this->view->limit = $limit;
        }

        public function previewAction()
        {


            global $mySession;
            $db = new Db();
            $varsuccess = '0';
            $tab = $this->getRequest()->getParam("property");
            $ppty_id = $this->getRequest()->getParam("ppty");
            $this->view->ppty_id = $ppty_id;



            $propertyArr = $db->runQuery("select * from " . PROPERTY . " 
									  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
									  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
									  left join " . STATE . "  on " . STATE . ".state_id = " . PROPERTY . ".state_id
         							  left join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
									  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
									  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
									  left join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id 
									  where " . PROPERTY . ".id = '" . $ppty_id . "' ");





            //========== Fetching Meta Information ===========================//
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 4");
            $Title = str_replace('[PAGE_NAME]', $staticArr[0]['page_title'], $metaArr[0]['meta_title']);
            $Description = $metaArr[0]['meta_title'];
            $Description = str_replace('[BREADCRUMB]', $Breadcrumb, $Description);
            $Description = str_replace('[BED]', $propertyArr[0]['bedrooms'] > 1 ? $propertyArr[0]['bedrooms'] . ' beds' : $propertyArr[0]['bedrooms'] . ' bed', $Description);
            $Description = str_replace('[PROPERTY_TYPE]', $propertyArr[0]['ptyle_name'], $Description);
            $Description = str_replace('[PROPERTY_NO]', $propertyArr[0]['propertycode'], $Description);

            $this->view->headTitle($Title)->offsetUnset(0);
            $this->view->headMeta('description', $Description);

            //code to check tab


            switch ($tab)
            {
                case '':

                case 'overview': $this->view->tab = '1';
                    $this->view->property = "overview";
                    $this->view->ppty_tab1 = 'class="active"';

                    //AMENITIES	


                    $amenityData = $db->runQuery("select * from " . AMENITY . " as a inner join " . AMENITY_ANS . " as aa on a.amenity_id = aa.amenity_id where aa.property_id = '" . $ppty_id . "' and aa.amenity_value ='1' and a.amenity_status = '1' ");
                    $this->view->amenityData = $amenityData;

                    break;
                case 'specification': $this->view->tab = '2';
                    $this->view->property = "specification";
                    $this->view->ppty_tab2 = 'class="active"';


                    $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");

                    $category_temp = "";

                    $i = 0;
                    $t = 0;
                    $finalArr[0]['category'][0] = "";
                    $cat_counter = 0;
                    $bathroom_counter = 0;
                    $xcounter = 0;
                    $max = 0;
                    foreach ($specArr as $key => $value)
                    {

                        if ($finalArr[$cat_counter]['category'] != $value['cat_name'])
                        {
                            if ($i > 0)
                                $cat_counter++;

                            $finalArr[$cat_counter]['category'] = $value['cat_name'];

                            $t = 0;
                        }




                        $selectOptionArr = $db->runQuery("select * from " . SPEC_CHILD . "
																		 inner join " . SPEC_ANS . " on " . SPEC_ANS . ".answer = " . SPEC_CHILD . ".cid	
																		 where " . SPEC_ANS . ".spec_id = '" . $value['spec_id'] . "' and " . SPEC_ANS . ".property_id = '" . $ppty_id . "' ");


                        if ($value['spec_id'] == '22' || $value['spec_id'] == '23' || $value['spec_id'] == '24')
                        {


                            foreach ($selectOptionArr as $key1 => $value1)
                            {
                                $array1 = explode('|||', $value1['answer']);

                                $max = count($array1) > $max ? count($array1) : $max;

                                $x = 0;
                                foreach ($array1 as $keybath => $bath)
                                {
                                    /* 														echo "select ".SPEC_CHILD.".option from ".SPEC_CHILD." where cid = '".$bath."' "; exit; */
                                    $bathArr = $db->runQuery("select " . SPEC_CHILD . ".option from " . SPEC_CHILD . " where cid = '" . $bath . "' ");

                                    //pr($bathArr);
                                    /* if(count($bathArr) == 0)
                                      $bathroom[$keybath][] = "";
                                     */

                                    foreach ($bathArr as $ckey => $calue)
                                    {

                                        if ($value['spec_id'] == '24')
                                        {

                                            //echo $xcounter;
                                            $bathroom[$xcounter][] = $calue['option'];
                                        }
                                        else
                                        {

                                            $bathroom[$keybath][] = $calue['option'];
                                        }
                                    }
                                    $x++;
                                }


                                foreach ($array1 as $keybath => $bath)
                                {

                                    $finalArr[$cat_counter]['ques'][$keybath] = $value['spec_display'] . " " . ($keybath + 1);
                                }
                                $finalArr[$cat_counter]['answer'] = $bathroom;




                                $j++;


                                if ($value['spec_id'] == '24')
                                {

                                    $xcounter++;
                                }
                            }

                            $bathroom_counter++;
                        }
                        else
                        {
                            if ($max != 0)
                                $t = $t + $max;
                            $max = 0;

                            if ($value['spec_type'] == '2' || $value['spec_type'] == '3')
                            {

                                $selectOptionArr = $db->runQuery("select * from " . SPEC_ANS . "  where " . SPEC_ANS . ".spec_id = '" . $value['spec_id'] . "' and " . SPEC_ANS . ".property_id = '" . $ppty_id . "' ");
                                /* 			prd($selectOptionArr); */

                                if ($selectOptionArr[0]['answer'] != "")
                                {

                                    $j = 0;
                                    $finalArr[$cat_counter]['ques'][$t] = $value['spec_display'];
                                    $finalArr[$cat_counter]['ticklist'][$t] = '0';
                                    $finalArr[$cat_counter]['answer'][$t][0] = $selectOptionArr[0]['answer'];
                                    $selectOptionArr = array();
                                }
                                else
                                    $t--;
                            }
                            elseif ($value['preview_display'] == '1' || count($selectOptionArr) > 0)
                            {


                                $finalArr[$cat_counter]['ques'][$t] = $value['spec_display'];

                                if ($value['spec_type'] == '4')
                                    $finalArr[$cat_counter]['ticklist'][$t] = '1';
                                else
                                    $finalArr[$cat_counter]['ticklist'][$t] = '0';
                            }
                            else
                                $t--;

                            $j = 0;
                            if (count($selectOptionArr) > 0)
                                foreach ($selectOptionArr as $key1 => $value1)
                                {


                                    $finalArr[$cat_counter]['answer'][$t][$j] = $value1['option'];

                                    $j++;
                                }


                            $t++;
                        }

                        $i++;
                    }

                    /* prd($finalArr); */

                    /* $specArr = $db->runQuery("select * from ".SPECIFICATION." 
                      inner join ".SPEC_CHILD." on ".SPEC_CHILD.".spec_id = ".SPECIFICATION.".spec_id
                      inner join ".SPEC_ANS." on ".SPEC_ANS.".answer = ".SPEC_CHILD.".cid
                      where ".SPEC_ANS.".property_id = ".$ppty_id."
                      "); */
                    $this->view->specArr = $finalArr;
                    break;
                case 'location': $this->view->tab = '3';
                    $this->view->property = "location";
                    $this->view->ppty_tab3 = 'class="active"';
                    break;
                case 'availability': $this->view->tab = '4';
                    $this->view->property = "availability";
                    $this->view->cal_default = $propertyArr[0]['cal_default'];
                    $this->view->ppty_tab4 = 'class="active"';

                    $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $ppty_id . "' ");

                    $this->view->calArr = $calArr;



                    $next = $this->getRequest()->getParam("cal");
                    if ($next != "")
                        $this->view->nexts = $next;
                    else
                        $this->view->nexts = 0;

                    break;
                case 'rental': $this->view->tab = '5';
                    $this->view->property = "rental";
                    $this->view->ppty_tab5 = 'class="active"';

                    $option_extra = $db->runQuery("select ename, (select exchange_rate from " . CURRENCY . " where " . CURRENCY . ".currency_code = (select currency_code from " . PROPERTY . " where id = '" . $ppty_id . "' ))*eprice as eprice,etype,stay_type  from " . EXTRAS . " where property_id = '" . $ppty_id . "' ");
                    $this->view->option_extra = $option_extra;

                    $calArr = $db->runQuery("select (select exchange_rate from " . CURRENCY . " where " . CURRENCY . ".currency_code = (select currency_code from " . PROPERTY . " where id = '" . $ppty_id . "') )*prate as prate,
														     nights,date_to,date_from from " . CAL_RATE . "  
															 where property_id = " . $ppty_id . "  order by date_from asc ");
                    $this->view->calData = $calArr;


                    $spclArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS from " . SPCL_OFFERS . " 
															  inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
															  where " . SPCL_OFFERS . ".property_id = '" . $ppty_id . "' 
															  and " . SPCL_OFFERS . ".activate = '1'  and " . SPCL_OFFERS . ".book_by >= curdate() ");


                    $this->view->spclData = $spclArr;



                    break;
                case 'gallery': $this->view->tab = '6';
                    $this->view->property = "gallery";
                    $this->view->ppty_tab6 = 'class="active"';
                    $galleryArr = $db->runQuery("select * from " . GALLERY . "  where property_id = " . $ppty_id);
                    $this->view->galleryArr = $galleryArr;
                    break;
                case 'reviews': $this->view->tab = '7';
                    $this->view->property = "review";
                    $this->view->ppty_tab7 = 'class="active"';

                    $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " as r
																inner join " . PROPERTY . " as p on p.id = r.property_id
																inner join " . USERS . " as u on u.user_id = p.user_id
																where r.property_id = '" . $ppty_id . "' and r.review_status = '1' order by r.review_id desc ");

                    //prd($reviewArr);	

                    $i = 0;
                    foreach ($reviewArr as $val)
                    {




                        if ($val['parent_id'] == 0)
                        {

                            $childArr = $db->runQuery("select * from " . OWNER_REVIEW . " where parent_id = '" . $val['review_id'] . "' ");

                            $reviewData[$i]['review_id'] = $val['review_id'];
                            $reviewData[$i]['uType'] = $val['uType'];
                            $reviewData[$i]['guest_name'] = $val['guest_name'];
                            $reviewData[$i]['owner_image'] = $val['guest_image'];
                            $reviewData[$i]['headline'] = $val['headline'];
                            $reviewData[$i]['review'] = $val['review'];
                            $reviewData[$i]['comment'] = $val['comment'];
                            $reviewData[$i]['location'] = $val['location'];
                            $reviewData[$i]['image'] = $val['image'];
                            $reviewData[$i]['review_date'] = $val['review_date'];
                            $reviewData[$i]['check_in'] = $val['check_in'];
                            $reviewData[$i]['rating'] = $val['rating'];

                            $k = 0;
                            foreach ($childArr as $val1)
                            {

                                $reviewData[$i]['child'][$k]['guest_name'] = $val1['guest_name'];
                                $reviewData[$i]['child'][$k]['owner_image'] = $val1['guest_image'];
                                $reviewData[$i]['child'][$k]['comment'] = $val1['comment'];
                                $reviewData[$i]['child'][$k]['review_date'] = $val1['review_date'];
                                $k++;
                            }
                        }
                        $i++;
                    }

                    //prd($reviewData);
                    //code for finding that the owner has the same property

                    $this->view->reviewArr = $reviewData;


                    /** review form dissplay code ends * */
                    break;
                case 'question': $this->view->tab = '8';
                    $this->view->property = "question";
                    $this->view->ppty_tab8 = 'class="active"';
                    //contact us
                    $myform = new Form_Ocontact($ppty_id);
                    $this->view->myform = $myform;
                    break;
            }



            //rate query


            /* echo "select 
              case
              when date_from <= CURDATE() and date_to >= CURDATE() then prate as RATE,nights from ".CAL_RATE."
              when date_from <= CURDATE() then min(prate) as RATE,nights from ".CAL_RATE."
              else prate as RATE, nights from ".CAL_RATE." end
              where property_id = '".$ppty_id."' ";
              exit;
             */




            /* $rateArr = $db->runQuery(" SELECT *
              FROM (
              select prate as RATE,nights,id from ".CAL_RATE." where property_id = '".$ppty_id."' and date_from <= CURDATE() and date_to >= CURDATE()
              union
              select min(prate) as RATE,nights,id from ".CAL_RATE." where property_id = '".$ppty_id."'
              ) AS a1
              WHERE a1.RATE IS NOT NULL group by a1.id"); */



            $rateArr = $db->runQuery("select round(prate*" . CURRENCY . ".exchange_rate) as RATE,nights,prate," . PROPERTY . ".id from " . CAL_RATE . " 
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id
								  inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
		                          where " . CAL_RATE . ".property_id = '" . $ppty_id . "' and prate = (select min(prate) from " . CAL_RATE . " where property_id = '" . $ppty_id . "' 
								   ) order by prate asc
								  ");



            $tmp = $rateArr[0]['RATE'];


            /* 		$actcurrArr = $db->runQuery("select exchange_rate*".($tmp[0] != "" ? $tmp[0]:0)." as mul from ".CURRENCY." inner join ".PROPERTY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code where ".PROPERTY.".id = '".$ppty_id."'  "); */




            /* 		$tmp[0] = $actcurrArr[0]['mul']; */

            $this->view->prate = $tmp != "" ? $tmp * $rateArr[0]['nights'] : "Unknown";
            $this->view->nights = $rateArr[0]['nights'] != "" ? $rateArr[0]['nights'] : "";

            //cal Query
            /* gadd $calAvailArr = $db->runQuery("select * from ".CAL_AVAIL." 
              inner join ".PROPERTY." on ".CAL_AVAIL.".property_id = ".PROPERTY.".id
              where ".CAL_AVAIL.".property_id = '".$ppty_id."' "); */


            $this->view->propertyData = $propertyArr;




            // property images
            $galleryData = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $ppty_id . "' ");

            $this->view->galleryData = $galleryData;



            if ($this->getRequest()->isPost() && !isset($_REQUEST['review']))
            {

                $myform = new Form_Ocontact($ppty_id);
                $request = $this->getRequest();



                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Users();
                    $Result = $myObj->ownercontactus($dataForm);
                    $mySession->sucessMsg = "Thank you, You will soon be contacted";
                    $varsuccess = 1;
                }
                else
                    $mySession->errorMsg = "Enter Proper details first";
            }
            $this->view->varsuccess = $varsuccess;
        }

        //function for saving review

        public function savereviewAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");

            if ($pptyId == "")
                $pptyId = $mySession->property_id;

            $data_update = array();
            $data_update['guest_name'] = $_REQUEST['Name'];
            $data_update['location'] = $_REQUEST['From'];
            $check_in = explode("/", $_REQUEST['Check_in']);
            $data_update['check_in'] = date('Y-m-d', strtotime($check_in[2] . "-" . $check_in[1] . "-" . $check_in[0]));
            $data_update['rating'] = $_REQUEST['Rating'];
            $data_update['headline'] = $_REQUEST['Headline'];
            $data_update['comment'] = $_REQUEST['Comment'];
            $data_update['review'] = $_REQUEST['Review'];
            $data_update['uType'] = '0';
            $data_update['review_date'] = date("Y-m-d");

            if ($pptyId == "")
                $data_update["property_id"] = $mySession->property_id;
            else
                $data_update["property_id"] = $pptyId;



            if (isset($mySession->reviewImage))
            {
                $data_update['guest_image'] = $mySession->reviewImage;
            }

            $db->save(OWNER_REVIEW, $data_update);

            $mySession->sucessMsg = "Your review has been submitted for approval by the admin";
            //$mySession->step = '8';		
            unset($mySession->reviewImage);


            //code for changing the status of step8
            $data_status['status_8'] = '1';
            $condition = "id = '" . $pptyId . "' ";
            $db->modify(PROPERTY, $data_status, $condition);


            exit;
        }

        public function checkheadlineAction()
        {
            global $mySession;
            $db = new Db();
            $checkQuery = $db->runQuery("select * from " . OWNER_REVIEW . " where property_id = '" . $mySession->property_id . "' and headline like '%" . $_REQUEST['Headline'] . "%'");
            if ($checkQuery != "" && count($checkQuery) > 0)
            {
                exit("f");
            }
            else
            {
                exit("s");
            }
        }

        public function savereviewreplyAction()
        {
            global $mySession;
            $db = new Db();
            $data_update = array();
            $pptyId = $this->getRequest()->getParam("pptyId");
            $ppty = $this->getRequest()->getParam("ppty");

            $ownerData = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");
            $data_update['guest_name'] = $ownerData[0]["first_name"] . " " . $ownerData[0]["last_name"];
            $data_update['location'] = $ownerData[0]['address'];
            $data_update['check_in'] = date('Y-m-d', strtotime($_REQUEST['Check_in']));
            $data_update['rating'] = $ownerData[0]['star_rating'];
            //$data_update['headline'] = $_REQUEST['Headline'];						
            $data_update['comment'] = $_REQUEST['Comment'];
            //$data_update['review'] = $_REQUEST['Review'];
            $data_update['uType'] = '0';
            $data_update['review_date'] = date("Y-m-d");
            $data_update['parent_id'] = $_REQUEST['Id'];
            $data_update['guest_image'] = $ownerData[0]['image'];

            if ($ppty != "")
                $data_update["property_id"] = $ppty;
            elseif ($pptyId != "")
                $data_update["property_id"] = $pptyId;
            else
                $data_update["property_id"] = $mySession->property_id;


            if ($ppty != "")
                copy(SITE_ROOT . "images/" . $ownerData[0]['image'], SITE_ROOT . "images/profile/" . $ownerData[0]['image']);

            $db->save(OWNER_REVIEW, $data_update);

            $mySession->sucessMsg = "Your reply has been sent for approval by the admin";
            exit;
        }

        public function setcaldefaultAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Value'] != "")
            {
                $data_update['cal_default'] = $_REQUEST['Value'];
                $data_update['status_6'] = '1';
                $condition = "id = '" . $mySession->property_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }
            exit;
        }

        public function setcurrencyAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $this->getRequest()->getParam("pptyId");
            if ($_REQUEST['Value'] != "")
            {
                $data_update['currency_code'] = $_REQUEST['Value'];
                $data_update['status_7'] = '1';
                if ($pptyId != "")
                    $condition = "id = '" . $pptyId . "' ";
                else
                    $condition = "id = '" . $mySession->property_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }
            exit;
        }

        public function suspendAction()
        {
            global $mySession;
            $db = new Db();
            $ppty_id = $this->getRequest()->getParam("ppty");

            if ($ppty_id != "")
            {
                // query for checkin the old status of the property
                $oldArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");

                $data_update['old_status'] = $oldArr[0]['status'];
                $data_update['status'] = '4';
                $condition = "id = '" . $ppty_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }

            $mySession->sucessMsg = "Property sucessfully suspended";
            $this->_redirect("myaccount/propertyportfolio");
        }

        public function reactivateAction()
        {
            global $mySession;
            $db = new Db();
            $ppty_id = $this->getRequest()->getParam("ppty");

            if ($ppty_id != "")
            {
                // query for checkin the old status of the property
                $oldArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");

                $data_update['old_status'] = NULL;
                $data_update['status'] = $oldArr[0]['old_status'];
                ;
                $condition = "id = '" . $ppty_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }

            $mySession->sucessMsg = "Property sucessfully Re-Activated";
            $this->_redirect("myaccount/propertyportfolio");
        }

        public function deletepptyAction()
        {
            global $mySession;
            $db = new Db();
            $ppty_id = $this->getRequest()->getParam("ppty");





            if ($ppty_id != "")
            {
                // query for checkin the old status of the property
                $oldArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");
                $data_update['old_status'] = $oldArr[0]['status'];
                $data_update['status'] = '0';
                $data_update['date_deleted'] = date('Y-m-d');
                $condition = "id = '" . $ppty_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }




            /* if($ppty_id != "")
              {
              $condition = "id=".$ppty_id;
              $db->delete(PROPERTY,$condition);
              $condition = "property_id = ".$ppty_id;
              $db->delete(AMENITY_ANS,$condition);
              $db->delete(CAL_AVAIL,$condition);
              $db->delete(EXTRAS,$condition);
              $db->delete(GALLERY,$condition);
              $db->delete(SPCL_OFFERS,$condition);
              $db->delete(SPEC_ANS,$condition);

              }
             */

            $mySession->sucessMsg = "Property sucessfully deleted";



            $this->_redirect("myaccount/propertyportfolio");
        }

        public function unsetsessionAction()
        {
            unset($mySession->activate);
            unset($mySession->property_id);
            unset($mySession->step);
            unset($mySession->ppty_no);
            unset($mySession->reviewImage);
            exit;
        }

        #-----------------------------------#
        # Booking list for customer         #
        #-----------------------------------#

        public function bookingcAction()
        {
            global $mySession;
            $db = new Db();
            $bookingyArr = $db->runQuery("select * from " . BOOKING . " 
									 inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									 inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
									 where 
									 " . ($mySession->LoggedUserType == '1' ? BOOKING . ".user_id = '" . $mySession->LoggedUserId . "'" : PROPERTY . ".user_id = '" . $mySession->LoggedUserId . "'") . " 
									 order by " . BOOKING . ".booking_id desc");

            $this->view->bookingData = $bookingyArr;
            //print_r($bookingyArr);
        }

        #-----------------------------------#
        # Booking view for customer         #
        #-----------------------------------#

        public function displaybookingAction()
        {
            global $mySession;
            $db = new Db();
            $bookingId = $this->getRequest()->getParam("bookingId");
            $userId = $this->getRequest()->getParam("uId");



            $bookingyArr = $db->runQuery("select *, " . PROPERTY . ".id as pid from " . BOOKING . " 
							         inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									 inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
									 left join " . SPCL_OFFERS . " ON " . SPCL_OFFERS . ".spcl_offer_id = " . BOOKING . ".offer_id
									 left join " . SPCL_OFFER_TYPES . " ON " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
                                      where
									   " . BOOKING . ".booking_id =" . $bookingId);




            $bookingycomplusoryArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='1' and booking_id =" . $bookingId);
            $bookingyextraArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='0' and booking_id =" . $bookingId);
            $this->view->bookingData = $bookingyArr;
            $this->view->optionComplusoryData = $bookingycomplusoryArr;
            $this->view->optionExtraData = $bookingyextraArr;
            $this->view->minrate = $bookingyArr[0]['min_rate'];
        }

        #-----------------------------------#
        # Booking list for owners           #
        #-----------------------------------#

        public function bookingoAction()
        {
            global $mySession;
            $db = new Db();
            $bookingyArr = $db->runQuery("select * from " . BOOKING . " 
									 inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									 inner join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id
									 where " . PROPERTY . ".user_id =" . $mySession->LoggedUserId . " order by " . BOOKING . ".booking_date desc");

            $this->view->bookingData = $bookingyArr;
        }

        #-----------------------------------#
        # Booking view for owners         #
        #-----------------------------------#

        public function displaybookingoAction()
        {
            global $mySession;
            $db = new Db();
            $bookingId = $this->getRequest()->getParam("bookingId");

            $bookingyArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS , " . BOOKING . ".property_id as PPTY_ID, case when " . EXTRAS . ".etype = '0' then 'optional'  when " . EXTRAS . ".etype = '1' then 'compulsory' end as ETYPE from " . BOOKING . " 
									 inner join " . PROPERTY . " on " . PROPERTY . ".Id=" . BOOKING . ".property_id  
									 inner join " . PAYMENT . " on " . PAYMENT . ".booking_id = " . BOOKING . ".booking_id
									 left join " . SPCL_OFFERS . " spcl on find_in_set(spcl.spcl_offer_id, " . BOOKING . ".offer_id)
									 inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFER_TYPES . ".id = spcl.offer_id 
									 left join " . EXTRAS . " on find_in_set(" . EXTRAS . ".eid, " . BOOKING . ".extras_id)
 									 where " . PROPERTY . ".user_id =" . $mySession->LoggedUserId . "  and " . BOOKING . ".booking_id =" . $bookingId);


            /* 	prd($bookingyArr);		 */
            $bookingycomplusoryArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='1' and booking_id =" . $bookingId);
            $bookingyextraArr = $db->runQuery("select * from " . BOOKING_EXTRA . " where option_status='0' and booking_id =" . $bookingId);
            $userinfoArr = $db->runQuery("select * from " . BOOKING . " inner join " . USERS . " on " . USERS . ".user_id=" . BOOKING . ".user_id inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id=" . USERS . ".country_id  where " . BOOKING . ".booking_id =" . $bookingId);
            $this->view->bookingData = $bookingyArr;
            $this->view->optionComplusoryData = $bookingycomplusoryArr;
            $this->view->optionExtraData = $bookingyextraArr;
            $this->view->userData = $userinfoArr;
        }

        #-----------------------------------#
        # Booking change status             #
        #-----------------------------------#

        public function changebookingstatusAction()
        {
            global $mySession;
            $db = new Db();
            $catId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
                $status = '0';
            else
                $status = '1';
            $data_update['booking_status'] = $status;
            $condition = BOOKING . ".booking_id='" . $catId . "'";
            $db->modify(BOOKING, $data_update, $condition);
            exit();
        }

        public function instuctionuploadAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();
            $data = $_FILES['arrival_instruction']['name'];


            // You can use the name given, or create a random name.
            // We will create a random name!

            $extnsn = explode(".", $data);


            $extnsns = array_pop($extnsn);

            $allowed_extnsn = explode(",", INSTRUCTION_EXTNSN);


            if (!in_array($extnsns, $allowed_extnsn))
            {
                $msg['error'] = "Only doc, image or pdf uploadable";
            }
            else
            {
                $randomName = time() . "_" . $_FILES['arrival_instruction']['name'];

                //checking query

                $chkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");


                if (move_uploaded_file($_FILES['arrival_instruction']['tmp_name'], SITE_ROOT . "uploads/instructions/" . $randomName))
                {
                    // process of saving in database //

                    if ($chkQuery[0]['arrival_instruction'] == "" || $chkQuery[0]['arrival_instruction'] == NULL)
                    {
                        $dataForm['arrival_instruction'] = $randomName;
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $dataForm, $condition);

                        $msg['success'] = "Instruction File 1 uploaded sucessfully";
                    }
                    elseif ($chkQuery[0]['arrival_instruction1'] == "" || $chkQuery[0]['arrival_instruction1'] == NULL)
                    {
                        $dataForm['arrival_instruction1'] = $randomName;
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $dataForm, $condition);

                        $msg['success'] = "Instruction File 2 uploaded sucessfully";
                    }
                    elseif ($chkQuery[0]['arrival_instruction2'] == "" || $chkQuery[0]['arrival_instruction2'] == NULL)
                    {
                        $dataForm['arrival_instruction2'] = $randomName;
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $dataForm, $condition);

                        $msg['success'] = "Instruction File 3 uploaded sucessfully";
                    }
                    else
                    {
                        $msg['error'] = "All 3 Instruction Files are already uploaded";
                    }

                    //echo "image uploaded sucessfully";
                }
                else
                {
                    $msg['error'] = "error in uploading image";
                }
            }

            echo json_encode($msg);
            exit;
        }

        public function deleteinstructionAction()
        {
            global $mySession;
            $db = new Db();

            $file = $this->getRequest()->getParam("file");

            $chkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

            switch ($file)
            {
                case '1':$data_update['arrival_instruction'] = NULL;

                    @unlink(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction']);

                    break;
                case '2':$data_update['arrival_instruction1'] = NULL;
                    @unlink(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction1']);
                    break;
                case '3':$data_update['arrival_instruction2'] = NULL;
                    @unlink(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction2']);
                    break;
            }

            $condition = "id=" . $mySession->property_id;
            $db->modify(PROPERTY, $data_update, $condition);

            exit;
        }

        public function updaterateAction()
        {
            global $mySession;
            $db = new Db();
            $adminId = $this->getRequest()->getParam("id");



            if ($adminId > 0)
            {
                $tmp = explode(".", $_REQUEST['Rate']);
                $start_date = date('Y-m-d', strtotime($_REQUEST['Date_f']));
                $end_date = date('Y-m-d', strtotime($_REQUEST['Date_t']));


                /* $dataForm['date_from'] = $start_date;
                  $dataForm['date_to'] = $end_date; */

                $dataForm['prate'] = $tmp[0];
                $dataForm['nights'] = $_REQUEST['Nights'];


                $condition = "id=" . $adminId;
                $db->modify(CAL_RATE, $dataForm, $condition);
                exit("1");
            }


            exit;
        }

        public function deletenewsAction()
        {
            global $mySession;
            $db = new Db();
            $newsId = $this->getRequest()->getParam("id");
            if ($newsId != "")
            {
                $updateQuery = $db->runQuery("select * from " . NEWS . " where news_id = '" . $newsId . "' ");
                $data_update['black_viewers'] = $updateQuery[0]['black_viewers'] . $mySession->LoggedUserId . ",";
                $condition = "news_id=" . $newsId;
                $db->modify(NEWS, $data_update, $condition);
            }
            exit;
        }

        public function deletefloorplanAction()
        {
            global $mySession;
            $db = new Db();
            $floorplanArr = $db->runQuery("select floor_plan from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

            if ($floorplanArr != "" && count($floorplanArr) > 0)
            {
                @unlink(SITE_ROOT . "images/floorplan/" . $floorplanArr[0]['floor_plan']);
                $dataUpdate['floor_plan'] = "";
                $condition = " id = " . $mySession->property_id;
                $db->modify(PROPERTY, $dataUpdate, $condition);
            }

            exit;
        }

        public function doajaxfileuploadAction()
        {
            global $mySession;
            $db = new Db();
            $error = "";

            $msg = "";
            $fileElementName = 'photo_res';


            $pptyId = $this->getRequest()->getParam("pptyId");

            if (empty($pptyId))
                $pptyId = $mySession->property_id;

            $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $pptyId . "' ");

            
            //echo $_REQUEST['value']; exit;
            if (count($imgArr) <= 24)
            {


                if (!empty($_FILES[$fileElementName]['error']))
                {
                    switch ($_FILES[$fileElementName]['error'])
                    {

                        case '1':
                            $msg['error'] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                            break;
                        case '2':
                            $msg['error'] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                            break;
                        case '3':
                            $msg['error'] = 'The uploaded file was only partially uploaded';
                            break;
                        case '4':
                            $msg['error'] = 'No file was uploaded.';
                            break;
                        case '6':
                            $msg['error'] = 'Missing a temporary folder';
                            break;
                        case '7':
                            $msg['error'] = 'Failed to write file to disk';
                            break;
                        case '8':
                            $msg['error'] = 'File upload stopped by extension';
                            break;
                        case '999':
                        default:
                            $msg['error'] = 'No error code avaiable';
                    }
                }
                elseif (empty($_FILES['photo_res']['tmp_name']) || $_FILES['photo_res']['tmp_name'] == 'none')
                {
                    $msg['error'] = 'No file was uploaded..';
                }
                else
                {
                    $allowed_extnsn = explode(",", strtolower(IMAGE_EXTNSN));

                    //prd($_FILES);
                    $extnsn = explode(".", strtolower($_FILES['photo_res']['name']));

                    if (in_array($extnsn[count($extnsn) - 1], $allowed_extnsn))
                    {
                        $newfilename = $_REQUEST['id'] . "." . $extnsn[count($extnsn) - 2] . time() . "." . $extnsn[count($extnsn) - 1];

                        if (!move_uploaded_file($_FILES['photo_res']['tmp_name'], SITE_ROOT . "images/property/" . $newfilename))
                        {
                            
                            $msg['error'] = "File Uploading Error";
                            exit(json_encode($msg));
                        }
                        else
                        {
                            $msg['number_of_files'] = 1;
                            $msg['success'] = 1;
                            $msg['name'] = $newfilename;
                        }
                        $data_update = array();
                        $data_update['property_id'] = $pptyId;
                        $dataUpdate['image_name'] = $newfilename;
                        $dataUpdate['image_title'] = $values['image_title'];

                        $db->save(GALLERY, $data_update);

                        $data_update1 = array();
                        if (count($imgArr)+1 >= 5)
                        {
                            $data_update1['status_5'] = '1';
                            $condition = "id=" . $pptyId;
                            $db->modify(PROPERTY, $data_update1, $condition);
                        }
                        else
                        {
                            $data_update1['status_5'] = '0';
                            $condition = "id=" . $pptyId;
                            $db->modify(PROPERTY, $data_update1, $condition);
                        }
                    }
                    else
                    {
                        $msg['error'] = $msg['extnsn'] = "Image Extension is wrong";
                    }
                }
            }
            else
            {
                $msg['error'] = "Image uploaded limit exceeded";
            }
            echo json_encode($msg);


            exit;
        }

        public function addreviewAction()
        {
            global $mySession;
            $db = new Db;
            $myform = new Form_Review();


            if ($this->getRequest()->isPost())
            {

                $request = $this->getRequest();

                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Review();

                    $Result = $myObj->saveReview($dataForm);

                    if ($Result > 0)
                        $mySession->sucessMsg = "Review Added Sucessfully";
                    else
                        $mySession->errorMsg = "Enter proper Property Code";
                }
                else
                    $mySession->errorMsg = "Enter proper details first";
            }

            $this->view->myform = $myform;
        }

        public function viewreviewAction()
        {
            global $mySession;
            $db = new Db();

            $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " 
		                            inner join " . PROPERTY . " on " . PROPERTY . ".id = " . OWNER_REVIEW . ".property_id 
									where " . OWNER_REVIEW . ".user_id = '" . $mySession->LoggedUserId . "' ");
            $this->view->reviewData = $reviewArr;
        }

    }

?>