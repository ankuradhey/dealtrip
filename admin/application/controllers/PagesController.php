<?php

    __autoloadDB('Db');

    class PagesController extends AppController
    {

        public function indexAction()
        {
            global $mySession;
            $this->view->pageHeading = "Manage Pages";
            $db = new Db();
            $this->view->ResData = $db->runQuery("select * from " . PAGES1);
        }

        public function addAction()
        {
            global $mySession;
            $myform = new Form_Pages();
            $myform->setAction("");
            $this->view->myform = $myform;
            $this->view->pageHeading = "Add New Page";

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {

                    $dataForm = $myform->getValues();
                    $dataForm['page_parent'] = "general";
                    $myObj = new Pages();
//                    prd($dataForm);

                    $Result = $myObj->SavePage($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Page details added successfully.";
                        $this->_redirect('pages/index');
                    }
                    else
                    {
                        $mySession->errorMsg = "Page name you entered already exists.";
                    }
                }
            }
        }

        public function savepageAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Add New Page";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Pages();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new Pages();
                    $Result = $myObj->SavePage($dataForm);
                    if ($Result == 1)
                    {
                        $mySession->errorMsg = "New page added successfully.";
                        $this->_redirect('pages/index');
                    }
                    else
                    {
                        $mySession->errorMsg = "Page name you entered is already exists.";
                        $this->view->myform = $myform;
                        $this->render('add');
                    }
                }
                else
                {
                    $this->view->myform = $myform;
                    $this->render('add');
                }
            }
            else
            {
                $this->_redirect('pages/add');
            }
        }

        public function editAction()
        {
            global $mySession;
            $pageId = $this->getRequest()->getParam('pageId');
            $this->view->pageId = $pageId;
            $myform = new Form_Pages($pageId);
            $this->view->pageHeading = "Edit Page";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                if ($myform->isValid($request->getPost()))
                {

                    $dataForm = $myform->getValues();
                    $myObj = new Pages();
//                    prd($dataForm);

                    $Result = $myObj->UpdatePage($dataForm, $pageId);
                    if ($Result == 1)
                    {
                        $mySession->sucessMsg = "Page details updated successfully.";
                        $this->_redirect('pages/index');
                    }
                    else
                    {
                        $mySession->errorMsg = "Page name you entered is already exists.";
                    }
                }
            }
            $this->view->myform = $myform;
        }

        public function deletepageAction()
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
                        $condition1 = "page_id='" . $Id . "'";
                        $db->delete(PAGES1, $condition1);
                    }
                }
            }
            exit();
        }


    }

?>