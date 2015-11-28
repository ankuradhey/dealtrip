<?php

    __autoloadDB('Db');

    class IndexController extends AppController
    {

        public function indexAction()
        {

            if (CRON_JOB)
                $this->updatecalendarAction();

            if (SITEMAP)
                $this->updatesitemapAction();

            global $mySession;
            $db = new Db();
            //prd($mySession->bookingUser);
            //========== Fetching Meta Information ===========================//
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 1 ");
            $this->view->headTitle($metaArr[0]['meta_title'])->offsetUnset(0);
//            $this->view->headMeta('description', $metaArr[0]['meta_description']);
            $this->view->headMeta($metaArr[0]['meta_description'], 'description');
            $this->view->headMeta($metaArr[0]['meta_keyword'], 'keywords');
            //--------------------------//
            //fetching latest properties//
            //--------------------------//

            $pptyArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode," . PROPERTY . ".property_title, " . PROPERTY . ".brief_desc, " . PROPERTY . ".star_rating, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                   bedrooms,   trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
                                                                  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  
								  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
								  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                  
								  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_status = '1' and lppty_type = '0'
								  group by id
								  order by lppty_order asc limit 0,6 ");

//prd($pptyArr);
            $countpptyArr = $db->runQuery("select count(*) as _count from " . SLIDES_PROPERTY . "
								  where lppty_status = '1' and lppty_type = '0'								  
								  order by lppty_order asc ");



            $this->view->count_latest_ppty = $countpptyArr[0]['_count'];
            $this->view->pptyArr = $pptyArr;



            //---------------------------//
            //fetching Special properties//
            //---------------------------//
            $spclArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode  ," . PROPERTY . ".property_title, " . PROPERTY . ".brief_desc, star_rating, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms,    trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  
                                                                  
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                                  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                      
								  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_status = '1' and lppty_type = '2'
								  group by id
								  order by lppty_order asc ");

            $this->view->countSpcl = ceil(count($spclArr) / 6);

            //query for slider
            $sliderArr = $db->runQuery("select * from " . HOMEPAGEIMG . " where status = '1' ORDER BY order_number ASC ");
            $this->view->slideArr = $sliderArr;

            //---------------------------//
            //fetching popular properties//
            //---------------------------//
            $popularArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode ," . PROPERTY . ".property_title, " . PROPERTY . ".brief_desc,star_rating, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms,    trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  
								  
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                                  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                      
                                                                  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_status = '1' and lppty_type = '1'
								  group by id
								  order by lppty_order asc limit 0,6 ");



            $countpopular = $db->runQuery("select count(*) as _count from " . SLIDES_PROPERTY . "
								  where lppty_status = '1' and lppty_type = '1'								  
								  order by lppty_order asc ");

            $this->view->countpopular = $countpopular[0]['_count'];
            $this->view->popularArr = $popularArr;


            //---------------------------//
            //  fetching Latest Reviews  //
            //---------------------------//
            //query for review 


            $reviewArr = $db->runQuery("select " . LATEST_REVIEW . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode," . GALLERY . ".image_name, property_title,brief_desc,  " . PROPERTY . ".star_rating, 
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms,    trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . LATEST_REVIEW . " 
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . LATEST_REVIEW . ".r_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  
								  
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                                  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id  
                                        
                                                                  left join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id
								  where r_status = '1'
								  group by " . LATEST_REVIEW . ".r_id	 
								  limit 0,6
								  ");





            $countpopular = $db->runQuery("select count(*) as _count from " . LATEST_REVIEW . "
                inner join " . PROPERTY . " on " . PROPERTY . ".id = " . LATEST_REVIEW . ".r_property_id
								  where r_status = '1' ");
            $this->view->countreview = $countpopular[0]['_count'];
            $this->view->reviewArr = $reviewArr;


            //query for fetching keywords
            $keyArr = $db->runQuery("select * from " . PROPERTY . " as p 
							    inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = p.property_type
								inner join " . COUNTRIES . " as c on c.country_id = p.country_id
								left join " . STATE . " as s on s.country_id = c.country_id
								left join " . CITIES . " as ci on ci.state_id = s.state_id
								left join " . SUB_AREA . " as sa on sa.city_id = ci.city_id
								left join " . LOCAL_AREA . " as la on la.sub_area_id = sa.sub_area_id
								where p.status = '3' 
								group by p.id
								");


            $keyword = array();
            foreach ($keyArr as $val)
            {
                $keyword[] = $keyArr[0]['property_name'];
                $keyword[] = $keyArr[0]['property_title'];
                $keyword[] = $keyArr[0]['country_name'];
                $keyword[] = $keyArr[0]['state_name'];
                $keyword[] = $keyArr[0]['city_name'];
                $keyword[] = $keyArr[0]['sub_area_name'];
                $keyword[] = $keyArr[0]['local_area_name'];
            }

            $this->view->keyword = $keyword;
            $this->view->amenityArr = $amenityArr;
            $this->view->specialArr = $this->spclofferpptyAction();
//            prd($this->spclofferpptyAction());

            $sql = "select synonyms,page_key from " . PAGES1;
            $records = $db->runQuery($sql);

            foreach ($records as $key => $value)
            {
                $new_array[$value["page_key"]] = $value["synonyms"];
            }
            $this->view->staticLinks = $new_array;

            $new_sql = "select synonyms, page_title from " . PAGES1 . " where page_parent = 'general'";
            $records = $db->runQuery($new_sql);
            $this->view->generalLinks = $records;

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

        public function logoutAction()
        {
            global $mySession;
            $mySession->LoggedUserId = "";
            unset($mySession->LoggedUserId);
            $mySession->LoggedUserType = "";
            unset($mySession->LoggedUserType);
            $mySession->OneBackUrl = "";
            unset($mySession->OneBackUrl);
            $this->_redirect(APPLICATION_URL . 'signin/index');
        }

        public function changecityAction()
        {

            global $mySession;
            $db = new Db();

            $id = $_REQUEST['CID'];
            $sql = 'select *from ' . ADMIN_CITIES . ' where id= ' . $id;
            $cityresult = $db->runQuery($sql);
            $mySession->cityid = $id;
            $mySession->cityname = $cityresult[0]['name'];

            echo $mySession->cityid;
            die;
            exit();
        }

        public function spclofferpptyAction($limit = "12")
        {

            global $mySession;
            $db = new Db();
            $next = $this->getRequest()->getParam("next");
            if ($next == "")
                $starti = 0;
            else
                $starti = $next * 4;

            $spclArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".propertycode ," . PROPERTY . ".id, " . PROPERTY . ".property_title,star_rating,bedrooms, bathrooms, ptyle_name ," . PROPERTY . ".brief_desc, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms,  trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
								  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
								  
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                                  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                      
                                                                  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_status = '1' and lppty_type = '2'
								  group by id
								  order by lppty_order asc limit $starti,$limit ");
            return $spclArr;
        }

        #--------------------------------------#
        #get state by country action function  #
        #--------------------------------------#

        public function getstatebycountryAction()
        {
            global $mySession;
            $db = new Db();
            $OptionState = "";
            $DataState = $db->runQuery("select * from " . STATE . " where country_id ='" . $_REQUEST['countryId'] . "' order by state_name");
            if ($DataState != "" and count($DataState) > 0)
            {
                foreach ($DataState as $key => $valueState)
                {
                    $OptionState.=$valueState['state_id'] . "|||" . $valueState['state_name'] . "***";
                }
                $OptionState = substr($OptionState, 0, strlen($OptionState) - 3);
            }
            echo $OptionState;
            exit();
        }

        #--------------------------------------#
        #  get state by country + count action function #
        #--------------------------------------#

        public function getstatebycountrycountAction()
        {
            global $mySession;
            $db = new Db();
            $OptionState = "";

            $DataState = $db->runQuery("select *, count(id) as _counter from " . STATE . " inner join  " . PROPERTY . " on " . PROPERTY . ".state_id = " . STATE . ".state_id
								  where " . STATE . ".country_id ='" . $_REQUEST['countryId'] . "' 
								  and " . PROPERTY . ".status = '3'
								  group by " . STATE . ".state_id order by " . STATE . ".state_name ");

            if ($DataState != "" and count($DataState) > 0)
            {
                foreach ($DataState as $key => $valueState)
                {
                    $OptionState.=$valueState['state_id'] . "|||" . $valueState['state_name'] . "|||" . $valueState['_counter'] . "***";
                }
                $OptionState = substr($OptionState, 0, strlen($OptionState) - 3);
            }
            echo $OptionState;
            exit();
        }

        #--------------------------------------#
        #  get cities by state action function #
        #--------------------------------------#

        public function getcitiesbystateAction()
        {
            global $mySession;
            $db = new Db();
            $OptionCities = "";
            $DataCity = $db->runQuery("select * from " . CITIES . " where state_id='" . $_REQUEST['stateId'] . "' order by city_name");
            if ($DataCity != "" and count($DataCity) > 0)
            {
                foreach ($DataCity as $key => $valueCity)
                {
                    $OptionCities.=$valueCity['city_id'] . "|||" . $valueCity['city_name'] . "***";
                }
                $OptionCities = substr($OptionCities, 0, strlen($OptionCities) - 3);
            }
            echo $OptionCities;
            exit();
        }

        #--------------------------------------#
        #  get cities by state with count action function #
        #--------------------------------------#

        public function getcitiesbystatecountAction()
        {
            global $mySession;
            $db = new Db();
            $OptionCities = "";
            $DataCity = $db->runQuery("select *, count(id) as _counter from " . CITIES . " 
								 inner join " . PROPERTY . " on " . PROPERTY . ".city_id = " . CITIES . ".city_id
								 where " . CITIES . ".state_id='" . $_REQUEST['stateId'] . "' 
								 and " . PROPERTY . ".status = '3'
								 group by " . CITIES . ".city_id
								 order by city_name");
            if ($DataCity != "" and count($DataCity) > 0)
            {
                foreach ($DataCity as $key => $valueCity)
                {
                    $OptionCities.=$valueCity['city_id'] . "|||" . $valueCity['city_name'] . "|||" . $valueCity['_counter'] . "***";
                }
                $OptionCities = substr($OptionCities, 0, strlen($OptionCities) - 3);
            }

            echo $OptionCities;
            exit();
        }

        #--------------------------------------#
        #  get sub area by city action function #
        #--------------------------------------#

        public function getsubareabycityAction()
        {
            global $mySession;
            $db = new Db();
            $OptionSub = "";
            $DataSub = $db->runQuery("select * from " . SUB_AREA . " where city_id='" . $_REQUEST['cityId'] . "' order by sub_area_name");

            if ($DataSub != "" and count($DataSub) > 0)
            {
                foreach ($DataSub as $key => $valueSub)
                {
                    $OptionSub .= $valueSub['sub_area_id'] . "|||" . $valueSub['sub_area_name'] . "***";
                }
                $OptionSubarea = substr($OptionSub, 0, strlen($OptionSubarea) - 3);
            }

            echo $OptionSubarea;
            exit();
        }

        #--------------------------------------#
        #  get sub area by city action function #
        #--------------------------------------#

        public function getsubareabycitycountAction()
        {
            global $mySession;
            $db = new Db();
            $OptionSub = "";
            $DataSub = $db->runQuery("select *, count(id) as _counter from " . SUB_AREA . "
								  inner join " . PROPERTY . " on " . PROPERTY . ".sub_area_id = " . SUB_AREA . ".sub_area_id	 
								  where " . SUB_AREA . ".city_id = '" . $_REQUEST['cityId'] . "' 
								  and " . PROPERTY . ".status = '3'
								  group by " . SUB_AREA . ".sub_area_id
								  order by sub_area_name");

            if ($DataSub != "" and count($DataSub) > 0)
            {
                foreach ($DataSub as $key => $valueSub)
                {
                    $OptionSub .= $valueSub['sub_area_id'] . "|||" . $valueSub['sub_area_name'] . "|||" . $valueSub['_counter'] . "***";
                }
                $OptionSubarea = substr($OptionSub, 0, strlen($OptionSubarea) - 3);
            }

            echo $OptionSubarea;
            exit();
        }

        #--------------------------------------#
        #  get local area by sub action function #
        #--------------------------------------#

        public function getlocalareabysubAction()
        {
            global $mySession;
            $db = new Db();
            $OptionLocal = "";
            $DataLocal = $db->runQuery("select * from " . LOCAL_AREA . " where sub_area_id='" . $_REQUEST['subId'] . "' order by local_area_name");

            if ($DataLocal != "" and count($DataLocal) > 0)
            {
                foreach ($DataLocal as $key => $valueLocal)
                {
                    $OptionLocal .= $valueLocal['local_area_id'] . "|||" . $valueLocal['local_area_name'] . "***";
                }
                $OptionLocalarea = substr($OptionLocal, 0, strlen($OptionLocalarea) - 3);
            }

            echo $OptionLocalarea;
            exit();
        }

        #--------------------------------------#
        #  get local area by sub action function #
        #--------------------------------------#

        public function getlocalareabysubcountAction()
        {
            global $mySession;
            $db = new Db();
            $OptionLocal = "";


            $DataLocal = $db->runQuery("select *, count(id) as _counter
								    from " . LOCAL_AREA . " 
									inner join " . PROPERTY . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
								    where " . LOCAL_AREA . ".sub_area_id='" . $_REQUEST['subId'] . "' 
									and " . PROPERTY . ".status = '3'
									group by " . LOCAL_AREA . ".local_area_id
									order by " . LOCAL_AREA . ".local_area_name");


            if ($DataLocal != "" and count($DataLocal) > 0)
            {
                foreach ($DataLocal as $key => $valueLocal)
                {
                    $OptionLocal .= $valueLocal['local_area_id'] . "|||" . $valueLocal['local_area_name'] . "|||" . $valueLocal['_counter'] . "***";
                }
                $OptionLocalarea = substr($OptionLocal, 0, strlen($OptionLocalarea) - 3);
            }

            echo $OptionLocalarea;
            exit();
        }

        public function fetchpropertyAction()
        {
            global $mySession;
            $db = new Db();
            $tab = $this->getRequest()->getParam("tab");
            $counter = $this->getRequest()->getParam("counter");
            $countArr = $db->runQuery("select * from " . PROPERTY . " where status = '3' ");

            switch ($tab)
            {
                case '1': $tot = count($countArr);

                    $limit = ($counter - 1) * 6;
                    //country_id 	state_id 	city_id 	sub_area_id 	local_area_id

                    $pptyArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode," . PROPERTY . ".property_title, " . PROPERTY . ".brief_desc, " . PROPERTY . ".star_rating,  bedrooms, bathrooms, ptyle_name,image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms, trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
								        
                                                                  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
								  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
								  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                  
								  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_status = '1' and lppty_type = '0'
								  group by id
								  order by lppty_order asc limit $limit,6 ");


                    //prd($pptyArr);
                    $i = 1;

                    foreach ($pptyArr as $values)
                    {
//                        if ($i % 3 != 0)
//                            echo '<div class="tab_conten_clmn">';
//                        else
//                            echo '<div class="tab_conten_clmn2">';

                        echo '<div class="tab_conten_clmn">';


                        echo'<div class="tab_box_text">
										  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
										  <div class="tab_box_detail">
											<div class="tab_box_heading">' . $values['property_title'] . '</div>
											<div class="tab_box_dis">' . (strlen($values['brief_desc'])>250?substr($values['brief_desc'],0,250)."...":$values['brief_desc']) . '</div>';
                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . "-Bed-" . $values['bathrooms'] . "-Bath-" . $values['ptyle_url'] . "/" . $values['propertycode'];

                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>										  </div>
										</div>
										
									  </div>';

                        $i++;
                    }

                    if (count($pptyArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
//                            if ($i % 3 != 0)
//                                echo '<div class="tab_conten_clmn">';
//                            else
//                                echo '<div class="tab_conten_clmn2">';

                            echo '<div class="tab_conten_clmn">';

                            echo '<div ' . $class . '>
										<div class="tab_box_text">
										  <div class="tab_box_detail">
											<div class="tab_box_heading">&nbsp;</div>
											<div class="tab_box_dis">&nbsp;</div>
											<div class="tab_box_more">&nbsp;</div>
										  </div>
										</div>
										
									  </div>';
                        }
                    }

                    break;


                case '2': $limit = ($counter - 1) * 6;


                    $popularArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode  ," . PROPERTY . ".property_title, star_rating," . PROPERTY . ".brief_desc,  bedrooms, bathrooms, ptyle_name, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms, trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . SLIDES_PROPERTY . "
								  
                                                                  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . SLIDES_PROPERTY . ".lppty_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id    
								  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                                                  left join " . GALLERY . " on " . GALLERY . ".property_id = " . SLIDES_PROPERTY . ".lppty_property_id
								  where lppty_type = '1' and lppty_status ='1'
								  group by id
								  order by lppty_order asc limit $limit,6");


                    $i = 1;

                    foreach ($popularArr as $values)
                    {
//                        if ($i % 3 != 0)
//                            echo '<div class="tab_conten_clmn">';
//                        else
//                            echo '<div class="tab_conten_clmn2">';

                        echo '<div class="tab_conten_clmn">';

                        echo '<div class="tab_box_text">
										  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
										  <div class="tab_box_detail">
											<div class="tab_box_heading">' . $values['property_title'] . '</div>
											<div class="tab_box_dis">' . (strlen($values['brief_desc'])>250?substr($values['brief_desc'],0,250)."...":$values['brief_desc']) . '</div>';

                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . "-Bed-" . $values['bathrooms'] . "-Bath-" . $values['ptyle_url'] . "/" . $values['propertycode'];

                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>
										  </div>
										</div>
										
									  </div>';

                        $i++;
                    }

                    if (count($popularArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
//                            if ($i % 3 != 0)
//                                echo '<div class="tab_conten_clmn">';
//                            else
//                                echo '<div class="tab_conten_clmn2">';

                            echo '<div class="tab_conten_clmn">';

                            echo '<div class="tab_box_text">
										  <div class="tab_box_detail">
											<div class="tab_box_heading">&nbsp;</div>
											<div class="tab_box_dis">&nbsp;</div>
											<div class="tab_box_more">&nbsp;</div>
										  </div>
										</div>
										
									  </div>';
                        }
                    }

                    break;


                case '3': //query for review 

                    $limit = ($counter - 1) * 6;

                    $reviewArr = $db->runQuery("select " . LATEST_REVIEW . ".*, " . GALLERY . ".image_name, " . PROPERTY . ".id, " . PROPERTY . ".propertycode, property_title, brief_desc,  bedrooms, bathrooms, ptyle_name, star_rating,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name,
                                                                  bedrooms, trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)) as bathrooms, ptyle_url
                                                                  from " . LATEST_REVIEW . " 
                                                                  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . LATEST_REVIEW . ".r_property_id
                                                                  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                                                  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                                                  inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
								  inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
								  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
								  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
								  left join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id
								  group by " . LATEST_REVIEW . ".r_id	 
								  limit $limit,6
								  ");


                    $i = 1;

                    foreach ($reviewArr as $values)
                    {

//                        if ($i % 3 != 0)
//                            echo '<div class="tab_conten_clmn">';
//                        else
//                            echo '<div class="tab_conten_clmn2">';

                        echo '<div class="tab_conten_clmn">';

                        echo '<div class="tab_box_text">
								  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
								  <div class="tab_box_detail">
									<div class="tab_box_heading">' . $values['property_title'] . '</div>
											<div class="tab_box_dis">' . (strlen($values['brief_desc'])>250?substr($values['brief_desc'],0,250)."...":$values['brief_desc']) . '</div>';

                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . "-Bed-" . $values['bathrooms'] . "-Bath-" . $values['ptyle_url'] . "/" . $values['propertycode'];


                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>
								  </div>
								</div>
								
							  </div>';

                        $i++;
                    }

                    if (count($reviewArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
//                            if ($i % 3 != 0)
//                                echo '<div class="tab_conten_clmn">';
//                            else
//                                echo '<div class="tab_conten_clmn2">';

                            echo '<div class="tab_conten_clmn">';

                            echo '<div class="tab_box_text">
								  <div class="tab_box_detail">
									<div class="tab_box_heading">&nbsp;</div>
									<div class="tab_box_dis">&nbsp;</div>
									<div class="tab_box_more">&nbsp;</div>
								  </div>
								</div>
								
							  </div>';
                        }
                    }



                    break;
            }

            exit;
        }

        /*         * ************************************************** */
        /* 	Function for dynamic review add	in slides(admin) */
        /*         * ************************************************** */

        public function getreviewbypropertyAction()
        {
            global $mySession;
            $db = new Db();
            $OptionLocal = "";


            $DataLocal = $db->runQuery("select guest_name,review_id,headline from " . OWNER_REVIEW . " 
									inner join " . PROPERTY . " on " . OWNER_REVIEW . ".property_id = " . PROPERTY . ".id
								    where " . PROPERTY . ".id='" . $_REQUEST['Id'] . "' and parent_id = '0'
									and " . OWNER_REVIEW . ".review_id not in
									(	select review_id from  " . OWNER_REVIEW . " 
										inner join " . PROPERTY . " on " . OWNER_REVIEW . ".property_id = " . PROPERTY . ".id  
										inner join " . LATEST_REVIEW . " on " . OWNER_REVIEW . ".review_id = " . LATEST_REVIEW . ".r_review_id  
									)
									");


            if ($DataLocal != "" and count($DataLocal) > 0)
            {
                foreach ($DataLocal as $key => $valueLocal)
                {
                    $OptionLocal .= $valueLocal['review_id'] . "|||" . $valueLocal['guest_name'] . ":  '" . $valueLocal['headline'] . "'" . "***";
                }
                $OptionLocalarea = substr($OptionLocal, 0, strlen($OptionLocalarea) - 3);
            }

            echo $OptionLocalarea;
            exit();
        }

        //=================== cron job funcion for updating availability calendar ==================================
        function updatecalendarAction()
        {

            __autoloadPlugin('Ciirus');
            __autoloadPlugin('Globalresorthomes');
            __autoloadPlugin('Contempovacation');

            global $mySession;
            $db = new Db();

            $this->debug = false;


            $pptyArr = $db->runQuery("select *, subscriber_id AS supplier_id, 
                                    (
                                        SELECT count( subscriber_key ) AS count
                                        FROM `subscriber`
                                        INNER JOIN property ON property.xml_subscriber_id = subscriber.subscriber_id
                                        WHERE subscriber.subscriber_id = supplier_id
                                    ) AS count  from " . PROPERTY . "
                                      inner join subscriber on subscriber.subscriber_id = " . PROPERTY . ".xml_subscriber_id  
                                      where xml_subscriber_id > 0 and status != '4' and status != '0'
                                      order by subscriber.subscriber_id asc");


            //prd($pptyArr);

            $res = new Ciirus("c346aeb90de54a3", "ff3a6f4e60ab4ec");
            $_counter = array();
            $_counter['ciirus'] = 0;
            $_counter['globalresorthomes'] = 0;
            $_counter['contempovacation'] = 0;
            //$reservation = $res->getReservations(50644);
            //$arr['propertyId'] = 50644; //property which is not available
            //$arr['propertyId'] = 52196;
            foreach ($pptyArr as $pKey => $pVal)
            {

                $property_id = $pVal['id'];
                $xml_property_id = $pVal["xml_property_id"];
                
                
                
                switch ($pVal['xml_subscriber_id'])
                {
                    //ciirus
                    case '1':

                        if (!empty($xml_property_id))
                        {

                            //check if property is available or not
                            $arr['propertyId'] = $xml_property_id;
                            $av = $res->getProperties($arr, 0, true, true, true);
                            if (!preg_match('/No rows returned/', $av))
                            {
                                //update availability calendar
                                $reservation = $res->getReservations($xml_property_id);

                                //delete the older entries
                                if (!empty($reservation) && count($reservation))
                                    $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                                foreach ($reservation as $rKey => $rVal)
                                    {
                                    if($rKey === 'ArrivalDate')
                                    break;
                                    $data_cal = array();
                                    $data_cal['property_id'] = $property_id;
                                    $date_from = explode('T', $rVal['ArrivalDate']);
                                    $data_cal['date_from'] = $date_from[0];
                                    $date_to = explode('T', $rVal['DepartureDate']);
                                    $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                                    //$data_cal['date_to'] = $date_to[0];
                                    $data_cal['cal_status'] = '0';

                                    if (!$this->debug)
                                        $db->save(CAL_AVAIL, $data_cal);
                                }
                                if($rKey === 'ArrivalDate')
                                {
                                    $data_cal = array();
                                    $data_cal['property_id'] = $property_id;
                                    $date_from = explode('T', $reservation['ArrivalDate']);
                                    $data_cal['date_from'] = $date_from[0];
                                    $date_to = explode('T', $reservation['DepartureDate']);
                                    $data_cal['date_to'] = date('Y-m-d', strtotime($date_to[0] . ' -1 day'));
                                    //$data_cal['date_to'] = $date_to[0];
                                    $data_cal['cal_status'] = '0';

                                    if (!$this->debug)
                                        $db->save(CAL_AVAIL, $data_cal);
                                }
                                echo ($pKey + 1) . ". Successfully Updated property no - " . $pVal['xml_property_id'] . "<br>";
                            }
                            else
                            {
                                echo ($pKey + 1) . ". Failed Updating property no - " . $pVal['xml_property_id'] . "<br>";
                            }
                        }

                        //code for checking if the supplier data is empty
                        if ($pVal['count'] == $_counter['ciirus'] + 1)
                            echo "<strong>" . $pVal['subscriber_key'] . " cron job completed!! </strong><br>";

                        $_counter['ciirus']++;

                        break;

                    case '2':

                        $res = new Globalresorthomes($pVal['subscriber_url']);
                        $res->getWebsite($xml_property_id);


                        if (!empty($xml_property_id))
                        {
                            //get booked dates
                            $reservation = $res->getReservations($xml_property_id, $pVal['subscriber_secondary_url']);

                            if (!empty($reservation) && count($reservation))
                            {
                                $db->delete(CAL_AVAIL, "property_id = " . $property_id);


                                foreach ($reservation as $rKey => $rVal)
                                {
                                    $data_cal = array();
                                    $data_cal['property_id'] = $property_id;
                                    $date_from = explode('-', $rVal);
                                    // prd($date_from);

                                    $data_cal['date_from'] = date('Y-m-d', strtotime($date_from[0]));

                                    if (count($date_from) > 1)
                                        $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[1]));
                                    else
                                        $data_cal['date_to'] = date('Y-m-d', strtotime($date_from[0]));

                                    //$data_cal['date_to'] = $date_to[0];
                                    $data_cal['cal_status'] = '0';

                                    if (!$this->debug)
                                        $db->save(CAL_AVAIL, $data_cal);
                                }
                                echo ($pKey + 1) . ". Successfully Updated property no - " . $pVal['xml_property_id'] . "<br>";
                            }
                        }
                        //code for checking if the supplier data is empty
                        if ($pVal['count'] == $_counter['globalresorthomes'] + 1)
                            echo "<strong>" . $pVal['subscriber_key'] . " cron job completed!! </strong><br>";

                        $_counter['globalresorthomes']++;
                        break;
                    case '3': $res = new Contempovacation($pVal['subscriber_url']);
                        $res->getWebsite($xml_property_id);
                        if (!empty($xml_property_id))
                        {
                            $reservation = $res->getReservations($xml_property_id, $pVal['subscriber_url']);

                            if (!empty($reservation) && count($reservation))
                            {
                                $db->delete(CAL_AVAIL, "property_id = " . $property_id);

                                foreach ($reservation as $rKey => $rVal)
                                {
                                    $data_cal = array();
                                    $data_cal['property_id'] = $property_id;
                                    $date_from = explode('|||', $rVal);
                                    $data_cal['date_from'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[0])));
                                    $data_cal['date_to'] = date('Y-m-d', strtotime(str_replace('/', '-', $date_from[1])));
                                    //$data_cal['date_to'] = $date_to[0];
                                    $data_cal['cal_status'] = '0';
                                    if (!$this->debug)
                                        $db->save(CAL_AVAIL, $data_cal);
                                }
                                echo ($pKey + 1) . ". Successfully Updated property no - " . $pVal['xml_property_id'] . "<br>";
                            }
                        }
                        if ($pVal['count'] == $_counter['contempovacation'] + 1)
                            echo "<strong>" . $pVal['subscriber_key'] . " cron job completed!! </strong><br>";

                        $_counter['contempovacation']++;
                        break;
                } //switch case ends
            }

            echo "perfect!!";
            die;
        }

        //function for updating sitemap
        function updatesitemapAction()
        {
            $db = new Db();
            $staticpagesUrl = array();

            //get static pages url
            $pages = $db->runQuery("select * from " . PAGES . " where uselink = 'Y'");
            foreach ($pages as $pKey => $pVal)
            {
                $staticpagesUrl[] = APPLICATION_URL . "contents/pages/slug/" . $pVal['synonyms'];
            }


            //get property urls
            $propertypagesUrl = array();
            $pptyArr = $db->runQuery("select concat_ws('/', trim(trailing '/' from '" . APPLICATION_URL . "'),'holiday-rentals'," . COUNTRIES . ".country_name," . STATE . ".state_name," . CITIES . ".city_name, "  . SUB_AREA . ".sub_area_name, ". LOCAL_AREA . ".local_area_name,concat(" . PROPERTY . ".bedrooms,'-Bed','-',trim(trailing '.' from trim(trailing 0 from " . PROPERTY . ".bathrooms)),'-Bath-'," . PROPERTY_TYPE . ".ptyle_url)," . PROPERTY . ".propertycode) as url, 
                                        concat_ws('/',trim(trailing '/' from '" . APPLICATION_URL . "'),'search','property'," . COUNTRIES . ".country_name," . STATE . ".state_name," . CITIES . ".city_name, " . SUB_AREA . ".sub_area_name, " . LOCAL_AREA . ".local_area_name ) as location_url,
                                        " . PROPERTY . ".id, " . PROPERTY . ".propertycode,
                                        " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name ,
                                        " . PROPERTY . ".bedrooms, " . PROPERTY . ".bathrooms, " . PROPERTY_TYPE . ".ptyle_name    
                                        from " . PROPERTY . "
                                        inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                        inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                        inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                        inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                        left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                        left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                        where status = '3' 
                                        group by id
                                        ");


            foreach ($pptyArr as $pptyKey => $pptyVal)
            {
                $propertypagesUrl[] = $pptyVal['url'];
                $propertypagesUrl[] = $pptyVal['url'] . "/rental";
                $propertypagesUrl[] = $pptyVal['url'] . "/availability";
                $propertypagesUrl[] = $pptyVal['url'] . "/overview";
                $propertypagesUrl[] = $pptyVal['url'] . "/specification";
                $propertypagesUrl[] = $pptyVal['url'] . "/location";
                $propertypagesUrl[] = $pptyVal['url'] . "/availability";
                $propertypagesUrl[] = $pptyVal['url'] . "/rental";
                $propertypagesUrl[] = $pptyVal['url'] . "/photo-gallery";
                $propertypagesUrl[] = $pptyVal['url'] . "/reviews";
                $propertypagesUrl[] = $pptyVal['url'] . "/question";
                $searchpagesUrl[] = $pptyVal['location_url'];
            }


            //get search page urls


            $xml = '<?xml version = "1.0" encoding = "UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk</loc><changefreq>daily</changefreq><priority>1.00</priority></url>' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk/signin</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk/signin/login</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk/signup/ownerregistration</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk/signup/forgotpassword</loc><changefreq>daily</changefreq><priority>0.69</priority></url>' . "\n";
            $xml .= '<url><loc>http://www.dealatrip.co.uk/signup</loc><changefreq>daily</changefreq><priority>0.69</priority></url>' . "\n";


            foreach ($staticpagesUrl as $staticKey => $staticVal)
            {
                $xml .= '<url><loc>' . $staticVal . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($propertypagesUrl as $pVal)
            {
                $xml .= '<url><loc>' . $pVal . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($searchpagesUrl as $sVal)
            {
                $xml .= '<url><loc>' . $sVal . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            //location keywords
            $countryArr = $db->runQuery("select country_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         where status = '3'
                                         group by country_name
                                         ");

            $statesArr = $db->runQuery("select country_name, state_name from " . PROPERTY . "
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         where status = '3'
                                         group by state_name
                                         ");


            $citiesArr = $db->runQuery("select country_name, state_name, city_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         where status = '3'
                                         group by city_name
                                         ");

            $subareasArr = $db->runQuery("select country_name, state_name, city_name,sub_area_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         where status = '3'
                                         group by sub_area_name
                                         ");

            $localareasArr = $db->runQuery("select country_name, state_name, city_name,sub_area_name, local_area_name from " . PROPERTY . " 
                                         inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                         inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                         inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                         inner join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                         inner join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                         where status = '3'
                                         group by local_area_name
                                         ");

            foreach ($countryArr as $countryKey => $countryVal)
            {
                $xml .= '<url><loc>' . APPLICATION_URL . "holiday-rentals/" . $countryVal['country_name'] . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($statesArr as $stateKey => $stateVal)
            {
                $xml .= '<url><loc>' . APPLICATION_URL . "holiday-rentals/" . $stateVal["country_name"] . "/" . $stateVal['state_name'] . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($citiesArr as $cityKey => $cityVal)
            {
                $xml .= '<url><loc>' . APPLICATION_URL . "holiday-rentals/" . $cityVal["country_name"] . "/" . $cityVal["state_name"] . "/" . $cityVal['city_name'] . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($subareasArr as $subareaKey => $subareaVal)
            {
                $xml .= '<url><loc>' . APPLICATION_URL . "holiday-rentals/" . $subareaVal["country_name"] . "/" . $subareaVal["state_name"] . "/" . $subareaVal["city_name"] . "/" . $subareaVal["sub_area_name"] . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }

            foreach ($localareasArr as $localareaKey => $localareaVal)
            {
                $xml .= '<url><loc>' . APPLICATION_URL . "holiday-rentals/" . $localareaVal["country_name"] . "/" . $localareaVal["state_name"] . "/" . $localareaVal["city_name"] . "/" . $localareaVal['sub_area_name'] . "/" . $localareaVal["local_area_name"] . '</loc><changefreq>daily</changefreq><priority>0.85</priority></url>' . "\n";
            }


            $xml .= '</urlset>';

            $file = fopen(SITE_ROOT . "sitemap.xml", 'w');
            fwrite($file, $xml);
            fclose($file);

            echo "Sitemap xml updated!!";

            die;
        }

    }

?>