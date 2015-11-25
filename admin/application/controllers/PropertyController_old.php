<?php

    __autoloadDB('Db');

    class PropertyController extends AppController
    {
        #-----------------------------------------------------------#
        # Property List Action Function
        #-----------------------------------------------------------#

        public function indexAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Property List";
            $db = new Db();
            $qry = "select * from " . PROPERTY . " as p
			  inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = p.property_type
			  inner join " . USERS . "  as u on u.user_id = p.user_id where p.status != '0'
			  order by p.property_title  ";

            $resData = $db->runQuery("$qry");
            $this->view->ResData = $db->runQuery("$qry");
        }

        public function inhousepropertylistAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Property List";
            $db = new Db();
            $qry = "select * from " . PROPERTY . " as p
			  inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = p.property_type
			  inner join " . USERS . "  as u on u.user_id = p.user_id 
                                                        where p.status != '0' and u.user_id = '11'
			  order by p.property_title  ";

            $resData = $db->runQuery("$qry");
            $this->view->ResData = $db->runQuery("$qry");
            $this->renderScript("property/index.phtml");
        }

        #-----------------------------------------------------------#
        # Property Type Action Function
        #-----------------------------------------------------------#

        public function protypeAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Property Type";
            $db = new Db();
            $qry = "select * from " . PROPERTYTYPE;
            $this->view->ResData = $db->runQuery("$qry");
        }

        #-----------------------------------------------------------#
        # Property Type Add Action Function
        #-----------------------------------------------------------#

        public function addprotypeAction()
        {
            global $mySession;
            $myform = new Form_Protype();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Property Type";
        }

        #-----------------------------------------------------------#
        # Property Type Save Action Function
        #-----------------------------------------------------------#

        public function saveprotypeAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Protype();
            $this->view->pgaeHeading = "Add New Property Type";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Protype();
                    $result = $myObj->saveProtype($dataForm);
                    if ($result == 1)
                    {
                        $mySession->sucessMsg = "New Property Type Added Sucessfully";
                        $this->_redirect('property/protype');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Property Type Already Exists.";
                        $this->view->myform = $myform;
                        $this->render("addprotype");
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render("addprotype");
                }
            }
        }

        #-----------------------------------------------------------#
        # Property Type Edit Action Function
        #-----------------------------------------------------------#

        public function editprotypeAction()
        {
            global $mySession;
            $ptyleId = $this->getRequest()->getParam('ptyleId');
            $this->view->ptyleId = $ptyleId;
            $myform = new Form_Protype($ptyleId);
            $this->view->pageHeading = "Edit Property Type";
            $this->view->myform = $myform;
        }

        #-----------------------------------------------------------#
        # Property Type Update Action Function
        #-----------------------------------------------------------#

        public function updateprotypeAction()
        {
            global $mySession;
            $db = new Db();
            $ptyleId = $this->getRequest()->getParam('ptyleId');
            $this->view->hotelId = $ptyleId;
            $this->view->pageHeading = "Edit Property Type";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Protype($ptyleId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Protype();
                    $Result = $myObj->saveProtype($dataForm, $ptyleId);
                    if ($Result == 1)
                    {
                        $mySession->errorMsg = "Property Type updated successfully.";
                        $this->_redirect('property/protype');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Property Type you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editprotype');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('editprotype');
                }
            }
            else
            {
                $this->_redirect('property/editprotype/ptyleId/' . $ptyleId);
            }
        }

        #-----------------------------------------------------------#
        # Property Type Change Status Action Function
        #-----------------------------------------------------------#

        public function changeprotypestatusAction()
        {
            global $mySession;
            $db = new Db();
            $ptyleId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Protype();
            $Result = $myObj->statusProtype($status, $ptyleId);
            exit();
        }

        #-----------------------------------------------------------#
        # Property Type Delete Action Function
        #-----------------------------------------------------------#

        public function deleteprotypeAction()
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
                        if ($Id > 1)
                        {
                            $myObj = new Protype();
                            $Result = $myObj->deleteProtype($Id);
                        }
                    }
                }
            }
            exit();
        }

        public function amenityAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Amenities";
            $db = new Db();
            $qry = "select * from " . AMENITY;
            $this->view->arrData = $db->runQuery("$qry");
        }

        public function addamenityAction()
        {
            global $mySession;
            $myform = new Form_Amenity();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Amenity";
        }

        /* Save amenity */

        public function processamenityAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Amenity();
            $this->view->pageHeading = "Add New Amenity";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Amenity();
                    $result = $myObj->saveAmenity($dataForm);
                    if ($result == 1)
                    {
                        $mySession->sucessMsg = "New Amenity Added Sucessfully";
                        $this->_redirect('property/amenity');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Amenity Already Exists.";
                        $this->view->myform = $myform;
                        $this->render("addamenity");
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render("addamenity");
                }
            }
        }

        #-----------------------------------------------------------#
        # Amenity Delete Action Function
        #-----------------------------------------------------------#

        public function deleteamenityAction()
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
                        if ($Id > 0)
                        {
                            $myObj = new Amenity();
                            $Result = $myObj->deleteAmenity($Id);
                        }
                    }
                }
            }
            exit();
        }

        #-----------------------------------------------------------#
        # Amenity Edit Action Function
        #-----------------------------------------------------------#

        public function editamenityAction()
        {
            global $mySession;
            $amenityId = $this->getRequest()->getParam('amenityId');
            $this->view->amenityId = $amenityId;
            $myform = new Form_Amenity($amenityId);
            $this->view->pageHeading = "Edit Amenity";
            $this->view->myform = $myform;
        }

        #-----------------------------------------------------------#
        # Amenity Update Action Function
        #-----------------------------------------------------------#

        public function updateamenityAction()
        {
            global $mySession;
            $db = new Db();
            $amenityId = $this->getRequest()->getParam('amenityId');

            $this->view->amenityId = $amenityId;
            $this->view->pageHeading = "Edit Amenity";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Amenity($amenityId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Amenity();
                    $Result = $myObj->saveAmenity($dataForm, $amenityId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Amenity updated successfully.";
                        $this->_redirect('property/amenity');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Amenity you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editamenity');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('editamenity');
                }
            }
            else
            {
                $this->_redirect('property/editamenity/amenityId/' . $amenityId);
            }
        }

        #-----------------------------------------------------------#
        # Amenity Change Status Action Function
        #-----------------------------------------------------------#

        public function changeamenitystatusAction()
        {
            global $mySession;
            $db = new Db();
            $ptyleId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Amenity();
            $Result = $myObj->statusAmenity($status, $ptyleId);
            exit();
        }

        #-----------------------------------------------------------#
        # Amenity page content
        #-----------------------------------------------------------#

        public function amenitypageAction()
        {
            global $mySession;
            $myform = new Form_Propertypage(1);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Amenity Page";
        }

        #-----------------------------------------------------------#
        # Save Amenity page content
        #-----------------------------------------------------------#

        public function saveamenitypageAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Amenity Page";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Propertypage(1);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Propertypage();
                    $Result = $myObj->Updatepage($dataForm, 1);
                    $mySession->errorMsg = "Amenity page updated successfully.";
                    $this->_redirect('property/amenitypage');
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('amenitypage');
                }
            }
            else
            {
                $this->_redirect('property/amenitypage');
            }
        }

        #-----------------------------------------------------------#
        # Property Specification Action Function
        #-----------------------------------------------------------#

        public function specificationAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Property Specification";
            $db = new Db();
            $qry = "select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . "  as psc on s.cat_id = psc.cat_id";
            $order = "order by spec_id desc";

            $this->view->arrData = $db->runQuery("$qry $where $order");
        }

        #-----------------------------------------------------------#
        # Add Property Specification Action Function
        #-----------------------------------------------------------#

        public function addspecificationAction()
        {
            global $mySession;
            $myform = new Form_Specification();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Property Specification";
        }

        public function getoptionsAction()
        {
            global $mySession;
            if ($_REQUEST['Id'] == '0' || $_REQUEST['Id'] == '1' || $_REQUEST['Id'] == '4')
                exit("1");
            else
                exit("0");
        }

        #-----------------------------------------------------------#
        # Save Property Specification Action Function
        #-----------------------------------------------------------#

        public function savespecificationAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Specification();
            $this->view->pgaeHeading = "Add New Specification";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Specification();
                    $result = $myObj->saveSpecification($dataForm);
                    if ($result == 1)
                    {
                        $mySession->sucessMsg = "New Property Specification Added Sucessfully";
                        $this->_redirect('property/specification');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Specification Already Exists.";
                        $this->view->myform = $myform;
                        $this->render("addspecification");
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render("addspecification");
                }
            }
        }

        #-----------------------------------------------------------#
        # Change Property Specification Status Action Function
        #-----------------------------------------------------------#

        public function changespecstatusAction()
        {
            global $mySession;
            $db = new Db();
            $specId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Specification();
            $Result = $myObj->statusSpecification($status, $specId);
            exit();
        }

        #-----------------------------------------------------------#
        # Property Specification Edit Action Function
        #-----------------------------------------------------------#

        public function editspecificationAction()
        {
            global $mySession;
            $specId = $this->getRequest()->getParam('specId');
            $this->view->specId = $specId;
            $myform = new Form_Specification($specId);
            $this->view->pageHeading = "Edit Property Specification";
            $this->view->myform = $myform;
        }

        #-----------------------------------------------------------#
        # Specification Update Action Function
        #-----------------------------------------------------------#

        public function updatespecificationAction()
        {
            global $mySession;
            $db = new Db();
            $specId = $this->getRequest()->getParam('specId');

            $this->view->specId = $specId;
            $this->view->pageHeading = "Edit Property Specification";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Specification($specId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Specification();
                    //prd($dataForm);
                    $Result = $myObj->saveSpecification($dataForm, $specId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Specification updated successfully.";
                        $this->_redirect('property/specification');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Specification you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editspecification');
                    }
                }
                else
                { //$dataForm = $myform->getValues();
                    //prd($dataForm);
                    $this->view->myform = $myform;
                    $this->render('editspecification');
                }
            }
            else
            {
                $this->_redirect('property/editspecification/specId/' . $specId);
            }
        }

        #-----------------------------------------------------------#
        # Specification Delete Action Function
        #-----------------------------------------------------------#

        public function deletespecificationAction()
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
                        if ($Id > 1)
                        {
                            $myObj = new Specification();
                            $Result = $myObj->deleteSpecification($Id);
                        }
                    }
                }
            }
            exit();
        }

        #-----------------------------------------------------------#
        # Property Specification Category Action Function
        #-----------------------------------------------------------#

        public function propertyspeccatAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Property Specification Category";
            $db = new Db();
            $qry = "select * from " . PROPERTY_SPEC_CAT;
            $this->view->ResData = $db->runQuery("$qry");
        }

        #-----------------------------------------------------------#
        # Add Property Specification Category Action Function
        #-----------------------------------------------------------#

        public function addspeccatAction()
        {
            global $mySession;
            $myform = new Form_Speccat();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Property Specification Category";
        }

        public function processspeccatAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Speccat();
            $this->view->pageHeading = "Add New Property Specification Category";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Speccat();
                    $result = $myObj->saveSpeccat($dataForm);
                    if ($result == 1)
                    {
                        $mySession->sucessMsg = "New Property Specification Category Added Sucessfully";
                        $this->_redirect('property/propertyspeccat');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Category name Already Exists.";
                        $this->view->myform = $myform;
                        $this->render("addspeccat");
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render("addspeccat");
                }
            }
        }

        #-----------------------------------------------------------#
        # Property Specification Category Edit Action Function
        #-----------------------------------------------------------#

        public function editspeccatAction()
        {
            global $mySession;
            $cId = $this->getRequest()->getParam('cId');
            $this->view->cId = $cId;
            $myform = new Form_Speccat($cId);
            $this->view->pageHeading = "Edit Property Specification Category";
            $this->view->myform = $myform;
        }

        #-----------------------------------------------------------#
        # Specification Update Action Function
        #-----------------------------------------------------------#

        public function updatespeccatAction()
        {
            global $mySession;
            $db = new Db();
            $cId = $this->getRequest()->getParam('cId');

            $this->view->specId = $specId;
            $this->view->pageHeading = "Edit Property Specification Category";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Speccat($specId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Speccat();
                    $Result = $myObj->saveSpeccat($dataForm, $cId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Property Specification Category updated successfully.";
                        $this->_redirect('property/propertyspeccat');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Category you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editspeccat');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('editspeccat');
                }
            }
            else
            {
                $this->_redirect('property/editspeccat/cId/' . $cId);
            }
        }

        #-----------------------------------------------------------#
        # Change Property Specification Category Status
        #-----------------------------------------------------------#

        public function changespeccatstatusAction()
        {
            global $mySession;
            $db = new Db();
            $cId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Speccat();
            $Result = $myObj->statusSpeccat($status, $cId);
            exit();
        }

        #-----------------------------------------------------------#
        # Property Specification Category Delete Action Function
        #-----------------------------------------------------------#

        public function deletepropertysepeccatAction()
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
                        if ($Id > 1)
                        {
                            $myObj = new Speccat();
                            $Result = $myObj->deleteSpeccat($Id);
                            if (!$Result)
                                exit("First delete the child entries");
                        }
                    }
                }
            }
            exit();
        }

        #-----------------------------------------------------------#
        # Property Special Offers Action Function
        #-----------------------------------------------------------#

        public function spclofferAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Manage Special Offer Category";
            $qry = "select * from " . SPCL_OFFER_TYPES;
            $this->view->ResData = $db->runQuery("$qry");
        }

        #-----------------------------------------------------------#
        # Property Special Offers Delete Action Function
        #-----------------------------------------------------------#

        public function addspclofferAction()
        {
            global $mySession;
            $myform = new Form_Spcloffer();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Special Offer Category";
        }

        public function processspclofferAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Spcloffer();
            $this->view->pageHeading = "Add New Special Offer Category";

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Spcloffer();
                    $result = $myObj->saveOffer($dataForm);
                    if ($result == 1)
                    {
                        $mySession->sucessMsg = "New Special Offer Category Added Sucessfully";
                        $this->_redirect('property/spcloffer');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Special Offer Category name Already Exists.";
                        $this->view->myform = $myform;
                        $this->render("addspcloffer");
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render("addspcloffer");
                }
            }
        }

        #-----------------------------------------------------------#
        # Property Special Offers Edit Action Function
        #-----------------------------------------------------------#

        public function editspclofferAction()
        {
            global $mySession;
            $sId = $this->getRequest()->getParam('sId');
            $this->view->sId = $sId;
            $myform = new Form_Spcloffer($sId);
            $this->view->pageHeading = "Edit Special Offer";
            $this->view->myform = $myform;
        }

        public function updatespclofferAction()
        {
            global $mySession;
            $db = new Db();
            $sId = $this->getRequest()->getParam('sId');
            $this->view->sId = $sId;

            $this->view->pageHeading = "Edit Special Offer";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Spcloffer($sId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Spcloffer();
                    $Result = $myObj->saveOffer($dataForm, $sId);
                    if ($Result == 1)
                    {
                        $mySession->errorMsg = "Special Offer updated successfully.";
                        $this->_redirect('property/spcloffer');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Special Offer Cateogory you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editspcloffer');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('editspcloffer');
                }
            }
            else
            {
                $this->_redirect('property/editspcloffer/sId/' . $sId);
            }
        }

        #-----------------------------------------------------------#
        # Property Special Offers delete Action Function
        #-----------------------------------------------------------#

        public function deletespclofferAction()
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
                        if ($Id > 1)
                        {
                            $myObj = new Spcloffer();
                            $Result = $myObj->deleteOffer($Id);
                        }
                    }
                }
            }
            exit();
        }

        #-----------------------------------------------------------#
        # Change Property Special Offer Status
        #-----------------------------------------------------------#

        public function changespclstatusAction()
        {
            global $mySession;
            $db = new Db();
            $cId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Property();
            $Result = $myObj->statusspclOffer($status, $cId);
            exit();
        }

        #-----------------------------------------------------------#
        # Change Property Status
        #-----------------------------------------------------------#

        public function changepptystatusAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            $myObj = new Property();
            $Result = $myObj->statusProperty($status, $pptyId);
            exit();
        }

        #-----------------------------------------------------------#
        # Change Property Status
        #-----------------------------------------------------------#

        public function editpropertyAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            $stepId = $this->getRequest()->getParam("step");

            $this->view->pptyId = $pptyId;
            $this->view->step = $stepId;
            $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");

            $preview_link_data = $db->runQuery("select " . PROPERTY . ".propertycode ," . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,ptyle_name, bedrooms, bathrooms  from " . PROPERTY . " 
                                                    inner join " . PROPERTY_TYPE . " on " . PROPERTY_TYPE . ".ptyle_id = " . PROPERTY . ".property_type
                                                    inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                    inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                                    inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                                    left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                                    left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                    where id = '" . $pptyId . "'
                                                   ");

            $this->view->assign($preview_link_data[0]);

            $this->view->pageHeading = "You are currently editing -- Property no - " . $ratingArr[0]['propertycode'];
            switch ($stepId)
            {
                case '0': $myform = new Form_Ownproperty($pptyId);

                    $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->rating = $ratingArr[0]['star_rating'];
                    break;
                case '1': $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");
                    $this->view->specData = $specArr;
                    $myform = new Form_Propertyspec($pptyId);
                    //bathroom no. 
                    $bathroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->no_bedrooms = $bathroomArr[0]['bedrooms'];
                    break;
                case '2': $myform = new Form_Amenities($pptyId);
                    $amenity = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
                    $this->view->amenityArr = $amenity;
                    break;
                case '3': $myform = new Form_Location($pptyId);
                    break;
                case '4': $Arr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->floorPlan = $Arr[0]['floor_plan'];
                    break;
                case '5': $myform = new Form_Cal();
                    $next = $this->getRequest()->getParam("cal");
                    if ($next != "")
                        $this->view->nexts = $next;
                    else
                        $this->view->nexts = 0;
                    $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $pptyId . "' ");
                    $this->view->calArr = $calArr;
                    //passing default value of calendar to view
                    $pptyArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->cal_default = $pptyArr[0]['cal_default'];
                    break;
                case '6': $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " as sot left join " . SPCL_OFFERS . " as so 
								   on  sot.id = so.offer_id where so.property_id = '" . $pptyId . "' order by so.spcl_offer_id asc
								   ");


                    if (count($offerArr) == 0)
                    {
                        $offerArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . "");
                        foreach ($offerArr as $values)
                        {
                            $dataForm['offer_id'] = $values['id'];
                            $dataForm["property_id"] = $pptyId;
                            $db->save(SPCL_OFFERS, $dataForm);
                        }
                    }


                    $this->view->offerArr = $offerArr;
                    //echo $mySession->step; exit;
                    //rental question
                    $rentalQues = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");

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
                case '7': $myform = new Form_Oreview($pptyId);

                    $uData = $db->runQuery("select * from " . OWNER_REVIEW . " where property_id = '" . $pptyId . "' ");
                    $this->view->myform = $myform;
                    if (!isset($mySession->reviewImage))
                        $mySession->reviewImage = "no_owner_pic.jpg";

                    $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " as r
													inner join " . PROPERTY . " as p on p.id = r.property_id
													inner join " . USERS . " as u on u.user_id = p.user_id
													where r.property_id = '" . $pptyId . "' order by r.review_id desc ");



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
                case '8': $myform = new Form_Arrival($pptyId);
                    $Arrival = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->arrival = $Arrival[0]['arrival_instruction'];
                    $this->view->arrival1 = $Arrival[0]['arrival_instruction1'];
                    $this->view->arrival2 = $Arrival[0]['arrival_instruction2'];
                    break;
            }

            $this->view->myform = $myform;
        }

        public function processpageAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            $stepId = $this->getRequest()->getParam("step");



            switch ($stepId)
            {
                case '0': $myform = new Form_Ownproperty($pptyId);
                    $ratingArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");
                    $this->view->rating = $ratingArr[0]['star_rating'];
                    break;
                case '1':
                    $myform = new Form_Propertyspec($pptyId);

                    break;
                case '2': $myform = new Form_Amenities($pptyId);
                    break;
                case '3': $myform = new Form_Location($pptyId);
                    break;
                case '7': $this->_redirect("myaccount/editproperty/ppty/" . $pptyId);
                    $myform = new Form_Oreview();
                    break;
                case '8': $myform = new Form_Arrival($pptyId);
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

//                    prd($dataForm);

                    $Result = $myObj->savePropertyintro($dataForm, $pptyId);
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
                    $this->render("editproperty");
                }
            }


            $this->_redirect("property/editproperty/pptyId/" . $pptyId . "/step/" . $stepId);
        }

        public function floorplanAction()
        {
            global $mySession;
            $db = new Db();
            $flag_status = 0;



            $pptyId = $this->getRequest()->getParam("pptyId");
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

        /* public function uploadAction()
          {
          global $mySession;
          $db=new Db();
          $this->_helper->layout()->disableLayout();

          $pptyId = $this->getRequest()->getParam("pptyId");

          $stepId = $this->getRequest()->getParam("step");

          $imgArr = $db->runQuery("select * from ".GALLERY." where property_id = '".$pptyId."' ");


          //echo $_REQUEST['value']; exit;
          if(count($imgArr) <= 24)
          {

          $data = explode(',', $_REQUEST['value']);
          // Encode it correctly
          $encodedData = str_replace(' ','+',$data[1]);
          $decodedData = base64_decode($encodedData);

          // You can use the name given, or create a random name.
          // We will create a random name!


          //process for trimming all the spaces and joining them
          $spacetrim = str_replace(" ","_",$_REQUEST['name']);


          $randomName = time()."_".$spacetrim;


          if(file_put_contents(SITE_ROOT."images/property/".$randomName, $decodedData)) {
          // process of saving in database //
          $dataForm['step'] = '5';
          $dataForm['image'] = $randomName;
          $dataForm['property_id'] = $pptyId;
          //$myObj = new Propertyintro();
          //$Result = $myObj->savePropertyintro($dataForm, $mySession->property_id);

          //check whether the image uploaded is the first one
          $orderQuery = $db->runQuery("select * from ".GALLERY." where property_id = '".$dataForm['property_id']."' ");
          $resultQuery = $db->runQuery("select * from ".PROPERTY." where id = '".$dataForm['property_id']."' ");


          $data_update = array();

          if(count($orderQuery) == "0")
          $data_update['image_title'] = $resultQuery[0]['property_title'];


          $data_update['property_id'] = $dataForm['property_id'];
          $data_update['image_name'] = $dataForm['image'];
          $db->save(GALLERY,$data_update);

          echo $randomName.":uploaded successfully";
          }
          else {
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
         */

        public function uploadedAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            $stepId = $this->getRequest()->getParam("step");

            $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $pptyId . "' ");

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

        public function deletepropertyAction()
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
                        $condition = "id=" . $Id;
                        $db->delete(PROPERTY, $condition);
                        $condition = "property_id = " . $Id;
                        $db->delete(AMENITY_ANS, $condition);
                        $db->delete(CAL_AVAIL, $condition);
                        $db->delete(EXTRAS, $condition);
                        $db->delete(GALLERY, $condition);
                        $db->delete(SPCL_OFFERS, $condition);
                        $db->delete(SPEC_ANS, $condition);
                    }
                }
            }

            $mySession->sucessMsg = "Property sucessfully deleted";
            exit;
        }

        public function changepopularstatusAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            $myObj = new Property();
            $Result = $myObj->statusPopularProperty($status, $pptyId);
            exit();
        }

        public function savecalendarstatAction()
        {
            global $mySession;
            $db = new Db();


            $pptyId = $this->getRequest()->getParam("pptyId");

            $ddt = explode("/", $_REQUEST['Datef']);
            $ddt1 = explode("/", $_REQUEST['Datet']);

            $start_date = date('Y-m-d', strtotime($ddt[0] . "-" . $ddt[1] . "-" . $ddt[2]));
            $end_date = date('Y-m-d', strtotime($ddt1[0] . "-" . $ddt1[1] . "-" . $ddt1[2]));

            $calendar_flag = 0;  //condition for checking
            $calendar_flag_total = 0;
            //query for checking earlier records related to calendar

            $chkCalData = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $pptyId . "' ");
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
                            //$dataUpdate['property_id'] = $pptyId;
                            $dataUpdate = array();
                            $dataUpdate['date_from'] = date("Y-m-d", mktime(0, 0, 0, date('m', strtotime($end_date)), date('d', strtotime($end_date)) + 1, date('Y', strtotime($end_date))));
                            $condition = 'cal_id=' . $values['cal_id'];
                            $db->modify(CAL_AVAIL, $dataUpdate, $condition);

                            $calendar_flag = 1;  //condition for checking
                        }

                        //saving ''


                        if ($calendar_flag != 1)
                        {
                            $condition = 'cal_id=' . $values['cal_id'];
                            $db->delete(CAL_AVAIL, $condition);
                        }

                        $dataForm = array();
                        $dataForm['property_id'] = $pptyId;
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
                        $dataForm['property_id'] = $pptyId;
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
                        //$condition = "  cal_id=".$values['cal_id'];
                        $condition = " date_from >= '" . $start_date . "' and date_to  <=  '" . $end_date . "'  and  property_id = '" . $pptyId . "' ";
                        $db->delete(CAL_AVAIL, $condition);
                    }
                    $dataForm = array();
                    $dataForm['property_id'] = $pptyId;
                    $dataForm['date_to'] = $end_date;
                    $dataForm['date_from'] = $start_date;
                    $dataForm['cal_status'] = $_REQUEST['Status'];
                    $db->save(CAL_AVAIL, $dataForm);
                }
            }
            else
            {

                $dataForm['property_id'] = $pptyId;
                $dataForm['date_from'] = $start_date;
                $dataForm['date_to'] = $end_date;
                $dataForm['cal_status'] = $_REQUEST['Status'];
                $db->save(CAL_AVAIL, $dataForm);
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
                if ($pptyId != "")
                    $condition = "id = '" . $pptyId . "' ";
                else
                    $condition = "id = '" . $mySession->property_id . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
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

                    $dataForm['offer_id'] = $chkQuery[0]['offer_id'];
                    $dataForm['discount_offer'] = $_REQUEST['Discount'];
                    $dataForm['valid_from'] = date('Y-m-d', strtotime($_REQUEST['Valid_f']));
                    $dataForm['valid_to'] = date('Y-m-d', strtotime($_REQUEST['Valid_t']));
                    $dataForm['min_night'] = $_REQUEST['Nights'];
                    $dataForm['book_by'] = date('Y-m-d', strtotime($_REQUEST['Book_by']));
                    $dataForm['activate'] = '1';

                    if ($pptyId == "")
                        $condition = "property_id=" . $mySession->property_id . " and spcl_offer_id = " . $_REQUEST['Spcl_offer_id'];
                    else
                        $condition = "property_id=" . $pptyId . " and spcl_offer_id = " . $_REQUEST['Spcl_offer_id'];
                    $db->modify(SPCL_OFFERS, $dataForm, $condition);

                    $pptyId = !empty($pptyId) ? $pptyId : $mySession->property_id;

                    //============== code for making entry to latest special offer properties ==========
                    //two cases are there
                    //1. if an already entry with same property is there
                    //2. if new entry is made for that property
                    $identifyArr = $db->runQuery("select * from  " . SLIDES_PROPERTY . " where lppty_type='2' and lppty_property_id = $pptyId ");

                    if (count($identifyArr) > 0 && $identifyArr != "")
                    {

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
                    }
                    else
                    {

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

        public function setratesAction()
        {



            global $mySession;
            $db = new Db();
            $rate_string = "";

            $pptyId = $this->getRequest()->getParam("pptyId");

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


                $chkCalData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $pptyId . "' ");



                $flag_check = 0;

                foreach ($chkCalData as $val)
                {
                    if ($start_date <= date('Y-m-d', strtotime($val['date_from'])) && $end_date >= date('Y-m-d', strtotime($val['date_to'])))
                    {

                        $flag_check = 1;
                        $condition = "id=" . $val['id'];
                        $db->delete(CAL_RATE, $condition);
                    }
                }




                $chkCalData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $pptyId . "' ");

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
                            $dataForm['property_id'] = $pptyId;
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

                            $dataForm['property_id'] = $pptyId;
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
                            $dataForm['property_id'] = $pptyId;
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
                            $dataForm['property_id'] = $pptyId;
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
                        $dataForm['property_id'] = $pptyId;
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
                    $dataForm['property_id'] = $pptyId;
                    $dataForm['date_from'] = date('Y-m-d', strtotime($start_date));
                    $dataForm['date_to'] = date('Y-m-d', strtotime($end_date));
                    $dataForm['nights'] = $_REQUEST['Nights'];
                    $dataForm['prate'] = $tmp[0];
                    $db->save(CAL_RATE, $dataForm);
                }
            }
            exit;
        }

        public function deleterentalrateAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $this->getRequest()->getParam("pptyId");
            $adminId = $this->getRequest()->getParam("id");

            if ($adminId > 0)
            {
                $condition = "id=" . $adminId;
                $db->delete(CAL_RATE, $condition);
                exit("1");
            }

            //code for changing the status of the step 7
            $chkStat = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $pptyId . "' ");

            if (count($chkStat) == 0)
            {
                $status_data['status_7'] = '0';
                $condition = "id=" . $pptyId;
                $db->modify(PROPERTY, $status_data, $condition);
            }

            exit;
        }

        public function getratesAction()
        {
            global $mySession;
            $db = new Db();
            $rate_string = "";
            $pptyId = $this->getRequest()->getParam("pptyId");

            $rateData = $db->runQuery("select * from " . CAL_RATE . " where property_id = '" . $pptyId . "' order by date_from asc ");

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

        public function getextrasAction()
        {
            global $mySession;
            $db = new Db();
            $extra_string = "";

            $pptyId = $this->getRequest()->getParam("pptyId");

            $extraData = $db->runQuery("select * from " . EXTRAS . " where property_id = '" . $pptyId . "' ");
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

        public function saveextrasAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            $rate_string = "";
            if ($_REQUEST['extra_name'] != "")
            {
                $dataForm['ename'] = $_REQUEST['extra_name'];
                $tmp = explode(".", $_REQUEST['extra_price']);
                $dataForm['eprice'] = $tmp[0];
                $dataForm['etype'] = $_REQUEST['extra_type'];
                $dataForm['stay_type'] = $_REQUEST['stay_type'];
                $dataForm["property_id"] = $pptyId;
                $db->save(EXTRAS, $dataForm);
            }
            exit;
        }

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

        public function savereviewAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            {
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
            }

            exit;
        }

        public function savereviewreplyAction()
        {
            global $mySession;
            $db = new Db();
            $data_update = array();
            $pptyId = $this->getRequest()->getParam("pptyId");
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

            if ($pptyId != "")
                $data_update["property_id"] = $pptyId;
            else
                $data_update["property_id"] = $mySession->property_id;
            $db->save(OWNER_REVIEW, $data_update);

            $mySession->sucessMsg = "Your reply has been sent for approval by the admin";
            exit;
        }

        #-----------------------------------------------------------#
        # Owner HOme Page Action Function
        #-----------------------------------------------------------#

        public function ownerhomeAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_Propertypage(2);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Amenity Page";
        }

        #-----------------------------------------------------------#
        # Save  HOme Page content
        #-----------------------------------------------------------#

        public function saveownerhomeAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Owner Home Page";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Propertypage(2);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Propertypage();
                    $Result = $myObj->Updatepage($dataForm, 2);
                    $mySession->errorMsg = "Owner Home page updated successfully.";
                    $this->_redirect('property/ownerhome');
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('ownerhome');
                }
            }
            else
            {
                $this->_redirect('property/ownerhome');
            }
        }

        #-----------------------------------------------------------#
        # list news action
        #-----------------------------------------------------------#

        public function newsAction()
        {
            global $mySession;
            $db = new Db();
            $qry = "select * from " . NEWS . "";
            $this->view->pageHeading = "Manage News";
            $resData = $db->runQuery("$qry");
            $this->view->ResData = $db->runQuery("$qry");
        }

        #-----------------------------------------------------------#
        # Add news action function
        #-----------------------------------------------------------#

        public function addnewsAction()
        {
            global $mySession;
            $db = new Db();
            $myform = new Form_News();
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New News";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new News();
                    $Result = $myObj->SaveNews($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "New News added successfully.";
                        $this->_redirect('property/news');
                    }
                    else
                    {
                        $mySession->errorMsg = "News date is already exists.";
                    }
                }
            }
        }

        #-----------------------------------------------------------#
        # News Edit Action Function
        #-----------------------------------------------------------#

        public function editnewsAction()
        {
            global $mySession;
            $db = new Db();
            $newsId = $this->getRequest()->getParam('newsId');
            $this->view->newsId = $newsId;
            $myform = new Form_News($newsId);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Edit News";

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();

                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new News();
                    $Result = $myObj->UpdateNews($dataForm, $newsId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "News details updated successfully.";
                        $this->_redirect('property/news');
                    }
                    else
                    {
                        $mySession->errorMsg = "News date is already exists.";
                    }
                }
            }
        }

        #-----------------------------------------------------------#
        # News Status Action Function
        #-----------------------------------------------------------#

        public function changenewsstatusAction()
        {
            global $mySession;
            $db = new Db();
            $ptyleId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new News();
            $Result = $myObj->statusNews($status, $ptyleId);
            exit();
        }

        #-----------------------------------------------------------#
        # Property Type Delete Action Function
        #-----------------------------------------------------------#

        public function deletenewsAction()
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
                        if ($Id > 1)
                        {
                            $myObj = new News();
                            $Result = $myObj->deleteNews($Id);
                        }
                    }
                }
            }
            exit();
        }

        public function instuctionuploadAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();

            $pptyId = $this->getRequest()->getParam("pptyId");

            $data = $_FILES['arrival_instruction']['name'];


            // You can use the name given, or create a random name.
            // We will create a random name!

            $extnsn = explode(".", $data);
            $extnsn = array_pop($extnsn);

            $allowed_extnsn = explode(",", INSTRUCTION_EXTNSN);

            if (!in_array($extnsn, $allowed_extnsn))
            {

                $msg['error'] = "Only doc, image or pdf uploadable";
            }
            else
            {
                $randomName = time() . "_" . $_FILES['arrival_instruction']['name'];

                //checking query

                $chkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");






                if (move_uploaded_file($_FILES['arrival_instruction']['tmp_name'], SITE_ROOT . "uploads/instructions/" . $randomName))
                {
                    // process of saving in database //

                    if ($chkQuery[0]['arrival_instruction'] == "" || $chkQuery[0]['arrival_instruction'] == NULL)
                    {
                        $dataForm['arrival_instruction'] = $randomName;
                        $condition = "id=" . $pptyId;
                        $db->modify(PROPERTY, $dataForm, $condition);

                        $msg['success'] = "Instruction File 1 uploaded sucessfully";
                    }
                    elseif ($chkQuery[0]['arrival_instruction1'] == "" || $chkQuery[0]['arrival_instruction1'] == NULL)
                    {
                        $dataForm['arrival_instruction1'] = $randomName;
                        $condition = "id=" . $pptyId;
                        $db->modify(PROPERTY, $dataForm, $condition);

                        $msg['success'] = "Instruction File 2 uploaded sucessfully";
                    }
                    elseif ($chkQuery[0]['arrival_instruction2'] == "" || $chkQuery[0]['arrival_instruction2'] == NULL)
                    {
                        $dataForm['arrival_instruction2'] = $randomName;
                        $condition = "id=" . $pptyId;
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

            $pptyId = $this->getRequest()->getParam("pptyId");
            $file = $this->getRequest()->getParam("file");

            $chkQuery = $db->runQuery("select * from " . PROPERTY . " where id = '" . $pptyId . "' ");

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

            $condition = "id=" . $pptyId;
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

        public function processrentalAction()
        {
            global $mySession;
            $db = new Db();
            $pptyId = $this->getRequest()->getParam("pptyId");

            if ($this->getRequest()->isPost() && isset($_REQUEST['step']) && $_REQUEST['step'] == '7')
            {
                if ($_REQUEST['rental_ques'] != "")
                {
                    $dataForm = array();
                    $dataForm['rental_ques'] = $_REQUEST['rental_ques'];
                    $condition = "id = " . $pptyId;
                    $db->modify(PROPERTY, $dataForm, $condition);
                }
                $mySession->step = '7';
            }

            $ppty_id = $this->getRequest()->getParam("ppty");
            if ($ppty_id != "")
                $this->_redirect("property/editproperty/ppty/" . $ppty_id);
            else
                $this->_redirect("inhouse/addproperty");
        }

        public function reviewsAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Reviews";
            $db = new Db();
            $qry = "select * from " . OWNER_REVIEW . " 
			  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . OWNER_REVIEW . ".property_id
			  order by " . OWNER_REVIEW . ".review_date desc ";
            $this->view->arrData = $db->runQuery("$qry");
        }

        public function editreviewAction()
        {
            global $mySession;
            $reviewId = $this->getRequest()->getParam('reviewId');
            $this->view->reviewId = $reviewId;
            $myform = new Form_Review($reviewId);
            $this->view->pageHeading = "Edit Review";
            $this->view->myform = $myform;
        }

        public function updatereviewAction()
        {
            global $mySession;
            $db = new Db();
            $reviewId = $this->getRequest()->getParam('reviewId');

            $this->view->reviewId = $reviewId;
            $this->view->pageHeading = "Edit Review";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Review($reviewId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Review();
                    $Result = $myObj->savereview($dataForm, $reviewId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Review updated successfully.";
                        $this->_redirect('property/reviews');
                    }
                    else
                    {
                        $mySession->errorMsg = "This Review you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('editreview');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('editreview');
                }
            }
            else
            {
                $this->_redirect('property/editreview/reviewId/' . $reviewId);
            }
        }

        public function changereviewstatusAction()
        {
            global $mySession;
            $db = new Db();
            $ptyleId = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else
            {
                $status = '1';
            }
            $myObj = new Review();
            $Result = $myObj->statusreview($status, $ptyleId);
            exit();
        }

        public function deletereviewAction()
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
                        if ($Id > 0)
                        {
                            $myObj = new Review();
                            $Result = $myObj->deletereview($Id);
                        }
                    }
                }
            }
            exit();
        }

        public function processphotoAction()
        {
            $this->_helper->layout()->disableLayout;
            global $mySession;
            $db = new Db();

            $pptyId = $this->_getParam("ppty", $mySession->property_id);


            //prd($_REQUEST['order_image']);
            $msg = array();
            //if ($this->getRequest()->isPost())
            {

                if (empty($_REQUEST['order_image']))
                {   //$mySession->errorMsg = "Images not chosen";
                    $msg['error'] = true;
                    $msg['message'] = "Image not chosen";
                    $msg['success'] = false;
                    ;

                    exit(json_encode($msg));
                }
                else
                {
                    $sort_images = explode('&', $_REQUEST['order_image']);
                    //pr($sort_images);

                    for ($i = 0; $i < count($sort_images); $i++)
                        $sort_image[] = explode('=', $sort_images[$i]);
                    //prd($sort_image);
                    //update the order of records
                    $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $pptyId . "' ");
                    //prd($imgArr);
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

                    $conditionUpdate = " property_id = " . $pptyId;
                    //pr($temp);
                    //prd($dataUpdate);
                    //echo $i;
                    //prd($dataUpdate);
                    //prd($temp);
                    $db->delete(GALLERY, $conditionUpdate);

                    for ($k = 0; $k < $i; $k++)
                    {
                        $pos = $temp[$k];
                        $dataInsert['property_id'] = $pptyId;
                        $dataInsert['image_name'] = $dataUpdate['image_name'][$pos];
                        $dataInsert['image_title'] = $dataUpdate['image_title'][$pos];
                        $db->save(GALLERY, $dataInsert);
                    }

                    //code for changing the active status 
                    $data_status['status_5'] = '1';
                    $condition = "id=" . $pptyId;
                    $db->modify(PROPERTY, $data_status, $condition);


                    //$mySession->step = '5';
                }
            }

            exit;
//            if ($ppty_id != "")
//                $this->_redirect("myaccount/editproperty/ppty/" . $ppty_id);
//            else
//                $this->_redirect("myaccount/addproperty");
        }

        public function setcaldefaultAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("pptyId");
            if ($_REQUEST['Value'] != "")
            {
                $data_update['cal_default'] = $_REQUEST['Value'];
                $data_update['status_6'] = '1';
                $condition = "id = '" . $pptyId . "' ";
                $db->modify(PROPERTY, $data_update, $condition);
            }
            exit;
        }

        //showing the deleted property list

        public function deletedpropertyAction()
        {
            global $mySession;
            $db = new Db();

            $this->view->pageHeading = "List of Deleted Properties";

            $pptyArr = $db->runQuery("select * from " . PROPERTY . " 
								  inner join  " . PROPERTY_TYPE . " on  " . PROPERTY_TYPE . ".ptyle_id = " . PROPERTY . ".property_type 
								  where " . PROPERTY . ".status = '0' order by " . PROPERTY . ".id desc ");

            /* $dateArr = $db->runQuery("select * from  ".PROPERTY." order by id desc ");


              $i = 0;
              foreach($dateArr as $values)
              {

              $date = date('Y-m-d');
              $dataForm['date_added'] = date('Y-m-d', strtotime('- '.$i.' day '.$date));
              $condition = 'id ='.$values['id'];
              $db->modify(PROPERTY,$dataForm,$condition);
              $i++;

              }
             */


            $this->view->ResData = $pptyArr;
        }

        //reactivating the property

        public function reactivateAction()
        {
            $db = new Db();
            global $mySession;

            $pptyId = $_REQUEST['Id'];

            $pptyArr = $db->runQuery("select old_status,status from " . PROPERTY . " where id = " . $pptyId . " ");



            if ($pptyArr != "" && count($pptyArr) > 0)
            {
                $dataUpdate['status'] = $pptyArr[0]['old_status'];
                $condition = " id = " . $pptyId;
                $db->modify(PROPERTY, $dataUpdate, $condition);

                exit("deleted");
            }

            exit("not deleted");
        }

        public function deletefloorplanAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("ppty");
            $floorplanArr = $db->runQuery("select floor_plan from " . PROPERTY . " where id = '" . $pptyId . "' ");

            if ($floorplanArr != "" && count($floorplanArr) > 0)
            {
                @unlink(SITE_ROOT . "images/floorplan/" . $floorplanArr[0]['floor_plan']);
                $dataUpdate['floor_plan'] = "";
                $condition = " id = " . $pptyId;
                $db->modify(PROPERTY, $dataUpdate, $condition);
            }

            exit;
        }

        public function doajaxfileuploadAction()
        {
            global $mySession;
            $db = new Db();
            $error = "";




            $delete = $this->getRequest()->getParam("delete");



            if ($delete)
            {
                $files = glob(SITE_ROOT . "ankit_ups/*");

                foreach ($files as $file)
                {

                    echo $file . "\n";

                    if (is_file($file) && preg_match("/$_REQUEST[id]/i", $file))
                        unlink($file);
                }
                exit;
            }



            $msg = "";
            $fileElementName = 'photo_res';
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

                    if (!move_uploaded_file($_FILES['photo_res']['tmp_name'], SITE_ROOT . "ankit_ups/" . $newfilename))
                        $msg['error'] = "File Uploading Error";
                    else
                    {
                        $files = glob(SITE_ROOT . "ankit_ups/*");
                        $msg['number_of_files'] = count($files);
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

    }

?>