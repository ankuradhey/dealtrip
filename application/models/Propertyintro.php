<?php

    __autoloadDB('Db');

    class Propertyintro extends Db
    {

        public function savePropertyintro($dataForm, $ptyleId = "")
        {
            global $mySession;
            $db = new Db();



            // Add Review in the property and Cutomers revires


            if ($ptyleId == "")
            {
                $chkQry = $db->runQuery("select * from " . PROPERTY . " where property_title = '" . mysql_escape_string(trim($dataForm['title'])) . "'  ");
                $maxQry = $db->runQuery("select max(ref_id) as MAX from " . PROPERTY . " ");
                $max = $maxQry[0]['MAX'];
//                if ($chkQry != "" and count($chkQry) > 0)
//                {
//                    //if Property Type Name is exists than return false / 0 
//                    // No Data Inserted
//                    return 0;
//                }
                //else
                {


                    //prd($dataForm);

                    $data_update['propertycode'] = $dataForm["ppty_no"];

                    //checking that the property number already exists or not
                    $chk = $db->runQuery("select * from " . PROPERTY . " where propertycode = '" . $dataForm['ppty_no'] . "' ");

                    if (count($chk) > 0)
                        $data_update['propertycode'] = generate_property_no($mySession->LoggedUserId);



                    //$data_update['ref_id'] = $max+1;
                    $data_update['property_type'] = $dataForm['accomodation_type'];
                    $data_update['property_title'] = $dataForm['title'];
                    //$data_update['telephone'] = $dataForm['telephone'];				
                    $data_update['website'] = $dataForm['website'];

                    $data_update['brief_desc'] = $dataForm['introduction'];
                    $data_update['country_id'] = $dataForm['country_id'];
                    $data_update['state_id'] = $dataForm['state_id'];
                    $data_update['city_id'] = $dataForm['city_id'];
                    $data_update['sub_area_id'] = $dataForm['sub_area'];
                    $data_update['local_area_id'] = $dataForm['local_area'];
                    $data_update['zipcode'] = $dataForm['zipcode'];
                    $data_update['date_added'] = date('Y-m-d');

                    /* other details */
                    $data_update['bedrooms'] = $dataForm['no_of_bedroom'];
                    $data_update['bathrooms'] = $dataForm['no_of_bathroom'];
                    $data_update['en_bedrooms'] = $dataForm['no_of_en_suite_bathroom'];
                    $data_update['maximum_occupancy'] = $dataForm['occupancy'];

                    $data_update['status'] = '1';

                    $meta_keywords = $dataForm['title'] . ", " . $dataForm['no_of_bedroom'] . " bedrooms, " . $dataForm['no_of_bathroom'] . " bathrooms, " . $dataForm['accomodation_type'] . ", " . $data_update['propertycode'];
                    $meta_description = substr($dataForm['introduction'], 0, 200);

                    $data_update['meta_keywords'] = addslashes($meta_keywords);
                    $data_update['meta_description'] = addslashes($meta_description);


                    $data_update['star_rating'] = $dataForm['rating'];

                    $data_update['user_id'] = $mySession->LoggedUserId;

                    //prd($data_update);
                    $db->save(PROPERTY, $data_update);
                    $mySession->property_id = $db->lastInsertId();
                    $mySession->ppty_no = $data_update['propertycode'];
                    $mySession->step = '1';
                    return 1;
                }
            }
            else
            {

                $maxQry = $db->runQuery("select max(ref_id) as MAX from " . PROPERTY . " ");
                $max = $maxQry[0]['MAX'];


                //$data_update['propertycode'] = "DAT/".date('y')."-".(date('y')+1)."/";
                //$data_update['ref_id'] = $max+1;


                if ($dataForm['step'] == "1")
                {
                    $chkQry = $db->runQuery("select * from " . PROPERTY . " where property_title = '" . mysql_escape_string(trim($dataForm['title'])) . "'  and id != '" . $ptyleId . "'");

                    if ($chkQry != "" && count($chkQry) > 0)
                        return 0;

                    $data_update['property_type'] = $dataForm['accomodation_type'];
                    $data_update['property_title'] = $dataForm['title'];
                    //$data_update['telephone'] = $dataForm['telephone'];				
                    $data_update['website'] = $dataForm['website'];

                    /* agent details */

                    /* 	$data_update['agent_cname'] = $dataForm['company_name'];				
                      $data_update['agent_name'] = $dataForm['agent_name'];
                      $data_update['agent_phone'] = $dataForm['agent_telephone'];
                      $data_update['agent_address'] = $dataForm['agent_address'];
                      $data_update['agent_email'] = $dataForm['agent_email'];
                      $data_update['agent_website'] = $dataForm['agent_website'];
                     */

                    /* agent details ends */

                    $data_update['brief_desc'] = $dataForm['introduction'];
                    $data_update['country_id'] = $dataForm['country_id'];
                    $data_update['state_id'] = $dataForm['state_id'];
                    $data_update['city_id'] = $dataForm['city_id'];
                    $data_update['sub_area_id'] = $dataForm['sub_area'];
                    $data_update['local_area_id'] = $dataForm['local_area'];

                    $data_update['zipcode'] = $dataForm['zipcode'];


                    /* other details */
                    $data_update['bedrooms'] = $dataForm['no_of_bedroom'];
                    $data_update['bathrooms'] = $dataForm['no_of_bathroom'];
                    $data_update['en_bedrooms'] = $dataForm['no_of_en_suite_bathroom'];
                    $data_update['maximum_occupancy'] = $dataForm['occupancy'];


                    /* instruction details */
                    /* 		$data_update['directions_to_property'] = $dataForm['direction_property'];
                      $data_update['late_arrival_instruction'] = $dataForm['late_instruction'];
                      $data_update['arrival_instruction'] = $dataForm['arrival_instruction'];
                      $data_update['key_instructions'] = $dataForm['key_instruction'];
                     */

                    $data_update['star_rating'] = $dataForm['rating'];

                    $meta_keywords = $dataForm['title'] . ", " . $dataForm['no_of_bedroom'] . " bedrooms, " . $dataForm['no_of_bathroom'] . " bathrooms, " . $dataForm['accomodation_type'] . ", " . $data_update['propertycode'];
                    $meta_description = substr($dataForm['introduction'], 0, 200);

                    $data_update['meta_keywords'] = addslashes($meta_keywords);
                    $data_update['meta_description'] = addslashes($meta_description);


                    $condition = "id = " . $ptyleId;
                    $result = $db->modify(PROPERTY, $data_update, $condition);

                    $mySession->step = '1';
                }


                if ($dataForm['step'] == "2")
                {

                    $bathroomArr = $db->runQuery("select * from  " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

                    $data_update = array();
                    $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1'  and s.status = '1' order by psc.cat_id, s.spec_order asc");





                    //$data_update['user_id'] = $mySession->LoggedUserId;
                    $data_update['property_id'] = $mySession->property_id;

                    $i = 0;

                    //prd($specArr);

                    $flag_tmp = 0;
                    $c = 0;

                    //prd($dataForm);
                    foreach ($specArr as $values)
                    {
                        $ques = "ques" . $i;



                        if ($values['spec_id'] == BEDROOM_ID || $values['spec_id'] == BATHROOM_ID || $values['spec_id'] == ADDITIONAL_BATHROOM_ID)
                        {

                            $ques = "ques" . $i . $flag_tmp;

                            if ($c == 0)
                            {
                                $c = $i + $bathroomArr[0]['bedrooms'];
                            }
                            else
                            {
                                $c += $bathroomArr[0]['bedrooms'];
                            }

                            $ques_fix = "ques" . $c . $bathroomArr[0]['bedrooms'] - 1;
                        }
                        elseif ($c != 0 && $values['spec_id'] != BEDROOM_ID && $values['spec_id'] != BATHROOM_ID)
                        {
                            $ques = "ques" . $c;
                            $c++;
                        }

                        if (!is_array($dataForm[$ques]) || $values['spec_id'] == ADDITIONAL_BATHROOM_ID)
                        {

                            if (isset($dataForm[$ques]) && $dataForm[$ques] != "")
                            {
                                $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = '" . $mySession->property_id . "' and spec_id = '" . $values['spec_id'] . "'");
                                if (count($chkSpec) > 0 && $chkSpec != "")
                                {
                                    $condition = " property_id = '" . $mySession->property_id . "'  and spec_id = '" . $values['spec_id'] . "' ";
                                    $db->delete(SPEC_ANS, $condition);
                                }


                                if ($values['spec_id'] == BEDROOM_ID || $values['spec_id'] == BATHROOM_ID)
                                {
                                    $ansData = "";
                                    for ($tmp = 0; $tmp < $bathroomArr[0]['bedrooms']; $tmp++)
                                    {
                                        $ques1 = "ques" . $i . $tmp;
                                        $data_update['spec_id'] = $values['spec_id'];
                                        $ansData .= $dataForm[$ques1] . "|||";
                                        $i++;
                                    }
                                    $i--;
                                    $ansData = substr($ansData, 0, strlen($ansData) - 3);
                                    $data_update['answer'] = $ansData;

                                    $db->save(SPEC_ANS, $data_update);
                                }
                                elseif ($values['spec_id'] == ADDITIONAL_BATHROOM_ID)
                                {
                                    $tmp = 0;
                                    $ques1 = "ques" . $i . $tmp;


                                    for ($tmp = 0; $tmp < $bathroomArr[0]['bedrooms']; $tmp++)
                                    {
                                        $ques1 = "ques" . $i . $tmp;
                                        $data_update['spec_id'] = $values['spec_id'];
                                        $ansData = "";

                                        for ($k = 0; $k < count($dataForm[$ques1]); $k++)
                                        {

                                            $data_update['spec_id'] = $values['spec_id'];

                                            $ansData .= $dataForm[$ques1][$k] . "|||";
                                        }
                                        $ansData = substr($ansData, 0, strlen($ansData) - 3);

                                        $data_update['answer'] = $ansData;


                                        $db->save(SPEC_ANS, $data_update);

                                        $i++;
                                    }



                                    $i--;
                                }
                                else
                                {
                                    $data_update['spec_id'] = $values['spec_id'];
                                    $data_update['answer'] = $dataForm[$ques];
                                    $db->save(SPEC_ANS, $data_update);
                                }
                            }
                        }
                        else
                        {
                            if (count($dataForm[$ques]) > 0)
                            {
                                $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = '" . $mySession->property_id . "' and spec_id = '" . $values['spec_id'] . "'");
                                if (count($chkSpec) > 0 && $chkSpec != "")
                                {
                                    $condition = " property_id = '" . $mySession->property_id . "' and spec_id = '" . $values['spec_id'] . "' ";
                                    $db->delete(SPEC_ANS, $condition);
                                }
                            }

                            for ($k = 0; $k < count($dataForm[$ques]); $k++)
                            {
                                $data_update['spec_id'] = $values['spec_id'];
                                $data_update['answer'] = $dataForm[$ques][$k];
                                $db->save(SPEC_ANS, $data_update);
                            }
                        }

                        $i++;
                    }

                    //code for checking if the step 2 has completed or not
                    $statusArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1'  and s.status = '1' and s.mandatory = '1' order by psc.cat_id, s.spec_order asc");

                    $status_flag = count($statusArr);
                    foreach ($statusArr as $sval)
                    {
                        $chkStat = $db->runQuery("select * from " . SPEC_ANS . " where property_id = '" . $mySession->property_id . "' and spec_id = '" . $sval['spec_id'] . "'");

                        if ($chkStat != "" && count($chkStat) > 0)
                            $status_flag--;
                    }

                    if ($status_flag == 0)
                    {
                        $status_data['status_2'] = '1';
                        $condition = "id = " . $mySession->property_id;
                        $db->modify(PROPERTY, $status_data, $condition);
                    }
                    else
                    {
                        $status_data['status_2'] = '0';
                        $condition = "id = " . $mySession->property_id;
                        $db->modify(PROPERTY, $status_data, $condition);
                    }

                    $mySession->step = '1';

                    if (isset($_REQUEST['allsubmit']) && $_REQUEST['allsubmit'] == 'Save & Next')
                    {
                        $mySession->step = '2';
                    }
                }


                if ($dataForm['step'] == "3")
                {



                    $data_update = array();
                    $data_update1 = array();

                    $amenArr = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");




                    $chkAmenity = $db->runQuery("select * from " . AMENITY_ANS . " where property_id = '" . $mySession->property_id . "' ");




                    if (count($chkAmenity) > 0 && $chkAmenity != "")
                    {
                        $condition = " property_id = '" . $mySession->property_id . "' ";
                        $db->delete(AMENITY_ANS, $condition);
                    }




                    //$data_update['user_id'] = $mySession->LoggedUserId;
                    //$data_update['property_id'] = $mySession->property_id;
                    $data_update1['big_desc'] = $dataForm['overview'];
                    $data_update1['amenity_ques'] = $dataForm['description'];
                    $data_update1['airport1'] = $dataForm['airport1'];
                    $data_update1['airport2'] = $dataForm['airport2'];
                    $data_update1['distance_airport1'] = $dataForm['distance1'];
                    $data_update1['distance_airport2'] = $dataForm['distance2'];

                    $i = 0;
                    $condition = "id = " . $mySession->property_id;


                    $db->modify(PROPERTY, $data_update1, $condition);
                    //prd($specArr);
                    foreach ($amenArr as $values)
                    {
                        $ques = "ques" . $i;
                        $data_update['amenity_id'] = $values['amenity_id'];
                        $data_update['amenity_value'] = $dataForm[$ques];
                        $data_update['property_id'] = $mySession->property_id;
                        $db->save(AMENITY_ANS, $data_update);
                        $i++;
                    }

                    //code for checking the status of step 3
                    $statusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' and big_desc != '' and airport1 != '' ");

                    if ($statusArr != "" && count($statusArr) > 0)
                    {
                        $data_status['status_3'] = '1';
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $data_status, $condition);
                    }
                    else
                    {
                        $data_status['status_3'] = '0';
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $data_status, $condition);
                    }

                    $mySession->step = '3';
                }

                /** step 4 * */
                if ($dataForm['step'] == "4")
                {

                    $data_update = array();
                    $data_update['cletitude'] = $dataForm['latitude'];
                    $data_update['clongitude'] = $dataForm['longitude'];
                    $data_update['address'] = $dataForm['address'];

                    $condition = " id = '" . $mySession->property_id . "' ";
                    $db->modify(PROPERTY, $data_update, $condition);

                    //code for checking the status of step 4
                    $statusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' and address != '' and cletitude != '' and clongitude != '' ");
                    if ($statusArr != "" && count($statusArr) > 0)
                    {
                        $data_status['status_4'] = '1';
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $data_status, $condition);
                    }
                    else
                    {
                        $data_status['status_4'] = '0';
                        $condition = "id=" . $mySession->property_id;
                        $db->modify(PROPERTY, $data_status, $condition);
                    }
                    //$mySession->step = '4';
                }


                /* if($dataForm['step'] == "8")
                  {

                  $checkQuery = $db->runQuery("select * from ".OWNER_REVIEW." where property_id = '".$mySession->property_id."' ");


                  if($checkQuery != "" && count($checkQuery) > 0)
                  {
                  $data_update = array();
                  $data_update['owner_name'] = $dataForm['full_name'];
                  $data_update['location'] = $dataForm['location'];
                  $data_update['check_in'] = date('Y-m-d',$dataForm['check_in']);
                  $data_update['rating'] = $dataForm['rating'];
                  $data_update['headline'] = $dataForm['headline'];
                  $data_update['comment'] = $dataForm['comment'];
                  $data_update['review'] = $dataForm['review'];
                  $condition = "property_id = '".$mySession->property_id."' ";

                  $db->modify(OWNER_REVIEW,$data_update, $condition);
                  $mySession->step = '8';

                  }
                  else
                  {
                  $data_update = array();
                  $data_update['owner_name'] = $dataForm['full_name'];
                  $data_update['location'] = $dataForm['location'];
                  $data_update['check_in'] = date('Y-m-d',$dataForm['check_in']);
                  $data_update['rating'] = $dataForm['rating'];
                  $data_update['headline'] = $dataForm['headline'];
                  $data_update['comment'] = $dataForm['comment'];
                  $data_update['review'] = $dataForm['review'];
                  $data_update["property_id"] = $mySession->property_id;

                  $db->save(OWNER_REVIEW,$data_update);
                  $mySession->step = '8';

                  }
                  }
                 */
                /* agent details */
                if ($dataForm['step'] == "9")
                {


                    $checkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

                    if ($checkQuery != "" && count($checkQuery) > 0)
                    {
                        @unlink(SITE_ROOT . "images/uploads/instructions/" . $checkQuery[0]['arrival_instruction']);
                    }


                    $data_update['agent_person'] = $dataForm['agent_name'];
                    $data_update['agent_name'] = $dataForm['company_name'];
                    $data_update['agent_phone'] = $dataForm['agent_telephone'];
                    $data_update['agent_address'] = $dataForm['agent_address'];
                    $data_update['agent_email'] = $dataForm['agent_email'];
                    $data_update['agent_website'] = $dataForm['agent_website'];


                    $data_update['property_name'] = $dataForm['property_name'];
                    $data_update['address1'] = $dataForm['address'];
                    $data_update['telephone'] = $dataForm['telephone'];
                    $data_update['emergency_no'] = $dataForm['emergency'];
                    $data_update['website'] = $dataForm['website'];
                    $data_update['master_cal_url'] = $dataForm['master_cal'];



                    /* instruction details */
                    $data_update['directions_to_property'] = $dataForm['direction_property'];
                    $data_update['late_arrival_instruction'] = $dataForm['late_instruction'];
                    $data_update['arrival_instruction'] = $dataForm['arrival_instruction'];
                    $data_update['key_instructions'] = $dataForm['key_instruction'];


                    if ($checkQuery[0]['status'] != '3')
                        $data_update['status'] = '2'; //pending approval


                    $condition = "id = '" . $mySession->property_id . "' ";
                    $db->modify(PROPERTY, $data_update, $condition);
                    //code to change the status of step 9

                    $data_status['status_9'] = '1';
                    $condition = "id=" . $mySession->property_id;
                    $db->modify(PROPERTY, $data_status, $condition);

                    //$mySession->step = '9';		
                }
                /** step 5 * */
                /* if($dataForm['step'] == "5")
                  {

                  /*$data_update = array();
                  $data_update['property_id'] = $dataForm['property_id'];
                  $data_update['image_name'] = $dataForm['image'];
                  $db->save(GALLERY,$data_update); */
                /* 			
                  $imageNewName=time()."_".$dataForm['photo_path'];
                  @rename(SITE_ROOT.'images/property/'.$dataForm['photo_path'],SITE_ROOT.'images/property/'.$imageNewName);
                  $data_update['floor_plan'] = $imageNewName;
                  $condition = "property_id = ".$mySession->property_id;
                  $db->modify(PROPERTY,$data_update,$condition);
                  $mySession->step = '6';


                  }
                 */



                return 1;
            }
        }

        #-----------------------------------------------------------#
        # Delete Property Type Function
        // Here delete Property Type record from PROPERTYTYPE table.
        #-----------------------------------------------------------#

        public function deleteAmenity($ptyleId)
        {
            global $mySession;
            $db = new Db();
            $condition1 = "amenity_id='" . $ptyleId . "'";
            $db->delete(AMENITY, $condition1);
        }

        #-----------------------------------------------------------#
        # Status Property Type Function
        // Here Property Type status changed from PROPERTYTYPE table.
        #-----------------------------------------------------------#

        public function statusAmenity($status, $ptyleId)
        {
            global $mySession;
            $db = new Db();
            $data_update['amenity_status'] = $status;
            $condition = "amenity_id='" . $ptyleId . "'";
            $db->modify(AMENITY, $data_update, $condition);
        }

        #-----------------------------------------------------------#
        # Copy Property Function
        #-----------------------------------------------------------#

        public function copyProperty($ppty_no, $param1, $param2, $param3, $param4)
        {
            global $mySession;
            $db = new Db();

            $chkQuery = $db->runQuery("select * from " . PROPERTY . " where propertycode = '" . $ppty_no . "' ");

            //step1 is saved in database
            $data_update['propertycode'] = generate_property_no($mySession->LoggedUserId);
            $data_update['property_type'] = $chkQuery[0]['property_type'];
            $data_update['property_title'] = $chkQuery[0]['property_title'];
            //$data_update['telephone'] = $dataForm['telephone'];				
            $data_update['website'] = $chkQuery[0]['website'];
            $data_update['brief_desc'] = $chkQuery[0]['brief_desc'];
            $data_update['country_id'] = $chkQuery[0]['country_id'];
            $data_update['state_id'] = $chkQuery[0]['state_id'];
            $data_update['city_id'] = $chkQuery[0]['city_id'];
            $data_update['sub_area_id'] = $chkQuery[0]['sub_area_id'];
            $data_update['local_area_id'] = $chkQuery[0]['local_area_id'];
            $data_update['zipcode'] = $chkQuery[0]['zipcode'];
            $data_update['date_added'] = date('Y-m-d');

            /* other details */
            $data_update['bedrooms'] = $chkQuery[0]['bedrooms'];
            $data_update['bathrooms'] = $chkQuery[0]['bathrooms'];
            $data_update['en_bedrooms'] = $chkQuery[0]['en_bedrooms'];
            $data_update['maximum_occupancy'] = $chkQuery[0]['maximum_occupancy'];

            $meta_keywords = $chkQuery[0]['property_title'] . ", " . $chkQuery[0]['bedrooms'] . " bedrooms, " . $chkQuery[0]['bathrooms'] . " bathrooms, " . $chkQuery[0]['property_type'] . ", " . $data_update['propertycode'];
            $meta_description = substr($chkQuery[0]['brief_desc'], 0, 200);

            $data_update['meta_keywords'] = addslashes($meta_keywords);
            $data_update['meta_description'] = addslashes($meta_description);

            //$data_update['status'] = '1';
            $data_update['star_rating'] = $chkQuery[0]['star_rating'];
            $data_update['user_id'] = $mySession->LoggedUserId;

            $data_update['status_2'] = $chkQuery[0]['status_2'];

            //prd($data_update);
            //step 3 
            $data_update['big_desc'] = $chkQuery[0]['big_desc'];
            $data_update['amenity_ques'] = $chkQuery[0]['amenity_ques'];
            $data_update['airport1'] = $chkQuery[0]['airport1'];
            $data_update['airport2'] = $chkQuery[0]['airport2'];
            $data_update['distance_airport1'] = $chkQuery[0]['distance_airport1'];
            $data_update['distance_airport2'] = $chkQuery[0]['distance_airport2'];
            $data_update['status_3'] = $chkQuery[0]['status_3'];

            //step 4
            $data_update['cletitude'] = $chkQuery[0]['cletitude'];
            $data_update['clongitude'] = $chkQuery[0]['clongitude'];
            $data_update['address'] = $chkQuery[0]['address'];
            $data_update['status_4'] = $chkQuery[0]['status_4'];

            //step 5 
            if ($chkQuery[0]['floor_plan'] != "")
            {
                $tmp_name = explode(".", $chkQuery[0]['floor_plan']);
                $randomname = "floorplan_" . $chkQuery[0]['user_id'] . "_" . time() . "." . $tmp_name[count($tmp_name) - 1];
                $chkQuery[0]['floor_plan'];

                copy(SITE_ROOT . "images/floorplan/" . $chkQuery[0]['floor_plan'], SITE_ROOT . "images/floorplan/" . $randomname);
                $data_update['floor_plan'] = $randomname;
            }



            //step 7
            $data_update['currency_code'] = $chkQuery[0]['currency_code'];

            //step 8
            $data_update['rental_ques'] = $chkQuery[0]['rental_ques'];

            //step 9



            $data_update['agent_person'] = $chkQuery[0]['agent_person'];
            $data_update['agent_name'] = $chkQuery[0]['agent_name'];
            $data_update['agent_phone'] = $chkQuery[0]['agent_phone'];
            $data_update['agent_address'] = $chkQuery[0]['agent_address'];
            $data_update['agent_email'] = $chkQuery[0]['agent_email'];
            $data_update['agent_website'] = $chkQuery[0]['agent_website'];


            $data_update['property_name'] = $chkQuery[0]['property_name'];
            $data_update['address1'] = $chkQuery[0]['address1'];
            $data_update['telephone'] = $chkQuery[0]['telephone'];
            $data_update['emergency_no'] = $chkQuery[0]['emergency_no'];
            $data_update['website'] = $chkQuery[0]['website'];
            $data_update['master_cal_url'] = $chkQuery[0]['master_cal_url'];



            /* instruction details */
            $data_update['directions_to_property'] = $chkQuery[0]['directions_to_property'];
            $data_update['late_arrival_instruction'] = $chkQuery[0]['late_arrival_instruction'];

            if ($chkQuery[0]['arrival_instruction'] != "")
            {
                $tmp_name = explode(".", $chkQuery[0]['arrival_instruction']);
                $randomname = $tmp_name[0] . date() . time() . "." . $tmp_name[count($tmp_name) - 1];
                copy(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction'], SITE_ROOT . "uploads/instructions/" . $randomname);
                $data_update['arrival_instruction'] = $randomname;
            }

            if ($chkQuery[0]['arrival_instruction1'] != "")
            {
                $tmp_name = explode(".", $chkQuery[0]['arrival_instruction1']);
                $randomname = $tmp_name[0] . date() . time() . "." . $tmp_name[count($tmp_name) - 1];
                copy(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction1'], SITE_ROOT . "uploads/instructions/" . $randomname);
                $data_update['arrival_instruction1'] = $randomname;
            }

            if ($chkQuery[0]['arrival_instruction2'] != "")
            {
                $tmp_name = explode(".", $chkQuery[0]['arrival_instruction2']);
                $randomname = $tmp_name[0] . date() . time() . "." . $tmp_name[count($tmp_name) - 1];
                copy(SITE_ROOT . "uploads/instructions/" . $chkQuery[0]['arrival_instruction2'], SITE_ROOT . "uploads/instructions/" . $randomname);
                $data_update['arrival_instruction2'] = $randomname;
            }


            $data_update['key_instructions'] = $chkQuery[0]['key_instructions'];
            $data_update['status_9'] = $chkQuery[0]['status_9'];


            $db->save(PROPERTY, $data_update);
            $mySession->property_id = $db->lastInsertId();
            $mySession->ppty_no = $data_update['propertycode'];
            //$mySession->step = '1';			
            //COPY SPECIFICATION
            $specArr = $db->runQuery("select * from " . SPEC_ANS . " where property_id = '" . $chkQuery[0]['id'] . "' ");

            foreach ($specArr as $val)
            {
                $data_update1['user_id'] = $mySession->LoggedUserId;
                $data_update1['property_id'] = $mySession->property_id;
                $data_update1['spec_id'] = $val['spec_id'];
                $data_update1['answer'] = $val['answer'];
                $db->save(SPEC_ANS, $data_update1);
            }


            //COPY AMENITY STEP

            $amenityArr = $db->runQuery("select * from " . AMENITY_ANS . " where property_id = '" . $chkQuery[0]['id'] . "' ");
            $data_update1 = array();
            foreach ($amenityArr as $val)
            {
                $data_update1['user_id'] = $mySession->LoggedUserId;
                $data_update1['property_id'] = $mySession->property_id;
                $data_update1['amenity_id'] = $val['amenity_id'];
                $data_update1['amenity_value'] = $val['amenity_value'];
                $db->save(AMENITY_ANS, $data_update1);
            }

            //IMAGE UPLOAD STEP 5
            if ($param4 == '1')
            {
                $galleryArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $chkQuery[0]['id'] . "' ");
                $data_update1 = array();

                foreach ($galleryArr as $val)
                {
                    $data_update1['property_id'] = $mySession->property_id;

                    //code for duplicating image as well as naming it
                    $gimage = explode(".", $val['image_name']);
                    $randomname = date() . time() . $val['gallery_id'] . "." . $gimage[count($gimage) - 1];

                    copy(SITE_ROOT . "images/property/" . $val['image_name'], SITE_ROOT . "images/property/" . $randomname);

                    $data_update1['image_name'] = $randomname;
                    $data_update1['image_title'] = $val['image_title'];
                    $db->save(GALLERY, $data_update1);
                }

                $status_update = "";
                $status_update['status_5'] = $chkQuery[0]['status_5'];
                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_update, $condition);
            }


            //AVAILABILITY CALENDAR STEP 6
            if ($param2 == '1')
            {
                $galleryArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $chkQuery[0]['id'] . "' ");
                $data_update1 = array();
                foreach ($galleryArr as $val)
                {
                    $data_update1['property_id'] = $mySession->property_id;
                    $data_update1['date_from'] = $val['date_from'];
                    $data_update1['date_to'] = $val['date_to'];
                    $data_update1['cal_status'] = $val['cal_status'];
                    $db->save(CAL_AVAIL, $data_update1);
                }
                $status_update = "";
                //step 6
                $status_update['cal_default'] = $chkQuery[0]['cal_default'];
                $status_update['status_6'] = $chkQuery[0]['status_6'];

                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_update, $condition);
            }


            //RENTAL RATES STEP 7
            if ($param1 == '1')
            {
                $rateArr = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $chkQuery[0]['id'] . "' ");
                $data_update1 = array();
                foreach ($rateArr as $val)
                {
                    $data_update1['property_id'] = $mySession->property_id;
                    $data_update1['date_from'] = $val['date_from'];
                    $data_update1['date_to'] = $val['date_to'];
                    $data_update1['nights'] = $val['nights'];
                    $data_update1['prate'] = $val['prate'];
                    $db->save(CAL_RATE, $data_update1);
                }


                //save extras
                $extraArr = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . $chkQuery[0]['id'] . "'  ");
                $data_update1 = array();
                foreach ($extraArr as $eval)
                {

                    $data_update1['property_id'] = $mySession->property_id;
                    $data_update1['ename'] = $eval['ename'];
                    $data_update1['eprice'] = $eval['eprice'];
                    $data_update1['etype'] = $eval['etype'];
                    $data_update1['stay_type'] = $eval['stay_type'];
                    $db->save(EXTRAS, $data_update1);
                }
                
                $status_update = "";
                $status_update['status_7'] = $chkQuery[0]['status_7'];
                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_update, $condition);
            }

            //RENTAL RATES EXTRAS STEP7
            $extrasArr = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . $chkQuery[0]['id'] . "' ");
            $data_update1 = array();
            foreach ($extrasArr as $val)
            {

                $data_update1['property_id'] = $mySession->property_id;
                $data_update1['ename'] = $val['ename'];
                $data_update1['eprice'] = $val['eprice'];
                $data_update1['etype'] = $val['etype'];
                $db->save(EXTRAS, $data_update1);
            }


            //SPECIAL OFFERS STEP 7
            if ($param3 == '1')
            {
                $offerArr = $db->runQuery("select * from " . SPCL_OFFERS . " where property_id = '" . $chkQuery[0]['id'] . "' ");
                $data_update1 = array();
                foreach ($offerArr as $val)
                {
                    $data_update1['property_id'] = $mySession->property_id;
                    $data_update1['offer_id'] = $val['offer_id'];
                    $data_update1['discount_offer'] = $val['discount_offer'];
                    $data_update1['valid_from'] = $val['valid_from'];
                    $data_update1['valid_to'] = $val['valid_to'];
                    $data_update1['min_night'] = $val['min_night'];
                    $data_update1['book_by'] = $val['book_by'];
                    $data_update1['activate'] = $val['activate'];
                    $db->save(SPCL_OFFERS, $data_update1);
                }
            }

            //code to check the status of the current property in process
            $chkstatusArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $mySession->property_id . "' ");

            if ($chkstatusArr[0]['status_2'] && $chkstatusArr[0]['status_3'] && $chkstatusArr[0]['status_4'] && $chkstatusArr[0]['status_5'] && $chkstatusArr[0]['status_6'] && $chkstatusArr[0]['status_7'] && $chkstatusArr[0]['status_8'] && $chkstatusArr[0]['status_9'])
            {
                $status_update = "";
                $status_update['status'] = '2';
                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_update, $condition);
            }
            else
            {
                $status_update = "";
                $status_update['status'] = '1';
                $condition = "id=" . $mySession->property_id;
                $db->modify(PROPERTY, $status_update, $condition);
            }
        }

    }

?>