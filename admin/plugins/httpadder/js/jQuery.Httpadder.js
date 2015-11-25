/**

 * --------------------------------------------------------------------

 * jQuery customfileinput plugin

 * Author: Scott Jehl, scott@filamentgroup.com

 * Copyright (c) 2009 Filament Group 

 * licensed under MIT (filamentgroup.com/examples/mit-license.txt)

 * --------------------------------------------------------------------

 * Modified by maimairel (maimairel@yahoo.com) for use in a ThemeForest theme

 * --------------------------------------------------------------------

 */

 

$.fn.httpAdder = function(){

	//apply events and styles for file input element



	return $(this).each(function() {


		var fileInput = $(this)

			.addClass('customfile-inputs') //add class for CSS

		
			.focus(function(){

				upload.addClass('customfile-focus'); 

				fileInput.data('val', fileInput.val());

			})

			.blur(function(){ 

				upload.removeClass('customfile-focus');

				$(this).trigger('checkChange');

			 })

			 .bind('disable',function(){

				fileInput.attr('disabled',true);

				upload.addClass('customfile-disabled');

			})

			.bind('enable',function(){

				fileInput.removeAttr('disabled');

				upload.removeClass('customfile-disabled');

			})

			.bind('checkChange', function(){

					if($(".customfiles label.error").length > 0)
					{
						$(".customfiles").after($(".customfiles label.error"));
						$(".customfiles label.error").remove();			
						
					}

			})

			
			.click(function(){ //for IE and Opera, make sure change fires after choosing a file, using an async callback
				
				

				fileInput.data('val', fileInput.val());

				setTimeout(function(){


					fileInput.trigger('checkChange');

				},100);

			});

			

		//create custom control container

		var upload = $('<div class="customfiles"></div>');

		//create custom control button

		var uploadButton = $('<span class="http-button" aria-hidden="true">http://</span>').appendTo(upload);

		//create custom control feedback


		

		//match disabled state

		if(fileInput.is('[disabled]')){

			fileInput.trigger('disable');

		}

			

		

		//on mousemove, keep file input under the cursor to steal click

		

			upload/*.mousemove(function(e){

				fileInput.css({

					'left': e.pageX - upload.offset().left - fileInput.outerWidth() + 20, //position right side 20px right of cursor X)

					'top': e.pageY - upload.offset().top - 3

				});

			})
*/
			.insertAfter(fileInput)
			
			.on("click",function(event){
			
			})
			
			; //insert after the input

		

		fileInput.appendTo(upload);

	});

};