<?php

    __autoloadDB('Db');

    class SocialController extends AppController
    {

        public function indexAction()
        {
            global $mySession;
            $this->view->pageHeading = "Social Media Configurations";
            $db = new Db();

            $sql = "select * from social_media";
            $result = $db->runQuery($sql);

            $form = new Form_Socialmedia();
            $form->setAction("");
            if (!empty($result))
                $form->populate($result[0]);
            $this->view->form = $form;

            if ($this->getRequest()->isPost())
            {
                $arr = $this->getRequest()->getPost();
                $db->modify("social_media", $arr, "1");
                $this->_redirect("social");
            }
        }

    }