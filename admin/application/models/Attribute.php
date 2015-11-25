<?php
__autoloadDB('Db');
class Attribute extends Db
{
	
	public function saveAttribute($dataForm,$attributeId="")
	{
		global $mySession;
		$db=new Db();
		$dataForm = SetupMagicQuotesTrim($dataForm);
		if($attributeId == "")
		{
			
			
			$chkQry = $db->runQuery("select * from ".ATTRIBUTE." where attribute_name like '%".mysql_escape_string(trim($dataForm['attribute_name']))."' ");
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Same Question exists than return false / 0 
				// No Data Inserted
        			return 0;
			}
			else
			{
                                $data = array();
                                $data['attribute_name'] = $dataForm['attribute_name'];
                                $data['attribute_status'] = $dataForm['attribute_status'];
				$db->save(ATTRIBUTE,$data);
				$latestId = $db->lastInsertId();				
				return 1;
			}
		}
		else
		{
			$chkQry=$db->runQuery("select * from ".ATTRIBUTE." where attribute_name like '%".mysql_escape_string(trim($dataForm['attribute_name']))."' and attribute_id != '".$attributeId."'  ");
				
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Same Question exists than return false / 0 
				// No Data Inserted
				return 0;
			}
			else
			{
                            $data = array();
                                $data['attribute_name'] = $dataForm['attribute_name'];
                                $data['attribute_status'] = $dataForm['attribute_status'];
				$condition = "attribute_id = ".$attributeId;
				$db->modify(ATTRIBUTE,$data,$condition);
				return 1;
			}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteAttribute($attributeId)
  {
	 global $mySession;
	 $db=new Db();
	 $condition1="attribute_id='$attributeId'"; 
	 $db->delete(ATTRIBUTE,$condition1);
         
         //delete all attribute_ans entries
         $condition2 = "ans_attribute_id= '$attributeId'";
         $db->delete(ATTRIBUTE_ANS, $condition2);
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusAttribute($status,$specId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['attribute_status'] = $status; 
	 $condition= "attribute_id='".$specId."'";
	 $db->modify(ATTRIBUTE,$data_update,$condition);
	 
  }	
	

	
}
?>