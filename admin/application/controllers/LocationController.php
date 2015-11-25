<?php

    __autoloadDB('Db');

    class LocationController extends AppController
    {

        public function countriesAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Manage Countries";
            $qry = "select * from " . COUNTRIES;
            $this->view->ResData = $db->runQuery($qry);
        }

        public function addcountryAction()
        {
            global $mySession;
            $myform = new Form_Country();
            $this->view->pageHeading = "Add New Country";
            if ($this->getRequest()->isPost())
            {
                $db = new Db();
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->SaveCountry($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Country added successfully.";
                        $this->_redirect('location/countries');
                    }
                    else
                    {
                        $mySession->errorMsg = "Country name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function editcountryAction()
        {
            global $mySession;
            $countryId = $this->getRequest()->getParam('countryId');
            $this->view->countryId = $countryId;
            $myform = new Form_Country($countryId);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Edit Country";

            if ($this->getRequest()->isPost())
            {
                $db = new Db();
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->UpdateCountry($dataForm, $countryId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Country Updated updated successfully.";
                    }
                    else
                    {
                        $mySession->errorMsg = "Country name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function deletecountryAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {
                    foreach ($arrId as $key => $Id)
                    {
                        $condition = "country_id='" . $Id . "'";
                        $db->delete(COUNTRIES, $condition);
                    }
                }
            }
            exit();
        }

        public function stateAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage State";

            $db = new Db();
            $sortname = 'state_name';
            $sort = "ORDER BY $sortname";
            $qry = "select * from " . STATE . " join " . COUNTRIES . " on(" . STATE . ".country_id=" . COUNTRIES . ".country_id)";

            $ResData = $db->runQuery($qry . " " . $where . " " . $sort);
            $this->view->ResData = $ResData;
        }

        public function addstateAction()
        {
            global $mySession;
            $myform = new Form_State();

            $this->view->pageHeading = "Add New State";
            if ($this->getRequest()->isPost())
            {
                $db = new Db();
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->SaveState($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "State added successfully.";
                        $this->_redirect('location/state');
                    }
                    else
                    {
                        $mySession->errorMsg = "State name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function editstateAction()
        {
            global $mySession;
            $stateId = $this->getRequest()->getParam('stateId');
            $this->view->stateId = $stateId;
            $myform = new Form_State($stateId);

            $this->view->pageHeading = "Edit State";
            if ($this->getRequest()->isPost())
            {
                $db = new Db();
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->UpdateState($dataForm, $stateId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "State Updated updated successfully.";
                        $this->_redirect('location/state');
                    }
                    else
                    {
                        $mySession->errorMsg = "State name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function deletestateAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {
                    foreach ($arrId as $key => $Id)
                    {
                        $condition = "state_id='" . $Id . "'";
                        $db->delete(STATE, $condition);
                    }
                }
            }
            exit();
        }

        public function citiesAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Cities";
            $db = new Db();
            $sortname = 'city_name';
            $where = "where 1=1 ";
            $sort = "ORDER BY $sortname";
            $qry = "select * from " . CITIES . "
		join " . COUNTRIES . " on(" . CITIES . ".country_id=" . COUNTRIES . ".country_id)
		join " . STATE . " on(" . CITIES . ".state_id=" . STATE . ".state_id)";
            $this->view->ResData = $db->runQuery("$qry $where $sort");
            /* foreach($ResData as $row)
              {
              if ($rc) $json .= ",";
              $json .= "\n{";
              $json .= "id:'".$row['city_id']."',";
              $json .= "cell:['".$start."'";
              $json .= ",'<input name=\'check".$i."\' id=\'check".$i."\' value=\'".$row['city_id']."\' onchange=\'return check_check(\"bcdel\",\"deletebcchk\")\' type=\'checkbox\'><script>$(\'#bcdel\').html(\'\');document.getElementById(\'deletebcchk\').checked = false;</script>'";
              $json .= ",'".addslashes($row['city_name'])."'";
              $json .= ",'".addslashes($row['country_name'])."'";
              $json .= ",'".addslashes($row['state_name'])."'";
              $json .= ",'<a href=\'".APPLICATION_URL_ADMIN."location/editcity/cityId/".$row['city_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
              $json .= "]}";
              $rc = true;
              $start++;
              $i++;
              } */
        }

        public function addcityAction()
        {
            global $mySession;
            $myform = new Form_City();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New City";
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_City();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->SaveCity($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "City added successfully.";
                        $this->_redirect('location/cities');
                    }
                    else
                    {
                        $mySession->errorMsg = "City name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function editcityAction()
        {
            global $mySession;
            $cityId = $this->getRequest()->getParam('cityId');
            $this->view->cityId = $cityId;
            $myform = new Form_City($cityId);
            $this->view->pageHeading = "Edit City";
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_City($cityId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();

                    $Result = $myObj->UpdateCity($dataForm, $cityId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "City Updated updated successfully.";
                        $this->_redirect('location/cities');
                    }
                    else
                    {
                        $mySession->errorMsg = "City name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function deletecityAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {
                    foreach ($arrId as $key => $Id)
                    {
                        $condition = "city_id='" . $Id . "'";
                        $db->delete(CITIES, $condition);
                    }
                }
            }
            exit();
        }

        public function getstatebycountryAction()
        {
            global $mySession;
            $db = new Db();
            $OptionState = "";
            $DataState = $db->runQuery("select * from " . STATE . " where country_id='" . $_REQUEST['countryId'] . "' order by state_name");
            if ($DataState != "" and count($DataState) > 0)
            {
                foreach ($DataState as $key => $valueState)
                {
                    $OptionState.=$valueState['state_id'] . "|||" . $valueState['state_name'] . "***";
                }
                $OptionState = substr($OptionState, 0, strlen($OptionState) - 3);
            }
            echo $OptionState;
            exit();
        }

        public function getcitiesbystateAction()
        {
            global $mySession;
            $db = new Db();
            $OptionCities = "";
            $DataCity = $db->runQuery("select * from " . CITIES . " where state_id='" . $_REQUEST['stateId'] . "' order by city_name");
            if ($DataCity != "" and count($DataCity) > 0)
            {
                foreach ($DataCity as $key => $valueCity)
                {
                    $OptionCities.=$valueCity['city_id'] . "|||" . $valueCity['city_name'] . "***";
                }
                $OptionCities = substr($OptionCities, 0, strlen($OptionCities) - 3);
            }
            echo $OptionCities;
            exit();
        }

        public function getsubbycitynameAction()
        {
            global $mySession;
            $db = new Db();
            $OptionCities = "";
            $DataCity = $db->runQuery("select * from " . SUB_AREA . " where city_id='" . $_REQUEST['cityId'] . "' order by sub_area_name");
            if ($DataCity != "" and count($DataCity) > 0)
            {
                foreach ($DataCity as $key => $valueCity)
                {
                    $OptionCities.=$valueCity['sub_area_id'] . "|||" . $valueCity['sub_area_name'] . "***";
                }
                $OptionCities = substr($OptionCities, 0, strlen($OptionCities) - 3);
            }
            echo $OptionCities;
            exit();
        }

        public function subareaAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Sub Area";
            $db = new Db();
            $sortname = SUB_AREA . '.sub_area_name';
            $where = "where 1=1";
            $sort = "ORDER BY $sortname";
            $qry = "select * from " . SUB_AREA . "
		join " . CITIES . " on(" . CITIES . ".city_id=" . SUB_AREA . ".city_id)
		join " . STATE . " on(" . CITIES . ".state_id=" . STATE . ".state_id)
		join " . COUNTRIES . " on(" . STATE . ".country_id= " . COUNTRIES . ".country_id)";


            $this->view->ResData = $db->runQuery("$qry $where $sort");
        }

        public function addsubareaAction()
        {
            global $mySession;
            $myform = new Form_Subarea();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Sub Area";
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Subarea();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->SaveSubarea($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Sub Area added successfully.";
                        $this->_redirect('location/subarea');
                    }
                    else
                    {
                        $mySession->errorMsg = "Sub Area name you entered already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function editsubareaAction()
        {
            global $mySession;
            $sId = $this->getRequest()->getParam('sId');

            $this->view->sId = $sId;
            $myform = new Form_Subarea($sId);
            $this->view->pageHeading = "Edit Sub Area";
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Subarea($sId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();

                    $Result = $myObj->UpdateSubarea($dataForm, $sId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Sub Area Updated successfully.";
                        $this->_redirect('location/subarea');
                    }
                    else
                    {
                        $mySession->errorMsg = "Sub Area name you entered already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        function deletesubareaAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {
                    foreach ($arrId as $key => $Id)
                    {
                        $condition = "sub_area_id='" . $Id . "'";
                        //first find that the child entry exist----- if exists then display error msg//
                        $chkQuery = $db->runQuery("select * from " . LOCAL_AREA . " where sub_area_id = '" . $Id . "' ");
                        if ($chkQuery != "" && count($chkQuery) > 0)
                            exit("first delete the child entries");
                        else
                            $db->delete(SUB_AREA, $condition);
                    }
                }
            }
            exit();
        }

        function deletelocalareaAction()
        {
            global $mySession;
            $db = new Db();
            if ($_REQUEST['Id'] != "")
            {
                $arrId = explode("|", $_REQUEST['Id']);
                if (count($arrId) > 0)
                {
                    foreach ($arrId as $key => $Id)
                    {
                        $condition = "local_area_id='" . $Id . "'";
                        $db->delete(LOCAL_AREA, $condition);
                    }
                }
            }
            exit();
        }

        public function localareaAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Local Area";
            $db = new Db();
            $sortname = LOCAL_AREA . '.local_area_name';
            $where = "where 1=1";
            $sort = "ORDER BY $sortname";
            $qry = "select * from " . LOCAL_AREA . "
		join " . SUB_AREA . " on(" . SUB_AREA . ".sub_area_id=" . LOCAL_AREA . ".sub_area_id)
		join " . CITIES . " on(" . CITIES . ".city_id=" . SUB_AREA . ".city_id)
		join " . STATE . " on(" . CITIES . ".state_id=" . STATE . ".state_id)
		join " . COUNTRIES . " on(" . STATE . ".country_id=" . COUNTRIES . ".country_id)";


            $this->view->ResData = $db->runQuery("$qry $where $sort");
        }

        public function addlocalareaAction()
        {
            global $mySession;
            $myform = new Form_Localarea();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Local Area";
            $db = new Db();
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Localarea();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->SaveLocalarea($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Local Area added successfully.";
                        $this->_redirect('location/localarea');
                    }
                    else
                    {
                        $mySession->errorMsg = "Local Area name you entered already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function editlocalareaAction()
        {
            global $mySession;
            $lId = $this->getRequest()->getParam('lId');

            $this->view->lId = $lId;
            $myform = new Form_Localarea($lId);
            $this->view->pageHeading = "Edit Local Area";
            $db = new Db();

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Localarea($lId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Location();
                    $Result = $myObj->UpdateLocalarea($dataForm, $lId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Local Area Updated successfully.";
                        $this->_redirect('location/localarea');
                    }
                    else
                    {
                        $mySession->errorMsg = "Local Area name you entered already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        function pageAction()
        {
            global $mySession;
            $db = new Db();
            $countryId = $this->_getParam('countryId');
            $stateId = $this->_getParam('stateId');
            $cityId = $this->_getParam('cityId');
            $subareaId = $this->_getParam('subareaId');
            $localareaId = $this->_getParam('localareaId');

            if (!empty($countryId))
            {
                $location_type = $data['loc_location_type'] = 'country';
                $location_id = $countryId;
                $lrecord = $db->runQuery("select country_name as location_name from " . COUNTRIES . " where country_id = $countryId ");
            }
            elseif (!empty($stateId))
            {
                $location_id = $stateId;
                $location_type = $data['loc_location_type'] = 'state';
                $lrecord = $db->runQuery("select state_name as location_name from " . STATE . " where state_id = $stateId ");
            }
            elseif (!empty($cityId))
            {
                $location_id = $cityId;
                $location_type = $data['loc_location_type'] = 'city';
                $lrecord = $db->runQuery("select city_name as location_name from " . CITIES . " where city_id = $cityId ");
            }
            elseif (!empty($subareaId))
            {
                $location_id = $subareaId;
                $location_type = $data['loc_location_type'] = 'sub_area';
                $lrecord = $db->runQuery("select sub_area_name as location_name from " . SUB_AREA . " where sub_area_id = $subareaId ");
            }
            elseif (!empty($localareaId))
            {
                $location_id = $localareaId;
                $location_type = $data['loc_location_type'] = 'local_area';
                $lrecord = $db->runQuery("select local_area_name as location_name from " . LOCAL_AREA . " where local_area_id = $localareaId ");
            }
            $this->view->pageHeading = "Location Page template";

            $record = $db->runQuery("select * from " . LOCATION_PAGE . " where loc_location_id = $location_id and loc_location_type = '$location_type' ");

            $questions = $db->runQuery("select * from " . LOCATION_QUESTIONS . " where (ques_loc_id = '" . $record[0]['loc_id'] . "' or ques_fixed = '1') and ques_status = '1' order by ques_fixed,ques_order asc ");
            $key_features = $db->runQuery(" select * from " . LOCATION_FEATURES . " ");
            $propertyType = $db->runQuery("select * from " . PROPERTY_TYPE . " where ptyle_status = '1'  ");

//            pr($record[0]);
            $this->view->record = $record[0];
            $this->view->location_name = $lrecord[0]['location_name'];
            $this->view->features = $key_features;
            $this->view->questions = $questions;
            $this->view->propertyType = $propertyType;
            if ($this->getRequest()->isPost())
            {
                $dataForm = $this->getRequest()->getPost();
                $dataForm['loc_location_id'] = $location_id;
                $dataForm['loc_location_type'] = $location_type;
                //image
                $data = array();

                $files = glob(SITE_ROOT . "images/location/" . trim($lrecord[0]['location_name']) . "/*");
                if (!empty($dataForm['caption'][0]))
                //foreach ($dataForm['caption'] as $fKey => $fVal)
                    foreach ($files as $fileK => $fileV)
                    {
                        $filename = array_pop(explode("/", $fileV));
                        $data[$filename]['caption'] = $dataForm['caption'][$fileK];
                    }

                if (!empty($dataForm['caption'][0]))
                    $dataForm['loc_images'] = json_encode($data);

                //saving order of questions
                foreach ($dataForm['question_ids'] as $qK => $qV)
                {
                    $ar = array();
                    $ar['ques_order'] = $qK + 1;
                    $db->modify(LOCATION_QUESTIONS, $ar, "ques_id = $qV");
                }

                //pr($question_answers);
                //pr($dataForm['question_ids']);
                //prd($dataForm['question_answers']);
                //question answers
                $data = array();
                foreach ($dataForm['question_answers'] as $qaK => $qaV)
                {
                    $data[$qaK]['id'] = $dataForm['question_ids'][$qaK];
                    $data[$qaK]['value'] = $qaV;
                }
//                prd($data);
                //rental type and size
                $dataForm['loc_rental_overview'] = implode(',', $dataForm['loc_rental_type_size']);
                unset($dataForm['question_answers'], $dataForm['question_ids'], $dataForm['caption'], $dataForm['question-text'], $dataForm['feature-text'], $dataForm['loc_rental_type_size'], $dataForm['maximum_occupancy'], $dataForm['type'], $dataForm['bedroom'], $dataForm['rental_text'], $dataForm['ques_content'], $dataForm['ques_loc_id']);
                $dataForm['loc_answers'] = json_encode($data);
//                prd($dataForm['loc_answers']);
                if (!empty($dataForm['loc_amenities'][0]))
                    $dataForm['loc_amenities'] = implode(",", $dataForm['loc_amenities']);

                //prd($dataForm['loc_amenities']);
                //5 key features
                if (!empty($dataForm['loc_key_features'][0]))
                    $dataForm['loc_key_features'] = implode(',', $dataForm['loc_key_features']);

                //find if already in database
                $res = $db->runQuery("select * from " . LOCATION_PAGE . " where loc_location_type = '" . $location_type . "' and loc_location_id = '$location_id' ");
                if (count($res))
                {
                    //prd("loc_location_type = '" . $location_type . "' and loc_location_id = '$location_id'");
                    //pr($dataForm);
                    $res = $db->modify(LOCATION_PAGE, $dataForm, "loc_location_type = '" . $location_type . "' and loc_location_id = '$location_id'");
                    //prd($res);
                }
                else
                    $db->save(LOCATION_PAGE, $dataForm);

                if ($location_type == 'local_area')
                    $location_type = 'localarea';
                if ($location_type == 'sub_area')
                    $location_type = 'subarea';


                $this->_redirect("location/page/{$location_type}Id/$location_id");
            }
        }

        function editquestionAction()
        {
            $db = new Db();
            $question = $this->getRequest()->getPost('question');
            $quesId = $this->getRequest()->getPost('ques_id');
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $msg = array();
            if (empty($question))
            {
                $msg['result'] = 'false';
                $msg['reason'] = 'question value is empty';
            }
            else
            {
                $arr = array();
                $arr['ques_content'] = $question;
                $arr['ques_date_modified'] = date();

                $db->modify(LOCATION_QUESTIONS, $arr, " ques_id= $quesId");
                $msg['question'] = $question;
                $msg['result'] = 'true';
            }
            echo json_encode($msg);
        }

        function addquestionAction()
        {
            $db = new Db();
            $question = $this->getRequest()->getPost('question');
            $locId = $this->getRequest()->getPost('loc_id');
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $msg = array();
            if (empty($question))
            {
                $msg['result'] = 'false';
                $msg['reason'] = 'question value is empty';
            }
            else
            {
                //get ques data
                $res = $db->runQuery("select ques_order from " . LOCATION_QUESTIONS . " order by ques_order desc limit 1 ");
                $data = array();
                $data['ques_content'] = $question;
                $data['ques_loc_id'] = $locId;
                $data['ques_order'] = $res[0]['ques_order'] + 1;
                $db->save(LOCATION_QUESTIONS, $data);
                $id = $db->lastInsertId();
                $msg['result'] = 'true';
                $msg['question'] = $question;
                $msg['question_id'] = $id;
            }

            echo json_encode($msg);
        }

        function addfeatureAction()
        {
            $db = new Db();
            $feature = $this->getRequest()->getPost('feature');
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $msg = array();
            if (empty($feature))
            {
                $msg['result'] = 'false';
                $msg['reason'] = 'feature value is empty';
            }
            else
            {
                //check if feature is already available
                $chkData = $db->runQuery(" select * from " . LOCATION_FEATURES . " where lower(feature_name) = '" . strtolower($feature) . "'   ");

                if ($chkData != "" && count($chkData))
                {

                    $msg['result'] = 'false';
                    $msg['reason'] = 'duplicate value found';
                    exit(json_encode($msg));
                }

                $data = array();
                $data['feature_name'] = $feature;
                $db->save(LOCATION_FEATURES, $data);
                $id = $db->lastInsertId();
                $msg['result'] = 'true';
                $msg['featureVal'] = $feature;
                $msg['featureId'] = $id;
            }

            echo json_encode($msg);
        }

        public function doajaxfileuploadAction()
        {
            global $mySession;
            $db = new Db();
            $error = "";

            $location_name = $this->getRequest()->getParam('location');

            $msg = "";
            $fileElementName = 'loc_images';
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
            elseif (empty($_FILES['loc_images']['tmp_name']) || $_FILES['loc_images']['tmp_name'] == 'none')
            {
                $msg['error'] = 'No file was uploaded..';
            }
            else
            {
                $allowed_extnsn = explode(",", strtolower(IMAGE_EXTNSN));


                //prd($_FILES);
                $extnsn = explode(".", strtolower($_FILES['loc_images']['name']));


                if (in_array($extnsn[count($extnsn) - 1], $allowed_extnsn))
                {
                    if (!is_dir(SITE_ROOT . "images/location/" . trim($location_name)))
                        mkdir(SITE_ROOT . "images/location/" . trim($location_name));

                    $files = glob(SITE_ROOT . "images/location/" . trim($location_name) . "/*");
                    //$newfilename = (count($files) + 1) . "." . $extnsn[count($extnsn) - 1];
                    $newfilename = time() . "." . $extnsn[count($extnsn) - 1];

                    if (!move_uploaded_file($_FILES['loc_images']['tmp_name'], SITE_ROOT . "images/location/" . trim($location_name) . "/" . $newfilename))
                        $msg['error'] = "File Uploading Error";
                    else
                    {
                        //$this->watermark_text(SITE_ROOT . "ankit_ups/" . $newfilename, SITE_ROOT . "ankit_ups/" . 'watermarked_' . $newfilename);
                        $msg['number_of_files'] = 1;
                        $msg['success'] = 1;
                        $msg['name'] = $newfilename;
                    }
                }
                else
                {
                    $msg['error'] = $msg['extnsn'] = "Image Extension is wrong";
                }
            }

            echo json_encode($msg);


            exit;
        }

        public function deleteimageAction()
        {
            $db = new Db();
            $img = $this->_getParam("img");
            $location = $this->_getParam("location");
            $url = $this->_getParam("url");
            
            $locationId = $this->_getParam("locationId");

            unlink(SITE_ROOT . "images/location/$location/$img");
            $res = $db->runQuery("select * from " . LOCATION_PAGE . " where loc_id = $locationId ");

            $caption = objectToArray(json_decode($res[0]['loc_images']));

            $captionArr = array();
            unset($caption[$img]);
            $data = array();
            $data['loc_images'] = json_encode($caption);
            $db->modify(LOCATION_PAGE, $data, " loc_id = $locationId ");
        
            $this->_redirect($url);
        }

    }

?>