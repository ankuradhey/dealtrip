<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="top" class="main_heading"><?=$this->pageHeading?></td>
</tr>
<tr>
<td align="left" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="center" valign="middle"><form name="myform" id="myform" action="<?php echo $this->url(array('controller'=>'classified', 'action'=>'updateclassified','c_id'=>$this->c_id), 'default', true)?>" method="post" enctype="multipart/form-data"><table width="100%" cellpadding="2" cellspacing="2">
<tr>
<td width="12%" height="54" align="left" valign="middle">Category Name :</td>
<td width="88%" align="left" valign="middle"><?=$this->myform->cat_id?></td>
</tr>
<tr>
<td height="66" align="left" valign="middle">Posting Title:</td>
<td align="left" valign="middle"><?=$this->myform->c_name?></td>
</tr>
<tr>
<td height="52" align="left" valign="middle">Price :</td>
<td align="left" valign="middle"><?=$this->myform->price?></td>
</tr>
<tr>
<td height="60" align="left" valign="middle">Location:</td>
<td align="left" valign="middle"><?=$this->myform->location?></td>
</tr>
<tr>
<td height="101" align="left" valign="middle">Description:</td>
<td align="left" valign="middle"><?=$this->myform->description?></td>
</tr>
<tr>
<td align="left" valign="top">&nbsp;</td>
<td align="left" valign="top"><input type="submit" name="Submit" id="Submit" value="Submit" class="myButton" /></td>
</tr>
</table></form></td>
</tr>
</table>
