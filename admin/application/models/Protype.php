<?php
__autoloadDB('Db');
class Protype extends Db
{
	
	public function saveProtype($dataForm,$ptyleId="")
	{
		global $mySession;
		$db=new Db();
		
		
		$data_update['ptyle_name']=$dataForm['ptyle_name'];
		
		
		if($ptyleId=="")
		{
		$chkQry=$db->runQuery("select * from ".PROPERTYTYPE." where ptyle_name='".mysql_escape_string(trim($dataForm['ptyle_name']))."'");
		if($chkQry!="" and count($chkQry)>0)
		{
			//if Property Type Name is exists than return false / 0 
			// No Data Inserted
		return 0;
		}
		else
		{
			# If Property Type Name Not Already Exista.
			# Insert New Record Into Database
			

		$db->save(PROPERTYTYPE,$data_update);
		return 1;
		}
		}
		else
		{
			$chkQry=$db->runQuery("select * from ".PROPERTYTYPE." where ptyle_name='".mysql_escape_string(trim($dataForm['ptyle_name']))."' and ptyle_id!=".$ptyleId);
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;
		}
		else
		{
			$condition='ptyle_id='.$ptyleId;
			$result=$db->modify(PROPERTYTYPE,$data_update,$condition);
			return 1;
		}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteProtype($ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="ptyle_id='".$ptyleId."'"; 
	 $db->delete(PROPERTYTYPE,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusProtype($status,$ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['ptyle_status']=$status; 
	 $condition=PROPERTYTYPE.".ptyle_id='".$ptyleId."'";
	 $db->modify(PROPERTYTYPE,$data_update,$condition);
	 
  }	
	

	
}
?>