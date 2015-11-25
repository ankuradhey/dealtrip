<?php
__autoloadDB('Db');
class Spcloffer extends Db
{
	
	public function saveOffer($dataForm,$sId="")
	{
		global $mySession;
		$db=new Db();
		
		if($sId == "")
		{
			
			
			$chkQry = $db->runQuery("select * from ".SPCL_OFFER_TYPES." where type_name = '".mysql_escape_string(trim($dataForm['offer_name']))."'  ");
			if($chkQry!="" and count($chkQry)>0)
			{
				return 0;
			}
			else
			{
				
				$data_update['type_name'] = $dataForm['offer_name'];				
				$data_update['status'] = $dataForm['status'];
				$data_update['promo_code'] = $dataForm['promo_code'];	
				$data_update['discount_type'] = $dataForm['discount_type'];	
				$data_update['min_nights_type'] = $dataForm['min_nights'];	
				if($dataForm['min_nights'] == '1')
				{
					$data_update['min_nights'] = $dataForm['min_nights_def'];	
				}
				
				$db->save(SPCL_OFFER_TYPES,$data_update);
				return 1;
			}
		}
		else
		{
			$chkQry=$db->runQuery("select * from ".SPCL_OFFER_TYPES." where type_name = '".mysql_escape_string(trim($dataForm['offer_name']))."' and id != '".$sId."'");
			if($chkQry!="" and count($chkQry)>0)
			{
				return 0;
			}
			else
			{
				$data_update['type_name'] = $dataForm['offer_name'];				
				$data_update['status'] = $dataForm['status'];	
				$data_update['promo_code'] = $dataForm['promo_code'];	
				$data_update['min_nights_type'] = $dataForm['min_nights'];					
				$data_update['discount_type'] = $dataForm['discount_type'];					
				if($dataForm['min_nights'] == '1')
				{
					$data_update['min_nights'] = $dataForm['min_nights_def'];	
				}

				$condition = "id = ".$sId;
				
				$result=$db->modify(SPCL_OFFER_TYPES,$data_update,$condition);
				return 1;
			}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteOffer($sId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="id='".$sId."'"; 
	 $db->delete(SPCL_OFFER_TYPES,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusOffer($status,$sId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['status'] = $status; 
	 $condition= "id='".$sId."'";
	 $db->modify(SPCL_OFFER_TYPES,$data_update,$condition);
	 
  }	
	

	
}
?>