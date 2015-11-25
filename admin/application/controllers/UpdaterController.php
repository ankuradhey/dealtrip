<?php

__autoloadDB('Db');

class UpdaterController extends AppController {

    public function indexAction() {
        global $mySession;
        $db = new Db();
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $propertyArr = $db->runQuery("select id,propertycode from ".PROPERTY." where propertycode like '11DAT%' ");
        $this->view->propertyArr = $propertyArr;
        $form = new Form_OverviewUpdater();
        $this->view->myform = $form;
        unset($mySession->filteredProperty);
    }
    /*
     * @author: ankit
     * @description: work page 
     */
    public function workpageAction(){
        global $mySession;
        $db = new Db();
        $propertyModel = new Property();
        $subscriberArr = $db->runQuery("select * from subscriber ");
        $this->view->subscriberArr = $subscriberArr;
                    
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (count($post['property-code']) && !empty($post['property-code']))
                $mySession->filteredProperty = implode(",", $post['property-code']);
            
            
            $filterArr['propertyList'] = $mySession->filteredProperty;
            
            $propertyList = $db->runQuery("select id, bedrooms, propertycode, coalesce(local_area_name, sub_area_name, city_name) as location_name ,ptyle_name from ".PROPERTY." p
                                      inner join ".PROPERTY_TYPE." pt on pt.ptyle_id = p.property_type
                                      inner join " . CITIES . " on " . CITIES . ".city_id = p.city_id
                                    left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = p.sub_area_id
                                    left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = p.local_area_id
                                      where id in (".$filterArr['propertyList'].") ");
//            $propertyList = $propertyModel->getProperties($filterArr);
            $this->view->supplierId = $post['supplier'];
            $this->view->propertyList = $propertyList;
        }
    }
    
    public function specificationAction() {
        $form = new Form_SpecificationUpdater();
        $db = new Db();
        global $mySession;
        $propertyModel = new Property();
        $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");
        $this->view->specData = $specArr;
        $this->view->no_bedrooms = 1;
        $filterArr = array();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (count($post['filteredProperty']) && !empty($post['filteredProperty']))
                $mySession->filteredProperty = implode(",", $post['filteredProperty']);
            
            $filterArr['propertyList'] = $mySession->filteredProperty;
            $propertyList = $propertyModel->getProperties($filterArr);
        }
        $this->view->propertyList = $propertyList;
        $this->view->myform = $form;
    }

    public function areaAction() {
        $form = new Form_Amenities();
        $db = new Db();
        global $mySession;
        $model = new Property();
        $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
        $this->view->amenityArr = $amenity;
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (count($post['filteredProperty']) && !empty($post['filteredProperty']))
                $mySession->filteredProperty = implode(",", $post['filteredProperty']);
            
            $filterArr['propertyList'] = $mySession->filteredProperty;
            $propertyList = $model->getProperties($filterArr);
        }
        $this->view->propertyList = $propertyList;
        $this->view->myform = $form;
    }

    public function rentalratesAction() {
        $db = new Db();
        global $mySession;
        $model = new Property();
        $offerTypes = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " where status = '1' ");
        $offerArr = array();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (count($post['filteredProperty']) && !empty($post['filteredProperty']))
                $mySession->filteredProperty = implode(",", $post['filteredProperty']);
            
            $filterArr['propertyList'] = $mySession->filteredProperty;
            $propertyList = $model->getProperties($filterArr);
        }
        $this->view->propertyList = $propertyList;
        $this->view->offerArr = $offerArr;
        $this->view->offerTypes = $offerTypes;
    }

    public function submitAction() {
        global $mySession;
        $model = new Property();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (isset($mySession->filteredProperty)) {
                $post['filteredProperty'] = explode(",", $mySession->filteredProperty);
                $res = $model->updateProperty($post);
                unset($mySession->filteredProperty);
                $this->_redirect("property/index");
            }
        }
    }

    public function propertyListAction() {
        global $mySession;
        $db = new Db();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $propertyModel = new Property();
        $responseData['output'] = 'false';
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $filterArr = array();
            $filterArr['property_type'] = $post['accomodation_type'];
            $filterArr['country_id'] = $post['country_id'];
            $filterArr['state_id'] = $post['state_id'];
            $filterArr['city_id'] = $post['city_id'];
            $filterArr['sub_area_id'] = $post['sub_area'];
            $filterArr['local_area_id'] = $post['local_area'];
            $filterArr['zipcode'] = $post['zipcode'];
            $filterArr['xml_subscriber_id'] = $post['supplier'];
            $filterArr['bedrooms'] = $post['no_of_bedroom'];
            $propertyList = $propertyModel->getProperties($filterArr);
            $responseData['data'] = $propertyList;
            $responseData['output'] = true;
        }
        $this->_helper->json($responseData, true);
    }

    public function setratesAction() {
        global $mySession;
        $db = new Db();
        $rate_string = "";

        $pptyId = $this->getRequest()->getParam("pptyId");

        if ($_REQUEST['Date_f'] != "" && isset($mySession->filteredProperty)) {
            $tmp = explode(".", $_REQUEST['Rate']);
            $ddt = explode("/", $_REQUEST['Date_f']);
            $ddt1 = explode("/", $_REQUEST['Date_t']);
            $start_date = date('Y-m-d', strtotime($ddt[0] . "-" . $ddt[1] . "-" . $ddt[2]));
            $end_date = date('Y-m-d', strtotime($ddt1[0] . "-" . $ddt1[1] . "-" . $ddt1[2]));

            $pptyArr = explode(",", $mySession->filteredProperty);

            $chkCalData = $db->runQuery("select * from " . CAL_RATE . " where property_id in (" . $mySession->filteredProperty . ") ");

            $flag_check = 0;

            $overwrite = $_REQUEST['Option'] == 'overwrite' ? true : false;
            //====== Overwrite=============
            if ($overwrite) {
                $condition = " property_id in (" . $mySession->filteredProperty . ") ";
                $db->delete(CAL_RATE, $condition);
            }

            $dataForm['date_from'] = date('Y-m-d', strtotime($start_date));
            $dataForm['date_to'] = date('Y-m-d', strtotime($end_date));
            $dataForm['nights'] = $_REQUEST['Nights'];
            $dataForm['prate'] = $tmp[0];
            foreach ($pptyArr as $pptyVal) {
                $dataForm['property_id'] = $pptyVal;
                $db->save(CAL_RATE, $dataForm);
            }
            $responseArr = array();
            $responseArr['output'] = 'success';
        } else {
            $responseArr['output'] = 'false';
            $responseArr['message'] = 'Data not found';
        }
        $this->_helper->json($responseData, true);
    }

    function saveextrasAction() {
        global $mySession;
        $db = new Db();

        $rate_string = "";
        if ($_REQUEST['extra_name'] != "") {
            $overwrite = $_REQUEST['Option'] == 'overwrite' ? true : false;
            $pptyArr = explode(",", $mySession->filteredProperty);

            //====== Overwrite=============
            if ($overwrite) {
                $condition = " property_id in (" . $mySession->filteredProperty . ") ";
                $db->delete(EXTRAS, $condition);
            }

            $dataForm['ename'] = $_REQUEST['extra_name'];
            $tmp = explode(".", $_REQUEST['extra_price']);
            $dataForm['eprice'] = $tmp[0];
            $dataForm['etype'] = $_REQUEST['extra_type'];
            $dataForm['stay_type'] = $_REQUEST['stay_type'];

            foreach ($pptyArr as $pptyVal) {
                $dataForm["property_id"] = $pptyVal;
                $db->save(EXTRAS, $dataForm);
            }
        }
        exit;
    }

    /*
     * Works only for overwrite condition
     * 
     * TO DO - add condition
     * ONLY works for overwrite
     */

    function saveoffersAction() {

        global $mySession;
        $db = new Db();
        $pptyId = $this->getRequest()->getParam("pptyId");
        $pptyArr = explode(",", $mySession->filteredProperty);
        //check that if value exist already

        if (isset($mySession->filteredProperty) && $mySession->filteredProperty != "") {
            $chkQuery = $db->runQuery("select * from " . SPCL_OFFERS . " as so 
                                        where so.property_id in (" . $mySession->filteredProperty . ") 
                                        and  so.offer_id = '" . $_REQUEST['offer_id'] . "' group by so.property_id ");

//            echo '<pre>'; print_r($chkQuery); exit('Macro die');
            foreach ($chkQuery as $chkVal) {
//                echo '<pre>'; print_r($countQuery); exit('Macro die');
                if ($_REQUEST['Valid_f'] != "") {
                    $_REQUEST['Valid_f'] = str_replace("/","-",$_REQUEST['Valid_f']);
                    $_REQUEST['Valid_t'] = str_replace("/","-",$_REQUEST['Valid_t']);
                    $_REQUEST['Book_by'] = str_replace("/","-",$_REQUEST['Book_by']);
                    //delete all non empty fields so that overwrite works
                    $deleteChkQuery = $db->delete(SPCL_OFFERS, "offer_id = '" . $chkVal['offer_id'] . "' and property_id = '" . $chkVal['property_id'] . "'");

                    $dataForm = array();
                    $dataForm['offer_id'] = $chkVal['offer_id'];
                    $dataForm['discount_offer'] = $_REQUEST['Discount'];
                    $dataForm['valid_from'] = date('Y-m-d', strtotime($_REQUEST['Valid_f']));
                    $dataForm['valid_to'] = date('Y-m-d', strtotime($_REQUEST['Valid_t']));
                    $dataForm['min_night'] = $_REQUEST['Nights'];
                    if (isset($_REQUEST['max_nights']))
                        $dataForm['max_night'] = $_REQUEST['max_nights'];
                    $dataForm['book_by'] = date('Y-m-d', strtotime($_REQUEST['Book_by']));
                    $dataForm['activate'] = '1';
                    $dataForm["property_id"] = $chkVal['property_id'];
                    $db->save(SPCL_OFFERS, $dataForm);

                    $pptyId = $chkVal['property_id'];

                    //empty value insert
                    $dataForm = array();
                    $dataForm['offer_id'] = $chkVal['offer_id'];
                    $dataForm["property_id"] = $chkVal['property_id'];
                    $db->save(SPCL_OFFERS, $dataForm);

                    //============== code for making entry to latest special offer properties ==========
                    //two cases are there
                    //1. if an already entry with same property is there
                    //2. if new entry is made for that property
                    $identifyArr = $db->runQuery("select * from  " . SLIDES_PROPERTY . " where lppty_type='2' and lppty_property_id = $pptyId ");

                    if (count($identifyArr) > 0 && $identifyArr != "") {

                        $db->delete(SLIDES_PROPERTY, 'lppty_id = ' . $identifyArr[0]['lppty_id']);

                        $dataUpdate = array();
                        $dataUpdate['lppty_order'] = new Zend_Db_Expr('lppty_order-1');

                        $db->modify(SLIDES_PROPERTY, $dataUpdate, 'lppty_type="2" and lppty_order > ' . $identifyArr[0]['lppty_order'] . ' ');

                        $dataUpdate = array();
                        $dataUpdate['lppty_order'] = new Zend_Db_Expr('lppty_order+1');
                        $db->modify(SLIDES_PROPERTY, $dataUpdate, 'lppty_type="2"');


                        $saveUpdate = array();
                        $saveUpdate['lppty_property_id'] = $pptyId;
                        $saveUpdate['lppty_order'] = '1';
                        $saveUpdate['lppty_status'] = '1';
                        $saveUpdate['lppty_type'] = '2';

                        $db->save(SLIDES_PROPERTY, $saveUpdate);
                    } else {

                        $dataUpdate = array();
                        $dataUpdate['lppty_order'] = new Zend_Db_Expr('lppty_order+1');

                        $db->modify(SLIDES_PROPERTY, $dataUpdate, 'lppty_type="2" ');

                        $saveUpdate = array();
                        $saveUpdate['lppty_property_id'] = $pptyId;
                        $saveUpdate['lppty_order'] = '1';
                        $saveUpdate['lppty_status'] = '1';
                        $saveUpdate['lppty_type'] = '2';

                        $db->save(SLIDES_PROPERTY, $saveUpdate);
                    }
                    //----------------------------------------------------------------------------------
                }
            }
        }
        exit;
    }

    public function getratesAction() {
        global $mySession;
        $propertyList = $mySession->filteredProperty;
        $pptyArr = explode(",", $mySession->filteredProperty);
        $db = new Db();
        $rate_string = "";

        $rateData = $db->runQuery("select * from " . CAL_RATE . " where property_id = " . $pptyArr[0] . " order by date_from asc ");

        if ($rateData != "" && count($rateData) > 0) {
            foreach ($rateData as $values) {
                $rate_string .= date('d-m-Y', strtotime($values['date_from'])) . "," . date('d-m-Y', strtotime($values['date_to'])) . "," . $values['nights'] . "," . $values['prate'] . "," . $values['id'] . "|";
            }
            echo $rate_string = substr($rate_string, 0, strlen($rate_string) - 1);
        } else {
            exit(0);
        }

        exit;
    }

    public function getextrasAction() {
        global $mySession;
        $propertyList = $mySession->filteredProperty;
        $pptyArr = explode(",", $mySession->filteredProperty);
        $db = new Db();
        $extra_string = "";

        $pptyId = $this->getRequest()->getParam("pptyId");

        $extraData = $db->runQuery("select * from " . EXTRAS . " where property_id = " . $pptyArr[0] . " ");
        if ($extraData != "" && count($extraData) > 0) {
            foreach ($extraData as $values) {
                $extra_string .= $values['ename'] . "," . $values['eprice'] . "," . $values['etype'] . "," . $values['eid'] . "," . $values['stay_type'] . "|";
            }
            echo $extra_string = substr($extra_string, 0, strlen($extra_string) - 1);
        } else {
            exit(0);
        }

        exit;
    }

    public function getspecialoffersAction() {
        global $mySession;
        $db = new Db();
        $post = $this->getRequest()->getPost();
        $get = $this->getRequest()->getParams();

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $offerObj = $dbAdapter->select()
                ->from(array("sot" => SPCL_OFFER_TYPES))
                ->join(array("so" => SPCL_OFFERS), "so.offer_id = sot.id ")
                ->join(array("property" => PROPERTY), "property.id = so.property_id ", array("propertycode"))
                ->where(" so.property_id in (" . $mySession->filteredProperty . ") ")
                ->where(" so.valid_from is not NULL ");

        $totalArr = $dbAdapter->select()
                        ->from(array("sot" => SPCL_OFFER_TYPES), array("count(*) as _totalRecords"))
                        ->join(array("so" => SPCL_OFFERS), "so.offer_id = sot.id ", array())
                        ->where(" so.property_id in (" . $mySession->filteredProperty . ") ")
                        ->where(" so.valid_from is not NULL ")
                        ->query()->fetch();
        
        /*
         * Paging
         */
        $sLimit = "";
        if ( $get['iDisplayLength'] != '-1') {
            $offerObj = $offerObj->limit($get['iDisplayLength'],$get['iDisplayStart']);
        }
        $offerObj = $offerObj->query()->fetchAll();
        foreach ($offerObj as $offerKey => $offerVal) {
            $offerArr[$offerKey][] = $offerVal['type_name'];
            $offerArr[$offerKey][] = $offerVal['promo_code'];
            $offerArr[$offerKey][] = $offerVal['valid_from'];
            $offerArr[$offerKey][] = $offerVal['valid_to'];
            $offerArr[$offerKey][] = $offerVal['min_nights_type'] == '0' ? $offerVal['min_night'] : $offerVal['min_nights'];
            $discountOffer = "";
            switch ($offerVal['discount_type']) {
                case 0: $discountOffer = $offerVal['discount_offer'] . " %";
                    break;
                case 1: $discountOffer = $offerVal['discount_offer'] . " Night Free";
                    break;
                case 2: $discountOffer = "Free Pool Heating";
                    break;
                case 3: $discountOffer = "7 Nights Free";
                    break;
            }
            $offerArr[$offerKey][] = $discountOffer;
            $offerArr[$offerKey][] = $offerVal['book_by'];
            $offerArr[$offerKey][] = $offerVal['propertycode'];
        }
        $output = array(
            "sEcho" => intval($get['sEcho'] ),
            "iTotalRecords" => $totalArr['_totalRecords'],
            "iTotalDisplayRecords" => count($offerArr),
            "aaData" => $offerArr
        );
        return $this->_helper->json($output, true);
    }

    /*
     * Old index function
     */

    public function indexOldAction() {
        global $mySession;
        $db = new Db();
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $model = new Property();
        $step = $this->getRequest()->getParam('step');

        if (isset($mySession->filteredProperty)) {
            $filteredProperties = explode(",", $mySession->filteredProperty);
            $filteredPpty = $dbAdapter->select()->from(array('p' => PROPERTY), array('p.id', 'g.image_name', 'p.propertycode', 'p.property_type', 'p.property_title', 'p.property_name', 'p.star_rating'))
                    ->joinLeft(array('g' => GALLERY), 'g.property_id = p.id ')
                    ->group('p.id')
                    ->where(" p.id in (" . $mySession->filteredProperty . ") ")
                    ->query()
                    ->fetchAll()
            ;
//                    $db->runQuery("select property_title, from ".PROPERTY." where id in (".$mySession->filteredProperty.") ");
            $this->view->filteredPpty = $filteredPpty;
            $this->view->pptyId = $filteredPpty[0]['id'];
        } else {
            if ($step > 0)
                $this->_redirect("updater/index");
        }
        switch ($step) {
            case '':
            case '0': if (isset($filteredProperties[0]))
                    $form = new Form_OverviewUpdater($filteredProperties[0]);
                else
                    $form = new Form_OverviewUpdater();

                $this->view->step = '1';
                $this->view->myform = $form;
                break;
            case '1': if (isset($filteredProperties[0]))
                    $form = new Form_SpecificationUpdater($filteredProperties[0]);
                else
                    $form = new Form_SpecificationUpdater();

                $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");
                $this->view->specData = $specArr;
                $this->view->step = '2';
                $this->view->myform = $form;
                break;
            case '2': if (isset($filteredProperties[0]))
                    $form = new Form_Amenities($filteredProperties[0]);
                else
                    $form = new Form_Amenities();

                $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
                $this->view->amenityArr = $amenity;
                $this->view->step = '3';
                $this->view->myform = $form;
                break;
            case '3': $this->view->step = '4';
//                $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
//                                           on  sot.id = so.offer_id where so.property_id = '" . $filteredProperties[0] . "' 
//                                           order by so.spcl_offer_id asc
//                                          ");
                $offerTypes = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " where status = '1' ");
                $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
                                           on  sot.id = so.offer_id 
                                           join property on property.id = so.property_id 
                                           where so.property_id in (" . $mySession->filteredProperty . ")
                                           order by so.offer_id asc
                                          ");
                $this->view->offerArr = $offerArr;
                $this->view->offerTypes = $offerTypes;
//                prd($offerArr);
                break;
        }

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (count($post['filteredProperty']) && !empty($post['filteredProperty'])) {
                $mySession->filteredProperty = implode(",", $post['filteredProperty']);

                if (!empty($post['supplier']))
                    $mySession->filteredCriteria['supplier'] = $post['supplier'];
                else
                    $mySession->filteredCriteria['supplier'] = NULL;

                if (!empty($post['no_of_bedroom']))
                    $mySession->filteredCriteria['no_of_bedroom'] = $post['no_of_bedroom'];
                else
                    $mySession->filteredCriteria['no_of_bedroom'] = NULL;

                $model->updateProperty($post);
            } elseif (isset($mySession->filteredProperty)) {
                $res = $model->updateProperty($post);
                if ($res == 'completed') {
                    unset($mySession->filteredProperty);
                    $this->_redirect("property/index");
                }
            } else {
                $this->_redirect("updater/index/step/0");
            }
            $this->_redirect("updater/index/step/" . ($step + 1));
        }
    }

}