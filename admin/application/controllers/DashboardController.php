<?php
__autoloadDB('Db');

class DashboardController extends AppController
{
	public function indexAction()
	{
		global $mySession;
		$db = new Db();
		
		$thisUserCount=$db->runQuery('Select count(user_id) as _count from '.USERS.'  as u  ');
		$this->view->userCount=$thisUserCount;
		
		
		
		$thisUserCount=$db->runQuery('Select count(user_id) as _count from '.USERS.'  as u  where u.uType="2" ');
		$this->view->userCountOwner=$thisUserCount;
		$thisUserCount=$db->runQuery('Select count(user_id) as _count from '.USERS.'  as u where u.uType="1" ');
		$this->view->userCountCustomer=$thisUserCount;
		
		
		$getUsersQ="select * from ".USERS." as u  where u.uType='1' order by u.user_id DESC LIMIT 0,10 ";
		$getUsers=$db->runQuery($getUsersQ);
		$this->view->recentUsers1=$getUsers;
		
		
		$getUsersQ="select * from ".USERS." as u  where u.uType='2' order by u.user_id DESC LIMIT 0,10 ";
		$getUsers=$db->runQuery($getUsersQ);
		$this->view->recentUsers2=$getUsers;
		
		//list of properties
		$pptyArr = $db->runQuery('Select count(id) as _count from '.PROPERTY.'  as u where status != "0" ');
		$this->view->pptyCount = $pptyArr;
		
		$pptyArr = $db->runQuery('Select * from '.PROPERTY.' left join '.GALLERY.' on '.GALLERY.'.property_id = '.PROPERTY.'.id   where status != "0"  group by property_id order by '.PROPERTY.'.id desc limit 0,10');
		$this->view->pptyArr = $pptyArr;
		
		//list of booked properties
		$pptyArr = $db->runQuery('Select count(booking_id) as _count from '.BOOKING.'   ');
		$this->view->bookpptyCount = $pptyArr;
		
		$pptyArr = $db->runQuery('Select * from '.BOOKING.' inner join '.PROPERTY.' on '.BOOKING.'.property_id = '.PROPERTY.'.id left join  '.GALLERY.' on '.GALLERY.'.property_id = '.PROPERTY.'.id  group by '.PROPERTY.'.id order by '.BOOKING.'.booking_id desc limit 0,10');
		
                //prd($pptyArr);
                $this->view->bookArr = $pptyArr;
                
                
		//list of reviews and comments
		$reviewArr = $db->runQuery('Select count(review_id) as _count from '.OWNER_REVIEW.'	where review_status = "1" and parent_id = 0  ');
		$this->view->reviewCount = $reviewArr;
		
		$reviewArr = $db->runQuery('Select * from '.OWNER_REVIEW.' where parent_id = 0 order by review_id desc  limit 0,10');
		$this->view->reviewArr = $reviewArr;		
		
	}

}
?>