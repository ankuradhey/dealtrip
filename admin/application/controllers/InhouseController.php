<?php

    __autoloadDB('Db');

    class InhouseController extends AppController
    {
        #-----------------------------------------------------------#
        # Property List Action Function
        #-----------------------------------------------------------#

        public function addpropertyAction()
        {
            global $mySession;
            $db = new Db();

            $this->view->pageHeading = "Add a Property";

            $ppty_no = generate_property_no(11);
            $this->view->ppty_no = $ppty_no;

            //code for checking the status of each step
            if (isset($mySession->admin_property_id))
            {
                $statusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
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
                    $condition = "id=" . $mySession->admin_property_id;
                    $db->modify(PROPERTY, $status_data, $condition);
                }
                $pptyId = $this->view->pptyId = $mySession->admin_property_id;

                $preview_link_data = $db->runQuery("select " . PROPERTY . ".propertycode ," . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,ptyle_name, ptyle_url, bedrooms, bathrooms  from " . PROPERTY . " 
                                                    inner join " . PROPERTY_TYPE . " on " . PROPERTY_TYPE . ".ptyle_id = " . PROPERTY . ".property_type
                                                    inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                    inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                                    inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                                    left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                                    left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                    where id = '" . $pptyId . "'
                                                   ");
                
                $this->view->pageHeading = "Add a Propery -- Property no - ".$preview_link_data[0]['propertycode'];
                $this->view->assign($preview_link_data[0]);
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



            $stepId = $this->getRequest()->getParam("step");


            if ($stepId != "")
                $mySession->step = $stepId - 1;

            if ($stepId == 'confirm')
            {
                $mySession->step = 0;
            }
            
            if(!isset($mySession->admin_property_id))
            {
                $mySession->step = 0;
            }

            //prd($mySession->step);
            switch ($mySession->step)
            {
                default:
                case '0':

                    $myform = new Form_Ownproperty($mySession->admin_property_id);
                    $this->view->step = '1';
                    $step = '1';

                    if (isset($mySession->admin_property_id))
                    {
                        $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
                        $this->view->rating = $ratingArr[0]['star_rating'];
                    }
                    //custom attributes
                    $customAttributes = $db->runQuery(" select * from ".ATTRIBUTE_ANS." where ans_property_id = '$pptyId' and ans_attribute_id='0' ");
                    $this->view->customAttributes = $customAttributes;
                    break;

                case '1': $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
                                                    where psc.cat_status = '1' 
                                                    and s.status = '1' order by psc.cat_id, s.spec_order asc
						   ");
                    $this->view->specData = $specArr;
                    $myform = new Form_Propertyspec($mySession->admin_property_id);
                    $this->view->stepId = $stepId;
                    $this->view->step = '2';
                    $step = '2';
                    //bathroom no. 
                    $bathroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
                    $this->view->no_bedrooms = $bathroomArr[0]['bedrooms'];
                    break;

                case '2': $amenityArr = $db->runQuery("select * from " . AMENITY_PAGE . " ");
                    $this->view->amenityData = $amenityArr;

                    $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
                    $this->view->amenityArr = $amenity;

                    if ($mySession->admin_property_id != '')
                        $myform = new Form_Amenities($mySession->admin_property_id);
                    else
                        $myform = new Form_Amenities();

                    $this->view->step = '3';
                    $step = '3';

                    break;

                case '3': if ($stepId == '4')
                        $myform = new Form_Location($mySession->admin_property_id);
                    else
                        $myform = new Form_Location();
                    $this->view->step = '4';
                    $step = '4';

                    break;

                case '4': $this->view->step = '5';

                    $Arr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
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

                    $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $mySession->admin_property_id . "' ");
                    $this->view->calArr = $calArr;

                    $this->view->step = '6';
                    $step = '6';

                    //passing default value of calendar to view
                    $pptyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
                    $this->view->cal_default = $pptyArr[0]['cal_default'];


                    break;

                case '6': $this->view->step = '7';
                    $step = '7';

                    $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id where so.property_id = '" . $mySession->admin_property_id . "' order by so.spcl_offer_id asc
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
                            $dataForm["property_id"] = $mySession->admin_property_id;
                            $db->save(SPCL_OFFERS, $dataForm);
                        }
                    }


                    $this->view->offerArr = $offerArr;
                    //echo $mySession->step; exit;
                    //rental question
                    $rentalQues = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");

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

                case '7': $myform = new Form_Oreview($mySession->admin_property_id);
                    //query for getting image
                    $this->view->step = '8';
                    //$uData = $db->runQuery("select * from ".OWNER_REVIEW." where property_id = '".$mySession->admin_property_id."' ");

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
													where r.property_id = '" . $mySession->admin_property_id . "' and r.review_status = '1' order by r.review_id desc ");



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

                case '8': if (isset($mySession->admin_property_id))
                        $myform = new Form_Arrival($mySession->admin_property_id);
                    else
                        $myform = new Form_Arrival();
                    $this->view->step = '9';
                    $this->view->myform = $myform;

                    $Arrival = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
                    $this->view->arrival = $Arrival[0]['arrival_instruction'];
                    $this->view->arrival1 = $Arrival[0]['arrival_instruction1'];
                    $this->view->arrival2 = $Arrival[0]['arrival_instruction2'];
                    if ($this->getRequest()->isPost())
                        $mySession->step = '9';
                    break;

                case '9': $this->view->step = '10';

                    break;
                case '10':
                    break;
            } //switch case ends		


            /* code used for step 2 */
            if ($this->getRequest()->isPost() && $_REQUEST['copy_ppty'] == "")
            {
                if ($_REQUEST['next'] != "")
                    $mySession->step = '2';
            }


            $this->view->myform = $myform;
            $this->view->varsuccess = $varsuccess;
            
            //list of default properties
            $defaultPropertyArr = $db->runQuery("select * from ".PROPERTY." where status = '5' ");
            $this->view->defaultPropertyArr = $defaultPropertyArr;
        }
        
        public function copypropertyAction(){
            global $mySession;
            $db = new Db();
            $redirect_to_list = false;
            
            if(empty($_REQUEST['pptyno']))
            $this->_redirect ('inhouse/addproperty');
            
            $stepId = $this->getRequest()->getParam("step");
            
            
            $myObj = new Propertyintro();
            
            
            $Result = $myObj->copyProperty($_REQUEST['pptyno'], true, true, true, true);
            
            
            if($Result > 0){
                $mySession->admin_property_id = $Result;
                //$mySession->step = 1;
            
                $this->_redirect("inhouse/addproperty");
        
            }else{
                $mySession->errorMsg = "Some error occurred while copying a property";
            }    
        }
        
        public function processpageAction()
        {
            global $mySession;
            $db = new Db();

            $myform = new Form_Ownproperty();
            $pptyId = $this->getRequest()->getParam("ppty");
            $stepId = $this->getRequest()->getParam("step");

            if (!empty($pptyId))
                $mySession->step = $stepId - 1;

            $redirect_to_list = false;
            
            //prd($mySession->step);
            switch ($mySession->step)
            {
                case '0': $myform = new Form_Ownproperty();
                    if (isset($mySession->admin_property_id))
                    {
                        $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->admin_property_id . "' ");
                        $this->view->rating = $ratingArr[0]['star_rating'];
                    }
                    break;
                case '1': if ($stepId != "")
                        $myform = new Form_Propertyspec($mySession->admin_property_id);
                    elseif ($mySession->admin_property_id != "")
                        $myform = new Form_Propertyspec($mySession->admin_property_id);
                    else
                        $myform = new Form_Propertyspec();
                    break;

                case '2': if ($stepId != "")
                        $myform = new Form_Amenities($mySession->admin_property_id);
                    elseif ($mySession->admin_property_id != "")
                        $myform = new Form_Amenities($mySession->admin_property_id);
                    else
                        $myform = new Form_Amenities();
                    break;
                case '3': if ($stepId != "")
                        $myform = new Form_Location($mySession->admin_property_id);
                    elseif ($mySession->admin_property_id != "")
                        $myform = new Form_Location($mySession->admin_property_id);
                    else
                        $myform = new Form_Location();
                    break;
                case '7': $mySession->step = '8';
                    $ppty_id = $this->getRequest()->getParam("ppty");
                    if ($ppty_id != "")
                        $this->_redirect("property/editproperty/ppty/" . $ppty_id);
                    else
                        $this->_redirect("inhouse/addproperty");

                    $myform = new Form_Oreview();
                    break;
                case '8': $myform = new Form_Arrival();
                    $redirect_to_list = true;
                    break;
            }


            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
//                    $dataForm = $myform->getValues();
                    $dataForm = $_REQUEST;

                    $myObj = new Propertyintro();

                    $dataForm['rating'] = $_REQUEST['rating'];
                    
                    if(isset($_REQUEST['library_save']))
                        $dataForm['library_save'] = true;
                    else
                        $dataForm['library_save'] = false;
                    
                    if($mySession->admin_property_id == "")
                        $mySession->admin_property_id = $pptyId;

                    
                    $Result = $myObj->savePropertyintro($dataForm, $mySession->admin_property_id);
                    
                    if($Result > 0)
                    {
                        $mySession->sucessMsg = "Successfully Saved";
                        $varsuccess = 1;
                    }
                    else
                    {
                        $mySession->errorMsg = "This type of property already exists";
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

            if ($redirect_to_list)
                $this->_redirect("property/index");

            $this->_redirect("inhouse/addproperty");
        }
        
        function movepropertyAction(){
            
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {

                    foreach ($arrId as $key => $Id)
                    {
                        if ($Id > 1)
                        {
                            $myObj = new Property();
                            $moveto = $_REQUEST['moveto'];
                            $Result = $myObj->moveToLibrary($Id,$moveto);
                        }
                    }
                }
            }
            exit();
        }

    }

?>