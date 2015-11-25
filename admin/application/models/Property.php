<?php

__autoloadDB('Db');

class Property extends Db {

    public function saveProperty($dataForm, $ptyleId = "") {
        global $mySession;
        $db = new Db();


        $data_update['ptyle_name'] = $dataForm['ptyle_name'];


        if ($ptyleId == "") {
            $chkQry = $db->runQuery("select * from " . PROPERTYTYPE . " where ptyle_name='" . mysql_escape_string(trim($dataForm['ptyle_name'])) . "'");
            if ($chkQry != "" and count($chkQry) > 0) {
                //if Property Type Name is exists than return false / 0 
                // No Data Inserted
                return 0;
            } else {
                # If Property Type Name Not Already Exista.
                # Insert New Record Into Database



                $db->save(PROPERTYTYPE, $data_update);
                return 1;
            }
        } else {
            $chkQry = $db->runQuery("select * from " . PROPERTYTYPE . " where ptyle_name='" . mysql_escape_string(trim($dataForm['ptyle_name'])) . "' and ptyle_id!=" . $ptyleId);
            if ($chkQry != "" and count($chkQry) > 0) {
                return 0;
            } else {
                $condition = 'ptyle_id=' . $ptyleId;
                $result = $db->modify(PROPERTYTYPE, $data_update, $condition);
                return 1;
            }
        }
    }

    #-----------------------------------------------------------#
    # Delete Property Type Function
    // Here delete Property Type record from PROPERTYTYPE table.
    #-----------------------------------------------------------#

    public function deleteProperty($ptyleId) {
        global $mySession;
        $db = new Db();
        $condition1 = "ptyle_id='" . $ptyleId . "'";
        $db->delete(PROPERTYTYPE, $condition1);
    }

    #-----------------------------------------------------------#
    # Status Property Type Function
    // Here Property Type status changed from PROPERTYTYPE table.
    #-----------------------------------------------------------#

    public function statusProperty($status, $pptyId) {
        global $mySession;
        $db = new Db();
        $data_update['status'] = $status;
        $condition = "id='" . $pptyId . "'";
        $db->modify(PROPERTY, $data_update, $condition);

        if ($status == '3') {
            $data = array();
            $data['lppty_property_id'] = $pptyId;
            $data['lppty_type'] = '0';
            $data['lppty_order'] = '1';
            $this->addToLatestProperty();
        } else {

            $pptyArr = $db->runQuery("select * from " . SLIDES_PROPERTY . " where lppty_property_id = '" . $pptyId . "' and lppty_type='0' ");
            if (!empty($pptyArr[0]['lppty_id'])) {
                $this->removeFromLatestProperty($pptyArr[0]['lppty_id']);
            }
        }
    }

    public function removeFromLatestProperty($pptyId) {

        global $mySession;
        $db = new Db();

        $pptyArr = $db->runQuery("select * from " . SLIDES_PROPERTY . " where lppty_id = '" . $pptyId . "' ");

        $updateData['lppty_order'] = new Zend_Db_Expr('lppty_order-1');
        $db->modify(SLIDES_PROPERTY, $updateData, 'lppty_type="0" and lppty_order > ' . $pptyArr[0]['lppty_order'] . ' ');

        $condition = "lppty_id = '" . $pptyId . "' ";
        $db->delete(SLIDES_PROPERTY, $condition);
    }

    public function addToLatestProperty($data) {
        global $mySession;
        $db = new Db();

        $updateData['lppty_order'] = new Zend_Db_Expr('lppty_order+1');
        $db->modify(SLIDES_PROPERTY, $updateData, 'lppty_type="0"');

        $db->save(SLIDES_PROPERTY, $data);
    }

    public function statusPopularProperty($status, $pptyId) {
        global $mySession;
        $db = new Db();
        $data_update['is_popular'] = $status;
        $condition = "id='" . $pptyId . "'";
        $db->modify(PROPERTY, $data_update, $condition);
    }

    public function statusspclOffer($status, $pptyId) {
        global $mySession;
        $db = new Db();
        $data_update['is_spcl'] = $status;
        $condition = "id='" . $pptyId . "'";
        $db->modify(PROPERTY, $data_update, $condition);
    }

    public function moveToLibrary($pptyId, $moveto = 'library') {
        global $mySession;
        $db = new Db();
        if ($moveto == 'inhouse')
            $data_update['status'] = '3';
        else
            $data_update['status'] = '5';


        $condition = "id = " . $pptyId;
        $db->modify(PROPERTY, $data_update, $condition);
    }

    public function getProperties($filterArr) {
        $db = new Db();
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $record = $dbAdapter->select()->from(array('p' => PROPERTY), array('p.id', 'g.image_name', 'p.propertycode', 'p.property_type', 'p.property_title', 'p.property_name'))
                ->joinLeft(array('g' => GALLERY), 'g.property_id = p.id ')
                ->group('p.id')
                ->where(" 1 ")
        ;
        $emptyFlag = true;
        if (is_array($filterArr) && !empty($filterArr)) {
            extract($filterArr);
            if (!empty($property_type)) {
                $record = $record->where(" p.property_type = $property_type ");
                $emptyFlag = false;
            }
            if (!empty($country_id)) {
                $record = $record->where(" p.country_id = $country_id ");
                $emptyFlag = false;
            }
            if (!empty($state_id)) {
                $record = $record->where(" p.state_id = $state_id ");
                $emptyFlag = false;
            }
            if (!empty($city_id)) {
                $record = $record->where(" p.city_id = $city_id ");
                $emptyFlag = false;
            }
            if (!empty($sub_area_id)) {
                $record = $record->where(" p.sub_area_id = $sub_area_id ");
                $emptyFlag = false;
            }
            if (!empty($local_area_id)) {
                $record = $record->where(" p.local_area_id = $local_area_id ");
                $emptyFlag = false;
            }
            if (!empty($zipcode)) {
                $record = $record->where(" p.zipcode = $zipcode ");
                $emptyFlag = false;
            }
            if (!empty($xml_subscriber_id)) {
                $record = $record->where(" p.xml_subscriber_id = $xml_subscriber_id ");
                $emptyFlag = false;
            }
            if (!empty($bedrooms)) {
                $record = $record->where(" p.bedrooms = $bedrooms ");
                $emptyFlag = false;
            }
            if (!empty($propertyList)) {
                $record = $record->where(" p.id in ($propertyList) ");
                $emptyFlag = false;
            }
//            echo '<pre>'; print_r($record->query()); exit('Macro die');
            if (!$emptyFlag)
                return $record = $record->query()->fetchAll();
            else {
                return;
            }
        } else {
            return;
        }
    }

    /*
     * Author: ankit
     * Description - used for updating property only used for property updater
     */

    public function updateProperty($postData) {
        $db = new Db();
        global $mySession;
        $step = $postData['step'];
        switch ($step) {
            case '1':
                $data = array();
                $data['bedrooms'] = $postData['no_of_bedroom'];
                $data['bathrooms'] = $postData['no_of_bathroom'];
                $data['en_bedrooms'] = $postData['no_of_en_suite_bathroom'];
                $data['maximum_occupancy'] = $postData['occupancy'];
                $data['property_title'] = $postData['title'];
                $data['brief_desc'] = $postData['introduction'];
                $data['meta_keywords'] = $postData['meta_keywords'];
                $data['meta_description'] = $postData['meta_description'];
                $pptyList = implode(",", $postData['filteredProperty']);
                $condition = " id in (" . $pptyList . ") ";
                //$db->modify(PROPERTY, $data, $condition);
                break;

            case '2':
                $dataForm = $postData;
                $pptyList = implode(",", $postData['filteredProperty']);
                $pptyArr = explode(",", $pptyList);
                $bathroomArr = $db->runQuery("select * from  " . PROPERTY . " where id = '" . $pptyArr[0] . "' ");
                $data_update = array();
                $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1'  and s.status = '1' order by psc.cat_id, s.spec_order asc");

                $data_update['property_id'] = $ptyleId;
                $i = 0;
                //prd($specArr);
                //prd($dataForm);
                $flag_tmp = 0;
                $c = 0;
                $i = 0;

                //prd($specArr);
                //prd($dataForm);
                $flag_tmp = 0;
                $c = 0;
                foreach ($specArr as $values) {
                    $ques = "ques" . $i;
                    if ($values['spec_id'] == BEDROOM_ID || $values['spec_id'] == BATHROOM_ID || $values['spec_id'] == ADDITIONAL_BATHROOM_ID) {
                        $ques = "ques" . $i . $flag_tmp;
                        if ($c == 0) {
                            $c = $i + 1;
                        } else {
                            $c += 1;
                        }
                        $ques_fix = "ques" . $c . "1" - 1;
                    } elseif ($c != 0 && $values['spec_id'] != BEDROOM_ID && $values['spec_id'] != BATHROOM_ID) {
                        $ques = "ques" . $c;
                        $c++;
                    }

                    if (!is_array($dataForm[$ques]) || $values['spec_id'] == ADDITIONAL_BATHROOM_ID) {
                        if (isset($dataForm[$ques]) && $dataForm[$ques] != "") {
                            //OVERWRITE
                            if ($dataForm['addUpdater'] == 'overwrite') {
                                $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id in (" . $pptyList . ")  and spec_id = '" . $values['spec_id'] . "'");
                                if (count($chkSpec) > 0 && $chkSpec != "") {
                                    $condition = " property_id in (" . $pptyList . ") and spec_id = '" . $values['spec_id'] . "' ";
                                    $db->delete(SPEC_ANS, $condition);
                                }
                            }

                            if ($values['spec_id'] == BEDROOM_ID || $values['spec_id'] == BATHROOM_ID) {
                                $ansData = "";
                                for ($tmp = 0; $tmp < 1; $tmp++) {
                                    $ques1 = "ques" . $i . $tmp;
                                    $data_update['spec_id'] = $values['spec_id'];
                                    $ansData .= $dataForm[$ques1] . "|||";
                                    $i++;
                                }
                                $i--;
                                $ansData = substr($ansData, 0, strlen($ansData) - 3);
                                $data_update['answer'] = $ansData;
                                //========== SAVE OPERATION ==============
                                foreach ($pptyArr as $pVal) {
                                    /*
                                     * ADD OPERATION UPDATER
                                     */
                                    //check whether specification is already recorded
                                    if ($dataForm['addUpdater'] == 'add') {
                                        $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = $pVal and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ");
                                        if (count($chkSpec) && !empty($chkSpec)) {
                                            $condition = " property_id = '$pVal' and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ";
                                            $db->delete(SPEC_ANS, $condition);
                                        }
                                    }
                                    $data_update['property_id'] = $pVal;
                                    $db->save(SPEC_ANS, $data_update);
                                }
                            } elseif ($values['spec_id'] == ADDITIONAL_BATHROOM_ID) {
                                $tmp = 0;
                                $ques1 = "ques" . $i . $tmp;


                                for ($tmp = 0; $tmp < 1; $tmp++) {
                                    $ques1 = "ques" . $i . $tmp;
                                    $data_update['spec_id'] = $values['spec_id'];
                                    $ansData = "";

                                    for ($k = 0; $k < count($dataForm[$ques1]); $k++) {

                                        $data_update['spec_id'] = $values['spec_id'];

                                        $ansData .= $dataForm[$ques1][$k] . "|||";
                                    }
                                    $ansData = substr($ansData, 0, strlen($ansData) - 3);

                                    $data_update['answer'] = $ansData;
                                    //========== SAVE OPERATION ==============
                                    foreach ($pptyArr as $pVal) {
                                        /*
                                         * ADD OPERATION UPDATER
                                         */
                                        //check whether specification is already recorded
                                        if ($dataForm['addUpdater'] == 'add') {
                                            $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = $pVal and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ");
                                            if (count($chkSpec) && !empty($chkSpec)) {
                                                $condition = " property_id = '$pVal' and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ";
                                                $db->delete(SPEC_ANS, $condition);
                                            }
                                        }
                                        $data_update['property_id'] = $pVal;
                                        $db->save(SPEC_ANS, $data_update);
                                    }

                                    $i++;
                                }



                                $i--;
                            } else {
                                $data_update['spec_id'] = $values['spec_id'];
                                $data_update['answer'] = $dataForm[$ques];
                                //========== SAVE OPERATION ==============
                                foreach ($pptyArr as $pVal) {

                                    /*
                                     * ADD OPERATION UPDATER
                                     */
                                    //check whether specification is already recorded
                                    if ($dataForm['addUpdater'] == 'add') {
                                        $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = $pVal and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ");
                                        if (count($chkSpec) && !empty($chkSpec)) {
                                            $condition = " property_id = '$pVal' and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ";
                                            $db->delete(SPEC_ANS, $condition);
                                        }
                                    }
                                    $data_update['property_id'] = $pVal;
                                    $db->save(SPEC_ANS, $data_update);
                                }
                            }
                        }
                    } else {
                            //OVERWRITE
                            if (count($dataForm[$ques]) > 0 && $dataForm['addUpdater'] == 'overwrite')
                            {
                                $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id in (" . $pptyList . ") and spec_id = '" . $values['spec_id'] . "'");
                                if (count($chkSpec) > 0 && $chkSpec != "")
                                {
                                    $condition = " property_id in (" . $pptyList . ") and spec_id = '" . $values['spec_id'] . "' ";
                                    $db->delete(SPEC_ANS, $condition);
                                }
                            }

                        for ($k = 0; $k < count($dataForm[$ques]); $k++) {
                            $data_update['spec_id'] = $values['spec_id'];
                            $data_update['answer'] = $dataForm[$ques][$k];
                            //========== SAVE OPERATION ==============
                            foreach ($pptyArr as $pVal) {
                                $data_update['property_id'] = $pVal;


                                /*
                                 * ADD OPERATION UPDATER
                                 */
                                //check whether specification is already recorded
                                if($dataForm['addUpdater'] == 'add'){
                                    $chkSpec = $db->runQuery("select * from " . SPEC_ANS . " where property_id = $pVal and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ");
                                    if (count($chkSpec) && !empty($chkSpec)) {
                                        $condition = " property_id = '$pVal' and spec_id = '" . $values['spec_id'] . "' and answer = '" . $data_update['answer'] . "' ";
                                        $db->delete(SPEC_ANS, $condition);
                                    }
                                }
                                $db->save(SPEC_ANS, $data_update);
                            }
                        }
                    }

                    $i++;
                }
                break;
            case 3:
                $data_update = array();
                $data_update1 = array();

                $dataForm = $postData;
                $pptyList = implode(",", $postData['filteredProperty']);
                ;
                $pptyArr = explode(",", $pptyList);

                $amenArr = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");

                $chkAmenity = $db->runQuery("select * from " . AMENITY_ANS . " where property_id in (" . $pptyList . ") ");

                if (count($chkAmenity) > 0 && $chkAmenity != "") {
                    $condition = " property_id in (" . $pptyList . ") ";
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
                $condition = "id in (" . $pptyList . ") ";

                $db->modify(PROPERTY, $data_update1, $condition);
                //prd($specArr);
                foreach ($amenArr as $values) {
                    $ques = "ques" . $i;
                    $data_update['amenity_id'] = $values['amenity_id'];
                    $data_update['amenity_value'] = $dataForm[$ques];
                    //------- SAVING AMENITIES FOR ALL SELECTED PROPERTIES ------------
                    foreach ($pptyArr as $propVal) {
                        $data_update['property_id'] = $propVal;
                        $db->save(AMENITY_ANS, $data_update);
                    }
                    $i++;
                }

                break;
            case 4:
                $data_update = array();
                $data_update['rental_ques'] = $postData['rental_ques'];
                $pptyList = implode(",", $postData['filteredProperty']);
                $pptyArr = explode(",", $pptyList);
                foreach ($pptyArr as $pptyVal) {
                    $condition = "id=" . $pptyVal;
                    $db->modify(PROPERTY, $data_update, $condition);
                }
                return 'completed';
                break;
        }
    }

}