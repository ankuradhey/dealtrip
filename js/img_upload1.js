$(document).ready(function() {
	
	// Makes sure the dataTransfer information is sent when we
	// Drop the item in the drop box.



	var tmp;	
	var z = 40;
	// The number of images to display
	var maxFiles = 24;
	var errMessage = 0;
	
	var dataObj ={};
	// Get all of the data URIs and put them in an array
	var dataArray = [];
	
	


	$(document).on("change",'#photo_res',function(e) {
			
		
		// To the dropped file
		
		//+++++++++++++++++++++  ajax +++++++++++++++++++++++//
		//+++++++++++++++++++++++++++++++++++++++++++++++++++//
		
		/*$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});*/

		$.ajaxFileUpload({
			
				url: SITEURL+'myaccount/doajaxfileupload',
				secureuri:false,
				fileElementId:'photo_res',
				dataType: 'json',
				data:{name:'logan', id:$("#pptyId").val()},
				success: function (data, status)
				{
					
				   dataObj = data;
					
					if(data.extnsn ||  data.error)
					{
						if(data.extnsn)
						$('#drop-files').html('Hey! Images only');
						else
						$('#drop-files').html(data.error);
						return false;
					}
					else
					{
						
						$('#uploaded-holder').show();

						dataArray.push(data.name);
						//showing files uploaded
						if(data.success == '1')
						{
								$('#upload-button span').html((data.number_of_files>1?data.number_of_files+" files":"1 file")+" to be uploaded");
						}
								// Place the image inside the dropzone
							z = z+40;
							image = SITEURL+"ankit_ups/"+data.name;	
							$('#dropped-files').append('<div class="image" style="left: '+z+'px; background: url('+image+'); background-size: cover;"> </div>'); 
							$('#upload-button').show().css("left",parseInt(z+100)+"px");
						
							
						
						
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		
		//return false;

		//+++++++++++++++++++++  ajax ends +++++++++++++++++++++++//
		//+++++++++++++++++++++++++++++++++++++++++++++++++++//

		
			
			
	
			
	 

	});
	
	function restartFiles() {  //process of deleting files from the server
		
		
	
		
	$("#photo_res").unbind("change");
		
		$.ajax({
				url: SITEURL+'myaccount/doajaxfileupload/delete/1',
				data:{id:$("#pptyId").val()},
				success: function (data, status)
				{
					
				}
		});
	

		
	
		// This is to set the loading bar back to its default state
		$('#loading-bar .loading-color').css({'width' : '0%'});
		$('#loading').css({'display' : 'none'});
		$('#loading-content').html(' ');
		// --------------------------------------------------------
		
		// We need to remove all the images and li elements as
		// appropriate. We'll also make the upload button disappear
		
		$('#upload-button').hide();
		$('#dropped-files > .image').remove();
		$('#extra-files #file-list li').remove();
		$('#extra-files').hide();
		$('#uploaded-holder').hide();
	
		// And finally, empty the array/set z to -40
		dataArray.length = 0;
		z = -40;
		
		return false;
	}
	
	
	$('#upload-button .upload').click(function() {
	
		$("#loading").show();

		var totalPercent = 100 / dataArray.length;
		var x = 0;
		var y = 0;
		
		$('#loading-content').html('Uploading '+dataArray[0]);
		

		$.each(dataArray, function(index, file) {	
			

			$.ajax({
				
				type: 'POST',
				url: SITEURL+'myaccount/upload',
				async:false,
				data: ({name:file}),
				success: function(data){

					
					var fileName = dataArray[index];
					++x;
	
					// Change the bar to represent how much has loaded
					$('#loading-bar .loading-color').css({'width' : totalPercent*(x)+'%'});
					
					if(totalPercent*(x) == 100) {
						// Show the upload is complete
						$('#loading-content').html('Uploading Complete!');
						
						// Reset everything when the loading is completed
						setTimeout(restartFiles, 500);
						
					} else if(totalPercent*(x) < 100) {
					
						// Show that the files are uploading
						$('#loading-content').html('Uploading '+fileName);
					
					}
					
					// Show a message showing the file URL.
					data = $.trim(data);
					var dataSplit = data.split(':');
					
					if(dataSplit[1] == 'uploaded successfully')
					{
						//$('#uploaded-files').text();
						//$('#uploaded-files').load(SITEURL+'myaccount/uploaded', function() {
							
						//});		
						$.ajax({
								type: 'GET',
								url: SITEURL+'myaccount/uploaded',
								async:false,
								success: function(data){
									data = $.trim(data);
									tmp = data.split("+++");
								}
						});
	
						if($.trim(tmp) != "")
						{	
	
							//$('#uploaded-files').each(function(index) {
								
	
								//$('#uploaded-files div').each(function(index) {
									
									for(var i=0;i<tmp.length;i++)
									{
											var tmp1 = tmp[i].split("|");
											
											if($.trim(tmp1[2]) == "")
											{	
												var title = "No Caption Set -- To set <a href='"+SITEURL+"myaccount/setcaption/id/"+tmp1[0]+"' id = 'email_link"+i+"' >Click here</a>";
												$("#img_blck"+i).removeClass("cancel");
											}
											/*else if(i == 0)
											var title = tmp1[2];*/
											else
											{	
												var title = tmp1[2]+"<br><a href='"+SITEURL+"myaccount/setcaption/id/"+tmp1[0]+"' id = 'email_link"+i+"' >Click here to change</a>";
												$("#img_blck"+i).removeClass("cancel");
											}
											
											$("#img_blck"+i).html("<ul id='mws-gallery' class='clearfix'><li style = 'margin:0;width:100%;display:inline-block;' onmouseover = 'image_caption("+i+");' onmouseout ='image_caption_remove("+i+");' ><div id = 'img_caption"+i+"' class = 'img_caption' align = 'center'>"+title+"</div><a href='javascript:void(0);' ><img id = 'img_ups' src = '"+SITEURL+"image.php?image="+SITEURL+"images/property/"+tmp1[1]+"&width=150&height=102'></a><div class = 'cone_ryt' id = 'cone_ryt"+i+"' onclick = 'deleteimage("+tmp1[0]+","+i+");' ><img src='"+SITEURL+"images/TRASH - EMPTY.png'></div></li></ul>");		//});
										
											$("#img_blck"+i).css("border","1px dashed rgba(0, 0, 0, 0.2)");
											//inserting in text box the order of images
											if(i != 0)
											$("#order_image").val($("#order_image").val()+"&img[]=blck"+i);
											else
											$("#order_image").val("img[]=blck0");
											// insertion on loading ends	
												
											$("#email_link"+i).fancybox({
											'width'				: 470,
											'height'			: 360,
											'autoScale'			: true,
											'transitionIn'		: 'none',
											'transitionOut'		: 'none',
											'type'				: 'iframe'
											});
									
									}
									
														$('.customfile-button').text("Browse");
		
							//});
		
						}
					
					}
						
				}
			});
		});
		
		return false;
	});
	// Just some styling for the drop file container.
	
	
	// For the file list
	$('#extra-files .number').toggle(function() {
		$('#file-list').show();
	}, function() {
		$('#file-list').hide();
	});
	
	$('#dropped-files #upload-button .delete').click(restartFiles);
	
	// Append the localstorage the the uploaded files section
	//if(window.localStorage.length > 0) {
		$('#uploaded-files').show();
		/*
		for (var t = 0; t < window.localStorage.length; t++) {
			var key = window.localStorage.key(t);
			var value = window.localStorage[key];
			// Append the list items
			if(value != undefined || value != '') {
				$('#uploaded-files').append(value);
			}
		}
	} else {
		$('#uploaded-files').hide();
	}*/
});