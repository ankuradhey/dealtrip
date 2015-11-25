<?php
__autoloadDB('Db');
__autoloadPlugin('Ciirus');
class Booking extends Db
{
	public function checkAvailablity($dataForm, $pptyId)
	{
		global $mySession;
		$db=new Db();
		
                if($dataForm['partySize'] < $dataForm['Adults'] + $dataForm['Children']) // condition 1 check [maximum allowed members]
		{
			return array("message"=>"Members area greater than the property size","output"=>false);
		}
		$arrivalDate = explode("/",$dataForm['date_from']);
		$narrivalDate = $arrivalDate[2]."-".$arrivalDate[1]."-".$arrivalDate[0];
		$dataForm['date_to'] =  $dataForm['date_to']-1;
		//echo "select * from  ".CAL_AVAIL." where ((date_from <= '".$narrivalDate."' and  date_to >= '".$narrivalDate."') or  (date_to >= adddate('".$narrivalDate."',$dataForm[date_to]) and date_from <= adddate('".$narrivalDate."',$dataForm[date_to])))  and cal_status = '0'  and property_id = '".$pptyId."'  "; exit;
		$chkQuery = $db->runQuery("select * from  ".CAL_AVAIL." where ((date_from <= '".$narrivalDate."' and  date_to >= '".$narrivalDate."') or  (date_to >= adddate('".$narrivalDate."','".$dataForm['date_to']."') and date_from <= adddate('".$narrivalDate."','".$dataForm['date_to']."')))  and cal_status = '0'  and property_id = '".$pptyId."'  ");
                $dateTo = date('Y-m-d', strtotime($narrivalDate." + ".$dataForm['date_to']." day"));
                
		if(count($chkQuery)>0) //condition 2 check [availability]
		{
                    return array("message"=>"The dates/nights you selected are not available, please try again","output"=>false);
                }
                else{
                    //checking if a supplier property
                    $supplierpptyArr = $db->runQuery("select * from property where id = '".$pptyId."' ");
                    if($supplierpptyArr[0]['xml_subscriber_id'] > 0 && !empty($supplierpptyArr[0]['xml_property_id']) && $supplierpptyArr[0]['xml_subscriber_id'] == '1'){
                        //xml api checking if a property is available
                        $res = new Ciirus("c346aeb90de54a3", "ff3a6f4e60ab4ec");
                        $arrivalDate = date('d-M-Y',strtotime($narrivalDate));
                        //prd($arrivalDate);
                        $departureDate = date('d-M-Y',strtotime($narrivalDate." + ".($dataForm['date_to']+1)." day"));
                        $dd = $res->isPropertyAvailable($supplierpptyArr[0]['xml_property_id'], $arrivalDate, $departureDate);
                        //prd($dd);
                        if(!$dd)
                            return array("message"=>"The dates/nights you selected are not available, please try again","output"=>false);
                    }
                }

		
		
		
		if($this->checkAmount($narrivalDate,$dateTo,$dataForm['date_to']+1,$pptyId) === 0)
		return array("message"=>"Allowed Minimum Nights failed","output"=>false);
		
		
		$mySession->Adults = $dataForm['Adults'];
		$mySession->Children =  $dataForm['Children'];
		$mySession->Infants =  $dataForm['Infants'];
		
		
		$mySession->noOfNights = $dataForm['date_to']+1;
		$mySession->arrivalDate = $dataForm['date_from'];
		$mySession->departureDate = date('d-m-Y',strtotime($dataForm['date_from']." + ".($dataForm['date_to']+1)." day"));
		$mySession->partySize = $dataForm['Adults'] + $dataForm['Children'];		
		$mySession->pptyId = $pptyId;
		//code to find the amount
		

		$mySession->totalCost = $this->checkAmount($narrivalDate,$dateTo,$dataForm['date_to']+1,$pptyId);
//                if(empty($mySession->totalCost)){
//                    return array("output"=>false,"message"=>"cost not available");
//                }
		$mySession->steps = '2';
//		return 1;
		return array("cost"=>$mySession->totalCost,"output"=>true);
	}
	
	
 	public function checkAmount($datefrom,$dateto, $minNights, $pptyId)
	{
		global $mySession;
		$db = new Db();
		$totalCost = 0;
		//echo "select  from ".CAL_RATE." where date_from >= '".date('Y-m-d',strtotime($datefrom))."' and date_to <= '".date('Y-m-d',strtotime($dateto))."' and property_id = '".$pptyId."' "; exit;
		
		//prd("$datefrom $dateto $minNights $pptyId");

		$sum_query = " select * ,
										 coalesce (sum(
											 case when ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( ".CAL_RATE.".date_from, ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."'
											 then (abs( datediff( '".$datefrom."', '".$dateto."' ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' 
											 then (abs( datediff( '".$datefrom."', ".CAL_RATE.".date_to ))+1) * round(prate*exchange_rate)
											 when ".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."'
											 then (abs( datediff( ".CAL_RATE.".date_from, '".$dateto."' ))+1) * round(prate*exchange_rate)
											 end), (select round(min(prate)*exchange_rate)*".$minNights.")) 
											 AS total_amount from ".CAL_RATE."  
											 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
											 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
											 where property_id = '".$pptyId."' ";
											 

		
		
		$unused_query = "select prate,nights from ".CAL_RATE." where date_from >= '".$datefrom."' and date_to <= '".$dateto."' and property_id = '".$pptyId."' ";

		$sql = $db->runQuery(" $sum_query ");
		/*prd($sql);*/

		if(count($sql) > 0)
		{
			$totalCost = $sql[0]['total_amount'];
		}
		else //find the minimum rate
		{
			$minrate = $db->runQuery("select round(min(prate)*exchange_rate) as MIN from ".CAL_RATE." 
									 inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
  								     inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
			                         where property_id = '".$pptyId."' ");
    		$totalCost = $minrate[0]['MIN']*$minNights;
		}
		
		//code for fetching the extras
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//$actcurrArr = $db->runQuery("select exchange_rate*".$totalCost." as mul from ".CURRENCY." inner join ".PROPERTY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code where ".PROPERTY.".id = '".$pptyId."'  ");
		
		
		//$totalCost = $actcurrArr[0]['mul'];
		
		
		
		return $totalCost;
	}
  
 
}
?>