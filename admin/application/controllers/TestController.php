<?php
__autoloadDB('Db');
class TestController extends AppController
{
	public function indexAction()
	{// echo 'hello'; exit();
		global $mySession;
		$db=new Db();		
		$myform=new Form_Test();
		$this->view->myform=$myform;
		$this->view->pageHeading="Upload Business Listing";		
	}

public function saveAction()
	{
		global $mySession;
		$db=new Db();
		$this->view->pageHeading="Save Business Listing";
		if ($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			$myform = new Form_Test();			
			if ($myform->isValid($request->getPost()))
			{
				$dataForm=$myform->getValues();
				$xlsfile=$dataForm['excelfile'];
				$zipfile=$dataForm['zipfile'];
				if($zipfile!="")
				{
					 $zip = new ZipArchive;
					 $res = $zip->open(SITE_ROOT."test/".$zipfile);
					 if	($res === TRUE) 
					 {
						$zip->extractTo(SITE_ROOT.'test/temp');
						$zip->close();
						$mySession->errorMsg='Data uploaded successfully';
					 } 
					 else 
					 {
						$mySession->errorMsg='Error in uploading';
					 }
				}
				error_reporting(1);
				ini_set("memory_limit","2048M");
				require_once SITE_ROOT.'Excel/reader.php';
				$data = new Spreadsheet_Excel_Reader();
				$data->read(SITE_ROOT."test/".$xlsfile);
				error_reporting(E_ALL ^ E_NOTICE);
				$SheetNo=0;
				$numRows=$data->sheets[$SheetNo]['numRows'];
				$numCols=$data->sheets[$SheetNo]['numCols'];
				for($i=2;$i<=$numRows;$i++)
				{
				 $busidata['business_title']=$data->sheets[$SheetNo]['cells'][$i][1];
				 $catename=$data->sheets[$SheetNo]['cells'][$i][2];
				 $sql="select * from ".SERVICE_CATEGORIES." where category_name='".$catename."'";
				 $result=$db->runQuery($sql);
				 if(count($result)>0)
				 {
					 $busidata['business_category_id']=$result[0]['cat_id'];
				 }
				 
				 $sql="select * from ".COUNTRIES." where country_name='".$data->sheets[$SheetNo]['cells'][$i][4]."'";
				 $result=$db->runQuery($sql);
				 if(count($result)>0)
				 {
				   $busidata['country_id']=$result[0]['country_id'];
				 }
		 $sql="select * from ".STATE." where country_id= ".$busidata['country_id']." and state_name='".$data->sheets[$SheetNo]['cells'][$i][5]."'";
				 $result=$db->runQuery($sql);
				 if(count($result)>0)
				 {
					$busidata['state_id']=$result[0]['state_id'] ;
				 }
				 $busidata['date_business_added']=date('Y-m-d H:i:s');
				 $busidata['city_name']=$data->sheets[$SheetNo]['cells'][$i][6];
 				 $businessImage=time()."_".$data->sheets[$SheetNo]['cells'][$i][3];
				 copy(SITE_ROOT."test/temp/".$data->sheets[$SheetNo]['cells'][$i][3],SITE_ROOT.'images/businesses/'.$businessImage);			 				 $busidata['zipcode']=$data->sheets[$SheetNo]['cells'][$i][7];
				 $busidata['description']=$data->sheets[$SheetNo]['cells'][$i][8];
				 $busidata['search_keywords']=$data->sheets[$SheetNo]['cells'][$i][9];
 				 $busidata['address']=$data->sheets[$SheetNo]['cells'][$i][10];
  				 $busidata['phone_number']=$data->sheets[$SheetNo]['cells'][$i][11];
				 $busidata['email_address']=$data->sheets[$SheetNo]['cells'][$i][12];
				 $busidata['Website']=$data->sheets[$SheetNo]['cells'][$i][13];
 				 $busidata['Business_image']= $businessImage;
		 		 $myLatLongData=getLatLongFromAddress($busidata['country_id'],$busidata['state_id'],$busidata['city_name'],$busidata['address']);
		 		 $explode=explode("::",$myLatLongData);
				 $Lat=$explode[0];
				 $Long=$explode[1];
				 $busidata['business_lat']=$Lat;
				 $busidata['business_long']=$Long;
				 $busidata['business_status']=1;
				 $db->save(SERVICE_BUSINESS,$busidata);
				 $counter++;
				}		     	

				$this->_redirect('test/index');	
			}
			else
			{	
				$this->view->myform = $myform;
				$this->render('index');
			}
		}
		else
		{			
			$this->_redirect('test/index');
		}

	}
	
	



}
?>