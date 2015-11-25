<?php
__autoloadDB('Db');
class Amenity extends Db
{
	
	public function saveAmenity($dataForm,$ptyleId="")
	{
		global $mySession;
		$db=new Db();
	

		
		
		if($ptyleId == "")
		{
			
			
			$chkQry=$db->runQuery("select * from ".AMENITY." where title = '".mysql_escape_string(trim($dataForm['amenity_name']))."'  ");
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Property Type Name is exists than return false / 0 
				// No Data Inserted
			return 0;
			}
			else
			{
				
				$data_update['title'] = $dataForm['amenity_name'];				
				$data_update['description'] = $dataForm['description'];				
				$data_update['amenity_status'] = $dataForm['amenity_status'];	
				
				$location_view = "";
				//comma separated values for location
				/*if($dataForm['box2View'] != "" && count($dataForm['box2View']) > 0)
				{
					for($i = 0; $i<count($dataForm['box2View']);$i++)	
					{
						$location_view .= $dataForm['box2View'][$i].",";
						
					}
					
					
				}
				
				$data_update['location_view'] = $location_view;*/
				$db->save(AMENITY,$data_update);
				return 1;
			}
		}
		else
		{
			$chkQry=$db->runQuery("select * from ".AMENITY." where title = '".mysql_escape_string(trim($dataForm['amenity_name']))."' and amenity_id != '".$ptyleId."'");
			if($chkQry!="" and count($chkQry)>0)
			{
				//if Property Type Name is exists than return false / 0 
				// No Data Inserted
			return 0;
			}
			else
			{
				
				$data_update['title'] = $dataForm['amenity_name'];				
				$data_update['description'] = $dataForm['description'];				
				$data_update['amenity_status'] = $dataForm['amenity_status'];				

				//comma separated values for location
				
				/*$location_view = "";
				if($dataForm['box2View'] != "" && count($dataForm['box2View']) > 0)
				{
					for($i = 0; $i<count($dataForm['box2View']);$i++)	
					{
						$location_view .= $dataForm['box2View'][$i].",";
						
					}
					
					
				}
			
				$data_update['location_view'] = $location_view;*/
				$condition = " amenity_id = ".$ptyleId;
				$result=$db->modify(AMENITY,$data_update,$condition);
				return 1;
			}
		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deleteAmenity($ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="amenity_id='".$ptyleId."'"; 
	 $db->delete(AMENITY,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusAmenity($status,$ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['amenity_status'] = $status; 
	 $condition= "amenity_id='".$ptyleId."'";
	 $db->modify(AMENITY,$data_update,$condition);
	 
  }	
	

	
}
?>