<?php
__autoloadDB('Db');
class Currencies extends Db
{
	
	public function saveCurrency($dataForm,$currencyId="")
	{
		global $mySession;
		$db=new Db();
		
		$data_update['currency_name']=trim($dataForm['currency_name']);
		$data_update['currency_code']=strtoupper(trim($dataForm['currency_code']));
		$data_update['exchange_rate'] = $dataForm['exchange_rate'];
		
		$maxquery = $db->runQuery("select max(currency_order) as MAX from ".CURRENCY." ");

		
		
		if($currencyId==""){$currencyId=0;}
		
		$chkQry2=$db->runQuery("select * from ".CURRENCY." where currency_code='".mysql_escape_string(trim($data_update['currency_code']))."' and currency_id!=".$currencyId);
		if($chkQry!="" and count($chkQry)>0)
			{ return 0;}
		else
			{
				if($currencyId>0)//insert
					{
						$condition='currency_id='.$currencyId;		
						$db->modify(CURRENCY,$data_update,$condition);
						return 1;
					}
				else//update
					{
						$data_update['currency_order'] = $maxquery[0]['MAX']+1;
						$db->save(CURRENCY,$data_update);
						return 1;
					}
			}
			
		 
	}
	
	#-----------------------------------------------------------#
	# Delete Currency Function
	
	// Here delete currency record from CURRENCY table.
	
	#-----------------------------------------------------------#
	
  public function deleteCurrency($currencyId)
  {
   	 global $mySession;
	 $db=new Db();
	 $condition1="currency_id='".$currencyId."'"; 
	 $db->delete(CURRENCY,$condition1);
	 
  }
	
	
	
}
?>