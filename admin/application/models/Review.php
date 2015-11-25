<?php
__autoloadDB('Db');
class Review extends Db
{
	
	public function savereview($dataForm,$ptyleId="")
	{
		global $mySession;
		$db=new Db();
	
		
		
		if($ptyleId == "")
		{
				$data_update['review'] = $dataForm['review'];				
				$data_update['comment'] = $dataForm['comment'];				
				$data_update['review_status'] = $dataForm['status'];	
				$db->save(OWNER_REVIEW,$data_update);
				return 1;
			
		}
		else
		{
	
				$data_update = array();
				
				$data_update['guest_name'] = $dataForm['full_name'];				
				$data_update['location'] = $dataForm['location'];				
//				$data_update['review_date'] = $dataForm['check_in'];								
				$data_update['check_in'] = $dataForm['check_in'];								
				$data_update['review'] = $dataForm['review'];				
				$data_update['rating'] = $dataForm['rating'];
				$data_update['headline'] = $dataForm['headline'];												
				$data_update['comment'] = $dataForm['comment'];				
				$data_update['review_status'] = $dataForm['status'];	
				$condition = " review_id = '".$ptyleId."' ";				
				$result = $db->modify(OWNER_REVIEW,$data_update,$condition);
		
				return 1;

		}
	}
	
	#-----------------------------------------------------------#
	# Delete Property Type Function
	
	// Here delete Property Type record from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function deletereview($ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="review_id='".$ptyleId."'"; 
	 
	 $chkQuery = $db->runQuery("select * from ".OWNER_REVIEW." where review_id = '".$ptyle_id."' ");
	 
	 if($chkQuery[0]['parent_id'] == '0')
	 {
			 $condition = "parent_id=".$ptyle_id;
			 $db->delete(OWNER_REVIEW,$condition);
	 }
	 
	 $db->delete(OWNER_REVIEW,$condition1);
	 
  }
	
	#-----------------------------------------------------------#
	# Status Property Type Function
	
	// Here Property Type status changed from PROPERTYTYPE table.
	
	#-----------------------------------------------------------#
	
  public function statusreview($status,$ptyleId)
  {
   	 global $mySession;
	 $db=new Db();
	 $data_update['review_status'] = $status; 
	 $condition= "review_id='".$ptyleId."'";
	 $db->modify(OWNER_REVIEW,$data_update,$condition);
	 
  }	
	

	
}
?>