<?php
__autoloadDB('Db');
class Categories extends Db
{
	public function SaveCategory($dataForm)
	{
		
		 
					
		global $mySession;
		$db=new Db();
		 
		 
		$chkQry=$db->runQuery("select * from ".CATEGORIES." where perent_id=".$dataForm['perent_id']." and category_name='".$dataForm['category_name']."'");
		if($chkQry!="" and count($chkQry)>0)
			{return 0;	}
		else
			{
				if(trim($dataForm['photo_path'])!=="")
				{
					$Arrpath=explode(".",$dataForm['photo_path']);
					$imageNewName=time()."_cat.".$Arrpath[1];
					rename(SITE_ROOT.'images/category/'.$dataForm['photo_path'],SITE_ROOT.'images/category/'.$imageNewName);
					$dataForm['cat_image']=$imageNewName;
				}
				unset($dataForm['photo_path']);
				
				$db->save(CATEGORIES,$dataForm);
				return 1;	
			}
	}
	public function UpdateCategory($dataForm,$category_id)	
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".CATEGORIES." where category_id!=".$category_id." and perent_id=".$dataForm['perent_id']." and category_name='".$dataForm['category_name']."'");

		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			 
			if(trim($dataForm['photo_path'])!=="")
				{
					$Arrpath=explode(".",$dataForm['photo_path']);
					$imageNewName=time()."_cat.".$Arrpath[1];
					rename(SITE_ROOT.'images/category/'.$dataForm['photo_path'],SITE_ROOT.'images/category/'.$imageNewName);
					$dataForm['cat_image']=$imageNewName;
				}
			unset($dataForm['photo_path']);
			$update_condition="category_id='".$category_id."'";
			$db->modify(CATEGORIES,$dataForm,$update_condition);
			return 1;	
		}
	}
}
?>