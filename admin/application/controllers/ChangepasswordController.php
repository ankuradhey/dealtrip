<?php
__autoloadDB('Db');
class ChangepasswordController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$db=new Db();
		$adminData=$db->runQuery("select * from administrator where admin_id='1'");
		$this->view->adminData=$adminData[0];
		$myform = new Form_Changepassword();
		
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{		
				$dataForm=$myform->getValues();
				if($dataForm['new_password']!=$dataForm['confirm_new_password'])
				{
					$mySession->errorMsg ="New Password and Confirm new password Should be same."; 
				}
				else
				{
					$data_update['admin_password']=md5($dataForm['new_password']);
					$condition="admin_id='".$adminData[0]['admin_id']."'";
					$db->modify('administrator',$data_update,$condition);
					$mySession->sucessMsg ="Password changed successfully."; 
				}				
			}
		}
		$this->view->myform=$myform;
	}
	 
}
?>