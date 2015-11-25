<?php

    __autoloadDB('Db');

    class PhotoController extends AppController
    {

        public function homepageimageAction()
        {

            $db = new Db();
            $qry = "SELECT * FROM homepageimg order by order_number ASC";

            $this->view->pageHeading = "Manage home page images";

            $this->view->ResData = $db->runQuery($qry);
        }

        public function homepageimageaddAction()
        {
            global $mySession;
            $myform = new Form_Photo(1, 0);
            
            $this->view->pageHeading = "Add New image";
            if ($this->getRequest()->isPost())
            {
                $db = new Db();
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    
                    $dataForm = $myform->getValues();
                    $myObj = new Photo();
                    $Result = $myObj->Savehpi($dataForm, 0);
                    
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Image added successfully.";
                        $this->_redirect('photo/homepageimage');
                    }
                    else
                    {
                        $mySession->errorMsg = "Image name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function homepageimageeditAction()
        {
            global $mySession;
            $db = new Db();
            $id = $this->getRequest()->getParam('id');
            $sql = "SELECT image_text FROM " . HOMEPAGEIMG . " WHERE id = $id";
//            prd($sql);
            $record = $db->runQuery($sql);
            $this->view->id = $id;
            $myform = new Form_Photo(1, $id);
            $myform->populate($record[0]);

            $this->view->pageHeading = "Edit image";

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Photo();
                    $Result = $myObj->Savehpi($dataForm, $id);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Image Updated updated successfully.";
                        $this->_redirect('photo/homepageimage');
                    }
                    else
                    {
                        $mySession->errorMsg = "Image name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function homepageimagedeleteAction()
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
                        //code to update order of images
                        $detailArr = $db->runQuery("select * from ".HOMEPAGEIMG." where id = '".$Id."'  ");
                        //prd($detailArr);
                        $updateData = array();
                        $updateData['order_number'] = new Zend_Db_Expr('order_number - 1');
                        $db->modify(HOMEPAGEIMG,$updateData,"order_number > ".$detailArr[0]['order_number']." ");
                        
                        
                        $condition = "id='" . $Id . "'";
                        $db->delete("homepageimg", $condition);
                    }
                }
            }
            exit();
        }

        public function homepageimagestatusAction()
        {
            global $mySession;
            $db = new Db();
            $id = $_REQUEST['Id'];
            $status = $_REQUEST['Status'];
            if ($status == '1')
            {
                $status = '0';
            }
            else if ($status == '0')
            {
                $status = '1';
            }
            $data_update['status'] = $status;
            $condition = "id='" . $id . "'";
            $db->modify("homepageimg", $data_update, $condition);
            exit();
        }

        public function homepageimgdeleteAction()
        {
            global $mySession;
            $db = new Db();
            $Id = $this->getRequest()->getParam('Id');
            $condition = "id='" . $Id . "'";
            $db->delete("homepageimg", $condition);
            $this->_redirect("photo/homepageimage");
        }

        public function orderpositionAction()
        {

            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();

            $current_element_id = $_REQUEST['id'];
            $fromPosition = $_REQUEST['fromPosition'];
            $toPosition = $_REQUEST['toPosition'];
            $direction = $_REQUEST['direction'];
            $group = $_REQUEST['group'];

            $lppty_type = $this->getRequest()->getParam("lppty_type");


            //$where = " 1 ";

            $chkposArr = $db->runQuery("select * from  " . HOMEPAGEIMG . " order by order_number asc  ");

            //pr($chkposArr);
            $dataUpdate['order_number'] = $chkposArr[$toPosition - 1]['order_number'];
            //pr($dataUpdate);

            echo $condition = "id = " . $current_element_id;
            $db->modify(HOMEPAGEIMG, $dataUpdate, $condition);

            $dataUpdate['order_number'] = $chkposArr[$fromPosition - 1]['order_number'];
            //pr($dataUpdate);
            echo $condition = "id = " . $chkposArr[$toPosition - 1]['id'];
            $db->modify(HOMEPAGEIMG, $dataUpdate, $condition);
            exit;
        }

    }

?>