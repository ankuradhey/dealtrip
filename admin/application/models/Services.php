<?php
__autoloadDB('Db');
class Services extends Db
{
	public function SaveCategory($dataForm)
	{
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".SERVICE_CATEGORIES." where category_name='".$dataForm['cat_name']."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
			if($dataForm['cat_image']!="")
			{
			$CatImage=time()."_".$dataForm['cat_image'];
			@rename(SITE_ROOT.'images/categories/'.$dataForm['cat_image'],SITE_ROOT.'images/categories/'.$CatImage);
			$dataInsert['cat_img_path']=$CatImage;
			}
		$dataInsert['category_name']=$dataForm['cat_name'];

		$db->save(SERVICE_CATEGORIES,$dataInsert);
		return 1;	
		}
	}
	public function UpdateCategory($dataForm,$categoryId)
	{
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);
		$chkQry=$db->runQuery("select * from ".SERVICE_CATEGORIES." where category_name='".$dataForm['cat_name']."' and cat_id!='".$categoryId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;	
		}
		else
		{
		if($dataForm['cat_image']!="")
		{
			if($dataForm['old_cat_image']!="" && file_exists(SITE_ROOT.'images/categories/'.$dataForm['old_cat_image']))
			{
				unlink(categories.'images/profileimgs/'.$dataForm['old_cat_image']);
			}
		$CatImage=time()."_".$dataForm['cat_image'];
		@rename(SITE_ROOT.'images/categories/'.$dataForm['cat_image'],SITE_ROOT.'images/categories/'.$CatImage);
		$dataUpdate['cat_img_path']=$CatImage;
		}
		$dataUpdate['category_name']=$dataForm['cat_name'];
		$conditionUpdate="cat_id='".$categoryId."'";
		$db->modify(SERVICE_CATEGORIES,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}
	//subcate here
	
		public function Savesubcategory($dataForm)
		{ 				//echo 'hello'; exit();
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);

//		echo $sql="select * from ".SERVICE_CATEGORIES." where cat_id='".$dataForm['category']."'";exit();

	//	$catedata=$db->runQuery($sql);
		//$cateid=$catedata[0]['cat_id'];	
//		echo "select * from ".SUBCATE." where subcate_name='".$dataForm['subcate_name']."' and cat_id=".$dataForm['category'];exit();	


		$chkQry=$db->runQuery("select * from ".SUBCATE." where subcate_name='".$dataForm['subcate_name']."' and cat_id=".$dataForm['category']);
		
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
			if($dataForm['subcate_image']!="")
			{
			
			$SubCatImage=time()."_".$dataForm['subcate_image'];
			@rename(SITE_ROOT.'images/Subcate/'.$dataForm['subcate_image'],SITE_ROOT.'images/Subcate/'.$SubCatImage);
			$dataInsert['subcate_img_path']=$SubCatImage;
			}
		$dataInsert['subcate_name']=$dataForm['subcate_name'];
		$dataInsert['cat_id']=$dataForm['category'];
		$dataInsert['subcate_status']='0';
//		prd($dataInsert);
		$db->save(SUBCATE,$dataInsert);
		return 1;	
		}
	}
	public function Updatesubcate($dataForm,$subcateid)
	{ 
		global $mySession;
		$db=new Db();
		$dataForm=SetupMagicQuotes($dataForm);	
	//	echo "select * from ".SUBCATE." where subcate_name='".$dataForm['subcate_name']."' and subcate_id=".$dataForm['category']." cat_id!='".$subcateid."'"; exit();
		$chkQry=$db->runQuery("select * from ".SUBCATE." where subcate_name='".$dataForm['subcate_name']."' and subcate_id=".$dataForm['category']);
	//	prd($chkQry);
	
	
	
		if($chkQry!="" and count($chkQry)>0)
		{
			return 0;	
		}
		else
		{
		if($dataForm['subcate_image']!="")
		{   
			if($dataForm['old_subcat_image']!="" && file_exists(SITE_ROOT.'images/Subcate/'.$dataForm['old_subcat_image']))
			{ 
				unlink(categories.'images/Subcate/'.$dataForm['old_subcate_image']);
			}
		$CatImage=time()."_".$dataForm['subcate_image'];
		@rename(SITE_ROOT.'images/Subcate/'.$dataForm['subcate_image'],SITE_ROOT.'images/Subcate/'.$CatImage);
		$dataUpdate['subcate_img_path']=$CatImage;
		}
		$dataUpdate['subcate_name']=$dataForm['subcate_name'];
		$dataUpdate['cat_id']=$dataForm['category'];

		$conditionUpdate="subcate_id='".$subcateid."'";
//		prd($dataUpdate);
		$db->modify(SUBCATE,$dataUpdate,$conditionUpdate);
		return 1;	
		}
	}


	// end of subcate here
	public function UpdateBusiness($dataForm,$businessId)
	{
		global $mySession;
		$db=new Db();
		$chkQry=$db->runQuery("select * from ".SERVICE_BUSINESS." where business_title='".mysql_escape_string($dataForm['business_title'])."' and business_id!='".$businessId."'");
		if($chkQry!="" and count($chkQry)>0)
		{
		return 0;
		}
		else
		{
			$BusinessImagesPath="";
			for($counter=1;$counter<=10;$counter++)
			{
				$businessImage=$dataForm['old_business_image'.$counter];
				if($dataForm['business_image'.$counter]!="")
				{
					if($businessImage!="" && file_exists(SITE_ROOT.'images/events/'.$businessImage))
					{
						unlink(SITE_ROOT.'images/events/'.$businessImage);
					}
				$businessImage=time()."_".$dataForm['business_image'.$counter];
				@rename(SITE_ROOT.'images/businesses/'.$dataForm['business_image'.$counter],SITE_ROOT.'images/businesses/'.$businessImage);
				}
				if($businessImage!=""){
				$BusinessImagesPath.=$businessImage.",";
				}
			}
			if($BusinessImagesPath!="")
			{
				$BusinessImagesPath=substr($BusinessImagesPath,0,strlen($BusinessImagesPath)-1);
			}
		$myLatLongData=getLatLongFromAddress($dataForm['country_id'],$dataForm['state_id'],$dataForm['city_name'],$dataForm['address']);
		$explode=explode("::",$myLatLongData);
		$Lat=$explode[0];
		$Long=$explode[1];
		$dataUpdate['business_title']=$dataForm['business_title'];		
		$dataUpdate['business_category_id']=$dataForm['business_category_id'];
		$dataUpdate['description']=$dataForm['description'];
		$dataUpdate['search_keywords']=$dataForm['search_keywords'];
		$dataUpdate['address']=$dataForm['address'];
		$dataUpdate['city_name']=$dataForm['city_name'];
		$dataUpdate['zipcode']=$dataForm['zipcode'];
		$dataUpdate['state_id']=$dataForm['state_id'];
		$dataUpdate['country_id']=$dataForm['country_id'];
		$dataUpdate['phone_number']=$dataForm['phone_number'];
		$dataUpdate['email_address']=$dataForm['email_address'];
		$dataUpdate['website']=$dataForm['website'];
		$dataUpdate['business_image']=$BusinessImagesPath;
		$dataUpdate['business_lat']=$Lat;
		$dataUpdate['business_long']=$Long;
		$dataUpdate['business_status']='1';
		$conditionUpdate="business_id='".$businessId."'";
		$db->modify(SERVICE_BUSINESS,$dataUpdate,$conditionUpdate);
		return 1;
		}
	}
}
?>