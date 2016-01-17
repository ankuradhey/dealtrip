<?php

    __autoloadDB('Db');

    class LocationController extends AppController
    {

        public function indexAction()
        {

            global $mySession;
            $db = new Db();
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();

            $location['country'] = $this->getRequest()->getParam('country');
            $location['state'] = $this->getRequest()->getParam('state');
            $location['city'] = $this->getRequest()->getParam('city');
            $location['sub_area'] = $this->getRequest()->getParam('sub_area');
            $location['local_area'] = $this->getRequest()->getParam('local_area');

            $location_type = !empty($location['local_area']) ? 'local_area' : '';
            $location_type = empty($location_type) && !empty($location['sub_area']) ? 'sub_area' : $location_type;
            $location_type = empty($location_type) && !empty($location['city']) ? 'city' : $location_type;
            $location_type = empty($location_type) && !empty($location['state']) ? 'state' : $location_type;
            $location_type = empty($location_type) && !empty($location['country']) ? 'country' : $location_type;

            $record = $dbAdapter->select()->from(array('p' => LOCATION_PAGE));

            //get review
            $reviewArr = $dbAdapter->select()->from(array('r' => OWNER_REVIEW))
                    ->join(array('p' => PROPERTY), 'p.id = r.property_id ')
                    ->join(array('u' => USERS), 'u.user_id = p.user_id')
                    ->join(array('pt' => PROPERTY_TYPE), 'p.property_type = pt.ptyle_id')
                    ->join(array('c' => COUNTRIES), 'c.country_id = p.country_id')
                    ->join(array('s' => STATE), 's.state_id = p.state_id')
                    ->join(array('city' => CITIES), 'city.city_id = p.city_id')
                    ->joinLeft(array('sa' => SUB_AREA), 'sa.sub_area_id = p.sub_area_id')
                    ->joinLeft(array('la' => LOCAL_AREA), 'la.local_area_id = p.local_area_id')
            ;

//            prd($reviewArr->__toString());
//            inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
//            inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
//            inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
//            left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
//            left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id

             
            if ($location_type == 'country')
            {
                $breadcrumb_array['country_name'] = $location['country'];
                $locationTree = $location['country'];
                $record = $record->join(array('c' => COUNTRIES), 'c.country_id = p.loc_location_id and loc_location_type = "country" ')
                        ->where(" c.country_name = '" . $location[$location_type] . "'  ");
                $reviewArr = $reviewArr
                        //->join(array('c'=>COUNTRIES),'c.country_id = p.country_id ')
                        ->where(" c.country_name = '" . $location[$location_type] . "'  ");
                
                $this->view->cloc = $db->runQuery("select country_name as url, country_name as title from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         where status = '3'
                                         group by country_name
                                         ");
            }
            elseif ($location_type == 'state')
            {
                $breadcrumb_array['state_name'] = $location['state'];
                $breadcrumb_array['country_name'] = $location['country'];

                $locationTree = $location['country'] . "/" . $location['state'];
                $record = $record->join(array('c' => STATE), 'c.state_id = p.loc_location_id and loc_location_type = "state" ')
                        ->where(" c.state_name = '" . $location[$location_type] . "'  ");
                $reviewArr = $reviewArr
                        ///->join(array('c'=>STATE),'c.state_id = p.state_id  ')
                        ->where(" s.state_name = '" . $location[$location_type] . "'  ");
                
                 $this->view->cloc = $db->runQuery("select CONCAT_WS('/', country_name, state_name) as url, state_name as title from " . PROPERTY . "
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         where status = '3' and " . COUNTRIES . ".country_name='".$location['country_name']."'
                                         group by state_name
                                         ");

            }
            elseif ($location_type == 'city')
            {
                $breadcrumb_array['city_name'] = $location['city'];
                $breadcrumb_array['state_name'] = $location['state'];
                $breadcrumb_array['country_name'] = $location['country'];

                $locationTree = $location['country'] . "/" . $location['state'] . "/" . $location['city'];
                $record = $record->join(array('c' => CITIES), 'c.city_id = p.loc_location_id and loc_location_type = "city" ')
                        ->where(" c.city_name = '" . $location[$location_type] . "'  ");
                $reviewArr = $reviewArr
                        //->join(array('c'=>CITIES),'c.city_id = p.city_id ')
                        ->where(" city.city_name = '" . $location[$location_type] . "'  ");
                
                 $this->view->cloc = $db->runQuery("select  CONCAT_WS('/', country_name, state_name, city_name) as url, city_name as title  from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         where status = '3' and " . STATE . ".state_name='".$location['state_name']."'
                                         group by city_name
                                         ");
            }
            elseif ($location_type == 'sub_area')
            {
                $breadcrumb_array['sub_area_name'] = $location['sub_area'];
                $breadcrumb_array['city_name'] = $location['city'];
                $breadcrumb_array['state_name'] = $location['state'];
                $breadcrumb_array['country_name'] = $location['country'];



                $locationTree = $location['country'] . "/" . $location['state'] . "/" . $location['city'] . "/" . $location['sub_area'];
                $record = $record->join(array('c' => SUB_AREA), 'c.sub_area_id = p.loc_location_id and loc_location_type = "sub_area" ')
                        ->where(" c.sub_area_name = '" . $location[$location_type] . "'  ");
                $reviewArr = $reviewArr
                        //->join(array('c'=>SUB_AREA),'c.sub_area_id = p.sub_area_id ')
                        ->where(" sa.sub_area_name = '" . $location[$location_type] . "'  ");
                
                $this->view->cloc = $db->runQuery("select CONCAT_WS('/', country_name, state_name, city_name,sub_area_name) as url, sub_area_name as title from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         where status = '3' and " . CITIES . ".city_name='".$location['city_name']."'
                                         group by sub_area_name
                                         ");
                
            }
            elseif ($location_type == 'local_area')
            {

                $breadcrumb_array['local_area_name'] = $location['local_area'];
                $breadcrumb_array['sub_area_name'] = $location['sub_area'];
                $breadcrumb_array['city_name'] = $location['city'];
                $breadcrumb_array['state_name'] = $location['state'];
                $breadcrumb_array['country_name'] = $location['country'];

                $locationTree = $location['country'] . "/" . $location['state'] . "/" . $location['city'] . "/" . $location['sub_area'] . "/" . $location['local_area'];
                $record = $record->join(array('c' => LOCAL_AREA), 'c.local_area_id = p.loc_location_id and loc_location_type = "local_area" ')
                        ->where(" c.local_area_name = '" . $location[$location_type] . "'  ");
                $reviewArr = $reviewArr
                        //->join(array('c'=>LOCAL_AREA),'c.local_area_id = p.local_area_id and status="3" ')
                        ->where(" la.local_area_name = '" . $location[$location_type] . "'  ");
                
                $this->view->cloc = $db->runQuery("select  CONCAT_WS('/', country_name, state_name, city_name,sub_area_name, local_area_name) as url, local_area_name as title  from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         inner join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                         where status = '3' and " . SUB_AREA . ".sub_area_name='".$location['sub_area']."'
                                         group by local_area_name
                                         ");
                
            }
            $reviewArr = $reviewArr->where(" p.status = '3' ")->order("review_id desc");
            $reviewArr = $reviewArr->query()->fetchAll();
            $this->view->reviewArr = $reviewArr;
            
            //get amenities list
            $amenityData = $dbAdapter->select()->from(array('as' => LOCATION_FEATURES))->query()->fetchAll();
            foreach ($amenityData as $amenKey => $amenVal)
            {
                $amenityArr[$amenVal['feature_id']] = $amenVal['feature_name'];
            }
            $this->view->amenityData = $amenityArr;
            $this->view->Location = $breadcrumb_array[$location_type . "_name"];
            $this->view->locationTree = $locationTree;


            $record = $record
                    ->where(" loc_location_type = '" . $location_type . "'");
            ;

            //prd($record->__toString());
            $record = $record->query()->fetchAll();

            //question data
            $question = $dbAdapter->select()->from(LOCATION_QUESTIONS)->where(" (ques_loc_id = '" . $record[0]['loc_id'] . "' or ques_loc_id is NULL ) and ques_status = '1' ")->query()->fetchAll();
            $questionArr = array();
            $fixedQues = array();
            foreach ($question as $qKey => $qVal)
            {
                $questionArr[$qVal['ques_id']] = $qVal['ques_content'];
                $fixedQues[$qVal['ques_id']]['ques_answer'] = $qVal['ques_answer'];
                $fixedQues[$qVal['ques_id']]['ques_content'] = $qVal['ques_content'];
                $fixedQues[$qVal['ques_id']]['ques_fixed'] = $qVal['ques_fixed'];
            }
            
            //get feature list
            $featureData = $dbAdapter->select()->from(LOCATION_FEATURES)->query()->fetchAll();
            foreach ($featureData as $featKey => $featVal)
            {
                $featureArr[$featVal['feature_id']] = $featVal['feature_name'];
            }
            $this->view->fixedQuestion = $fixedQues;
            $this->view->question = $questionArr;
            $this->view->record = $record[0];
            $this->view->breadCrumbArray = $breadcrumb_array;
            $this->view->featureData = $featureArr;
            //arsort($breadcrumb_array);
            $breadcrumb_array = implode('-', $breadcrumb_array);
            $this->view->headMeta($record[0]['loc_meta_keywords'], 'keywords');
            $this->view->headMeta($record[0]['loc_information'], 'description');
            $this->view->headTitle($breadcrumb_array)->offsetUnset(0);

            //location keywords
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

    }