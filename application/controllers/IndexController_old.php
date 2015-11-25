<?php

    __autoloadDB('Db');

    class IndexController extends AppController
    {

        public function indexAction()
        {
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
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name 
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




            $countpptyArr = $db->runQuery("select count(*) as _count from " . SLIDES_PROPERTY . "
								  where lppty_status = '1' and lppty_type = '0'								  
								  order by lppty_order asc ");



            $this->view->count_latest_ppty = $countpptyArr[0]['_count'];
            $this->view->pptyArr = $pptyArr;



            //---------------------------//
            //fetching Special properties//
            //---------------------------//
            $spclArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode  ," . PROPERTY . ".property_title, " . PROPERTY . ".brief_desc, star_rating, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name
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
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name
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
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name 
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

        public function spclofferpptyAction($limit="12")
        {

            global $mySession;
            $db = new Db();
            $next = $this->getRequest()->getParam("next");
            if ($next == "")
                $starti = 0;
            else
                $starti = $next * 4;

            $spclArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".propertycode ," . PROPERTY . ".id, " . PROPERTY . ".property_title,star_rating,bedrooms, bathrooms, ptyle_name ," . PROPERTY . ".brief_desc, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name
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
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name 
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
                        if ($i % 3 != 0)
                            echo '<div class="tab_conten_clmn">';
                        else
                            echo '<div class="tab_conten_clmn2">';


                        echo'<div class="tab_box_text">
										  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
										  <div class="tab_box_detail">
											<div class="tab_box_heading">' . $values['property_title'] . '</div>
											<div class="tab_box_dis">' . substr($values['brief_desc'], 0, 50) . '</div>';
                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . " Beds " . $values['bathrooms'] . " Bath " . $values['ptyle_name'] . "/" . $values['propertycode'];

                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>										  </div>
										</div>
										<div class="star_rank">' . rating_views($values['star_rating']) . '</div>
									  </div>';

                        $i++;
                    }

                    if (count($pptyArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
                            if ($i % 3 != 0)
                                echo '<div class="tab_conten_clmn">';
                            else
                                echo '<div class="tab_conten_clmn2">';

                            echo '<div ' . $class . '>
										<div class="tab_box_text">
										  <div class="tab_box_detail">
											<div class="tab_box_heading">&nbsp;</div>
											<div class="tab_box_dis">&nbsp;</div>
											<div class="tab_box_more">&nbsp;</div>
										  </div>
										</div>
										<div class="star_rank">&nbsp;</div>
									  </div>';
                        }
                    }

                    break;


                case '2': $limit = ($counter - 1) * 6;


                    $popularArr = $db->runQuery("select " . SLIDES_PROPERTY . ".*, " . PROPERTY . ".id, " . PROPERTY . ".propertycode  ," . PROPERTY . ".property_title, star_rating," . PROPERTY . ".brief_desc,  bedrooms, bathrooms, ptyle_name, image_name,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name                         
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
                        if ($i % 3 != 0)
                            echo '<div class="tab_conten_clmn">';
                        else
                            echo '<div class="tab_conten_clmn2">';

                        echo '<div class="tab_box_text">
										  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
										  <div class="tab_box_detail">
											<div class="tab_box_heading">' . $values['property_title'] . '</div>
											<div class="tab_box_dis">' . substr($values['brief_desc'], 0, 50) . '</div>';

                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . " Beds " . $values['bathrooms'] . " Bath " . $values['ptyle_name'] . "/" . $values['propertycode'];

                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>
										  </div>
										</div>
										<div class="star_rank">' . rating_views($values['star_rating']) . '</div>
									  </div>';

                        $i++;
                    }

                    if (count($popularArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
                            if ($i % 3 != 0)
                                echo '<div class="tab_conten_clmn">';
                            else
                                echo '<div class="tab_conten_clmn2">';

                            echo '<div class="tab_box_text">
										  <div class="tab_box_detail">
											<div class="tab_box_heading">&nbsp;</div>
											<div class="tab_box_dis">&nbsp;</div>
											<div class="tab_box_more">&nbsp;</div>
										  </div>
										</div>
										<div class="star_rank">&nbsp;</div>
									  </div>';
                        }
                    }

                    break;


                case '3': //query for review 

                    $limit = ($counter - 1) * 6;

                    $reviewArr = $db->runQuery("select " . LATEST_REVIEW . ".*, " . GALLERY . ".image_name, " . PROPERTY . ".id, " . PROPERTY . ".propertycode, property_title, brief_desc,  bedrooms, bathrooms, ptyle_name, star_rating,
                                                                  " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . LOCAL_AREA . ".local_area_name, " . SUB_AREA . ".sub_area_name  
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

                        if ($i % 3 != 0)
                            echo '<div class="tab_conten_clmn">';
                        else
                            echo '<div class="tab_conten_clmn2">';

                        echo '<div class="tab_box_text">
								  <div class="tab_box_pic">' . ppty_image_display($values['image_name']) . '</div>
								  <div class="tab_box_detail">
									<div class="tab_box_heading">' . $values['property_title'] . '</div>
									<div class="tab_box_dis">' . substr($values['brief_desc'], 0, 50) . '</div>';

                        $url = APPLICATION_URL . "holiday-rentals/" . $values['country_name'] . "/" . $values['state_name'] . "/" . $values['city_name'] . "/";
                        if (!empty($values['sub_area']))
                        {
                            $url .= $values['sub_area_name'] . "/";

                            if (!empty($values['local_area_name']))
                                $url .= $values['local_area_name'] . "/";
                        }

                        $url .= $values['bedrooms'] . " Beds " . $values['bathrooms'] . " Bath " . $values['ptyle_name'] . "/" . $values['propertycode'];


                        echo '<div class="tab_box_more"><a href="' . $url . '">read more&nbsp;&gt;&gt;</a></div>
								  </div>
								</div>
								<div class="star_rank">' . rating_views($values['star_rating']) . '</div>
							  </div>';

                        $i++;
                    }

                    if (count($reviewArr) == 0)
                    {
                        for ($i = 1; $i <= 6; $i++)
                        {
                            if ($i % 3 != 0)
                                echo '<div class="tab_conten_clmn">';
                            else
                                echo '<div class="tab_conten_clmn2">';

                            echo '<div class="tab_box_text">
								  <div class="tab_box_detail">
									<div class="tab_box_heading">&nbsp;</div>
									<div class="tab_box_dis">&nbsp;</div>
									<div class="tab_box_more">&nbsp;</div>
								  </div>
								</div>
								<div class="star_rank">&nbsp;</div>
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

    }

?>