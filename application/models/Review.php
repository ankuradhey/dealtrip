<?php
__autoloadDB('Db');
class Review extends Db
{
   public function saveReview($dataForm)
  { 
		global $mySession;
		$db=new Db();
		$data_update = array();
		
		$chckArr = $db->runQuery("select * from ".PROPERTY." where propertycode = '".trim($dataForm['ppty_no'])."' and status = '3'");
		if(count($chckArr) > 0 && $chckArr != "")
		{
		
			$data_update['guest_name'] = $dataForm['full_name'];
			$data_update['location'] = $dataForm['location'];
			$check_in = explode("/",$dataForm['check_in']);
			$data_update['check_in'] = date('Y-m-d',strtotime($check_in[2]."-".$check_in[1]."-".$check_in[0]));
			
			$data_update['rating'] = $dataForm['rating'];	
			$data_update['user_id'] = $mySession->LoggedUserId;						
			$data_update['headline'] = $dataForm['headline'];						
			$data_update['comment'] = $dataForm['comment'];						
			$data_update['review'] = $dataForm['review'];
			$data_update['uType'] = $mySession->LoggedUserType=='1'?"1":"0";			
			$data_update['review_date'] = date("Y-m-d");						
			
			$data_update["property_id"] = $chckArr[0]['id'];
						
			$data_update['guest_image'] = $mySession->LoggedUser['image'];
			
			copy(SITE_ROOT."images/".$mySession->LoggedUser['image'],SITE_ROOT."images/profile/".$mySession->LoggedUser['image']); 						
			
			$db->save(OWNER_REVIEW,$data_update);
			
                        $review_id = $db->lastInsertId();
                        
                        
                        
                        //====== code to enter new latest review properties ===============
                        //two cases are there
                        //1. if already an entry is there within latest reviews
                        //2. first entry is made for specific property
                        
                        $reviewPptyArr = $db->runQuery("select * from ".LATEST_REVIEW." where r_property_id = '".$chckArr[0]['id']."'  ");
                        //case 1
                        if(count($reviewPptyArr) >0 && $reviewPptyArr != ""){
                            $db->delete(LATEST_REVIEW,"r_id = ".$reviewPptyArr[0]['r_id']);
                            
                            $updateData = array();
                            $updateData['r_order'] = new Zend_Db_Expr('r_order-1');
                            $db->modify(LATEST_REVIEW,$updateData,"r_order > ".$reviewPptyArr[0]['r_order']);
                        }else{
                            
                            
                            
                            
                            $updateData = array();
                            $updateData['r_order'] = new Zend_Db_Expr('r_order+1');
                            $db->modify(LATEST_REVIEW,$updateData);
                            
                            
                            
                            $saveData = array();
                            $saveData['r_property_id'] = $chckArr[0]['id']; 
                            $saveData['r_order'] = '1'; 
                            //$saveData['r_review_id'] = $review_id; 
                            $saveData['r_status'] = '1'; 
                        
                            $db->save(LATEST_REVIEW,$saveData);
                        }
                        //-----------------------------------------------------------------
			return 1;
		}
		else
		return 0;
  }
  
 
}
?>