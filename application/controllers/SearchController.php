<?php

    __autoloadDB('Db');
//error_reporting(-1);
//ini_set('display_errors', 'On');
    class SearchController extends AppController
    {

        public function indexAction()
        {
            global $mySession;
            $db = new Db();
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            
            /*
              Meta Tags
              ---------------------------------- */
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 2");
            $Title = $metaArr[0]['meta_title'];
            $Description = $metaArr[0]['meta_description'];

            $this->view->propertiesstatus = $propertiesstatus = $this->getRequest()->getParam("propertiesstatus");
            $mySession->country_id=$this->view->country_id = $country_id = $this->getRequest()->getParam("country_id");
            $mySession->state_id=$this->view->state_id = $state_id = $this->getRequest()->getParam("state_id");
            $mySession->city_id=$this->view->city_id = $city_id = $this->getRequest()->getParam("city_id");
            $mySession->sub_area_id=$this->view->sub_area_id = $sub_area_id = $this->getRequest()->getParam("sub_area_id");
            $mySession->local_area_id=$this->view->local_area_id = $local_area_id = $this->getRequest()->getParam("local_area_id");
            /*
              SEO changes made after ponky
              ----------------------------------------- */
            $mySession->country= $location['country'] = $this->getRequest()->getParam('country');
            $mySession->state=$location['state'] = $this->getRequest()->getParam('state');
            $mySession->city=$location['city'] = $this->getRequest()->getParam('city');
            $mySession->sub_area=$location['sub_area'] = $this->getRequest()->getParam('sub_area');
            $mySession->local_area=$location['local_area'] = $this->getRequest()->getParam('local_area');

            $location = array_filter($location, "strlen");

            switch (count($location))
            {
                case 0: $this->view->headTitle(str_replace('[BREADCRUMB]', 'All', $Title))->offsetUnset(0);
                    $pageTitle = "";
                    break;
                case 1: $lArr = $db->runQuery("select country_id from " . COUNTRIES . " where lower(country_name) = '" . strtolower($location['country']) . "' ");
                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['country'], $Title))->offsetUnset(0);
                    $pageTitle = $location['country']." has ";
                    break;

                case 2: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, state_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' ");


                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);
                    $pageTitle = $location['state'].", ".$location['country']." has ";
                    break;

                case 3: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'   ");

                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');


                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $pageTitle = $location['city'].", ".$location['state'].", ".$location['country']." has ";
                    break;

                case 4: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id,  " . SUB_AREA . ".sub_area_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'  
											and lower(sub_area_name)  = '" . strtolower($location['sub_area']) . "' ");


                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');


                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['sub_area'] . '>' . $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $this->view->sub_area_id = $sub_area_id = $lArr[0]['sub_area_id'];
                    $pageTitle = $location['sub_area'].", ".$location['city'].", ".$location['state']." has ";
                    break;


                case 5: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id, " . SUB_AREA . ".sub_area_id, " . LOCAL_AREA . ".local_area_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											inner join " . LOCAL_AREA . " on " . SUB_AREA . ".sub_area_id = " . LOCAL_AREA . ".sub_area_id											
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'  
											and lower(sub_area_name)  = '" . strtolower($location['sub_area']) . "' 
											and lower(local_area_name)  = '" . strtolower($location['local_area']) . "' 
											");



                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');

                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['local_area'] . '>' . $location['sub_area'] . '>' . $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $this->view->sub_area_id = $sub_area_id = $lArr[0]['sub_area_id'];
                    $this->view->local_area_id = $local_area_id = $lArr[0]['local_area_id'];
                    $pageTitle = $location['local_area'].", ".$location['sub_area'].", ".$location['city']." has ";
                    break;
            }

            
			/*======= property type list =========*/
			$propertyTypeArr = $db->runQuery("select * from " . PROPERTYTYPE . " order by pstyle_order asc  ");
			$this->view->propertyTypeArr = $propertyTypeArr;
            
            //-----------------------------------------
            $specArr = $db->runQuery("select * from " . SPEC_CHILD . " order by spec_order asc ");

            $this->view->specArr = $specArr;
            $spclOffrArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " where status = '1'  ");
            $this->view->spclOfferArr = $spclOffrArr;
            $_spclOffr = $this->getRequest()->getParam("spcloffr");
            $this->view->spclOffr = $_spclOffr;
            //$this->view->pageTitle="Customer Sign In";

            $laterDate = $this->getRequest()->getParam("laterDate");
            $this->view->laterDate = $laterDate;


            @$myform = new Form_Search($lArr[0]);
            $this->view->myform = $myform;
            $this->view->Datefrom = $datefrom = $this->getRequest()->getParam("Datefrom");

            $dateto = $this->getRequest()->getParam("DateTo");
            $this->view->Dateto = $dateto;

            if ($dateto != "")
            {
                $dateto = date('Y-m-d', strtotime($datefrom . '+' . ($dateto - 1) . ' days'));
                $this->view->dateto = $dateto;
            }

            $bedroom = $this->getRequest()->getParam("bedroom");
            $bathroom = $this->getRequest()->getParam("bathroom");
            $amenity = $this->getRequest()->getParam("amenityRow");
            $swimming = $this->getRequest()->getParam("swimming");
            $facilities = $this->getRequest()->getParam("facilities");
            $themes = $this->getRequest()->getParam("themes");
            $propertyname = $this->getRequest()->getParam("propertyname");
			$ptypeRow = $this->getRequest()->getParam("ptypeRow");
			$pstarRow = $this->getRequest()->getParam("pstarRow");

            $this->view->bedroom = $bedroom;
            $this->view->bathroom = $bathroom;
            $this->view->amenity = $amenity;
            $this->view->swimming = $swimming;
            $this->view->facilities = $facilities;
            $this->view->themes = $themes;
            $this->view->propertyname = $propertyname;
			$this->view->ptypeRow = $ptypeRow;
			$this->view->pstarRow = $pstarRow;

            if ($datefrom != "")
                $datefrom = date('Y-m-d', strtotime($datefrom));

            $check_spec = 0; // variable used for finding out that the facilities has been selected
            //$where .= " and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."'  ";
            $where = "";
            $where1 = "";
            if ($propertiesstatus == 1)
            {
                $where .= " and " . PROPERTY . ".cal_default != '" . $propertiesstatus . "' ";
            }

            if ($country_id != "")
            {
                $where .= " and " . PROPERTY . ".country_id = '" . $country_id . "' ";
            }

            if ($state_id != "")
            {
                $where .= " and " . PROPERTY . ".state_id = '" . $state_id . "' ";
            }

            if ($city_id != "")
            {
                $where .= " and " . PROPERTY . ".city_id = '" . $city_id . "' ";
            }

            if ($sub_area_id != "")
            {
                $where .= " and " . PROPERTY . ".sub_area_id = '" . $sub_area_id . "' ";
            }

            if ($local_area_id != "")
            {
                $where .= " and " . PROPERTY . ".local_area_id = '" . $local_area_id . "' ";
            }

//
//            $availablePptyArr = $db->runQuery(" select group_concat(" . CAL_AVAIL . ".property_id) as ppty_list from " . CAL_AVAIL . " where
//						(
//							( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "')
//							or
//							( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "')											
//                                                            or
//        						( date_from >= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to <= '" . date('Y-m-d', strtotime($dateto)) . "')
//						)
//						and
//						cal_status = '0' ");

            $availablePptyArr = $db->runQuery(" select ".CAL_AVAIL . ".property_id as ppty_list from " . CAL_AVAIL . " where
						(
							( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "')
							or
							( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "')											
                                                            or
        						( date_from >= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to <= '" . date('Y-m-d', strtotime($dateto)) . "')
						)
						and
						cal_status = '0' ");
            
            foreach($availablePptyArr as $_k=>$_v){
                $_availableArr[0]['ppty_list'][] = $_v['ppty_list'];
            }

            $availablePptyArr = array();
            $availablePptyArr[0]['ppty_list'] = implode(",",$_availableArr[0]['ppty_list']);
            
            
            
            $availablePptyDb = $dbAdapter->select()
                    ->from(CAL_AVAIL, array(new Zend_Db_Expr("group_concat(" . CAL_AVAIL . ".property_id) as ppty_list")))
                    ->where("(( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "') or ( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "'))")
                    ->where("cal_status = '0'")
            ;

            
            $ppty_list = rtrim($availablePptyArr[0]['ppty_list'],',');
            
            if ($datefrom != "")
            {
                if ($ppty_list != "")
                    $where1 = "and  " . PROPERTY . ".id not in (" . $ppty_list . ") ";
                else
                    $where1 = "";
            }

            if (count($ptypeRow) > 0 && $ptypeRow[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($ptypeRow); $i++)
                {
					if($i==0){
					$where .= " " . PROPERTY . ".property_type = " . $ptypeRow[$i] . "  ";
					}else{
                     $where .= " or " . PROPERTY . ".property_type = " . $ptypeRow[$i] . "  ";
					}
                }
                $where .= ")";
            }
			
			 if (count($pstarRow) > 0 && $pstarRow[0] != "")
            {
                $where .= " and (";
				$i=0;
				$starcon="";
				foreach($pstarRow as $strow){
					switch($strow){
						case 3:
						$starcon="(star_rating < 4 ) ";
						break;
						case 4:
						$starcon=" (star_rating = 4 ) ";
						break;
						case 5:
						$starcon=" (star_rating = 5 )";
						
						break;
						case 6:
						$starcon=" (star_rating = 6 ) ";
						
						break;
					}
					
					if($i==0){
					$where .= " " .$starcon;
					}else{
                     $where .= " or ".$starcon;
					}
					
               $i++;
				}
                $where .= ")";
            }
			
			
			
			
            if (count($bedroom) > 0 && $bedroom[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($bedroom); $i++)
                {
                    if ($i == 0)
                    {
                        if ($bedroom[$i] == '7')
                            $where .= " " . PROPERTY . ".bedrooms >= '" . $bedroom[$i] . "'  ";
                        else
                            $where .= " " . PROPERTY . ".bedrooms = '" . $bedroom[$i] . "'  ";
                    }
                    else
                    {
                        if ($bedroom[$i] == '7')
                            $where .= " or " . PROPERTY . ".bedrooms >= '" . $bedroom[$i] . "'  ";
                        else
                            $where .= " or " . PROPERTY . ".bedrooms = '" . $bedroom[$i] . "'  ";
                    }
                }
                $where .= ")";
            }

            if (count($bathroom) > 0 && $bathroom[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($bathroom); $i++)
                {
                    if ($i == 0)
                    {
                        if ($bathroom[$i] == '4')
                            $where .= " " . PROPERTY . ".bathrooms >= '" . $bathroom[$i] . "'  ";
                        else
                            $where .= " " . PROPERTY . ".bathrooms = '" . $bathroom[$i] . "' or ".PROPERTY.".bathrooms = '".$bathroom[$i].".5' ";
                    }
                    else
                    {
                        if ($bathroom[$i] == '4')
                            $where .= " or " . PROPERTY . ".bathrooms >= '" . $bathroom[$i] . "'  ";
                        else
                            $where .= " or " . PROPERTY . ".bathrooms = '" . $bathroom[$i] . "' or ".PROPERTY.".bathrooms = '".$bathroom[$i].".5' ";
                    }
                }
                $where .= ")";
            }
            if (count($amenity) > 0)
            {
                for ($i = 0; $i < count($amenity); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$amenity[$i]."'  ";
                    $specSearch[] = $amenity[$i];
                }

                $check_spec = 1;
            }
            if (count($swimming) > 0)
            {
                for ($i = 0; $i < count($swimming); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$swimming[$i]."'  ";	
                    $specSearch[] = $swimming[$i];
                }
                $check_spec = 1;
            }



            if (count($themes) > 0)
            {
                for ($i = 0; $i < count($themes); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$themes[$i]."'  ";		
                    $specSearch[] = $themes[$i];
                }
                $check_spec = 1;
            }


            $having = $Having = "";

            if ($check_spec):
                $specArr = implode(",", $specSearch);
                //$where .= "   and     " . SPEC_ANS . ".answer in  (" . $specArr . ") ";
                $_specWhere .= "   and     " . SPEC_ANS . ".answer in  (" . $specArr . ") ";

                //$having = " having count(distinct ppty.answer) = " . count($specSearch) . " ";
                $_specGroupby = " group by property_id ";
                $_specHaving = " having count(distinct answer) = " . count($specSearch) . " ";
                //$Having = " count(distinct ppty.answer) = " . count($specSearch) . " ";
                
//                $specQuery = $dbAdapter->select()->from(PROPERTY,array("id"))
//                             ->join(SPEC_ANS,SPEC_ANS.".property_id = ".PROPERTY.".id",array())
//                             ->where(SPEC_ANS.".answer in ('$specArr')")
//                             ->group(PROPERTY.".id")
//                             ->having(" count(distinct ".SPEC_ANS.".answer ) = '".count($specSearch)."' ")
//                             ;
//                
                $specQuery = $dbAdapter->select()->from(SPEC_ANS)
                             ->where(" answer in ($specArr)")
                             ->group("property_id")
                             ->having(" count(distinct answer ) = '".count($specSearch)."' ")
                             ;
                
                //prd($specQuery->__toString());
                $specQuery = $specQuery->query()
                             ->fetchAll()
                             //->having("count(distinct answer) = " . count($specSearch) . " ")
                             ;
                
                $_specProperty = array();
                foreach($specQuery as $ssK=>$ssVal){
                    //$_specProperty[] = $ssVal['id'];
                    $_specProperty[] = $ssVal['property_id'];
                }
                $_specProperty = implode(",",$_specProperty);
                
                if($_specProperty)
                $where .= " and ".PROPERTY.".id in ($_specProperty) ";
                else
                $where .= " and 1=0 ";    
            endif;

            if (count($facilities) > 0)
            {
                for ($i = 0; $i < count($facilities); $i++)
                {
                    //$where .= " and ".AMENITY_ANS.".amenity_id in (".$facilities[$i]."' and ".AMENITY_ANS.".amenity_value = '1' ";		
                    $amenitySearch[] = $facilities[$i];
                }
                $check_amenity = 1;
            }

            if (@$check_amenity):
                $specArr = implode(",", $amenitySearch);
                //$where .= " and  " . AMENITY_ANS . ".amenity_id in  (" . $specArr . ")  and " . AMENITY_ANS . ".amenity_value = '1' ";

                
                $amenityQuery = $dbAdapter->select()->from(PROPERTY,array("id"))
                             ->join(AMENITY_ANS,AMENITY_ANS.".property_id = ".PROPERTY.".id",array("amenity_id"))
                             ->where(AMENITY_ANS.".amenity_id in ($specArr) and " . AMENITY_ANS . ".amenity_value = '1' ")
                             ->group(PROPERTY.".id")
                             ->having(" count(distinct ".AMENITY_ANS.".amenity_id ) = '".count($amenitySearch)."' ");
//                echo '<pre>'; print_r($amenityQuery->__toString()); exit('Macro die');
                
                $amenityQuery = $amenityQuery
                             ->query()
                             ->fetchAll()
                ;
                
                $_amenityProperty = array();
                foreach($amenityQuery as $ssK=>$ssVal){
                    $_amenityProperty[] = $ssVal['id'];
                }
                $_amenityProperty = implode(",",$_amenityProperty);
                
                if($_amenityProperty)
                $where .= " and ".PROPERTY.".id in ($_amenityProperty) ";
                else
                $where .= " and  1=0 ";    
                
//                if ($check_spec)
//                {
//                    $having .= " and count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                    $Having .= " and count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                }
//                else
//                {
//                    $having .= " having count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                    $Having .= " count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                }
            endif;


            $sum_query = "";


            if (!empty($_spclOffr))
            {
                $spclArr = implode(",", $_spclOffr);


                $where .= " and ( " . PROPERTY . ".id in (select property_id from spcl_offers where offer_id in (" . $spclArr . ") and book_by > curdate() ) )";
                //"and " . SPCL_OFFERS . ".book_by >= curdate() ";
            }

            $sumQuery = "";
            if ($datefrom != "" && $dateto != "")
            {


                /*                 * ***************** latest query ends************************ */
                $sum_query = " select " . CAL_RATE . ".*, " . CURRENCY . ".exchange_rate ,
                                coalesce (sum(
                                                 case when " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                 then (abs( datediff( " . CAL_RATE . ".date_from, " . CAL_RATE . ".date_to ))+1) * round(prate*exchange_rate) 
                                                 when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "'
                                                 then (abs( datediff( '" . $datefrom . "', '" . $dateto . "' ))+1) * round(prate*exchange_rate)  
                                                 when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                 then (abs( datediff( '" . $datefrom . "', " . CAL_RATE . ".date_to ))+1) * round(prate*exchange_rate) 
                                                 when " . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "'
                                                 then (abs( datediff( " . CAL_RATE . ".date_from, '" . $dateto . "' ))+1) * round(prate*exchange_rate) 
                                                 end),round(min(prate)*exchange_rate)*" . (dateDiff($datefrom, $dateto) + 1) . " ) 

                         AS total_amount from " . CAL_RATE . " 
                                 inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id  
                                 left join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code 
                                 group by " . CAL_RATE . ".property_id 
                        ";

                $sumQuery = $dbAdapter->select()
                        ->from(CAL_RATE, array(CURRENCY . ".exchange_rate", CAL_RATE . ".prate", CAL_RATE . ".property_id", CAL_RATE . ".nights", new Zend_Db_Expr(" coalesce (sum(
                                                    case when " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate) 
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "'
                                                    then (abs( datediff( '" . $datefrom . "', '" . $dateto . "' ))+1) * ceil(prate*exchange_rate)  
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( '" . $datefrom . "', " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate) 
                                                    when " . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "'
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, '" . $dateto . "' ))+1) * ceil(prate*exchange_rate) 
                                                    end),ceil(min(prate)*exchange_rate)*" . (dateDiff($datefrom, $dateto) + 1) . " ) 
                                                    AS total_amount ")))
                        ->join(PROPERTY, PROPERTY . ".id = " . CAL_RATE . ".property_id", array())
                        ->joinLeft(CURRENCY, PROPERTY . ".currency_code = " . CURRENCY . ".currency_code", array())
                        ->group(CAL_RATE . ".property_id")
                ;
            }

            if ($propertyname != "")
            {
                $propertyname = strtolower($propertyname);
                $where .= " and ( lower(" . PROPERTY . ".property_title) like '%" . $propertyname . "%' 
					   or lower(" . PROPERTY . ".property_name) like '%" . $propertyname . "%' 	
					   or lower(" . COUNTRIES . ".country_name) like '%" . $propertyname . "%' 
					   or lower(" . STATE . ".state_name) like '%" . $propertyname . "%' 
					   or lower(" . CITIES . ".city_name) like '%" . $propertyname . "%' 
					   or lower(" . SUB_AREA . ".sub_area_name) like '%" . $propertyname . "%' 
					   or lower(" . LOCAL_AREA . ".local_area_name) like '%" . $propertyname . "%'
					   or " . PROPERTY . ".propertycode like '%" . $propertyname . "%'  )
			";
            }

            $limit = 10; //5 records per page

            $start = $this->getRequest()->getParam("start");

            $page = $this->getRequest()->getParam("start");
            if (empty($start))
            {
                $start = 1;
                $page = 0;
            }
            $page = $page - 1;
            $starti = ($start - 1) * 10;

            //code for converting an array to request params
            $nvp_string = "";
            $nvp_string = addQueryString('start', $page+1);

            if (!isset($mySession->gridType))
                $mySession->gridType = 1;

            if ($this->getRequest()->isPost())
            {
                switch ($_REQUEST['sorti'])
                {
                    case '1': $order = $Order = "";
                        $mySession->order = '1';
                        break;
                    case '2': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount desc";
                            $Order = "ppty.total_amount desc";
                        else:
                            $order = "order by ppty.prate desc";
                            $Order = "order by ppty.prate desc";
                        endif;

                        $mySession->order = '2';
                        break;
                    case '3':
                        $order = "order by ppty.bedrooms asc";
                        $Order = "ppty.bedrooms asc";
                        $mySession->order = '3';
                        break;
                    case '4':
                        $order = "order by ppty.bedrooms desc";
                        $Order = "ppty.bedrooms desc";
                        $mySession->order = '4';
                        break;
                }

                switch ($_REQUEST['grid'])
                {
                    case '1': $mySession->gridType = '1';
                        break;
                    case '2': $mySession->gridType = '2';
                        break;
                    case '3': $mySession->gridType = '3';
                        break;
                }
            }


            if ($datefrom != "" && $dateto != "")
            {
                $order = "order by ppty.total_amount asc";
                $Order = "ppty.total_amount asc";
            }
            else
            {
                $order = "order by ppty.prate asc";
                $Order = "ppty.prate asc";
            }



            if (isset($mySession->order))
            {
                switch ($mySession->order)
                {
                    case '1': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount asc";
                            $Order = "ppty.total_amount asc";
                        else:
                            $order = "order by prate asc";
                            $Order = "prate asc";
                        endif;

                        break;
                    case '2': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount desc";
                            $Order = "ppty.total_amount desc";
                        else:
                            $order = "order by prate desc";
                            $Order = "prate desc";
                        endif;

                        break;
                    case '3':
                        $order = "order by bedrooms asc";
                        $Order = "bedrooms asc";
                        break;
                    case '4':
                        $order = "order by bedrooms desc";
                        $Order = "bedrooms desc";
                        break;
                }
            }






            $no_date_query = "select * from 
						  (select property_id, date_from, date_to, nights, ceil(prate*exchange_rate) as prate  from " . CAL_RATE . " 
						  inner join " . PROPERTY . " on " . CAL_RATE . ".property_id = " . PROPERTY . ".id 	
						  inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
                                                  where " . CAL_RATE . ".date_from >= curdate()
						  order by prate asc) as abc group by abc.property_id";


            $noDateQuery = $dbAdapter->select()->from(
                            array("abc" => $dbAdapter->select()
                                ->from(CAL_RATE, array('property_id', 'date_from', 'date_to', 'nights', 'ceil(prate*exchange_rate) as prate'))
                                ->join(PROPERTY, CAL_RATE . ".property_id = " . PROPERTY . ".id ", array())
                                ->join(CURRENCY, PROPERTY . ".currency_code = " . CURRENCY . ".currency_code", array())
                                ->where(CAL_RATE . ".date_to >= curdate()")
                                ->order("prate asc")
                    ))
                    ->group("abc.property_id");
            ;



            $subProperty = $dbAdapter->select()
                    //->from(PROPERTY, array("property.id as pid", "cletitude", "clongitude", "propertycode", "(ptyle_name)", "property_title", CURRENCY . ".currency_code", "(country_name)", "(state_name)", "(city_name)", "(sub_area_name)", "(local_area_name)", "bedrooms", new Zend_Db_Expr('trim(trailing "." from trim(trailing 0 from '.PROPERTY.'.bathrooms)) as bathrooms'), "(ptyle_url)","en_bedrooms", "maximum_occupancy", "star_rating", "cal_default", "(amenity_id)", SPEC_ANS . ".answer", "cal_availability.prate as prate", "cal_availability.nights", (($sumQuery == '') ? '' : 'cal_availability.total_amount') . ""))
                    ->from(PROPERTY, array("property.id as pid", "cletitude", "clongitude", "propertycode", "(ptyle_name)", "property_title", CURRENCY . ".currency_code", "(country_name)", "(state_name)", "(city_name)", "(sub_area_name)", "(local_area_name)", "bedrooms", new Zend_Db_Expr('trim(trailing "." from trim(trailing 0 from '.PROPERTY.'.bathrooms)) as bathrooms'), "(ptyle_url)","en_bedrooms", "maximum_occupancy", "is_insured","star_rating", "cal_default", "cal_availability.prate as prate", "cal_availability.nights", (($sumQuery == '') ? '' : 'cal_availability.total_amount') . ""))
                    //->setIntegrityCheck(false)
                    ->join(array('pt' => PROPERTY_TYPE), "pt.ptyle_id = " . PROPERTY . ".property_type ", array())
                    //->joinLeft(SPEC_ANS, SPEC_ANS . ".property_id = " . PROPERTY . ".id", array())
                    //->joinLeft(AMENITY_ANS, AMENITY_ANS . ".property_id = " . PROPERTY . ".id", array())
                    ->join(COUNTRIES, COUNTRIES . ".country_id = " . PROPERTY . ".country_id", array())
                    ->join(STATE, STATE . ".state_id = " . PROPERTY . ".state_id", array())
                    ->join(CITIES, CITIES . ".city_id = " . PROPERTY . ".city_id", array())
                    ->join(CURRENCY, CURRENCY . ".currency_code = " . PROPERTY . ".currency_code", array())
                    ->joinLeft(SUB_AREA, SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id", array())
                    ->joinLeft(LOCAL_AREA, LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id", array())

            ;

            if (!empty($sumQuery))
                $subProperty = $subProperty->joinLeft(array('cal_availability' => $sumQuery), "cal_availability.property_id = " . PROPERTY . ".id", array());
            else
                $subProperty = $subProperty->joinLeft(array('cal_availability' => $noDateQuery), "cal_availability.property_id = " . PROPERTY . ".id", array());

            $subProperty = $subProperty->where(PROPERTY . ".status = '3'")
                    ->where("1 " . $where . " " . $where1)
            ;
            
             

            //prd($subProperty->__toString());

            if (!empty($sumQuery))
                $subProperty = $subProperty->group("pid");

            $subProperty = $subProperty->order("cal_availability.prate asc");


            $propertyDbAdapter = $dbAdapter->select()->from(array("ppty" => $subProperty))
                    ->group("ppty.pid");

            if ($Having)
                $propertyDbAdapter = $propertyDbAdapter->having($Having);

            $propertyDbAdapter = $propertyDbAdapter->order($Order);

            //prd($propertyDbAdapter->__toString());

            

            $adapter = new Zend_Paginator_Adapter_DbSelect($propertyDbAdapter);
            
            $paginator = new Zend_Paginator($adapter);
            
            $paginator->setItemCountPerPage(10);

            $paginator->setCurrentPageNumber($page + 1);
            //$paginator->setItemCountPerPage(10);
            $totalCount = $paginator->getTotalItemCount();
            $this->view->paginator = $paginator;

            
            $propertyArr = $propertyDbAdapter->limitPage($page + 1, 10)->query()->fetchAll();

            
            //prd($propertyArr);
//            $propertyArr = $db->runQuery("select * from  
//                                    (select " . PROPERTY . ".id as pid, cletitude, clongitude, propertycode, ptyle_name, property_title, " . CURRENCY . ".currency_code, country_name, state_name, city_name, sub_area_name, local_area_name,
//                                     bedrooms, bathrooms, en_bedrooms, maximum_occupancy, star_rating, cal_default, amenity_id, " . SPEC_ANS . ".answer,  cal_availability.prate as prate, cal_availability.nights   
//                                     " . (($sum_query == '') ? '' : ', cal_availability.total_amount  ') . " 
//                                    from " . PROPERTY . " 
//                                    inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = " . PROPERTY . ".property_type
//                                    left join " . SPEC_ANS . " on " . SPEC_ANS . ".property_id = " . PROPERTY . ".id
//                                    left join " . AMENITY_ANS . " on " . AMENITY_ANS . ".property_id = " . PROPERTY . ".id
//                                    inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
//                                    inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
//                                    inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
//                                    inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
//                                    left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
//                                    left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id  
//                                    left join 
//                                     " . (($sum_query == '') ? "(" . $no_date_query . ")" : "(" . $sum_query . ")") . " as cal_availability
//                                     on cal_availability.property_id = " . PROPERTY . ".id
//                                     where " . PROPERTY . ".status = '3'  
//                                     $where 
//                                     $where1  " . (($sum_query == '') ? '' : 'group by  pid') . " order by cal_availability.prate asc) as ppty
//                                     where ppty.pid != '0' 
//                                     group by ppty.pid    
//                                     $having  
//                                     $order 
//                                        
//                                   ");
            //prd($propertyArr);

            /*             * ************************* FINAL QUERY ENDS ****************************************** */
            //prd($propertyArr[0]['__counti']);
            $propertyData = array();
            $t = 0;
            $x = 0;
            $k = 0;

            
            foreach ($propertyArr as $key => $val)
            {
                //CODE FOR INSERTING IMAGE
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = " . $val['pid'] . " ");
                //prd("select answer from ".SPEC_ANS." where property_id = ".$val['pid']." $_specWhere ");
                $_specArr = $db->runQuery("select answer from ".SPEC_ANS." where property_id = ".$val['pid']." ");
                $_amenityArr = $db->runQuery("select amenity_id from ".AMENITY_ANS." where property_id = ".$val['pid']."  ");
                $subpropertyData[$x]['image_name'] = $imgArr[0]['image_name'];
                $subpropertyData[$x]['image_title'] = $imgArr[0]['image_title'];
                $propertyData[$x]['image_name'] = $imgArr[0]['image_name'];
                $propertyData[$x]['image_title'] = $imgArr[0]['image_title'];
                $propertyData[$x]['amenity_id'] = $_amenityArr[0]['amenity_id'];
                
                foreach ($val as $keys => $values)
                {
                    if(($_specWhere && count($_specArr) > 0) || !$specWhere){
                        $propertyData[$x][$keys] = $values;
                        $subpropertyData[$x][$keys] = $values;
                    }
                }
                $x++;
            }


            //prd($subpropertyData);

            /* 		$this->view->propertyData=$propertyData; */
            
            $this->view->total = count($propertyData);
            $this->view->start = $start;
            $this->view->now = APPLICATION_URL . "search?" . $nvp_string;
            if(!empty($nvp_string))
            $this->view->queryString = "?" . $nvp_string;
            else
            $this->view->queryString = "";    
            $this->view->limit = $limit;


            //prd($subpropertyData);

            if ($mySession->gridType != '3')
            {
                $Description = str_replace(array('[PROPERTY_NO_START]', '[PROPERTY_NO_END]'), array($subpropertyData[0]['propertycode'], $subpropertyData[count($subpropertyData) - 1]['propertycode']), $Description);
                $this->view->headMeta('description', $Description);
                $this->view->propertyData = $subpropertyData;
            }
            else
            {
                $Description = str_replace(array('[PROPERTY_NO_START]', '[PROPERTY_NO_END]'), array($propertyData[0]['propertycode'], $propertyData[count($propertyData) - 1]['propertycode']), $Description);
                $this->view->headMeta('description', $Description);
                $this->view->propertyData = $propertyData;
            }
            
            $pageTitle .= $totalCount." Holiday Rentals";
            $this->view->headlineText = $pageTitle;

            $this->view->gridType = $mySession->gridType;

            //amenity arr
            $amenityArr = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
            $this->view->amenityArr = $amenityArr;

            //prd($propertyData);
            
        }
		public function searchajaxAction()
        {ini_set('max_execution_time', 300);
            global $mySession;
            $db = new Db();
            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $this->_helper->layout->disableLayout();
            /*
              Meta Tags
              ---------------------------------- */
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 2");
            $Title = $metaArr[0]['meta_title'];
            $Description = $metaArr[0]['meta_description'];
			
			 $this->view->country_id = $country_id = $mySession->country_id;
           $this->view->state_id = $state_id = $mySession->state_id;
            $this->view->city_id = $city_id = $mySession->city_id;
            $this->view->sub_area_id = $sub_area_id = $mySession->sub_area_id;
            $this->view->local_area_id = $local_area_id =$mySession->local_area_id;

            /*
              SEO changes made after ponky
              ----------------------------------------- */
            $location['country'] =$mySession->country;
            $location['state'] =$mySession->state;
            $location['city'] =$mySession->city;
            $location['sub_area'] =$mySession->sub_area;
            $location['local_area'] =$mySession->local_area;



            

            $location = array_filter($location, "strlen");

            switch (count($location))
            {
                case 0: $this->view->headTitle(str_replace('[BREADCRUMB]', 'All', $Title))->offsetUnset(0);
                    $pageTitle = "";
                    break;
                case 1: $lArr = $db->runQuery("select country_id from " . COUNTRIES . " where lower(country_name) = '" . strtolower($location['country']) . "' ");
                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['country'], $Title))->offsetUnset(0);
                    $pageTitle = $location['country']." has ";
                    break;

                case 2: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, state_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' ");


                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);
                    $pageTitle = $location['state'].", ".$location['country']." has ";
                    break;

                case 3: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'   ");

                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');


                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $pageTitle = $location['city'].", ".$location['state'].", ".$location['country']." has ";
                    break;

                case 4: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id,  " . SUB_AREA . ".sub_area_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'  
											and lower(sub_area_name)  = '" . strtolower($location['sub_area']) . "' ");


                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');


                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['sub_area'] . '>' . $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $this->view->sub_area_id = $sub_area_id = $lArr[0]['sub_area_id'];
                    $pageTitle = $location['sub_area'].", ".$location['city'].", ".$location['state']." has ";
                    break;


                case 5: $lArr = $db->runQuery("select " . COUNTRIES . ".country_id, " . STATE . ".state_id, " . CITIES . ".city_id, " . SUB_AREA . ".sub_area_id, " . LOCAL_AREA . ".local_area_id from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											inner join " . LOCAL_AREA . " on " . SUB_AREA . ".sub_area_id = " . LOCAL_AREA . ".sub_area_id											
											where lower(country_name) = '" . strtolower($location['country']) . "'
											and lower(state_name) = '" . strtolower($location['state']) . "' 
											and lower(city_name) = '" . strtolower($location['city']) . "'  
											and lower(sub_area_name)  = '" . strtolower($location['sub_area']) . "' 
											and lower(local_area_name)  = '" . strtolower($location['local_area']) . "' 
											");



                    if (count($lArr) == 0)
                        $this->renderScript('/error/error.phtml');

                    //Meta title					
                    $this->view->headTitle(str_replace('[BREADCRUMB]', $location['local_area'] . '>' . $location['sub_area'] . '>' . $location['city'] . '>' . $location['state'] . '>' . $location['country'], $Title))->offsetUnset(0);

                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    $this->view->state_id = $state_id = $lArr[0]['state_id'];
                    $this->view->city_id = $city_id = $lArr[0]['city_id'];
                    $this->view->sub_area_id = $sub_area_id = $lArr[0]['sub_area_id'];
                    $this->view->local_area_id = $local_area_id = $lArr[0]['local_area_id'];
                    $pageTitle = $location['local_area'].", ".$location['sub_area'].", ".$location['city']." has ";
                    break;
            }

            
            //-----------------------------------------
            $specArr = $db->runQuery("select * from " . SPEC_CHILD . "  ");

            $this->view->specArr = $specArr;
            $spclOffrArr = $db->runQuery("select * from " . SPCL_OFFER_TYPES . " where status = '1'  ");
            $this->view->spclOfferArr = $spclOffrArr;
            $_spclOffr = $this->getRequest()->getParam("spcloffr");
            $this->view->spclOffr = $_spclOffr;
            //$this->view->pageTitle="Customer Sign In";

            $laterDate = $this->getRequest()->getParam("laterDate");
            $this->view->laterDate = $laterDate;


            @$myform = new Form_Search($lArr[0]);
            $this->view->myform = $myform;
            $this->view->Datefrom = $datefrom = $this->getRequest()->getParam("Datefrom");

            $dateto = $this->getRequest()->getParam("DateTo");
            $this->view->Dateto = $dateto;

            if ($dateto != "")
            {
                $dateto = date('Y-m-d', strtotime($datefrom . '+' . ($dateto - 1) . ' days'));
                $this->view->dateto = $dateto;
            }

            $bedroom = $this->getRequest()->getParam("bedroom");
            $bathroom = $this->getRequest()->getParam("bathroom");
            $amenity = $this->getRequest()->getParam("amenityRow");
            $swimming = $this->getRequest()->getParam("swimming");
            $facilities = $this->getRequest()->getParam("facilities");
            $themes = $this->getRequest()->getParam("themes");
            $propertyname = $this->getRequest()->getParam("propertyname");
			$propertiesstatus = $this->getRequest()->getParam("propertiesstatus");
         	$ptypeRow = $this->getRequest()->getParam("ptypeRow");
			$pstarRow = $this->getRequest()->getParam("pstarRow");

            $this->view->bedroom = $bedroom;
            $this->view->bathroom = $bathroom;
            $this->view->amenity = $amenity;
            $this->view->swimming = $swimming;
            $this->view->facilities = $facilities;
            $this->view->themes = $themes;
            $this->view->propertyname = $propertyname;
			$this->view->ptypeRow = $ptypeRow;
			$this->view->pstarRow = $pstarRow;

            if ($datefrom != "")
                $datefrom = date('Y-m-d', strtotime($datefrom));

            $check_spec = 0; // variable used for finding out that the facilities has been selected
            //$where .= " and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."'  ";
            $where = "";
            $where1 = "";
			 if ($propertiesstatus ==1)
            {
                $where .= " and " . PROPERTY . ".cal_default != '" . $propertiesstatus . "' ";
            }
            if ($country_id != "")
            {
                $where .= " and " . PROPERTY . ".country_id = '" . $country_id . "' ";
            }

            if ($state_id != "")
            {
                $where .= " and " . PROPERTY . ".state_id = '" . $state_id . "' ";
            }

            if ($city_id != "")
            {
                $where .= " and " . PROPERTY . ".city_id = '" . $city_id . "' ";
            }

            if ($sub_area_id != "")
            {
                $where .= " and " . PROPERTY . ".sub_area_id = '" . $sub_area_id . "' ";
            }

            if ($local_area_id != "")
            {
                $where .= " and " . PROPERTY . ".local_area_id = '" . $local_area_id . "' ";
            }

//
//            $availablePptyArr = $db->runQuery(" select group_concat(" . CAL_AVAIL . ".property_id) as ppty_list from " . CAL_AVAIL . " where
//						(
//							( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "')
//							or
//							( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "')											
//                                                            or
//        						( date_from >= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to <= '" . date('Y-m-d', strtotime($dateto)) . "')
//						)
//						and
//						cal_status = '0' ");

            $availablePptyArr = $db->runQuery(" select ".CAL_AVAIL . ".property_id as ppty_list from " . CAL_AVAIL . " where
						(
							( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "')
							or
							( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "')											
                                                            or
        						( date_from >= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to <= '" . date('Y-m-d', strtotime($dateto)) . "')
						)
						and
						cal_status = '0' ");
            
            foreach($availablePptyArr as $_k=>$_v){
                $_availableArr[0]['ppty_list'][] = $_v['ppty_list'];
            }

            $availablePptyArr = array();
            $availablePptyArr[0]['ppty_list'] = implode(",",$_availableArr[0]['ppty_list']);
            
            
            
            $availablePptyDb = $dbAdapter->select()
                    ->from(CAL_AVAIL, array(new Zend_Db_Expr("group_concat(" . CAL_AVAIL . ".property_id) as ppty_list")))
                    ->where("(( date_from <= '" . date('Y-m-d', strtotime($datefrom)) . "' and date_to >= '" . date('Y-m-d', strtotime($datefrom)) . "') or ( date_from <= '" . date('Y-m-d', strtotime($dateto)) . "' and date_to >= '" . date('Y-m-d', strtotime($dateto)) . "'))")
                    ->where("cal_status = '0'")
            ;

            
            $ppty_list = rtrim($availablePptyArr[0]['ppty_list'],',');
            
            if ($datefrom != "")
            {
                if ($ppty_list != "")
                    $where1 = "and  " . PROPERTY . ".id not in (" . $ppty_list . ") ";
                else
                    $where1 = "";
            }


			/*= property type =*/
			if (count($ptypeRow) > 0 && $ptypeRow[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($ptypeRow); $i++)
                {
					if($i==0){
					$where .= " " . PROPERTY . ".property_type = " . $ptypeRow[$i] . "  ";
					}else{
                     $where .= " or " . PROPERTY . ".property_type = " . $ptypeRow[$i] . "  ";
					}
                }
                $where .= ")";
            }
			
			 if (count($pstarRow) > 0 && $pstarRow[0] != "")
            {
                $where .= " and (";
				$i=0;
				$starcon="";
				foreach($pstarRow as $strow){
					switch($strow){
						case 3:
						$starcon="(star_rating < 4 ) ";
						break;
						case 4:
						$starcon=" (star_rating = 4 ) ";
						break;
						case 5:
						$starcon=" (star_rating = 5 )";
						
						break;
						case 6:
						$starcon=" (star_rating = 6 ) ";
						
						break;
					}
					
					if($i==0){
					$where .= " " .$starcon;
					}else{
                     $where .= " or ".$starcon;
					}
					
               $i++;
				}
                $where .= ")";
            }
			
			
            if (count($bedroom) > 0 && $bedroom[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($bedroom); $i++)
                {
                    if ($i == 0)
                    {
                        if ($bedroom[$i] == '7')
                            $where .= " " . PROPERTY . ".bedrooms >= '" . $bedroom[$i] . "'  ";
                        else
                            $where .= " " . PROPERTY . ".bedrooms = '" . $bedroom[$i] . "'  ";
                    }
                    else
                    {
                        if ($bedroom[$i] == '7')
                            $where .= " or " . PROPERTY . ".bedrooms >= '" . $bedroom[$i] . "'  ";
                        else
                            $where .= " or " . PROPERTY . ".bedrooms = '" . $bedroom[$i] . "'  ";
                    }
                }
                $where .= ")";
            }

            if (count($bathroom) > 0 && $bathroom[0] != "")
            {
                $where .= " and (";
                for ($i = 0; $i < count($bathroom); $i++)
                {
                    if ($i == 0)
                    {
                        if ($bathroom[$i] == '4')
                            $where .= " " . PROPERTY . ".bathrooms >= '" . $bathroom[$i] . "'  ";
                        else
                            $where .= " " . PROPERTY . ".bathrooms = '" . $bathroom[$i] . "' or ".PROPERTY.".bathrooms = '".$bathroom[$i].".5' ";
                    }
                    else
                    {
                        if ($bathroom[$i] == '4')
                            $where .= " or " . PROPERTY . ".bathrooms >= '" . $bathroom[$i] . "'  ";
                        else
                            $where .= " or " . PROPERTY . ".bathrooms = '" . $bathroom[$i] . "' or ".PROPERTY.".bathrooms = '".$bathroom[$i].".5' ";
                    }
                }
                $where .= ")";
            }
            if (count($amenity) > 0)
            {
                for ($i = 0; $i < count($amenity); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$amenity[$i]."'  ";
                    $specSearch[] = $amenity[$i];
                }

                $check_spec = 1;
            }
            if (count($swimming) > 0)
            {
                for ($i = 0; $i < count($swimming); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$swimming[$i]."'  ";	
                    $specSearch[] = $swimming[$i];
                }
                $check_spec = 1;
            }



            if (count($themes) > 0)
            {
                for ($i = 0; $i < count($themes); $i++)
                {
                    //$where .= " and ".SPEC_ANS.".answer = '".$themes[$i]."'  ";		
                    $specSearch[] = $themes[$i];
                }
                $check_spec = 1;
            }


            $having = $Having = "";

            if ($check_spec):
                $specArr = implode(",", $specSearch);
                //$where .= "   and     " . SPEC_ANS . ".answer in  (" . $specArr . ") ";
                $_specWhere .= "   and     " . SPEC_ANS . ".answer in  (" . $specArr . ") ";

                //$having = " having count(distinct ppty.answer) = " . count($specSearch) . " ";
                $_specGroupby = " group by property_id ";
                $_specHaving = " having count(distinct answer) = " . count($specSearch) . " ";
                //$Having = " count(distinct ppty.answer) = " . count($specSearch) . " ";
                
//                $specQuery = $dbAdapter->select()->from(PROPERTY,array("id"))
//                             ->join(SPEC_ANS,SPEC_ANS.".property_id = ".PROPERTY.".id",array())
//                             ->where(SPEC_ANS.".answer in ('$specArr')")
//                             ->group(PROPERTY.".id")
//                             ->having(" count(distinct ".SPEC_ANS.".answer ) = '".count($specSearch)."' ")
//                             ;
//                
                $specQuery = $dbAdapter->select()->from(SPEC_ANS)
                             ->where(" answer in ($specArr)")
                             ->group("property_id")
                             ->having(" count(distinct answer ) = '".count($specSearch)."' ")
                             ;
                
                //prd($specQuery->__toString());
                $specQuery = $specQuery->query()
                             ->fetchAll()
                             //->having("count(distinct answer) = " . count($specSearch) . " ")
                             ;
                
                $_specProperty = array();
                foreach($specQuery as $ssK=>$ssVal){
                    //$_specProperty[] = $ssVal['id'];
                    $_specProperty[] = $ssVal['property_id'];
                }
                $_specProperty = implode(",",$_specProperty);
                
                if($_specProperty)
                $where .= " and ".PROPERTY.".id in ($_specProperty) ";
                else
                $where .= " and 1=0 ";    
            endif;

            if (count($facilities) > 0)
            {
                for ($i = 0; $i < count($facilities); $i++)
                {
                    //$where .= " and ".AMENITY_ANS.".amenity_id in (".$facilities[$i]."' and ".AMENITY_ANS.".amenity_value = '1' ";		
                    $amenitySearch[] = $facilities[$i];
                }
                $check_amenity = 1;
            }

            if (@$check_amenity):
                $specArr = implode(",", $amenitySearch);
                //$where .= " and  " . AMENITY_ANS . ".amenity_id in  (" . $specArr . ")  and " . AMENITY_ANS . ".amenity_value = '1' ";

                
                $amenityQuery = $dbAdapter->select()->from(PROPERTY,array("id"))
                             ->join(AMENITY_ANS,AMENITY_ANS.".property_id = ".PROPERTY.".id",array("amenity_id"))
                             ->where(AMENITY_ANS.".amenity_id in ($specArr) and " . AMENITY_ANS . ".amenity_value = '1' ")
                             ->group(PROPERTY.".id")
                             ->having(" count(distinct ".AMENITY_ANS.".amenity_id ) = '".count($amenitySearch)."' ");
//                echo '<pre>'; print_r($amenityQuery->__toString()); exit('Macro die');
                
                $amenityQuery = $amenityQuery
                             ->query()
                             ->fetchAll()
                ;
                
                $_amenityProperty = array();
                foreach($amenityQuery as $ssK=>$ssVal){
                    $_amenityProperty[] = $ssVal['id'];
                }
                $_amenityProperty = implode(",",$_amenityProperty);
                
                if($_amenityProperty)
                $where .= " and ".PROPERTY.".id in ($_amenityProperty) ";
                else
                $where .= " and  1=0 ";    
                
//                if ($check_spec)
//                {
//                    $having .= " and count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                    $Having .= " and count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                }
//                else
//                {
//                    $having .= " having count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                    $Having .= " count(distinct ppty.amenity_id ) = " . count($amenitySearch) . " ";
//                }
            endif;


            $sum_query = "";


            if (!empty($_spclOffr))
            {
                $spclArr = implode(",", $_spclOffr);


                $where .= " and ( " . PROPERTY . ".id in (select property_id from spcl_offers where offer_id in (" . $spclArr . ") and book_by > curdate() ) )";
                //"and " . SPCL_OFFERS . ".book_by >= curdate() ";
            }

            $sumQuery = "";
            if ($datefrom != "" && $dateto != "")
            {


                /*                 * ***************** latest query ends************************ */
                $sum_query = " select " . CAL_RATE . ".*, " . CURRENCY . ".exchange_rate ,
                                coalesce (sum(
                                                 case when " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                 then (abs( datediff( " . CAL_RATE . ".date_from, " . CAL_RATE . ".date_to ))+1) * round(prate*exchange_rate) 
                                                 when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "'
                                                 then (abs( datediff( '" . $datefrom . "', '" . $dateto . "' ))+1) * round(prate*exchange_rate)  
                                                 when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                 then (abs( datediff( '" . $datefrom . "', " . CAL_RATE . ".date_to ))+1) * round(prate*exchange_rate) 
                                                 when " . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "'
                                                 then (abs( datediff( " . CAL_RATE . ".date_from, '" . $dateto . "' ))+1) * round(prate*exchange_rate) 
                                                 end),round(min(prate)*exchange_rate)*" . (dateDiff($datefrom, $dateto) + 1) . " ) 

                         AS total_amount from " . CAL_RATE . " 
                                 inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id  
                                 left join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code 
                                 group by " . CAL_RATE . ".property_id 
                        ";

                $sumQuery = $dbAdapter->select()
                        ->from(CAL_RATE, array(CURRENCY . ".exchange_rate", CAL_RATE . ".prate", CAL_RATE . ".property_id", CAL_RATE . ".nights", new Zend_Db_Expr(" coalesce (sum(
                                                    case when " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate) 
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "'
                                                    then (abs( datediff( '" . $datefrom . "', '" . $dateto . "' ))+1) * ceil(prate*exchange_rate)  
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( '" . $datefrom . "', " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate) 
                                                    when " . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "'
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, '" . $dateto . "' ))+1) * ceil(prate*exchange_rate) 
                                                    end),ceil(min(prate)*exchange_rate)*" . (dateDiff($datefrom, $dateto) + 1) . " ) 
                                                    AS total_amount ")))
                        ->join(PROPERTY, PROPERTY . ".id = " . CAL_RATE . ".property_id", array())
                        ->joinLeft(CURRENCY, PROPERTY . ".currency_code = " . CURRENCY . ".currency_code", array())
                        ->group(CAL_RATE . ".property_id")
                ;
            }

            if ($propertyname != "")
            {
                $propertyname = strtolower($propertyname);
                $where .= " and ( lower(" . PROPERTY . ".property_title) like '%" . $propertyname . "%' 
					   or lower(" . PROPERTY . ".property_name) like '%" . $propertyname . "%' 	
					   or lower(" . COUNTRIES . ".country_name) like '%" . $propertyname . "%' 
					   or lower(" . STATE . ".state_name) like '%" . $propertyname . "%' 
					   or lower(" . CITIES . ".city_name) like '%" . $propertyname . "%' 
					   or lower(" . SUB_AREA . ".sub_area_name) like '%" . $propertyname . "%' 
					   or lower(" . LOCAL_AREA . ".local_area_name) like '%" . $propertyname . "%'
					   or " . PROPERTY . ".propertycode like '%" . $propertyname . "%'  )
			";
            }

            $limit = 10; //5 records per page

            $start = $this->getRequest()->getParam("start");

            $page = $this->getRequest()->getParam("start");
            if (empty($start))
            {
                $start = 1;
                $page = 0;
            }
            $page = $page - 1;
            $starti = ($start - 1) * 10;

            //code for converting an array to request params
            $nvp_string = "";
            $nvp_string = addQueryString('start', $page+1);

            if (!isset($mySession->gridType))
                $mySession->gridType = 1;

            if ($this->getRequest()->isPost())
            {
                switch ($_REQUEST['sorti'])
                {
                    case '1': $order = $Order = "";
                        $mySession->order = '1';
                        break;
                    case '2': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount desc";
                            $Order = "ppty.total_amount desc";
                        else:
                            $order = "order by ppty.prate desc";
                            $Order = "order by ppty.prate desc";
                        endif;

                        $mySession->order = '2';
                        break;
                    case '3':
                        $order = "order by ppty.bedrooms asc";
                        $Order = "ppty.bedrooms asc";
                        $mySession->order = '3';
                        break;
                    case '4':
                        $order = "order by ppty.bedrooms desc";
                        $Order = "ppty.bedrooms desc";
                        $mySession->order = '4';
                        break;
                }

                switch ($_REQUEST['grid'])
                {
                    case '1': $mySession->gridType = '1';
                        break;
                    case '2': $mySession->gridType = '2';
                        break;
                    case '3': $mySession->gridType = '3';
                        break;
                }
            }


            if ($datefrom != "" && $dateto != "")
            {
                $order = "order by ppty.total_amount asc";
                $Order = "ppty.total_amount asc";
            }
            else
            {
                $order = "order by ppty.prate asc";
                $Order = "ppty.prate asc";
            }



            if (isset($mySession->order))
            {
                switch ($mySession->order)
                {
                    case '1': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount asc";
                            $Order = "ppty.total_amount asc";
                        else:
                            $order = "order by prate asc";
                            $Order = "prate asc";
                        endif;

                        break;
                    case '2': if ($datefrom != "" && $dateto != ""):
                            $order = "order by ppty.total_amount desc";
                            $Order = "ppty.total_amount desc";
                        else:
                            $order = "order by prate desc";
                            $Order = "prate desc";
                        endif;

                        break;
                    case '3':
                        $order = "order by bedrooms asc";
                        $Order = "bedrooms asc";
                        break;
                    case '4':
                        $order = "order by bedrooms desc";
                        $Order = "bedrooms desc";
                        break;
                }
            }






            $no_date_query = "select * from 
						  (select property_id, date_from, date_to, nights, ceil(prate*exchange_rate) as prate  from " . CAL_RATE . " 
						  inner join " . PROPERTY . " on " . CAL_RATE . ".property_id = " . PROPERTY . ".id 	
						  inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
                                                  where " . CAL_RATE . ".date_from >= curdate()
						  order by prate asc) as abc group by abc.property_id";


            $noDateQuery = $dbAdapter->select()->from(
                            array("abc" => $dbAdapter->select()
                                ->from(CAL_RATE, array('property_id', 'date_from', 'date_to', 'nights', 'ceil(prate*exchange_rate) as prate'))
                                ->join(PROPERTY, CAL_RATE . ".property_id = " . PROPERTY . ".id ", array())
                                ->join(CURRENCY, PROPERTY . ".currency_code = " . CURRENCY . ".currency_code", array())
                                ->where(CAL_RATE . ".date_to >= curdate()")
                                ->order("prate asc")
                    ))
                    ->group("abc.property_id");
            ;



            $subProperty = $dbAdapter->select()
                    //->from(PROPERTY, array("property.id as pid", "cletitude", "clongitude", "propertycode", "(ptyle_name)", "property_title", CURRENCY . ".currency_code", "(country_name)", "(state_name)", "(city_name)", "(sub_area_name)", "(local_area_name)", "bedrooms", new Zend_Db_Expr('trim(trailing "." from trim(trailing 0 from '.PROPERTY.'.bathrooms)) as bathrooms'), "(ptyle_url)","en_bedrooms", "maximum_occupancy", "star_rating", "cal_default", "(amenity_id)", SPEC_ANS . ".answer", "cal_availability.prate as prate", "cal_availability.nights", (($sumQuery == '') ? '' : 'cal_availability.total_amount') . ""))
                    ->from(PROPERTY, array("property.id as pid", "cletitude", "clongitude", "propertycode", "(ptyle_name)", "property_title", CURRENCY . ".currency_code", "(country_name)", "(state_name)", "(city_name)", "(sub_area_name)", "(local_area_name)", "bedrooms", new Zend_Db_Expr('trim(trailing "." from trim(trailing 0 from '.PROPERTY.'.bathrooms)) as bathrooms'), "(ptyle_url)","en_bedrooms", "maximum_occupancy", "is_insured","star_rating", "cal_default", "cal_availability.prate as prate", "cal_availability.nights", (($sumQuery == '') ? '' : 'cal_availability.total_amount') . ""))
                    //->setIntegrityCheck(false)
                    ->join(array('pt' => PROPERTY_TYPE), "pt.ptyle_id = " . PROPERTY . ".property_type ", array())
                    //->joinLeft(SPEC_ANS, SPEC_ANS . ".property_id = " . PROPERTY . ".id", array())
                    //->joinLeft(AMENITY_ANS, AMENITY_ANS . ".property_id = " . PROPERTY . ".id", array())
                    ->join(COUNTRIES, COUNTRIES . ".country_id = " . PROPERTY . ".country_id", array())
                    ->join(STATE, STATE . ".state_id = " . PROPERTY . ".state_id", array())
                    ->join(CITIES, CITIES . ".city_id = " . PROPERTY . ".city_id", array())
                    ->join(CURRENCY, CURRENCY . ".currency_code = " . PROPERTY . ".currency_code", array())
                    ->joinLeft(SUB_AREA, SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id", array())
                    ->joinLeft(LOCAL_AREA, LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id", array())

            ;

            if (!empty($sumQuery))
                $subProperty = $subProperty->joinLeft(array('cal_availability' => $sumQuery), "cal_availability.property_id = " . PROPERTY . ".id", array());
            else
                $subProperty = $subProperty->joinLeft(array('cal_availability' => $noDateQuery), "cal_availability.property_id = " . PROPERTY . ".id", array());

            $subProperty = $subProperty->where(PROPERTY . ".status = '3'")
                    ->where("1 " . $where . " " . $where1)
            ;
            
             

            //prd($subProperty->__toString());

            if (!empty($sumQuery))
                $subProperty = $subProperty->group("pid");

            $subProperty = $subProperty->order("cal_availability.prate asc");


            $propertyDbAdapter = $dbAdapter->select()->from(array("ppty" => $subProperty))
                    ->group("ppty.pid");

            if ($Having)
                $propertyDbAdapter = $propertyDbAdapter->having($Having);

            $propertyDbAdapter = $propertyDbAdapter->order($Order);

            //prd($propertyDbAdapter->__toString());

            

            $adapter = new Zend_Paginator_Adapter_DbSelect($propertyDbAdapter);
            
            $paginator = new Zend_Paginator($adapter);
            
            $paginator->setItemCountPerPage(10);

            $paginator->setCurrentPageNumber($page + 1);
            //$paginator->setItemCountPerPage(10);
            $totalCount = $paginator->getTotalItemCount();
            $this->view->paginator = $paginator;

            
            $propertyArr = $propertyDbAdapter->limitPage($page + 1, 10)->query()->fetchAll();

            
            //prd($propertyArr);
//            $propertyArr = $db->runQuery("select * from  
//                                    (select " . PROPERTY . ".id as pid, cletitude, clongitude, propertycode, ptyle_name, property_title, " . CURRENCY . ".currency_code, country_name, state_name, city_name, sub_area_name, local_area_name,
//                                     bedrooms, bathrooms, en_bedrooms, maximum_occupancy, star_rating, cal_default, amenity_id, " . SPEC_ANS . ".answer,  cal_availability.prate as prate, cal_availability.nights   
//                                     " . (($sum_query == '') ? '' : ', cal_availability.total_amount  ') . " 
//                                    from " . PROPERTY . " 
//                                    inner join " . PROPERTY_TYPE . " as pt on pt.ptyle_id = " . PROPERTY . ".property_type
//                                    left join " . SPEC_ANS . " on " . SPEC_ANS . ".property_id = " . PROPERTY . ".id
//                                    left join " . AMENITY_ANS . " on " . AMENITY_ANS . ".property_id = " . PROPERTY . ".id
//                                    inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
//                                    inner join " . STATE . " on " . STATE . ".state_id = " . PROPERTY . ".state_id
//                                    inner join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
//                                    inner join " . CURRENCY . " on " . CURRENCY . ".currency_code = " . PROPERTY . ".currency_code
//                                    left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
//                                    left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id  
//                                    left join 
//                                     " . (($sum_query == '') ? "(" . $no_date_query . ")" : "(" . $sum_query . ")") . " as cal_availability
//                                     on cal_availability.property_id = " . PROPERTY . ".id
//                                     where " . PROPERTY . ".status = '3'  
//                                     $where 
//                                     $where1  " . (($sum_query == '') ? '' : 'group by  pid') . " order by cal_availability.prate asc) as ppty
//                                     where ppty.pid != '0' 
//                                     group by ppty.pid    
//                                     $having  
//                                     $order 
//                                        
//                                   ");
            //prd($propertyArr);

            /*             * ************************* FINAL QUERY ENDS ****************************************** */
            //prd($propertyArr[0]['__counti']);
            $propertyData = array();
            $t = 0;
            $x = 0;
            $k = 0;

            
            foreach ($propertyArr as $key => $val)
            {
                //CODE FOR INSERTING IMAGE
                $imgArr = $db->runQuery("select * from " . GALLERY . " where property_id = " . $val['pid'] . " ");
                //prd("select answer from ".SPEC_ANS." where property_id = ".$val['pid']." $_specWhere ");
                $_specArr = $db->runQuery("select answer from ".SPEC_ANS." where property_id = ".$val['pid']." ");
                $_amenityArr = $db->runQuery("select amenity_id from ".AMENITY_ANS." where property_id = ".$val['pid']."  ");
                $subpropertyData[$x]['image_name'] = $imgArr[0]['image_name'];
                $subpropertyData[$x]['image_title'] = $imgArr[0]['image_title'];
                $propertyData[$x]['image_name'] = $imgArr[0]['image_name'];
                $propertyData[$x]['image_title'] = $imgArr[0]['image_title'];
                $propertyData[$x]['amenity_id'] = $_amenityArr[0]['amenity_id'];
                
                foreach ($val as $keys => $values)
                {
                    if(($_specWhere && count($_specArr) > 0) || !$specWhere){
                        $propertyData[$x][$keys] = $values;
                        $subpropertyData[$x][$keys] = $values;
                    }
                }
                $x++;
            }


            //prd($subpropertyData);

            /* 		$this->view->propertyData=$propertyData; */
            
            $this->view->total = count($propertyData);
            $this->view->start = $start;
            $this->view->now = APPLICATION_URL . "search?" . $nvp_string;
            if(!empty($nvp_string))
            $this->view->queryString = "?" . $nvp_string;
            else
            $this->view->queryString = "";    
            $this->view->limit = $limit;


            //prd($subpropertyData);

            if ($mySession->gridType != '3')
            {
                $Description = str_replace(array('[PROPERTY_NO_START]', '[PROPERTY_NO_END]'), array($subpropertyData[0]['propertycode'], $subpropertyData[count($subpropertyData) - 1]['propertycode']), $Description);
                $this->view->headMeta('description', $Description);
                $this->view->propertyData = $subpropertyData;
            }
            else
            {
                $Description = str_replace(array('[PROPERTY_NO_START]', '[PROPERTY_NO_END]'), array($propertyData[0]['propertycode'], $propertyData[count($propertyData) - 1]['propertycode']), $Description);
                $this->view->headMeta('description', $Description);
                $this->view->propertyData = $propertyData;
            }
            
            $pageTitle .= $totalCount." Holiday Rentals";
            $this->view->headlineText = $pageTitle;

            $this->view->gridType = $mySession->gridType;

            //amenity arr
            $amenityArr = $db->runQuery("select * from " . AMENITY . " where amenity_status = '1' ");
            $this->view->amenityArr = $amenityArr;
			$this->renderScript('search/searchajax.phtml');
            
        }


        public function favouriteAction()
        {
            global $mySession;
            $db = new Db();
            $propertyArr = $db->runQuery("select * from " . PROPERTY . " as p
									  left join " . GALLERY . " as g on g.property_id = p.id	
									  group by p.id limit 0,5");
            $this->view->propertyData = $propertyArr;
        }

        protected function getPropertyDetails($propertyId){
            $db = new Db();
            //property details
            $propertyArr = $db->runQuery("select *, trim(trailing '.' from trim(trailing 0 from ".PROPERTY.".bathrooms)) as bathrooms," . PROPERTY . ".id as pid from " . PROPERTY . " 
                                        inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
                                        inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
                                        left join " . STATE . "  on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                        left join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
                                        left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
                                        left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
                                        left join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id 
                                        where " . PROPERTY . ".id = '" . $propertyId . "'
                                        ");
            return $propertyArr;
        }
        
        public function getspecificationsAction(){
            $db = new Db();
            $propertyId = $this->getRequest()->getParam("property_id");
            $propertyArr = $this->getPropertyDetails($propertyId);
            $this->view->propertyData = $propertyArr;
            $this->_helper->layout->disableLayout();
            //specifications
            
            $this->view->tab = '2';
            $this->view->property = "specification";
            $this->view->ppty_tab2 = 'class="active"';

            $specArr = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on s.cat_id = psc.cat_id 
                                                                  where psc.cat_status = '1' 
                                                                  and s.status = '1' order by psc.cat_id, s.spec_order asc
                                                                  ");

                    $category_temp = "";

                    $i = 0;
                    $t = 0;
                    $finalArr[0]['category'][0] = "";
                    $cat_counter = 0;
                    $bathroom_counter = 0;
                    $xcounter = 0;
                    $max = 0;

                    foreach ($specArr as $key => $value)
                    {

                        if ($finalArr[$cat_counter]['category'] != $value['cat_name'])
                        {
                            if ($i > 0)
                                $cat_counter++;

                            $finalArr[$cat_counter]['category'] = $value['cat_name'];

                            $t = 0;
                        }
                        $selectOptionArr = $db->runQuery("select * from " . SPEC_CHILD . " inner join " . SPEC_ANS . " on " 
                                . SPEC_ANS . ".answer = " . SPEC_CHILD . ".cid
                                 where " . SPEC_ANS . ".spec_id = '" . $value['spec_id'] . "' and " . SPEC_ANS . ".property_id = '" . $propertyId . "' ");

                        if ($value['spec_id'] == '22' || $value['spec_id'] == '23' || $value['spec_id'] == '24')
                        {


                            foreach ($selectOptionArr as $key1 => $value1)
                            {
                                $array1 = explode('|||', $value1['answer']);

                                $max = count($array1) > $max ? count($array1) : $max;

                                $x = 0;
                                foreach ($array1 as $keybath => $bath)
                                {
                                    /* 														echo "select ".SPEC_CHILD.".option from ".SPEC_CHILD." where cid = '".$bath."' "; exit; */
                                    $bathArr = $db->runQuery("select " . SPEC_CHILD . ".option from " . SPEC_CHILD . " where cid = '" . $bath . "' ");

                                    //pr($bathArr);
                                    /* if(count($bathArr) == 0)
                                      $bathroom[$keybath][] = "";
                                     */

                                    foreach ($bathArr as $ckey => $calue)
                                    {

                                        if ($value['spec_id'] == '24')
                                        {

                                            //echo $xcounter;
                                            $bathroom[$xcounter][] = $calue['option'];
                                        }
                                        else
                                        {

                                            $bathroom[$keybath][] = $calue['option'];
                                        }
                                    }
                                    $x++;
                                }


                                foreach ($array1 as $keybath => $bath)
                                {

                                    $finalArr[$cat_counter]['ques'][$keybath] = $value['spec_display'] . " " . ($keybath + 1);
                                }
                                $finalArr[$cat_counter]['answer'] = $bathroom;




                                $j++;


                                if ($value['spec_id'] == '24')
                                {

                                    $xcounter++;
                                }
                            }

                            $bathroom_counter++;
                        }
                        else
                        {
                            if ($max != 0)
                                $t = $t + $max;
                            $max = 0;

                            if ($value['spec_type'] == '2' || $value['spec_type'] == '3')
                            {

                                $selectOptionArr = $db->runQuery("select * from " . SPEC_ANS . "  where " . SPEC_ANS . ".spec_id = '" . $value['spec_id'] . "' and " . SPEC_ANS . ".property_id = '" . $propertyId . "' ");

                                if ($selectOptionArr[0]['answer'] != "")
                                {

                                    $j = 0;
                                    $finalArr[$cat_counter]['ques'][$t] = $value['spec_display'];
                                    $finalArr[$cat_counter]['ticklist'][$t] = '0';
                                    $finalArr[$cat_counter]['answer'][$t][0] = $selectOptionArr[0]['answer'];
                                    $selectOptionArr = array();
                                }
                                else
                                    $t--;
                            }
                            elseif ($value['preview_display'] == '1' || count($selectOptionArr) > 0)
                            {


                                $finalArr[$cat_counter]['ques'][$t] = $value['spec_display'];

                                if ($value['spec_type'] == '4')
                                    $finalArr[$cat_counter]['ticklist'][$t] = '1';
                                else
                                    $finalArr[$cat_counter]['ticklist'][$t] = '0';
                            }
                            else
                                $t--;

                            $j = 0;
                            if (count($selectOptionArr) > 0)
                                foreach ($selectOptionArr as $key1 => $value1)
                                {


                                    $finalArr[$cat_counter]['answer'][$t][$j] = $value1['option'];

                                    $j++;
                                }


                            $t++;
                        }

                        $i++;
                    }
                    $this->view->specArr = $finalArr;

            
        }
        
        public function getrentalratesAction(){
            $db = new Db();
            $this->_helper->layout->disableLayout();
            $ppty_id = $this->getRequest()->getParam('property_id');
            $this->view->property = "rental";
            $this->view->ppty_tab5 = 'class="active"';
            $option_extra = $db->runQuery("select ename, (select exchange_rate from " . CURRENCY . " where " . CURRENCY . ".currency_code = (select currency_code from " . PROPERTY . " where id = '" . $ppty_id . "' ))*eprice as eprice,etype,stay_type  from " . EXTRAS . " where property_id = '" . $ppty_id . "' ");
            $this->view->option_extra = $option_extra;
            $calArr = $db->runQuery("select ceil((select exchange_rate from " . CURRENCY . " where " . CURRENCY . ".currency_code = (select currency_code from " . PROPERTY . " where id = '" . $ppty_id . "') )*prate) as prate,
                                    nights,date_to,date_from from " . CAL_RATE . "  
                                    where property_id = " . $ppty_id . "  order by date_from asc ");
            $newCalArr = array();
            foreach ($calArr as $c_key => $c_value)
            {
                $date_from = $c_value["date_from"];
                $date_to = $c_value["date_to"];
                if (strtotime($date_to) >= time())
                    $newCalArr[] = $c_value;
            }
//                    $this->view->calData = $calArr;
            $this->view->calData = $newCalArr;
            $spclArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS from " . SPCL_OFFERS . " 
                                    inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
                                    where " . SPCL_OFFERS . ".property_id = '" . $ppty_id . "' 
                                    and " . SPCL_OFFERS . ".activate = '1'  and " . SPCL_OFFERS . ".book_by >= curdate() ");
            $this->view->spclData = $spclArr;

        }
        
        public function getreviewAction(){
        $db = new Db();
        $this->_helper->layout->disableLayout();
        $ppty_id = $this->getRequest()->getParam('property_id');
        $this->view->property = "review";
        $this->view->ppty_tab7 = 'class="active"';
        $propertyArr = $this->getPropertyDetails($ppty_id);
        /*         * * review form display code * */


        $this->view->propertyData = $propertyArr;
        //if(!isset($mySession->reviewImage))
        //$mySession->reviewImage = "no_owner_pic.jpg";
        $reviewArr = $db->runQuery("select * from " . OWNER_REVIEW . " as r
                                                inner join " . PROPERTY . " as p on p.id = r.property_id
                                                inner join " . USERS . " as u on u.user_id = p.user_id
                                                where r.property_id = '" . $ppty_id . "' and r.review_status = '1' order by r.review_id desc ");

        //prd($reviewArr);	

        $i = 0;
        foreach ($reviewArr as $val) {

            if ($val['parent_id'] == 0) {

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
                foreach ($childArr as $val1) {

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
        //code for finding that the owner has the same property

        $this->view->reviewArr = $reviewData;
    }
        
        public function searchdetailAction()
        {
            global $mySession;
            $db = new Db();
            $varsuccess = '0';
            $tab = $this->getRequest()->getParam("tab");
            $country_name = $this->getRequest()->getParam("country");
            $state_name = $this->getRequest()->getParam("state");
            $city_name = $this->getRequest()->getParam("city");
            $sub_area_name = $this->getRequest()->getParam("sub_area");
            $local_area_name = $this->getRequest()->getParam("local_area");
            $property_code = $this->getRequest()->getParam("property_code");
            $property_type = $this->getRequest()->getParam("property_type");

            $this->view->Datefrom = $datefrom = $this->getRequest()->getParam("datefrom");
            $this->view->dateto = $dateto = $this->getRequest()->getParam("dateto");
            $Breadcrumb = $location_condition = '';

            if ($local_area_name != '')
            {
                $location_condition = " and " . SUB_AREA . ".sub_area_name = '" . strtolower($sub_area_name) . "' and " . LOCAL_AREA . ".local_area_name = '" . strtolower($local_area_name) . "'  ";
            }
            elseif ($sub_area_name != '')
            {
                $location_condition = " and " . SUB_AREA . ".sub_area_name = '" . strtolower($sub_area_name) . "' ";
            }


            $propertyArr = $db->runQuery("select *, trim(trailing '.' from trim(trailing 0 from ".PROPERTY.".bathrooms)) as bathrooms," . PROPERTY . ".id as pid from " . PROPERTY . " 
									  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
									  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
									  left join " . STATE . "  on " . STATE . ".state_id = " . PROPERTY . ".state_id
                                                                          left join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
									  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
									  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
									  left join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id 
									  where lower(" . COUNTRIES . ".country_name) = '" . strtolower($country_name) . "'
									  and lower(" . STATE . ".state_name) = '" . strtolower($state_name) . "'
									  and lower(" . CITIES . ".city_name) = '" . strtolower($city_name) . "'
									  $location_condition
									  and " . PROPERTY . ".propertycode = '" . $property_code . "'
									  ");

            
            //prd($propertyArr);

            $Breadcrumb .= (!empty($propertyArr[0]['country_name'])) ? $propertyArr[0]['country_name'] : '';
            $Breadcrumb .= (!empty($propertyArr[0]['state_name'])) ? '>' . $propertyArr[0]['state_name'] : '';
            $Breadcrumb .= (!empty($propertyArr[0]['city_name'])) ? '>' . $propertyArr[0]['city_name'] : '';
            $Breadcrumb .= (!empty($propertyArr[0]['sub_area_name'])) ? '>' . $propertyArr[0]['sub_area_name'] : '';
            $Breadcrumb .= (!empty($propertyArr[0]['local_area_name'])) ? '>' . $propertyArr[0]['local_area_name'] : '';


            $breadcrumb_array = array(
                "country_name" => $propertyArr[0]['country_name'],
                "state_name" => $propertyArr[0]['state_name'],
                "city_name" => $propertyArr[0]['city_name'],
                "sub_area_name" => $propertyArr[0]['sub_area_name'],
                "local_area_name" => $propertyArr[0]['local_area_name'],
            );

            $this->view->breadCrumbArray = $breadcrumb_array;

            $this->view->headMeta($propertyArr[0]['meta_description'], 'description');
            $this->view->headMeta($propertyArr[0]['meta_keywords'], 'keywords');

            $Arr = explode('>', $Breadcrumb);

            krsort($Arr);

            $newHeadTtitleTest = implode(' - ', ($Arr));
            $Breadcrumb = implode('>', $Arr);
            //========== Fetching Meta Information ===========================//
            $metaArr = $db->runQuery("select meta_title, meta_keyword, meta_description from  " . META_INFO . " where meta_id = 4");
            $Title = $metaArr[0]['meta_title'];
            $Title = str_replace('[BREADCRUMB]', $newHeadTtitleTest, $Title);
            $Title = str_replace('[BED]', $propertyArr[0]['bedrooms'] > 1 ? $propertyArr[0]['bedrooms'] . ' beds' : $propertyArr[0]['bedrooms'] . ' bed', $Title);
            $Title = str_replace('[PROPERTY_TYPE]', $propertyArr[0]['ptyle_name'], $Title);
            $Title = str_replace('[PROPERTY_NO]', $propertyArr[0]['propertycode'], $Title);
            

            $Description = $metaArr[0]['meta_description'];
            $Description = str_replace('[PROPERTY_TITLE]', $propertyArr[0]['property_title'], $Description);
            $Description = str_replace('[PROPERTY_DESCRIPTION]', $propertyArr[0]['brief_desc'], $Description);

            $this->view->headTitle($Title)->offsetUnset(0);
            $this->view->ppty_id = $ppty_id = $propertyArr[0]['pid'];
            //custom attributes 
            $customAttributes = $db->runQuery("select * from ".ATTRIBUTE_ANS." attr_ans left join ".ATTRIBUTE." attr on attr.attribute_id = attr_ans.ans_attribute_id where attr_ans.ans_property_id = $ppty_id ");
            $this->view->customAttrib = $customAttributes;
            //AMENITIES	
            $amenityData = $db->runQuery("select * from " . AMENITY . " as a inner join " . AMENITY_ANS . " as aa on a.amenity_id = aa.amenity_id where aa.property_id = '" . $ppty_id . "' and aa.amenity_value ='1' and a.amenity_status = '1' ");
            $this->view->amenityData = $amenityData;
            if (count($propertyArr) == 0)
            {
                $this->_redirect("error/error");
            }
            $this->view->ppty_id = $ppty_id;
            
            //calendar
            $this->view->property = "availability";
            $this->view->cal_default = $propertyArr[0]['cal_default'];
            $calArr = $db->runQuery("select * from " . CAL_AVAIL . " where property_id = '" . $ppty_id . "' ");
            $this->view->calArr = $calArr;
            $next = $this->getRequest()->getParam("cal");
            if ($next != "")
                $this->view->nexts = $next;
            else
                $this->view->nexts = 0;

            //gallery
            $galleryArr = $db->runQuery("select * from " . GALLERY . "  where property_id = " . $ppty_id);
            $this->view->galleryArr = $galleryArr;
            
            if ($datefrom != ""):
                $datefrom = date('Y-m-d', strtotime($datefrom));
                $dateto = date('Y-m-d', strtotime($dateto));


                $rateArr = $db->runQuery(" select  *, 
                                            coalesce (sum(
                                                    case when " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate)
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $dateto . "'
                                                    then (abs( datediff( '" . $datefrom . "', '" . $dateto . "' ))+1) * ceil(prate*exchange_rate)
                                                    when " . CAL_RATE . ".date_from <= '" . $datefrom . "' and " . CAL_RATE . ".date_to >= '" . $datefrom . "' and " . CAL_RATE . ".date_to <= '" . $dateto . "' 
                                                    then (abs( datediff( '" . $datefrom . "', " . CAL_RATE . ".date_to ))+1) * ceil(prate*exchange_rate)
                                                    when " . CAL_RATE . ".date_to >= '" . $dateto . "' and " . CAL_RATE . ".date_from >= '" . $datefrom . "' and " . CAL_RATE . ".date_from <= '" . $dateto . "'
                                                    then (abs( datediff( " . CAL_RATE . ".date_from, '" . $dateto . "' ))+1) * ceil(prate*exchange_rate)
                                                    end),(select ceil(min(prate)*exchange_rate)*" . (dateDiff($datefrom, $dateto) + 1) . " from " . CAL_RATE . " 
                                                    where " . CAL_RATE . ".property_id = '" . $ppty_id . "'))
                                                    AS RATE from " . CAL_RATE . " 
                                                    inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id
                                                    inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
                                                    where property_id = '" . $ppty_id . "' group by " . CAL_RATE . ".property_id ");




                $this->view->nights = dateDiff($datefrom, $dateto) + 1;




            else:


                $rateArr = $db->runQuery("select nights*ceil(prate*" . CURRENCY . ".exchange_rate) as RATE,nights,prate," . PROPERTY . ".id from " . CAL_RATE . " 
								  inner join " . PROPERTY . " on " . PROPERTY . ".id = " . CAL_RATE . ".property_id
								  inner join " . CURRENCY . " on " . PROPERTY . ".currency_code = " . CURRENCY . ".currency_code
                                                                  where " . CAL_RATE . ".property_id = '" . $ppty_id . "' and prate = (select min(prate) from " . CAL_RATE . " where property_id = '" . $ppty_id . "' and " . CAL_RATE . ".date_to >= curdate()
								   ) order by prate asc
								  ");
                //echo "select prate*nights as RATE,nights,prate,id from ".CAL_RATE." where prate = (select min(prate) from ".CAL_RATE." where property_id = '".$ppty_id."'  ) ";
                $this->view->nights = $rateArr[0]['nights'] != "" ? $rateArr[0]['nights'] : "";
            endif;

            $this->view->prate = $rateArr[0]['RATE'] != "" ? $rateArr[0]['RATE'] : "Unknown";
            //cal Query
            $this->view->propertyData = $propertyArr;

            // property images
            $galleryData = $db->runQuery("select * from " . GALLERY . " where property_id = '" . $ppty_id . "' ");
            $this->view->galleryData = $galleryData;
            
            //contact us
            $myform = new Form_Ocontact($ppty_id);
            $this->view->myform = $myform;


            # the response from reCAPTCHA
            $resp = null;
            # the error code from reCAPTCHA, if any
            $error = null;


            if ($this->getRequest()->isPost() && !empty($_POST["recaptcha_response_field"]))
            {

                $request = $this->getRequest();
                $dataForm = $myform->getValues();

                $resp = recaptcha_check_answer(CAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

                if ($resp->is_valid)
                {
                    $myObj = new Users();
                    $Result = $myObj->ownercontactus($dataForm);
                    $mySession->sucessMsg = "Thank you, You will soon be contacted";
                    $varsuccess = 1;
                }
                else
                {
                    $mySession->errorMsg = "Please Enter Correct Human Verification Code";
                }
            }



            $this->view->error = $error;
            $this->view->varsuccess = $varsuccess;
        }

        /*         * ********* save review Action ********** */
        /*         * *************************************** */

        public function savereviewAction()
        {
            global $mySession;
            $db = new Db();

            $pptyId = $this->getRequest()->getParam("ppty");


            //get user details
            $userArr = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");

            $data_update = array();
            $data_update['guest_name'] = $userArr[0]['first_name'] . " " . $userArr[0]['last_name'];
            $data_update['location'] = $userArr[0]['address'];
            $data_update['check_in'] = date('Y-m-d', strtotime($_REQUEST['Check_in']));
            $data_update['rating'] = $_REQUEST['Rating'];
            $data_update['headline'] = $_REQUEST['Headline'];
            $data_update['comment'] = $_REQUEST['Comment'];
            $data_update['review'] = $_REQUEST['Review'];
            if ($mySession->LoggedUserType == '2')
                $data_update['uType'] = '0';
            else
                $data_update['uType'] = '1';
            $data_update['review_date'] = date("Y-m-d");


            $data_update["property_id"] = $pptyId;
            $data_update['guest_image'] = $userArr[0]['image'];

            copy(SITE_ROOT . "images/" . $userArr[0]['image'], SITE_ROOT . "images/profile/" . $userArr[0]['image']);



            $db->save(OWNER_REVIEW, $data_update);

            $mySession->sucessMsg = "Your review has been submitted for approval by the admin";
            //$mySession->step = '8';		
            //code for changing the status of step8


            exit;
        }

        public function getpptydetailsAction()
        {
            global $mySession;
            $db = new Db();
            $this->_helper->layout()->disableLayout();
            $ppty = $this->getRequest()->getParam("ppty");

            $fetchArr = $db->runQuery("select * from " . PROPERTY . " inner join " . GALLERY . " on " . GALLERY . ".property_id = " . PROPERTY . ".id   where " . PROPERTY . ".id = '" . $ppty . "' group by id");

            if ($fetchArr[0]['image_name'])
                $image = $fetchArr[0]['image_name'];
            else
                $image = "generic.jpg";

            $display = '<div class="hotel-image-holder">
        				 <img title="View hotel details" alt="Hotel photo" src="' . APPLICATION_URL . 'image.php?image=images/property/' . $image . '&height=100&width=150" class="hotel-image-gallery-view trp-round-3 image-shadow viewDetailsLink">
					</div>
			<h3 class="hotel-name ellipses-overflow">
            	<a title="View hotel details" id="hotelName-126513" class="viewDetailsLink">' . substr($fetchArr[0]['property_title'], 0, 30) . '</a>
            </h3>
		  <span class="map_ppty_no" >Property No: <span class="green">' . $fetchArr[0]['propertycode'] . '</span></span>
	        <h5 class="hotel-location">' . $fetchArr[0]['bedrooms'] . ' bedrooms /' . ceil($fetchArr[0]['bathrooms']) . ' bathrooms <br />
										Sleeps up to ' . $fetchArr[0]['maximum_occupancy'] . '
			</h5>
				<div class="edit_btns">
					<div class="btns" ><!-- first div -->
						<a href="' . APPLICATION_URL . 'search/searchdetail/ppty/' . $ppty . '">View Detail</a>
					</div>
				</div>
        	</div>';

            echo $display;
            exit;
        }

        public function savefavAction()
        {
            global $mySession;
            $db = new Db();

            $fetchArr = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");
            $ppty = $this->getRequest()->getParam("ppty");
            $del = $this->getRequest()->getParam("del");



            if ($fetchArr[0]['fav_ppty'])
                $tmp = explode(",", $fetchArr[0]['fav_ppty']);

            if ($del == '0' && count($tmp) <= 12)
            {
                if (count($tmp) > 0 && $tmp[0] != "")
                    $data_update['fav_ppty'] = $fetchArr[0]['fav_ppty'] . $ppty . ",";
                else
                    $data_update['fav_ppty'] = $ppty . ",";
            }
            elseif (count($tmp) > 12)
                exit("fail");

            if ($del == '1')
            {
                $data_update['fav_ppty'] = str_replace($ppty . ",", "", $fetchArr[0]['fav_ppty']);
                $data_update['fav_ppty'] = str_replace($ppty . ",", "", $fetchArr[0]['fav_ppty']);
            }

            $condition = "user_id=" . $mySession->LoggedUserId;
            $db->modify(USERS, $data_update, $condition);

            exit;
        }

        public function loadfavAction()
        {
            global $mySession;
            $db = new Db();
            $sno = $this->getRequest()->getParam("sno");

            $fetchArr = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");

            $favArr = explode(",", $fetchArr[0]['fav_ppty']);



            $i = 0;

            if (count($favArr) == 0 || $favArr[0] == "")
                exit(0);
            else
                foreach ($favArr as $values)
                {
                    if ($sno == $i && $values != "")
                    {
                        $imgArr = $db->runQuery("select * from " . PROPERTY . " inner join " . GALLERY . " on " . PROPERTY . ".id = " . GALLERY . ".property_id where " . PROPERTY . ".id = '" . $values . "' limit 0,1");

                        if (count($imgArr) > 0)
                            exit("<a href='" . APPLICATION_URL . "search/searchdetail/ppty/" . $values . "'><img src='" . IMAGES_URL . "property/" . $imgArr[0]['image_name'] . "' width='40' height = '40'></a>");
                        else
                            exit("<a href='" . APPLICATION_URL . "search/searchdetail/ppty/" . $values . "'><img src='" . IMAGES_URL . "property/generic.jpg' width='40' height = '40'></a>");
                    }
                    $i++;
                }

            exit;
        }

        public function propertyAction()
        {
			
            global $mySession;
            $db = new Db();

            if ($this->getRequest()->isPost())
            {
                throw new Exception("mergeQueryString only works on GET requests.");
            }
            $q = $this->getRequest()->getQuery();
            $p = $this->getRequest()->getParams();


            $allowed['country'] = $q['country_id'];
            $allowed['state'] = $q['state_id'];
            $allowed['city'] = $q['city_id'];
            $allowed['sub_area'] = $q['sub_area_id'];
            $allowed['local_area'] = $q['local_area_id'];






            $allowed = array_filter($allowed, 'strlen');


            //get values in the form of name



            switch (count($allowed))
            {

                case 0: break;
                case 1: $lArr = $db->runQuery("select country_name from " . COUNTRIES . " where country_id = '" . $allowed['country'] . "' ");
                    $this->view->country_id = $country_id = $lArr[0]['country_id'];
                    break;

                case 2: $lArr = $db->runQuery("select " . COUNTRIES . ".country_name, state_name from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											where " . COUNTRIES . ".country_id = '" . $allowed['country'] . "'
											and " . STATE . ".state_id = '" . $allowed['state'] . "' ");
                    break;

                case 3: $lArr = $db->runQuery("select " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											where " . COUNTRIES . ".country_id = '" . $allowed['country'] . "'
											and " . STATE . ".state_id = '" . $allowed['state'] . "' 
											and city_id = '" . $allowed['city'] . "'   ");

                    break;

                case 4: $lArr = $db->runQuery("select " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name,  " . SUB_AREA . ".sub_area_name from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											where " . COUNTRIES . ".country_id = '" . $allowed['country'] . "'
											and " . STATE . ".state_id = '" . $allowed['state'] . "' 
											and " . CITIES . ".city_id = '" . $allowed['city'] . "'  
											and sub_area_id  = '" . $allowed['sub_area'] . "' ");
                    break;


                case 5: $lArr = $db->runQuery("select " . COUNTRIES . ".country_name, " . STATE . ".state_name, " . CITIES . ".city_name, " . SUB_AREA . ".sub_area_name , " . LOCAL_AREA . ".local_area_name from " . COUNTRIES . " 
											inner join " . STATE . " on " . STATE . ".country_id = " . COUNTRIES . ".country_id
											inner join " . CITIES . " on " . CITIES . ".state_id = " . STATE . ".state_id
											inner join " . SUB_AREA . " on " . CITIES . ".city_id = " . SUB_AREA . ".city_id
											inner join " . LOCAL_AREA . " on " . SUB_AREA . ".sub_area_id = " . LOCAL_AREA . ".sub_area_id											
											where " . COUNTRIES . ".country_id = '" . $allowed['country'] . "'
											and " . STATE . ".state_id = '" . $allowed['state'] . "' 
											and " . CITIES . ".city_id = '" . $allowed['city'] . "'  
											and " . SUB_AREA . ".sub_area_id  = '" . $allowed['sub_area'] . "' 
											and local_area_id  = '" . $allowed['local_area'] . "' 
											");

                    break;
            }

            $action = $p['action'];
            $controller = $p['controller'];
            $module = $p['module'];

            unset($p['action'], $p['controller'], $p['module']);




            $params = array_merge($p, $q);
//		prd($params);
            //preparing the url
            $slug = "search/property/";
            $slug .= $lArr[0]['country_name'] ? $lArr[0]['country_name'] . "/" . ($lArr[0]['state_name'] != '' ? $lArr[0]['state_name'] . "/" . ($lArr[0]['city_name'] ? $lArr[0]['city_name'] . "/" . ($lArr[0]['sub_area_name'] ? ($lArr[0]['sub_area_name'] . "/" . ($lArr[0]['local_area_name'] ? $lArr[0]['local_area_name'] : '')) : '') : '') : '') : '';


            if ($slug === 'search/property/')
                $slug = "search/index";

            $filterArr = array('country_id', 'state_id', 'city_id', 'sub_area_id', 'local_area_id', 'x', 'y');

            foreach ($q as $keys => $val)
            {
                if (in_array($keys, $filterArr))
                    unset($q[$keys]);
            }

            $q = http_build_query($q);

            $slug .= '?' . $q;



            $this->_redirect($slug);
        }
	public function specialoffers($ppty_id){
	$db = new Db();
	
	$spclArr = $db->runQuery("select *, " . SPCL_OFFER_TYPES . ".min_nights as MIN_NIGHTS from " . SPCL_OFFERS . " 
															  inner join " . SPCL_OFFER_TYPES . " on " . SPCL_OFFERS . ".offer_id = " . SPCL_OFFER_TYPES . ".id
															  where " . SPCL_OFFERS . ".property_id = '" . $ppty_id . "' 
															  and " . SPCL_OFFERS . ".activate = '1'  and " . SPCL_OFFERS . ".book_by >= curdate() ");
			return $spclArr;														
	}	
		
	 public function getspecialofferAction()
	 {
	 global $mySession;
            
	 $this->_helper->layout()->disableLayout();
     //$this->_helper->viewRenderer->setNoRender(true);
	  if ($this->getRequest()->isPost())
            {
                 $q = $this->getRequest()->getPost();
				 $ppty_id = $q['pid'];
				
				$spclArr = array();
				 
               $spclArr = $this->specialoffers($ppty_id);
			   
			   $this->view->spclData = $spclArr;
			   
                    
					
            }
           
		
	 }	
		

    }

?>
