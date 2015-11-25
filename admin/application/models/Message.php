<?php
__autoloadDB('Db');
class Message extends Db
{
	
	public function sendMessage($dataForm)
	{
		global $mySession;
		$db=new Db();
		
		foreach($dataForm['box2View'] as $val)
		{
			$dataMessage['receiver_id'] = $val;
			$dataMessage['sender_id'] = 0;
			$dataMessage['message_subject'] = $dataForm['message_subject'];
			$dataMessage['message_text'] = $dataForm['message_text'];
			$dataMessage['date_message_sent'] = date('Y-m-d H:i:s');
			$db->save(MESSAGES,$dataMessage);
			
							
			$user = $db->runQuery("select * from  ".USERS." where user_id=".$dataMessage['receiver_id']."");							
			$receiveuser = $user[0]['first_name'].'&nbsp;'.$user[0]['last_name'];
			$useremail=$user[0]['email_address'];
											
			$templateData=$db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='8'");
	
			$messageText=$templateData[0]['email_body'];
			$subject = $dataForm['message_subject'];
			
			$messageText = str_replace("[NAME]",$receiveuser,$messageText);
			$messageText = str_replace("[SITENAME]",SITE_NAME,$messageText);
			
			SendEmail($useremail,$subject,$messageText);
			return 1;
		}
		
		return 0;
		
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