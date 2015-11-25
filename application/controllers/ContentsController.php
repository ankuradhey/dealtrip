<?php

    __autoloadDB('Db');

    class ContentsController extends AppController
    {

        public function pagesAction()
        {
            global $mySession;
            $db = new Db();

            $slug = $this->getRequest()->getParam("slug");
            $userArr = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");
            $sql = "select * from " . PAGES1 . " where synonyms = '$slug'";
            $staticArr = $db->runQuery($sql);

            if(count($staticArr) == 0){
                $this->render("error/error");
            }
            
            //========== Fetching Meta Information ===========================//
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 3");
            $Title = str_replace('[PAGE_NAME]', $staticArr[0]['page_title'], $metaArr[0]['meta_title']);
            $Description = str_replace('[PAGE_NAME]', $staticArr[0]['page_description'], $metaArr[0]['meta_title']);
            $this->view->headTitle($Title)->offsetUnset(0);
//            $this->view->headMeta('description', $Description);
            $this->view->headMeta($staticArr[0]['meta_description'], 'description');
            $this->view->headMeta($staticArr[0]['meta_keywords'], 'keywords');


            $strContent = str_replace("[SITEURL]", APPLICATION_URL, $staticArr[0]['page_content']);
            $strContent = str_replace("[FULLNAME]", $userArr[0]['email_address'], $strContent);
            if (!isLogged()):
                $strContent = str_replace("[LOGINMSG]", "<a href='" . APPLICATION_URL . "/signin'>click here</a> to Login", $strContent);
            else:
                $strContent = str_replace("[LOGINMSG]", "", $strContent);
            endif;
            $this->view->pageContent = $strContent;
            $this->view->pageTitle = $staticArr[0]['page_title'];

            $this->view->page_id = $staticArr[0]['page_id'];

            if ($staticArr[0]['page_id'] == '12' || $staticArr[0]['page_id'] == '63')
            {
                $myform = new Form_Contact();
                $this->view->myform = $myform;
            }

            if ($staticArr[0]['page_id'] == '80')
            {
                $myform = new Form_Ocontact();
                $this->view->myform = $myform;
            }

            //location keywords for destination page
            if ($staticArr[0]['page_id'] == '25')
            {
                
                $this->view->countryArr = $countryArr = $db->runQuery("select country_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         where status = '3'
                                         group by country_name
                                         ");

                $this->view->statesArr = $statesArr = $db->runQuery("select country_name, state_name from " . PROPERTY . "
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         where status = '3'
                                         group by state_name
                                         ");


                $this->view->citiesArr = $citiesArr = $db->runQuery("select country_name, state_name, city_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         where status = '3'
                                         group by city_name
                                         ");

                $this->view->subareasArr = $subareasArr = $db->runQuery("select country_name, state_name, city_name,sub_area_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         where status = '3'
                                         group by sub_area_name
                                         ");

                $this->view->localareasArr = $localareasArr = $db->runQuery("select country_name, state_name, city_name,sub_area_name, local_area_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         inner join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                         where status = '3'
                                         group by local_area_name
                                         ");
            }

            $varsuccess = 0;
            # the response from reCAPTCHA
            $resp = null;
            # the error code from reCAPTCHA, if any
            $error = null;

            if ($this->getRequest()->isPost())
            {

                $request = $this->getRequest();


                $dataForm = $myform->getValues();
                //prd($dataForm);

                if ($myform->isValid($request->getPost()) && !empty($_POST["recaptcha_response_field"]))
                {
                    $dataForm = $myform->getValues();

                    $resp = recaptcha_check_answer(CAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

                    if ($resp->is_valid)
                    {
                        $myObj = new Users();
                        if ($staticArr[0]['page_id'] == '80')
                            $Result = $myObj->ownercontactus($dataForm);
                        else
                            $Result = $myObj->contactus($dataForm);
                        $mySession->sucessMsg = "Thank you, You will soon be contacted";
                        $varsuccess = 1;
                    }
                    else
                        $mySession->errorMsg = $error = $resp->error;
                }
                else
                {
                    $dataForm = $myform->getValues();

                    $mySession->errorMsg = "Human Verification Error";
                }
            }
            $this->view->error = $error;
            $this->view->myform = $myform;
            $this->view->varsuccess = $varsuccess;


            __bookSessionClear();
        }

    }

?>