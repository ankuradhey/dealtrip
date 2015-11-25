<?php

    __autoloadDB('Db');

    class SystemController extends AppController
    {

        public function configurationAction()
        {
            global $mySession;
            $myform = new Form_Configuration();
            $this->view->pageHeading = "Configuration";
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Configuration();
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new System();
                    $Result = $myObj->SaveConfiguration($dataForm);
                    $mySession->sucessMsg = "Configuration saved successfully.";
                }
            }
            $this->view->myform = $myform;
        }

        public function emailtemplatesAction()
        {
            global $mySession;
            $db = new Db();
            $this->view->pageHeading = "Manage Email Templates";

            $qry = "select * from " . EMAIL_TEMPLATES . "";
            $this->view->ResData = $db->runQuery($qry);

            /* $countQuery=$db->runQuery("$qry $where");
              $total=count($countQuery);
              $json = "";
              $json .= "{\n";
              $json .= "page: $page,\n";
              $json .= "total: $total,\n";
              $json .= "rows: [";
              $rc = false;
              if(isset($ResData[0]) && $ResData[0]['template_id']!="")
              {
              $start=$start+1;
              c
              {
              if ($rc) $json .= ",";
              $json .= "\n{";
              $json .= "id:'".$row['template_id']."',";
              $json .= "cell:['".$start."'";
              $json .= ",'".addslashes($row['template_title'])."'";
              $json .= ",'".addslashes($row['email_subject'])."'";
              $json .= ",'<a href=\'".APPLICATION_URL_ADMIN."system/edittemplate/templateId/".$row['template_id']."\'><img src=\'".IMAGES_URL_ADMIN."edit.png\' border=\'0\' title=\'Edit\' alt=\'Edit\'></a>'";
              $json .= "]}";
              $rc = true;
              $start++;
              }
              }
              $json .= "]\n";
              $json .= "}";
              echo $json;
              exit(); */
        }

        public function edittemplateAction()
        {
            global $mySession;
            $db = new Db();
            $templateId = $this->getRequest()->getParam('templateId');
            $this->view->templateId = $templateId;
            $templateData = $db->runQuery("select * from " . EMAIL_TEMPLATES . " where template_id='" . $templateId . "'");
            $myform = new Form_Mailtemplate($templateId);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Edit - " . $templateData[0]['template_title'];
            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $myform = new Form_Mailtemplate($templateId);
                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();
                    $myObj = new System();
                    $Result = $myObj->UpdateTemplate($dataForm, $templateId);
                    if (isset($_REQUEST['save_or_send']) && $_REQUEST['save_or_send'] == '2')
                    {
                        $mySession->sucessMsg = "Newsletter successfully saved and sent to all subscribed members.";
                    }
                    else
                    {
                        $mySession->sucessMsg = "Email Template updated successfully.";
                    }
                    $this->_redirect('system/emailtemplates');
                }
            }
            $this->view->myform = $myform;
        }

        public function metaAction()
        {
            global $mySession;
            $db = new Db();
            $metaArr = $db->runQuery("select * from " . META_INFO . " ");

            $limit = '0';
            $myform = new Form_Meta($metaArr[0]['meta_id']);
            $this->view->myform = $myform;
            $this->view->pageHeading = "Edit - MetaData ";

            if ($this->getRequest()->isPost())
            {
                $request = $this->getRequest();
                $limit = '0';

                if ($myform->isValid($request->getPost()))
                {
                    $dataForm = $myform->getValues();

                    $dataInsert['meta_title'] = $dataForm['title'];
                    $dataInsert['meta_keyword'] = stripslashes($dataForm['meta_keyword']);
                    $dataInsert['meta_description'] = stripslashes($dataForm['meta_description']);
                    $condition = "meta_id = " . $dataForm['page_name'];

                    $db->modify(META_INFO, $dataInsert, $condition);
                    $mySession->sucessMsg = "Data succesfully updated";
                    $this->_redirect("system/meta");
                }
            }
        }

        public function getmetadataAction()
        {
            global $mySession;
            $db = new Db();
            $page_id = $_REQUEST['Name'];
            $metaArr = $db->runQuery("select * from " . META_INFO . " where meta_id = '" . $page_id . "' ");


            $send_text['title'] = $metaArr[0]['meta_title'];
            $send_text['keyword'] = $metaArr[0]['meta_keyword'];
            $send_text['description'] = $metaArr[0]['meta_description'];
            echo json_encode($send_text);
            exit;
        }

    }

?>