<?php
__autoloadDB('Db');
class System extends Db
{
	public function SaveConfiguration($dataForm)
	{
		global $mySession;
		$db=new Db();
		
	
		$dataUpdate = array();
		$dataUpdate['site_title']=$dataForm['site_title'];
		$dataUpdate['site_description']=$dataForm['site_description'];
		$dataUpdate['site_keyword']=$dataForm['site_keyword'];
		$dataUpdate['admin_email']=$dataForm['admin_email'];
		$dataUpdate['paypal_email']=$dataForm['paypal_email'];
		$dataUpdate['admin_fullname']=$dataForm['admin_name'];
		//$dataUpdate['currency_symbol']=$dataForm['currency_symbol'];
		$conditionUpdate="admin_id= 1 ";
		$db->modify(ADMINISTRATOR,$dataUpdate,$conditionUpdate);
		//($dataUpdate);
		
		return true;
	}
	public function UpdateTemplate($dataForm,$templateId)
	{
		global $mySession;		
		$db=new Db();
		$dataUpdate['email_subject']=$dataForm['email_subject'];
		$dataUpdate['email_body']=$dataForm['email_body'];
		$conditionUpdate="template_id='".$templateId."'";
		$db->modify(EMAIL_TEMPLATES,$dataUpdate,$conditionUpdate);
		
		//Code to send newsletter email to subscribed members
		if(isset($_REQUEST['save_or_send']) && $_REQUEST['save_or_send']=='2')
		{
			$newsuserData=$db->runQuery("select * from ".USERS." where newsletter_subscribe='1'");
			if($newsuserData!="" and count($newsuserData)>0)
			{
				foreach($newsuserData as $key=>$valueUserData)
				{
					SendEmail($valueUserData['email_address'],$dataForm['email_subject'],$dataForm['email_body']);
				}
			}			
		}
		//Code to send newsletter email to subscribed members
		return true;
	}
}
?>