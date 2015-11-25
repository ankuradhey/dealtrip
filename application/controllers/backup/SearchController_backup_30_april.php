<?php
__autoloadDB('Db');
class SearchController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$db=new Db();
		
	//prd($this->getRequest());
		$specArr = $db->runQuery("select * from ".SPEC_CHILD."  ");

		$this->view->specArr = $specArr;
		//$this->view->pageTitle="Customer Sign In";
		$this->view->country_id = $country_id = $this->getRequest()->getParam("country_id");
		$this->view->state_id = $state_id = $this->getRequest()->getParam("state_id");
		$this->view->city_id = $city_id = $this->getRequest()->getParam("city_id");
		$this->view->sub_area_id = $sub_area_id = $this->getRequest()->getParam("sub_area_id");
		$this->view->local_area_id = $local_area_id = $this->getRequest()->getParam("local_area_id");
		$laterDate = $this->getRequest()->getParam("laterDate");
		$this->view->laterDate = $laterDate;
		
		
		$myform=new Form_Search();
		$this->view->myform=$myform;
		$this->view->Datefrom= $datefrom = $this->getRequest()->getParam("Datefrom");
		
		$dateto = $this->getRequest()->getParam("DateTo");
		$this->view->Dateto = $dateto;		
		
		if($dateto != "")
		{
			$dateto = date('Y-m-d',strtotime($datefrom.'+'.($dateto-1).' days'));
			$this->view->dateto =$dateto;	
		}
		
		
		
		$bedroom = $this->getRequest()->getParam("bedroom");
		$bathroom=$this->getRequest()->getParam("bathroom");
		$amenity=$this->getRequest()->getParam("amenityRow");
		$swimming=$this->getRequest()->getParam("swimming");
		$facilities=$this->getRequest()->getParam("facilities");
		$themes=$this->getRequest()->getParam("themes");
		$propertyname=$this->getRequest()->getParam("propertyname");
		$this->view->bedroom=$bedroom;
		$this->view->bathroom=$bathroom;
		$this->view->amenity=$amenity;
		$this->view->swimming=$swimming;
		$this->view->facilities=$facilities;
		$this->view->themes=$themes;
		$this->view->propertyname=$propertyname;
		
		//prd($dateto);
		if($datefrom != "")		
		$datefrom = date('Y-m-d',strtotime($datefrom));
		
		$check_spec = 0; // variable used for finding out that the facilities has been selected
		
		
		//$where .= " and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."'  ";
		
		if($country_id != "" )
		{
			$where .= " and ".PROPERTY.".country_id = '".$country_id."' ";
		}
		
		if($state_id != "")
		{	
			$where .= " and ".PROPERTY.".state_id = '".$state_id."' ";
		}
		
		if($city_id != "")
		{	
			$where .= " and ".PROPERTY.".city_id = '".$city_id."' ";
		}
		
		if($sub_area_id != "")
		{
			$where .= " and ".PROPERTY.".sub_area_id = '".$sub_area_id."' ";
		}
		
		if($local_area_id != "")
		{
			$where .= " and ".PROPERTY.".local_area_id = '".$local_area_id."' ";
		}
		
		
		$availablePptyArr = $db->runQuery(" select group_concat(".CAL_AVAIL.".property_id) as ppty_list from ".CAL_AVAIL." where
						(
							( date_from <= '".date('Y-m-d',strtotime($datefrom))."' and date_to >= '".date('Y-m-d',strtotime($datefrom))."')
							or
							( date_from <= '".date('Y-m-d',strtotime($dateto))."' and date_to >= '".date('Y-m-d',strtotime($dateto))."')											
						)
						and
						cal_status = '0' ");




		$ppty_list = $availablePptyArr[0]['ppty_list'];

		
		if($datefrom != "")
		{
		/*	$where .= " and (";*/
			
			/*$where1 = " and  ".PROPERTY.".id not in ( 
					  
					  	select ".CAL_AVAIL.".property_id from ".CAL_AVAIL." where
						(
							( date_from <= '".date('Y-m-d',strtotime($datefrom))."' and date_to >= '".date('Y-m-d',strtotime($datefrom))."')
							or
							( date_from <= '".date('Y-m-d',strtotime($dateto))."' and date_to >= '".date('Y-m-d',strtotime($dateto))."')											
						)
						and
						cal_status = '0'
						
						)";
					  		*/
							
			if($ppty_list != "")				
			$where1 = "and  ".PROPERTY.".id not in (".$ppty_list.") "; 
			else
			$where1 = "";
									
		}
	
					
		

		if(count($bedroom)>0 && $bedroom[0] != "")
		{
			$where .= " and (";
			for($i=0;$i<count($bedroom);$i++)
			{
				if($i == 0)
				{
						if($bedroom[$i] == '5')
						$where .= " ".PROPERTY.".bedrooms >= '".$bedroom[$i]."'  ";	
						else
						$where .= " ".PROPERTY.".bedrooms = '".$bedroom[$i]."'  ";	
				}
				else
				{
					if($bedroom[$i] == '5')
					$where .= " or ".PROPERTY.".bedrooms >= '".$bedroom[$i]."'  ";	
					else
					$where .= " or ".PROPERTY.".bedrooms = '".$bedroom[$i]."'  ";	
				}
			}
			$where .= ")";
		}
		
		if(count($bathroom)>0 && $bathroom[0] != "")
		{
			$where .= " and (";			
			for($i=0;$i<count($bathroom);$i++)
			{
				if($i == 0)	
				{
					if($bathroom[$i] == '4')
					$where .= " ".PROPERTY.".bathrooms >= '".$bathroom[$i]."'  ";		
					else
					$where .= " ".PROPERTY.".bathrooms = '".$bathroom[$i]."'  ";		
					
				}
				else
				{
					if($bathroom[$i] == '4')
					$where .= " or ".PROPERTY.".bathrooms >= '".$bathroom[$i]."'  ";
					else
					$where .= " or ".PROPERTY.".bathrooms = '".$bathroom[$i]."'  ";	
				}
			}
			$where .= ")";			
			
		}
		if(count($amenity)>0)
		{
			for($i=0;$i<count($amenity);$i++)
			{
				//$where .= " and ".SPEC_ANS.".answer = '".$amenity[$i]."'  ";
				$specSearch[] = $amenity[$i];
			}
			
			$check_spec = 1;
			
		}
		if(count($swimming)>0)
		{
			for($i=0;$i<count($swimming);$i++)
			{
				//$where .= " and ".SPEC_ANS.".answer = '".$swimming[$i]."'  ";	
				$specSearch[] = $swimming[$i];	
			}
			$check_spec = 1;			
		}
		
		if(count($themes)>0)
		{
			for($i=0;$i<count($themes);$i++)
			{
				//$where .= " and ".SPEC_ANS.".answer = '".$themes[$i]."'  ";		
				$specSearch[] = $themes[$i];				
			}
			$check_spec = 1;			
		}
		

		$having = "";

		if($check_spec):
			$specArr = implode(",",$specSearch);
			$where .= "   and     ".SPEC_ANS.".answer in  (".$specArr.") ";
			
			$having = " having count(distinct ppty.answer) = ".count($specSearch)." "; 
		endif;

		if(count($facilities)>0)
		{
			for($i=0;$i<count($facilities);$i++)
			{
				//$where .= " and ".AMENITY_ANS.".amenity_id in (".$facilities[$i]."' and ".AMENITY_ANS.".amenity_value = '1' ";		
				$amenitySearch[] = $facilities[$i];
				
			}
			$check_amenity = 1;			
		}
				
		if($check_amenity):
			$specArr = implode(",",$amenitySearch);
			$where .= " and  ".AMENITY_ANS.".amenity_id in  (".$specArr.")  and ".AMENITY_ANS.".amenity_value = '1' "; 
			
			if($check_spec)
			$having .= " and count(distinct ppty.amenity_id ) = ".count($amenitySearch)." "; 
			else
			$having .= " having count(distinct ppty.amenity_id ) = ".count($amenitySearch)." "; 
		endif;
				
		
		$sum_query = "";
		
		//code for collecting exchange rate
		
		/*$exchng_rate = $db->runQuery("select exchange_rate from ".CURRENCY."
									  inner join ".PROPERTY." on ".PROPERTY.".currency_code =  ".CURRENCY.".currency_code");
		$exchng_rate = $exchng_rate[0]['exchange_rate'];*/
		
		if($datefrom != "" && $dateto != "")
		{	
		
				/*$where .= "and ( ";
				$where .= "case when ".CAL_RATE.".date_from <= '".date('Y-m-d',strtotime($datefrom))."'  and ".CAL_RATE.".date_to  >=  '".date('Y-m-d',strtotime($dateto))."' then ";
				$where .= CAL_RATE.".date_from <= '".date('Y-m-d',strtotime($datefrom))."' ";					
				$where .= " and ".CAL_RATE.".date_to  >=  '".date('Y-m-d',strtotime($dateto))."' ";
				$where .= " ) ";
				*/
				
				
				/*$where .= " and ( ";
				$where .= " ( ".CAL_RATE.".date_from between '".$datefrom."' and '".$dateto."'  ) ";
				$where .= " or ( ".CAL_RATE.".date_to between  '".$datefrom."' and '".$dateto."'  )";
				$where .= ")";*/
				
				

				
		/********************* latest query ***********************/	
	/*$sum_query = " select ".CAL_RATE.".*, ".CURRENCY.".exchange_rate ,
									case when '".$datefrom."' between ".CAL_RATE.".date_from and ".CAL_RATE.".date_to
									or
									'".$dateto."' between ".CAL_RATE.".date_from and ".CAL_RATE.".date_to
									then
									coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate) 
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * round(prate*exchange_rate)  
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate) 
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * round(prate*exchange_rate) 
											 end),0) 
									else
									round(min(prate)*exchange_rate)*".(dateDiff($datefrom,$dateto)+1)." end			   
									 AS total_amount from ".CAL_RATE." 
									 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id  
									 left join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code  
									 group by ".CAL_RATE.".property_id 
								";*/
			
/******************* latest query ends*************************/
				$sum_query = " select ".CAL_RATE.".*, ".CURRENCY.".exchange_rate ,
									
									coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate) 
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * round(prate*exchange_rate)  
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate) 
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * round(prate*exchange_rate) 
											 end),round(min(prate)*exchange_rate)*".(dateDiff($datefrom,$dateto)+1)." ) 
									
								 AS total_amount from ".CAL_RATE." 
									 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id  
									 left join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code  
									 group by ".CAL_RATE.".property_id 
								";
		}
		
		/*else
		{
			if($check_spec == 1)
			{	
				$where .= "and ( ";
				$where .= CAL_RATE.".date_from >= '".date('Y-m-d')."' ";
			}
			else
			$where .= "or ".CAL_RATE.".date_from >= '".date('Y-m-d')."' ";
		}*/
		
	/*	if($dateto != "" && $datefrom != "")
		{

			
			
		}
		*/
		
		
		if($propertyname != "")
		{
			$propertyname = strtolower($propertyname);
			$where .= " and ( lower(".PROPERTY.".property_title) like '%".$propertyname."%' 
					   or lower(".PROPERTY.".property_name) like '%".$propertyname."%' 	
					   or lower(".COUNTRIES.".country_name) like '%".$propertyname."%' 
					   or lower(".STATE.".state_name) like '%".$propertyname."%' 
					   or lower(".CITIES.".city_name) like '%".$propertyname."%' 
					   or lower(".SUB_AREA.".sub_area_name) like '%".$propertyname."%' 
					   or lower(".LOCAL_AREA.".local_area_name) like '%".$propertyname."%'
					   or ".PROPERTY.".propertycode like '%".$propertyname."%'  )
			";			
		}
		

									
		$limit = 10; //5 records per page
		
		$start = $this->getRequest()->getParam("start");
		if($start == "")
		{
			$start = 1;
		}
		
		$starti = ($start-1)*10;
		
		
		//code for converting an array to request params
			
		$nvp_string = "";
				
		foreach($_REQUEST as $key=>$val)
		{
			if($key != "start")
			{	
				if($nvp_string == "")
				$nvp_string .= $key.'='.urlencode($val);
				else	
				$nvp_string .= '&'.$key.'='.urlencode($val);	
			}
		}
										
					

		
		//code for converting an array to request param ends
		
		
		
		

		
		
		if(!isset($mySession->gridType))
		$mySession->gridType = 1;		
				
		if($this->getRequest()->isPost())
		{
	
			
			switch($_REQUEST['sorti'])
			{
				case '1':	$order = "";
							$mySession->order = '1';
							break;
				case '2':	if($datefrom != "" && $dateto != ""):
							$order = "order by ppty.total_amount desc";
							else:
							$order = "order by ppty.prate desc";
							endif;
							
							$mySession->order = '2';
							break;
				case '3':	$order = "order by ppty.bedrooms asc";
							$mySession->order = '3';
							break;
				case '4':	$order = "order by ppty.bedrooms desc";
							$mySession->order = '4';
							break;
			}
			
			switch($_REQUEST['grid'])
			{
				case '1': $mySession->gridType = '1';
						  break;
				case '2': $mySession->gridType = '2';
						  break;
				case '3': $mySession->gridType  = '3';
						  break;						
				
			}
			
		}
		
		
		if($datefrom != "" && $dateto != "")
		$order = "order by ppty.total_amount asc";
		else
		$order = "order by ppty.prate asc";
		
		
		
		
		if(isset($mySession->order))
		{
			switch($mySession->order)
			{
				case '1':	if($datefrom != "" && $dateto != ""):
							$order = "order by ppty.total_amount asc";
							else:
							$order = "order by prate asc";	
							endif;
							
							break;
				case '2':	if($datefrom != "" && $dateto != ""):
							$order = "order by ppty.total_amount desc";
							else:
							$order = "order by prate desc";
							endif;
							
							break;
				case '3':	$order = "order by bedrooms asc";
							break;
				case '4':	$order = "order by bedrooms desc";
							break;
			}
		}
		
			/*	$totalRecords = $db->runQuery("select *, ".PROPERTY.".id as pid from ".PROPERTY." 
										  inner join ".PROPERTY_TYPE." as pt on pt.ptyle_id = ".PROPERTY.".property_type
										  left join ".SPEC_ANS." on ".SPEC_ANS.".property_id = ".PROPERTY.".id
										  inner join ".COUNTRIES."  on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
										  left join ".STATE." on ".STATE.".country_id = ".COUNTRIES.".country_id
										  left join ".CITIES." on ".CITIES.".state_id = ".STATE.".state_id
										  left join ".SUB_AREA." on ".SUB_AREA.".city_id = ".CITIES.".city_id
										  left join ".LOCAL_AREA." on ".LOCAL_AREA.".sub_area_id = ".SUB_AREA.".sub_area_id
										  left join ".CAL_AVAIL." on ".CAL_AVAIL.".property_id = ".PROPERTY.".id
		     							  left join ".CAL_RATE." on ".CAL_RATE.".property_id = ".PROPERTY.".id
										  left join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id
										  where ".PROPERTY.".status = '3' $where 
										  group by ".PROPERTY.".id 	
									");
*/



/*
		echo " 	select ".CAL_AVAIL.".property_id from ".CAL_AVAIL." where
						(
							( date_from <= '".date('Y-m-d',strtotime($datefrom))."' and date_to >= '".date('Y-m-d',strtotime($datefrom))."')
							or
							( date_from <= '".date('Y-m-d',strtotime($dateto))."' and date_to >= '".date('Y-m-d',strtotime($dateto))."')											
						)
						and
						cal_status = '0'
						
						";
						exit;
*/
	
		
		
		$no_date_query = "select * from 
						  (select property_id, date_from, date_to, nights, round(prate*exchange_rate) as prate  from ".CAL_RATE." 
						  inner join ".PROPERTY." on ".CAL_RATE.".property_id = ".PROPERTY.".id 	
						  inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
						  order by prate asc) as abc group by abc.property_id";
						  

		
		/*prd("select * from  
										 (select ".PROPERTY.".id as pid, propertycode, ptyle_name, property_title, currency_code, country_name, state_name, city_name, sub_area_name, local_area_name, 
										  bedrooms, bathrooms, en_bedrooms, maximum_occupancy, star_rating, cal_default, amenity_id, ".SPEC_ANS.".answer,  cal_availability.prate, cal_availability.nights   
										  ".(($sum_query=='')?'':',cal_availability.total_amount')."
										  from ".PROPERTY." 
										  inner join ".PROPERTY_TYPE." as pt on pt.ptyle_id = ".PROPERTY.".property_type
										  left join ".SPEC_ANS." on ".SPEC_ANS.".property_id = ".PROPERTY.".id
										  left join ".AMENITY_ANS." on ".AMENITY_ANS.".property_id = ".PROPERTY.".id
										  inner join ".COUNTRIES."  on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
										  inner join ".STATE." on ".STATE.".country_id = ".COUNTRIES.".country_id
										  inner join ".CITIES." on ".CITIES.".state_id = ".STATE.".state_id
										  left join ".SUB_AREA." on ".SUB_AREA.".city_id = ".CITIES.".city_id
										  left join ".LOCAL_AREA." on ".LOCAL_AREA.".sub_area_id = ".SUB_AREA.".sub_area_id
										  left join 
										  ".(($sum_query=='')?"(".$no_date_query.")":"(".$sum_query .")")." as cal_availability
										  on cal_availability.property_id = ".PROPERTY.".id
										  where ".PROPERTY.".status = '3'  $where 
										  $where1  ".(($sum_query=='')?'':'group by  pid')." order by cal_availability.prate asc) as ppty
										  where ppty.pid != '0' 
										  group by ppty.pid    
										  $having  
										  $order 
										");
										*/
										
										
					   
                           					
		
		/********************* LATEST LAST QUERy **********************/
		/*prd("select * from  
										 (select ".PROPERTY.".id as pid, cletitude, clongitude, propertycode, ptyle_name, property_title, ".CURRENCY.".currency_code, country_name, state_name, city_name, sub_area_name, local_area_name, 
										  bedrooms, bathrooms, en_bedrooms, maximum_occupancy, star_rating, cal_default, amenity_id, ".SPEC_ANS.".answer,  cal_availability.prate as prate, cal_availability.nights   
										  ".(($sum_query=='')?'':', cal_availability.total_amount  ')." 
										 from ".PROPERTY." 
										 inner join ".PROPERTY_TYPE." as pt on pt.ptyle_id = ".PROPERTY.".property_type
                                         left join ".SPEC_ANS." on ".SPEC_ANS.".property_id = ".PROPERTY.".id										
										 left join ".AMENITY_ANS." on ".AMENITY_ANS.".property_id = ".PROPERTY.".id
										 inner join ".COUNTRIES." on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
										 inner join ".STATE." on ".STATE.".state_id = ".PROPERTY.".state_id
										 inner join ".CITIES." on ".CITIES.".city_id = ".PROPERTY.".city_id
 										 inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
										 left join ".SUB_AREA." on ".SUB_AREA.".sub_area_id = ".PROPERTY.".sub_area_id
										 left join ".LOCAL_AREA." on ".LOCAL_AREA.".local_area_id = ".PROPERTY.".local_area_id  
										 left join 
										  ".(($sum_query=='')?"(".$no_date_query.")":"(".$sum_query .")")." as cal_availability
										  on cal_availability.property_id = ".PROPERTY.".id
										  where ".PROPERTY.".status = '3'  
										  $where 
										  $where1  ".(($sum_query=='')?'':'group by  pid')." order by cal_availability.prate asc) as ppty
										  where ppty.pid != '0' 
										  group by ppty.pid    
										  $having  
										  $order 
										");*/

		
		$propertyArr = $db->runQuery("select * from  
										 (select ".PROPERTY.".id as pid, cletitude, clongitude, propertycode, ptyle_name, property_title, ".CURRENCY.".currency_code, country_name, state_name, city_name, sub_area_name, local_area_name, 
										  bedrooms, bathrooms, en_bedrooms, maximum_occupancy, star_rating, cal_default, amenity_id, ".SPEC_ANS.".answer,  cal_availability.prate as prate, cal_availability.nights   
										  ".(($sum_query=='')?'':', cal_availability.total_amount  ')." 
										 from ".PROPERTY." 
										 inner join ".PROPERTY_TYPE." as pt on pt.ptyle_id = ".PROPERTY.".property_type
                                         left join ".SPEC_ANS." on ".SPEC_ANS.".property_id = ".PROPERTY.".id										
										 left join ".AMENITY_ANS." on ".AMENITY_ANS.".property_id = ".PROPERTY.".id
										 inner join ".COUNTRIES." on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
										 inner join ".STATE." on ".STATE.".state_id = ".PROPERTY.".state_id
										 inner join ".CITIES." on ".CITIES.".city_id = ".PROPERTY.".city_id
 										 inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
										 left join ".SUB_AREA." on ".SUB_AREA.".sub_area_id = ".PROPERTY.".sub_area_id
										 left join ".LOCAL_AREA." on ".LOCAL_AREA.".local_area_id = ".PROPERTY.".local_area_id  
										 left join 
										  ".(($sum_query=='')?"(".$no_date_query.")":"(".$sum_query .")")." as cal_availability
										  on cal_availability.property_id = ".PROPERTY.".id
										  where ".PROPERTY.".status = '3'  
										  $where 
										  $where1  ".(($sum_query=='')?'':'group by  pid')." order by cal_availability.prate asc) as ppty
										  where ppty.pid != '0' 
										  group by ppty.pid    
										  $having  
										  $order 
										");


	
	
	
	/*************************** FINAL QUERY ENDS *******************************************/
										
				

		//prd($propertyArr);
		
		
		$propertyData = array();					
		$t=0;			
		$x = 0;
		$k = 0;
		foreach($propertyArr as $key=>$val)
		{	

				
			//CODE FOR INSERTING IMAGE
			$imgArr = $db->runQuery("select * from ".GALLERY." where property_id = ".$val['pid']." ");
			if($x >= $starti && $x < $limit+$starti)
			{
				$subpropertyData[$x]['image_name'] = $imgArr[0]['image_name'];	
			}
				$propertyData[$x]['image_name'] =  $imgArr[0]['image_name'];
						
			foreach($val as $keys=>$values)
			{
				
				$propertyData[$x][$keys] = $values;
				
				if($x >= $starti && $x < $limit+$starti)
				{
					
					$subpropertyData[$x][$keys] = $values;	
					
				}
			
			}
			$x++;

		}
		

		//prd($subpropertyData);

/*		$this->view->propertyData=$propertyData;*/
		
		$this->view->total = count($propertyData);
		$this->view->start = $start;
		$this->view->now = APPLICATION_URL."search?".$nvp_string;
		$this->view->limit = $limit;
		
		
		//prd($subpropertyData);
		
		if($mySession->gridType != '3')
		$this->view->propertyData = $subpropertyData;
		else
		$this->view->propertyData = $propertyData;
	
		

		$this->view->gridType = $mySession->gridType;
		
		//user Fav query
		/*$favArr = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' ");
		$favArr = explode(",",$favArr[0]['fav_ppty']);
		
		if(count($favArr)>0)
		$this->view->favArr = $favArr;
		else
		$this->view->favArr = "";*/
	

		
		//amenity arr
		$amenityArr = $db->runQuery("select * from ".AMENITY." where amenity_status = '1' ");
		$this->view->amenityArr = $amenityArr;

		//prd($propertyData);
	

	}
	
	public function favouriteAction()
	{
		global $mySession;
		$db = new Db();
		$propertyArr = $db->runQuery("select * from ".PROPERTY." as p
									  left join ".GALLERY." as g on g.property_id = p.id	
									  group by p.id limit 0,5");
		$this->view->propertyData = $propertyArr;	
	}
	
	public function searchdetailAction()
	{
		global $mySession;
		$db = new Db();	
			
		$varsuccess = '0';
	 	$tab = $this->getRequest()->getParam("property");
		$ppty_id = $this->getRequest()->getParam("ppty");
		$this->view->ppty_id = $ppty_id;
		
		$this->view->Datefrom = $datefrom = $this->getRequest()->getParam("datefrom");
		$this->view->dateto = $dateto = $this->getRequest()->getParam("dateto");

		$propertyArr = $db->runQuery("select * from ".PROPERTY." 
									  inner join ".COUNTRIES." on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
									  inner join ".PROPERTY_TYPE." on ".PROPERTY.".property_type = ".PROPERTY_TYPE.".ptyle_id
									  left join ".STATE."  on ".STATE.".country_id = ".COUNTRIES.".country_id
         							  left join ".CITIES." on ".CITIES.".state_id = ".STATE.".state_id
									  left join ".SUB_AREA." on ".SUB_AREA.".city_id = ".CITIES.".city_id
									  left join ".LOCAL_AREA." on ".LOCAL_AREA.".sub_area_id = ".SUB_AREA.".sub_area_id
									  left join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id 
									  where ".PROPERTY.".id = '".$ppty_id."'");	
									  
	
		switch($tab)
		{
			case '': 			
					 			
			case 'overview':		$this->view->tab = '1';
									$this->view->property = "overview";
									$this->view->ppty_tab1 = 'class="active"';
									
									//AMENITIES	
									$amenityData = $db->runQuery("select * from ".AMENITY." as a inner join ".AMENITY_ANS." as aa on a.amenity_id = aa.amenity_id where aa.property_id = '".$ppty_id."' and aa.amenity_value ='1' and a.amenity_status = '1' ");
									$this->view->amenityData = $amenityData;
									
									break;		 
	
			case 'specification':	$this->view->tab = '2';
									$this->view->property = "specification";			
									$this->view->ppty_tab2 = 'class="active"';
									
									$specArr = $db->runQuery("select * from ".SPECIFICATION." 
															 inner join ".SPEC_CHILD." on ".SPEC_CHILD.".spec_id = ".SPECIFICATION.".spec_id
															 inner join ".SPEC_ANS." on ".SPEC_ANS.".answer = ".SPEC_CHILD.".cid	
															 where ".SPEC_ANS.".property_id = ".$ppty_id."
									 						");
															
									
									$specArr = $db->runQuery("select * from ".SPECIFICATION." as s inner join ".PROPERTY_SPEC_CAT." as psc on s.cat_id = psc.cat_id 
									  where psc.cat_status = '1' 
									  and s.status = '1' order by psc.cat_id, s.spec_order asc
									  ");	
									
									$category_temp = "";

									$i=0;
									$t = 0;
									$finalArr[0]['category'][0] = "";
									$cat_counter = 0;
									$bathroom_counter = 0;
										$xcounter = 0;	
										$max = 0;								
									foreach($specArr as $key=>$value)
									{
										
										if($finalArr[$cat_counter]['category'] != $value['cat_name'])
										{
											if($i>0)
											$cat_counter++;
											
											$finalArr[$cat_counter]['category'] = $value['cat_name'];
											
											$t=0;
											
										}
										
										
										
																		 
										$selectOptionArr = $db->runQuery("select * from ".SPEC_CHILD."
																		 inner join ".SPEC_ANS." on ".SPEC_ANS.".answer = ".SPEC_CHILD.".cid	
																		 where ".SPEC_ANS.".spec_id = '".$value['spec_id']."' and ".SPEC_ANS.".property_id = '".$ppty_id."' ");


										if($value['spec_id'] == '22' || $value['spec_id'] == '23' || $value['spec_id'] == '24')
										{
											
											
											foreach($selectOptionArr as $key1=>$value1)
											{
													$array1 = explode('|||',$value1['answer']);
													
													$max = count($array1)>$max ? count($array1):$max;
													
													$x = 0;
													foreach($array1 as $keybath=>$bath)
													{
/*														echo "select ".SPEC_CHILD.".option from ".SPEC_CHILD." where cid = '".$bath."' "; exit;*/
														$bathArr = $db->runQuery("select ".SPEC_CHILD.".option from ".SPEC_CHILD." where cid = '".$bath."' ");
										
														//pr($bathArr);
														/*if(count($bathArr) == 0)
														$bathroom[$keybath][] = "";
														*/
														
														foreach($bathArr as $ckey=>$calue)
														{	

															if($value['spec_id'] == '24' )
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
													
													
														foreach($array1 as $keybath=>$bath)
														{

															$finalArr[$cat_counter]['ques'][$keybath]	= $value['spec_display']." ".($keybath+1);
														}
															$finalArr[$cat_counter]['answer']	= $bathroom;	
														
														
													
													
													$j++;
													
													
													if($value['spec_id'] == '24')
													{
												
														$xcounter++;
														
													}
											
											}
											
											$bathroom_counter++;
											
											
											
										}
										else
										{
													if($max != 0) $t = $t+$max;
													$max = 0;
										
													if($value['spec_type'] == '2' || $value['spec_type'] == '3')
													{
															
														$selectOptionArr = $db->runQuery("select * from ".SPEC_ANS."  where ".SPEC_ANS.".spec_id = '".$value['spec_id']."' and ".SPEC_ANS.".property_id = '".$ppty_id."' ");
											/*			prd($selectOptionArr);*/

														if($selectOptionArr[0]['answer'] != "")
														{
															 
															$j = 0;
															$finalArr[$cat_counter]['ques'][$t]	= $value['spec_display'];
															$finalArr[$cat_counter]['ticklist'][$t]	= '0';
															$finalArr[$cat_counter]['answer'][$t][0] = $selectOptionArr[0]['answer'];
															$selectOptionArr = array();
														
														}
														else
														$t--;	

													}
													elseif($value['preview_display'] == '1' || count($selectOptionArr)>0 )
													{
														
														
														$finalArr[$cat_counter]['ques'][$t]	= $value['spec_display'];										
														
														if($value['spec_type'] == '4')
														$finalArr[$cat_counter]['ticklist'][$t]	= '1';
														else
														$finalArr[$cat_counter]['ticklist'][$t]	= '0';
														
														
														
													}
													else
													$t--;
													
													$j=0;
													if(count($selectOptionArr) > 0)
													foreach($selectOptionArr as $key1=>$value1)
													{
														
				
														$finalArr[$cat_counter]['answer'][$t][$j]	= $value1['option'];
														
														$j++;
														
													}
										
										
													$t++;	
										}
																			
										$i++;
										
										
									}
									
									/*prd($finalArr);*/
									
									/*$specArr = $db->runQuery("select * from ".SPECIFICATION." 
															 inner join ".SPEC_CHILD." on ".SPEC_CHILD.".spec_id = ".SPECIFICATION.".spec_id
															 inner join ".SPEC_ANS." on ".SPEC_ANS.".answer = ".SPEC_CHILD.".cid	
															 where ".SPEC_ANS.".property_id = ".$ppty_id."
									 						");*/
									$this->view->specArr = $finalArr;						
															
									
									

									
									break;		 	
			case 'location':		$this->view->tab = '3';
									$this->view->property = "location";			
									$this->view->ppty_tab3 = 'class="active"';
									break;	
			case 'availability':	$this->view->tab = '4';
									$this->view->property = "availability";			
									$this->view->cal_default = $propertyArr[0]['cal_default'];
									$this->view->ppty_tab4 = 'class="active"';
									
									$calArr = $db->runQuery("select * from ".CAL_AVAIL." where property_id = '".$ppty_id."' ");

									$this->view->calArr = $calArr;	
									
									$next = $this->getRequest()->getParam("cal");		
									if($next != "")
									$this->view->nexts = $next;
									else	
									$this->view->nexts = 0;
									
									break;		 	 
			case 'rental':			$this->view->tab = '5';
									$this->view->property = "rental";			
									$this->view->ppty_tab5 = 'class="active"';
									$option_extra = $db->runQuery("select ename, (select exchange_rate from ".CURRENCY." where ".CURRENCY.".currency_code = (select currency_code from ".PROPERTY." where id = '".$ppty_id."' ))*eprice as eprice,etype,stay_type  from ".EXTRAS." where property_id = '".$ppty_id."' ");
									$this->view->option_extra=$option_extra;
									
									$calArr = $db->runQuery("select (select exchange_rate from ".CURRENCY." where ".CURRENCY.".currency_code = (select currency_code from ".PROPERTY." where id = '".$ppty_id."') )*prate as prate,
														     nights,date_to,date_from from ".CAL_RATE."  
															 where property_id = ".$ppty_id."  order by date_from asc ");

									$this->view->calData = $calArr;
									
									
									$spclArr = $db->runQuery("select *, ".SPCL_OFFER_TYPES.".min_nights as MIN_NIGHTS from ".SPCL_OFFERS." 
															  inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id = ".SPCL_OFFER_TYPES.".id
															  where ".SPCL_OFFERS.".property_id = '".$ppty_id."' 
															  and ".SPCL_OFFERS.".activate = '1'  and ".SPCL_OFFERS.".book_by >= curdate() ");

									$this->view->spclData = $spclArr;
									
									
									
									break;		 			
			case 'gallery':			$this->view->tab = '6';
									$this->view->property = "gallery";			
									$this->view->ppty_tab6 = 'class="active"';
									$galleryArr = $db->runQuery("select * from ".GALLERY."  where property_id = ".$ppty_id);
									$this->view->galleryArr = $galleryArr;
									break;		 				
			case 'reviews':			$this->view->tab = '7';
									$this->view->property = "review";			
									$this->view->ppty_tab7 = 'class="active"';
									
									/*** review form display code **/
									

									
									//if(!isset($mySession->reviewImage))
									//$mySession->reviewImage = "no_owner_pic.jpg";
									$reviewArr = $db->runQuery("select * from ".OWNER_REVIEW." as r
																inner join ".PROPERTY." as p on p.id = r.property_id
																inner join ".USERS." as u on u.user_id = p.user_id
																where r.property_id = '".$ppty_id."' and r.review_status = '1' order by r.review_id desc ");
									
									//prd($reviewArr);	
			
									$i = 0;
									foreach($reviewArr as $val)
									{
										
										if($val['parent_id'] == 0)
										{

											$childArr = $db->runQuery("select * from ".OWNER_REVIEW." where parent_id = '".$val['review_id']."' ");

											$reviewData[$i]['review_id'] = $val['review_id'];
											$reviewData[$i]['uType'] =  $val['uType'];
											$reviewData[$i]['guest_name'] =  $val['guest_name'];
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
											foreach($childArr as $val1)
											{

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
										
										
										/** review form dissplay code ends **/
									
									break;		 				
			case 'question':		$this->view->tab = '8';
									$this->view->property = "question";			
									$this->view->ppty_tab8 = 'class="active"';
									//contact us
									$myform = new Form_Ocontact();
									$this->view->myform = $myform;
									break;		 				
																																
			
		}


	

		//rate query
		/*echo "select prate as RATE,nights from ".CAL_RATE." where property_id = '".$ppty_id."' and date_from <= CURDATE() and date_to >= CURDATE() 
								  union 
								  select min(prate) as RATE, nights from ".CAL_RATE." where property_id = '".$ppty_id."' and date_from <= CURDATE() 
								  union 
								  select min(prate) as RATE, nights from ".CAL_RATE." where property_id = '".$ppty_id."'"; exit;*/
		
		

		
		
		if($datefrom != ""):
		$datefrom = date('Y-m-d',strtotime($datefrom));
		$dateto = date('Y-m-d',strtotime($dateto));
		
		
/*		prd(" select  
										 coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * prate
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * `prate`
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * `prate`
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * `prate`
											 end),(select min(prate) from ".CAL_RATE." where ".CAL_RATE.".property_id = '".$ppty_id."'))*exchange_rate 
											 AS RATE from ".CAL_RATE." 
											 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
											 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
											 where property_id = '".$ppty_id."' group by ".CAL_RATE.".property_id ");*/
		
		
		/*$rateArr = $db->runQuery(" select  
										 coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * prate
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * `prate`
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * `prate`
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * `prate`
											 end),(select prate*".dateDiff($datefrom,$dateto)." from ".CAL_RATE." where ".CAL_RATE.".property_id = '".$ppty_id."' having prate = min(prate)  ))*exchange_rate 
											 AS RATE from ".CAL_RATE." 
											 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
											 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
											 where property_id = '".$ppty_id."' group by ".CAL_RATE.".property_id ");*/
					
					
		/*	prd(" select  *, 
										 coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * round(prate*exchange_rate)
											 end),(select round(min(prate)*exchange_rate)*".(dateDiff($datefrom,$dateto)+1)." from ".CAL_RATE." 
											 where ".CAL_RATE.".property_id = '".$ppty_id."'))
											 AS RATE from ".CAL_RATE." 
											 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
											 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
											 where property_id = '".$ppty_id."' group by ".CAL_RATE.".property_id ")	;*/
											 
		$rateArr = $db->runQuery(" select  *, 
										 coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * round(prate*exchange_rate)
											 end),(select round(min(prate)*exchange_rate)*".(dateDiff($datefrom,$dateto)+1)." from ".CAL_RATE." 
											 where ".CAL_RATE.".property_id = '".$ppty_id."'))
											 AS RATE from ".CAL_RATE." 
											 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
											 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
											 where property_id = '".$ppty_id."' group by ".CAL_RATE.".property_id ");											 
											 

		
											 
		$this->view->nights = dateDiff($datefrom,$dateto)+1;
		


		
		else:					
		

		$rateArr = $db->runQuery("select nights*round(prate*".CURRENCY.".exchange_rate) as RATE,nights,prate,".PROPERTY.".id from ".CAL_RATE." 
								  inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
								  inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
		                          where ".CAL_RATE.".property_id = '".$ppty_id."' and prate = (select min(prate) from ".CAL_RATE." where property_id = '".$ppty_id."' 
								   ) order by prate asc
								  ");
		//echo "select prate*nights as RATE,nights,prate,id from ".CAL_RATE." where prate = (select min(prate) from ".CAL_RATE." where property_id = '".$ppty_id."'  ) ";
		$this->view->nights = $rateArr[0]['nights']!=""?$rateArr[0]['nights']:"";
		endif;
		
		


		//$tmp = explode(".",$rateArr[0]['RATE']);
		

		
		/*$actcurrArr = $db->runQuery("select exchange_rate*".($tmp[0] != "" ? $tmp[0]:0)." as mul from ".CURRENCY." inner join ".PROPERTY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code where ".PROPERTY.".id = '".$ppty_id."'  ");
		
		$tmp[0] = $actcurrArr[0]['mul'];*/
		
		
		
		$this->view->prate = $rateArr[0]['RATE']!=""?$rateArr[0]['RATE']:"Unknown";
		

		
		//cal Query
		/* gadd $calAvailArr = $db->runQuery("select * from ".CAL_AVAIL." 
									  inner join ".PROPERTY." on ".CAL_AVAIL.".property_id = ".PROPERTY.".id
									  where ".CAL_AVAIL.".property_id = '".$ppty_id."' ");*/
		
		
		$this->view->propertyData = $propertyArr;
		
	
		
		
		// property images
		$galleryData = $db->runQuery("select * from ".GALLERY." where property_id = '".$ppty_id."' ");
		$this->view->galleryData = $galleryData;
		
		
		
		if ($this->getRequest()->isPost() && !isset($_REQUEST['review']))
		{

			$request=$this->getRequest();
			$dataForm=$myform->getValues();
			
			if($dataForm['captcha'] == $_SESSION['captcha'])
			{
				$myObj = new Users();
				$Result=$myObj->ownercontactus($dataForm);
				$mySession->sucessMsg = "Thank you, You will soon be contacted";
				$varsuccess = 1;
			}
			else
			{
				$mySession->errorMsg = "Please Enter Correct Human Verification Code";	
			}
					
		}
		
		
		
		
		$this->view->varsuccess = $varsuccess;
		

	}
	
	/*********** save review Action ***********/
	/******************************************/
	
	public function savereviewAction()
	{
		global $mySession;
		$db = new Db();		
		
		$pptyId = $this->getRequest()->getParam("ppty");

		
		//get user details
		$userArr = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' ");
		
			$data_update = array();
			$data_update['guest_name'] = $userArr[0]['first_name']." ".$userArr[0]['last_name'];
			$data_update['location'] = $userArr[0]['address'];
			$data_update['check_in'] = date('Y-m-d',strtotime($_REQUEST['Check_in']));
			$data_update['rating'] = $_REQUEST['Rating'];						
			$data_update['headline'] = $_REQUEST['Headline'];						
			$data_update['comment'] = $_REQUEST['Comment'];						
			$data_update['review'] = $_REQUEST['Review'];
			if($mySession->LoggedUserType == '2')
			$data_update['uType'] = '0';
			else
			$data_update['uType'] = '1';			
			$data_update['review_date'] = date("Y-m-d");						
			

			$data_update["property_id"] = $pptyId;
			$data_update['guest_image'] = $userArr[0]['image'];						
		
			copy(SITE_ROOT."images/".$userArr[0]['image'],SITE_ROOT."images/profile/".$userArr[0]['image']);
	

	
			$db->save(OWNER_REVIEW,$data_update);
			
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
		
		$fetchArr = $db->runQuery("select * from ".PROPERTY." inner join ".GALLERY." on ".GALLERY.".property_id = ".PROPERTY.".id   where ".PROPERTY.".id = '".$ppty."' group by id");
		
		if($fetchArr[0]['image_name'])
		$image = $fetchArr[0]['image_name'];
		else
		$image = "generic.jpg";
		
		$display = '<div class="hotel-image-holder">
        				 <img title="View hotel details" alt="Hotel photo" src="'.APPLICATION_URL.'image.php?image=images/property/'.$image.'&height=100&width=150" class="hotel-image-gallery-view trp-round-3 image-shadow viewDetailsLink">
					</div>
			<h3 class="hotel-name ellipses-overflow">
            	<a title="View hotel details" id="hotelName-126513" class="viewDetailsLink">'.substr($fetchArr[0]['property_title'],0,30).'</a>
            </h3>
		  <span class="map_ppty_no" >Property No: <span class="green">'.$fetchArr[0]['propertycode'].'</span></span>
	        <h5 class="hotel-location">'.$fetchArr[0]['bedrooms'].' bedrooms /'.ceil($fetchArr[0]['bathrooms']).' bathrooms <br />
										Sleeps up to '.$fetchArr[0]['maximum_occupancy'].'
			</h5>
				<div class="edit_btns">
					<div class="btns" ><!-- first div -->
						<a href="'.APPLICATION_URL.'search/searchdetail/ppty/'.$ppty.'">View Detail</a>
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
		
		$fetchArr = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' ");
		$ppty = $this->getRequest()->getParam("ppty");	
		$del = $this->getRequest()->getParam("del");
		
		
		
		if($fetchArr[0]['fav_ppty'])
		$tmp = explode(",",$fetchArr[0]['fav_ppty']);
		
		if($del == '0' && count($tmp) <= 12)
		{
			if(count($tmp)>0 && $tmp[0] != "")
			$data_update['fav_ppty'] = $fetchArr[0]['fav_ppty'].$ppty.",";
			else
			$data_update['fav_ppty'] = $ppty.",";
		}
		elseif(count($tmp) > 12)
		exit("fail");
		
		if($del == '1')
		{
			$data_update['fav_ppty'] = str_replace($ppty.",","",$fetchArr[0]['fav_ppty']);
			$data_update['fav_ppty'] = str_replace($ppty.",","",$fetchArr[0]['fav_ppty']);
			
		}
		
		$condition = "user_id=".$mySession->LoggedUserId;
		$db->modify(USERS,$data_update,$condition);
		
		exit;
	}
	
	public function loadfavAction()
	{
		global $mySession;
		$db = new Db();
		$sno = $this->getRequest()->getParam("sno");
		
		$fetchArr = $db->runQuery("select * from ".USERS." where user_id = '".$mySession->LoggedUserId."' ");
		
		$favArr = explode(",",$fetchArr[0]['fav_ppty']);
		


		$i = 0;
		
		if(count($favArr) == 0 || $favArr[0] == "")
		exit(0);
		else		
		foreach($favArr as $values)
		{
			if($sno == $i && $values != "")
			{
				$imgArr = $db->runQuery("select * from ".PROPERTY." inner join ".GALLERY." on ".PROPERTY.".id = ".GALLERY.".property_id where ".PROPERTY.".id = '".$values."' limit 0,1");	

				if(count($imgArr)>0)
				exit("<a href='".APPLICATION_URL."search/searchdetail/ppty/".$values."'><img src='".IMAGES_URL."property/".$imgArr[0]['image_name']."' width='40' height = '40'></a>");
				else
				exit("<a href='".APPLICATION_URL."search/searchdetail/ppty/".$values."'><img src='".IMAGES_URL."property/generic.jpg' width='40' height = '40'></a>");
			}
			$i++;			
		}
		
		exit;			
	}
}
?>