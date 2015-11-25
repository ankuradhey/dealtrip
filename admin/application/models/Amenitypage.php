<?php
__autoloadDB('Db');
class Amenitypage extends Db
{
	public function UpdatePage($dataForm)
	{
		global $mySession;
		$db=new Db();
		//$dataPage['page_cat_id']=$dataForm['page_cat_id'];
		$dataPage['page_title']=$dataForm['page_title'];
		$dataPage['content']=$dataForm['page_content'];
		$condition = " id = 1 ";
		
		$db->modify(AMENITY_PAGE,$dataPage, $condition);
		return 1;	
	
	}
}