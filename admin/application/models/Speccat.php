<?php
__autoloadDB('Db');
class Speccat extends Db
{
	
	public function saveSpeccat($dataForm,$cId="")
	{
		global $mySession;
		$db=new Db();
		if($cId == "")
		{
			
			
			$chkQry=$db->runQuery("select * from ".PROPERTY_SPEC_CAT." where cat_name = '".mysql_escape_string(trim($dataForm['category']))."'  ");
			if($chkQry!="" and count($chkQry)>0)
			{
				return 0;
			}
			else
			{
				$data_update['cat_name'] = $dataForm['category'];				
				$data_update['cat_status'] = $dataForm['status'];				
				$db->save(PROPERTY_SPEC_CAT,$data_update);
				return 1;
			}
		}
		else
		{
			$chkQry=$db->runQuery("select * from ".PROPERTY_SPEC_CAT."  where cat_name = '".mysql_escape_string(trim($dataForm['category']))."' and cat_id != '".$cId."'");
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Property Type Name is exists than return false / 0 
				// No Data Inserted
			return 0;
			}
			else
			{
				
				$data_update['cat_name'] = $dataForm['category'];				
				$data_update['cat_status'] = $dataForm['status'];	
				$condition = " cat_id = ".$cId;
				$result=$db->modify(PROPERTY_SPEC_CAT,$data_update,$condition);
				return 1;
			}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteSpeccat($cId)
  {
   	 global $mySession;
	 $db=new Db();
	 
	 //check if the child entry is there
	 $chkQuery = $db->runQuery("select * from ".SPECIFICATION." where cat_id = '".$cId."' ");
	 if($chkQuery != "" && count($chkQuery) > 0)
	 {
	 	 return 0;
	 }
	 else
	 {
		 $condition1 = "cat_id='".$cId."'"; 
		 $db->delete(PROPERTY_SPEC_CAT,$condition1);
		 return 1;
	 }
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusSpeccat($status,$cId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['cat_status'] = $status; 
	 $condition= "cat_id='".$cId."'";
	 $db->modify(PROPERTY_SPEC_CAT,$data_update,$condition);
	 
  }	
	

	
}
?>