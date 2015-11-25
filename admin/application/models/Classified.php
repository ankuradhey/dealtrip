<?php
__autoloadDB('Db');
class Classified extends Db
{
	public function SaveCategory($dataForm)
	{

		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".CATEGORY." where cat_name='".$dataForm['cat_name']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
			if($dataForm['cat_image']!="")
			{
			$CatImage=time()."_".$dataForm['cat_image'];
			@rename(SITE_ROOT.'images/'.$dataForm['cat_image'],SITE_ROOT.'images/'.$CatImage);
			$dataInsert['cat_image']=$CatImage;
			}
		$dataInsert['cat_name']=$dataForm['cat_name'];

		$db->save(CATEGORY,$dataInsert);
		return 1;	
		}
	}
	public function UpdateCategory($dataForm,$categoryId)
	{
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".CATEGORY." where cat_name='".$dataForm['cat_name']."' and cat_id!='".$categoryId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		if($dataForm['cat_image']!="")
		{
			if($dataForm['old_cat_image']!="" && file_exists(SITE_ROOT.'images/'.$dataForm['old_cat_image']))
			{
				unlink(categories.'images/profileimgs/'.$dataForm['old_cat_image']);
			}
		$CatImage=time()."_".$dataForm['cat_image'];
		@rename(SITE_ROOT.'images/'.$dataForm['cat_image'],SITE_ROOT.'images/'.$CatImage);
		$dataUpdate['cat_image']=$CatImage;
		}
		$dataUpdate['cat_name']=$dataForm['cat_name'];
		$conditionUpdate="cat_id='".$categoryId."'";
		$db->modify(CATEGORY,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}
	//subcate here
	
		public function Saveclassified($dataForm)
		{ 			
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);


		$chkQry=$db->runQuery("select * from ".CLASSIFIED." where c_name='".$dataForm['c_name']."' and cat_id=".$dataForm['cat_id']);
		
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
		$date1=date('Y-m-d');
		$dataInsert['c_name']=$dataForm['c_name'];
		$dataInsert['cat_id']=$dataForm['cat_id'];
		$dataInsert['price']=$dataForm['price'];
		$dataInsert['location']=$dataForm['location'];
		$dataInsert['description']=$dataForm['description'];
		$dataInsert['zipcode']=$dataForm['zipcode'];
		$dataInsert['date_posted']=$date1;
		
		if($mySession->LoggedUserId==0 && $mySession->LoggedUserId=="")
		{
		 $dataInsert['user_id']=0;
		}
//		prd($dataInsert);
		$db->save(CLASSIFIED,$dataInsert);
		return 1;	
		}
	}
	public function Updateclassified($dataForm,$c_id)
	{ 
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);	

		$chkQry=$db->runQuery("select * from ".CLASSIFIED." where c_name='".$dataForm['c_name']."' and c_id=".$dataForm['cat_id']);
	
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
		$dataUpdate['c_name']=$dataForm['c_name'];
		$dataUpdate['cat_id']=$dataForm['cat_id'];
		$dataUpdate['price']=$dataForm['price'];
		$dataUpdate['location']=$dataForm['location'];
		$dataUpdate['description']=$dataForm['description'];
		$dataUpdate['zipcode']=$dataForm['zipcode'];
		$conditionUpdate="c_id='".$c_id."'";
		$db->modify(CLASSIFIED,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}


	// end of subcate here
	
	
}
?>