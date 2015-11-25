<?php
__autoloadDB('Db');
class Specification extends Db
{
	
	public function saveSpecification($dataForm,$specId="")
	{
		global $mySession;
		$db=new Db();
		$dataForm = SetupMagicQuotesTrim($dataForm);
		
		
		if($specId == "")
		{
			
			
			$chkQry = $db->runQuery("select * from ".SPECIFICATION." where question like '%".mysql_escape_string(trim($dataForm['question']))."'  and cat_id = '".$dataForm['category']."' ");
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Same Question exists than return false / 0 
				// No Data Inserted
			return 0;
			}
			else
			{
				
				$data_update['cat_id'] = $dataForm['category'];				
				$data_update['question'] = $dataForm['question'];				
				$data_update['spec_type'] = $dataForm['input_type'];
				$data_update['mandatory'] = $dataForm['mandatory'];				
				$data_update['status'] = $dataForm['spec_status'];
				
				//code for inserting order
				$orderArr = $db->runQuery("select * from ".SPECIFICATION." where cat_id = '".$dataForm['category']."' ");
				$data_update['spec_order'] = count($orderArr)+1;
				
				$db->save(SPECIFICATION,$data_update);
				$latestId = $db->lastInsertId();				
				
				
				for($x = 1;$x <= 53;$x++)
				{   $options_add = "options_add".$x;
					if(trim($dataForm[$options_add]) != "")	
					{		

							$data_update1['spec_id'] = $latestId; 
							$data_update1['option'] = sanisitize_input($dataForm[$options_add]);
							$db->save(SPEC_CHILD,$data_update1);
					}
					
				}
				
				
				return 1;
			}
		}
		else
		{
//			echo "select * from ".SPECIFICATION." where question like '%".mysql_escape_string(trim($dataForm['question']))."'  and cat_id = '".$dataForm['category']."' ";
//			exit;
			$chkQry=$db->runQuery("select * from ".SPECIFICATION." where question like '%".mysql_escape_string(trim($dataForm['question']))."' and spec_id != '".$specId."' and cat_id = '".$dataForm['category']."' ");
				
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Same Question exists than return false / 0 
				// No Data Inserted
				return 0;
			}
			else
			{
				

				$data_update['cat_id'] = $dataForm['category'];							
				$data_update['question'] = $dataForm['question'];				
				$data_update['spec_type'] = $dataForm['input_type'];				
				$data_update['status'] = $dataForm['spec_status'];	
				$data_update['mandatory'] = $dataForm['mandatory'];							
				$condition = "spec_id = ".$specId;
				$db->modify(SPECIFICATION,$data_update,$condition);
				$specsId = "";				
					
				
				$db->delete(SPEC_CHILD,$condition);
				for($x = 1;$x <= 53;$x++)
				{   $options_add = "options_add".$x;
					if(trim($dataForm[$options_add]) != "")	
					{		
							$data_update1['spec_id'] = $specId; 
							$data_update1['option'] = $dataForm[$options_add];
							$db->save(SPEC_CHILD,$data_update1);
				
					}
					
				}
				
				
				return 1;
			}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteSpecification($specId)
  {
   	 echo $specId;

	 global $mySession;
	 $db=new Db();
	 $condition1="spec_id='".$specId."'"; 
	 $db->delete(SPECIFICATION,$condition1);
	 $db->delete(SPEC_CHILD,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusSpecification($status,$specId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['status'] = $status; 
	 $condition= "spec_id='".$specId."'";
	 $db->modify(SPECIFICATION,$data_update,$condition);
	 
  }	
	

	
}
?>