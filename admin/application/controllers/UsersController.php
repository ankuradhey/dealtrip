<?php

__autoloadDB('Db');

class UsersController extends AppController {
    #-----------------------------------------------------------#
    # Index Action Function
    #-----------------------------------------------------------#

    public function indexAction() {
        global $mySession;

        $uType = $this->getRequest()->getParam('uType');

        if ($uType == '1')
            $this->view->pageHeading = "Manage Customers";
        else
            $this->view->pageHeading = "Manage Owners";

        $this->view->uType = $uType;
        global $mySession;
        $db = new Db();
        $where = "where 1=1 and " . USERS . ".uType=" . $uType;

        $order = "order by " . USERS . ".user_id desc ";

        $qry = "select * from " . USERS . " inner join " . COUNTRIES . " on  " . COUNTRIES . ".country_id=" . USERS . ".country_id ";
        $this->view->ResData = $db->runQuery("$qry $where $order");
    }

    #-----------------------------------------------------------#
    # User Add Action Function
    #-----------------------------------------------------------#

    public function adduserAction() {
        global $mySession;
        $myform = new Form_User();
        $this->view->pageHeading = "Add New User";
        $uType = $this->getRequest()->getParam('uType');
        $this->view->uType = $uType;
        $db = new Db();
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            if ($myform->isValid($request->getPost())) {
                $dataForm = $myform->getValues();
                $myObj = new Users();
                $dataForm['uType'] = $uType;
                if ($dataForm['password'] != $dataForm['password_c']) {
                    $mySession->errorMsg = "Password and Conform Password Does not Match !.";
                } else {
                    $Result = $myObj->SaveUser($dataForm);
                    if ($Result == 1) {
                        $mySession->sucessMsg = "New User added successfully.";
                        $this->_redirect('users/index/uType/' . $uType);
                    } else {
                        $mySession->errorMsg = "Username you entered is already exists.";
                    }
                }
            }
        }
        $this->view->myform = $myform;
    }

    #-----------------------------------------------------------#
    # User Edit Action Function
    #-----------------------------------------------------------#

    public function edituserAction() {
        global $mySession;
        $userId = $this->getRequest()->getParam('userId');
        $uType = $this->getRequest()->getParam('uType');
        $this->view->uType = $uType;
        $this->view->userId = $userId;
        $myform = new Form_User($userId);

        $this->view->pageHeading = "Edit User";
        $db = new Db();

        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();

            if ($myform->isValid($request->getPost())) {
                $dataForm = $myform->getValues();
                $myObj = new Users();

                $Result = $myObj->UpdateUser($dataForm, $userId);
                if ($Result == 1) {
                    $mySession->sucessMsg = "User details updated successfully.";
                    $this->_redirect('users/index/uType/' . $uType);
                } else {
                    $mySession->errorMsg = "Username you entered is already exists.";
                }
            }
        }
        $this->view->myform = $myform;
    }

    #-----------------------------------------------------------#
    # User Change Password Action Function
    #-----------------------------------------------------------#

    public function changepasswordAction() {
        global $mySession;
        $db = new Db();
        $this->view->pageHeading = "Change Password";
        $uType = $this->getRequest()->getParam('uType');
        $this->view->uType = $uType;
        $userId = $this->getRequest()->getParam('userId');
        $condition1 = " where user_id='" . $userId . "'";
        $qry = "select * from " . USERS . $condition1;
        $ResData = $db->runQuery("$qry ");
        $this->view->email = $ResData[0]['email_address'];
        $myform = new Form_Changepassword();
        $this->view->myform = $myform;
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            if ($myform->isValid($request->getPost())) {
                $dataForm = $myform->getValues();
                if ($dataForm['new_password'] != $dataForm['confirm_new_password']) {
                    $mySession->errorMsg = "New Password And Confirm Password Does Not Match !";
                } else {
                    $data_update['password'] = md5($dataForm['new_password']);
                    $condition = "user_id='" . $userId . "'";
                    $db->modify(USERS, $data_update, $condition);
                    $mySession->sucessMsg = "Password changed successfully.";
                }
            }
        }
    }

    #-----------------------------------------------------------#
    # Delete User Action Function
    #-----------------------------------------------------------#

    public function deleteuserAction() {
        global $mySession;
        $db = new Db();
        if ($_REQUEST['Id'] != "") {
            $arrId = explode("|", $_REQUEST['Id']);
            if (count($arrId) > 0) {
                foreach ($arrId as $key => $Id) {
                    $condition1 = "user_id='" . $Id . "'";
                    $db->delete(USERS, $condition1);
                }
            }
        }
        exit();
    }

    #-----------------------------------------------------------#
    #  Change Status User Action Function
    #-----------------------------------------------------------#

    public function changeuserstatusAction() {
        global $mySession;
        $db = new Db();
        $BcID = $_REQUEST['Id'];
        $status = $_REQUEST['Status'];
        if ($status == '1') {
            $status = '0';
        } else {
            $status = '1';
        }
        $data_update['user_status'] = $status;
        $condition = "user_id='" . $BcID . "'";
        $db->modify(USERS, $data_update, $condition);
        exit();
    }

    /*
     * Validate user against email address
     */

    public function validateuserAction() {
        global $mySession;
        $db = new Db();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $msg = array("error" => 'true', 'success' => 'false', 'message' => 'No Method other than POST supported');
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $emailAddress = $post['email'];
            $res = $db->runQuery("select * from users where email_address = '$emailAddress' and user_status = '1'  ");
            if (count($res)) {
                $msg['error'] = 'false';
                $msg['success'] = 'true';
                $msg['message'] = 'Success';
                $msg['userId'] = $res[0]['user_id'];
            }
        }
        echo $this->_helper->json($msg);
    }

    /*
     * Register user using ajax post request
     */

    public function registeruserAction() {
        global $mySession;
        $db = new Db();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $responseArr = array('error' => 'true', 'success' => 'false', 'message' => 'data not processed');
        if ($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $dataForm = $request->getPost();
            $myObj = new Users();
            $dataForm['uType'] = '1';
            $Result = $myObj->SaveUser($dataForm);
            if ($Result) {
                $responseArr['success'] = 'true';
                $responseArr['error'] = 'false';
                $responseArr['message'] = 'New User added successfully.';
                $responseArr['userId'] = $Result;
            } else {
                $responseArr['message'] = "Username you entered is already exists.";
            }
        }
        echo $this->_helper->json($responseArr);
    }

}

?>