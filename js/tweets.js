/**
 * TweetQuote (tweetquote.co.uk)
 *
 * The MIT License
 * 
 * Copyright (c) 2009 Paul James Campbell (tweetquote.co.uk)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
var tweetquote = function () {
	
	// Variables
	var config = {
		'refreshRate'			:  10000, 
		'phrase'					: 	false, 		
		'username'				: 	false, 
		'hashtag'				: 	false,
		'geocode'				: 	false, 
		'advancedquery'		: 	false,
		'includeauthor'		: 	true,
		'stripurls'				: 	true, 
		'stripTwitterTags'	: 	true,
		'classname'				:  false, 
		'lang'					: 	"en", // http://en.wikipedia.org/wiki/ISO_639-1
		'animationtype'		:  'fade', 
		'animationspeed'		:  'slow', 
		'defaulttext'			:  "Deal a Trip Tweets",
		'className'             : 'left_deal_clmn',
		'tweettextClass'		: 'left_deal_detail'
	};
	
	var rootId = 'tweet_quote', wrapId = 'tweet_quote_wrapper', textId = "tweet_quote_text", authorId = 'tweet_quote_author', rootDom, quoteDom, intId, sinceId, handlerTextParser, handlerQuoteUpdate; 
	
	// Construct
	(function () {
		if( !window['jQuery'] ) {
			// Load jQuery from Google API
			document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>');
		}		
	})();
	
	
	// Initiate
	function init () { 
		try {
			if( config.phrase || config.username || config.hashtag || config.advancedquery ) {
				
				document.write('<div id="' + rootId + '" class = "'+config.className+'"></div><div id="' + rootId + '1" class = "'+config.className+'"></div><div id="' + rootId + '2" class = "'+config.className+'"></div>');
				rootDom = $("#" + rootId);
				rootDom1 = $("#" + rootId+"1");
				rootDom2 = $("#" + rootId+"2");
				//rootDom3 = $("#" + rootId+"3");
				
				if( config.classname.constructor === String ) {
					rootDom.addClass( config.classname );
				}
				
				if( config.defaulttext.constructor === String ) {
					rootDom.html( '<div id="' + wrapId + '" class = "'+config.tweettextClass+'"><span id="' + textId + '">' + config.defaulttext + '</span></div>' );
					rootDom1.html( '<div id="' + wrapId + '" class = "'+config.tweettextClass+'"><span id="' + textId + '">' + config.defaulttext + '</span></div>' );
					rootDom2.html( '<div id="' + wrapId + '" class = "'+config.tweettextClass+' noborder"><span id="' + textId + '">' + config.defaulttext + '</span></div>' );
					//rootDom3.html( '<div id="' + wrapId + '" class = "'+config.tweettextClass+'"><span id="' + textId + '">' + config.defaulttext + '</span></div>' );
				}
				
			}
		} catch ( e ) {
			logError( e );
			return;
		}		
		
		getTweet();
		//initRefresh();
	}
	
	function initRefresh () {
		intId = setInterval( getTweet, config.refreshRate );
	}
	
	function stopRefresh () {
		clearInterval( intId );
	}
	
	function getTweet () {
		// http://apiwiki.twitter.com/Search+API+Documentation
		var api = 'http://search.twitter.com/search.json?rpp=4&callback=tweetquote.onApiCallback&lang=' + config.lang + "&"; // 140 character limit
		
		if( sinceId ) {
			api += 'since_id=' + sinceId + '&';
		}
		
		
		// Construct API call
		if( config.phrase ) {
			api += 'q=' + config.phrase;
		} else if ( config.username ) {
			api += 'q=from%3A' + config.username;
			
		} else if ( config.hashtag ) {
			api += 'q=%23' + config.hashtag;
		} else if ( config.advancedquery ) {
			api += 'q=' + config.advancedquery;
		}
		
		if( config.geocode ) {
			api += "&geocode=" + config.geocode;
		}
		
		try {

			rootDom.before('<script type="text/javascript" src="' + api + '"></script>');
		} catch ( e ) {
			logError ( e );
		}
	}
	
	function filterTweet ( json ) { 
		if( json.results && json.results.length > 0 ) {
			
		for(var i=0;i<json.results.length;i++)
		{	
				var tweet = json.results[i];
				sinceId = tweet.id;

				html = '<div id="' + wrapId + '">' + getTweetText( tweet ) + '</div>';
				
				if(i == 2)
				$(wrapId).addClass("noborder");

				
				switch(i)
				{
					case 0:		if(rootDom.html() != html) {
									rootDom.html( html );
									//wrapperNode = $('#' + wrapId);
									//wrapperNode.show();									
								}
								break;

					case 1:   if(rootDom1.html() != html) {
									rootDom1.html( html );
									//wrapperNode = $('#' + wrapId);
									//wrapperNode.show();									
								}
								break;	
					case 2:	if(rootDom2.html() != html) {
									rootDom2.html( html );
									$("#tweet_quote2").addClass("noborder");
									
									//wrapperNode = $('#' + wrapId);
									//wrapperNode.show();									
								}
								break;
			
				}
				
				
				/*if(rootDom.html() != html) {
					
					rootDom.html( html );
					wrapperNode = $('#' + wrapId);
					wrapperNode.hide();
					
					switch( config.animationtype ) {
						case 'fade' :
							wrapperNode.fadeIn( config.animationspeed );
							break;
						case 'slide' :
							wrapperNode.slideIn( config.animationspeed );
							break;
						default :
							// Nothing
							wrapperNode.show();
							break;
					}
				*/
					
					
					
					
					if( handlerQuoteUpdate ) {
						handlerQuoteUpdate( wrapperNode );
					}
			}//end for loop
			
				
			
		}
	}
	
	function getTweetText ( tweet ) {
		var text = tweet.text;
		var image = tweet.profile_image_url;
		if ( config.stripurls ) {
			var pattern_url = /https?:\/\/([\-\w\.]+)+(:\d+)?(\/([\w\/_\.]*(\?\S+)?)?)?/ig;
			text = text.replace( pattern_url , '');
//			image = image.replace( pattern_url , '');
		}
		
		if( config.stripTwitterTags ) { // Still working on
			var pattern_tags = /RT|\'|\"|/ig;
			text = text.replace( pattern_tags , '');
	//		image = image.replace( pattern_tags , '');			
		}
		
		var html =    '<div class="left_deal_pic"><img src="'+image+'"></div>';
		
		if( config.includeauthor ) {
			html += '<div class="left_deal_detail"><div class = "left_deal_head" >'+tweet.from_user + '</div>';
		}

		html += '<div class = "left_deal_dis" id="' + textId + '">' + text + '</div>';
		html +='<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		html += '<div class = "left_deal_social"><div class ="left_deal_fb"><div class="fb-like" data-href="http://www.facebook.com/pages/Ankitdealatrip-Community/308209855947034" data-send="false" data-layout="button_count" data-width="200" data-show-faces="false"></div></div><div class ="left_deal_twit"><a href="https://twitter.com/anku_radhe" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @twitterapi</a></div></div></div>';
		
		if( handlerTextParser ) {
			return( handlerTextParser( html, tweet ) );
		}
		return( html.replace(/^\s+|\s+$/g, '') );
	
	
	}
	
	function logError ( e ) {
		// For clued up Firefox/Safari developers
		if(console && console.log) {
			console.log("TQ: " + e);
		}
	}
	
	return {
		
		load : function ( options ) {
			
			if( options.constructor === String) {
				// Assume basic string, add as phrase
				config.phrase = options;
			} else {
				$.extend(config, options);
			}
			
			init();
		},
		
		stop : function () {
			stopRefresh();
		},
		
		// Public method for Twitter search API (don't use)
		onApiCallback : function ( json ) {
			filterTweet( json );
		},
		
		// Custom callback handlers
		onTextParse : function ( func ) {
			handlerTextParser = func;
		},
		
		onQuoteUpdate : function ( func ) {
			handlerQuoteUpdate = func;
		}
		
	};
	
}();
window.tweetquote = tweetquote; // Ensure global scope





var tweetquoteOther = function () {
	
	// Variables
	var config = {
		'refreshRate'			:  10000, 
		'phrase'					: 	false, 		
		'username'				: 	false, 
		'hashtag'				: 	false,
		'geocode'				: 	false, 
		'advancedquery'		: 	false,
		'includeauthor'		: 	true,
		'stripurls'				: 	true, 
		'stripTwitterTags'	: 	true,
		'classname'				:  false, 
		'lang'					: 	"en", // http://en.wikipedia.org/wiki/ISO_639-1
		'animationtype'		:  'fade', 
		'animationspeed'		:  'slow', 
		'defaulttext'			:  "Hold up, we're just loading some tweets."
	};
	
	var rootId = 'tweet_quote_other', wrapId = 'tweet_quote_wrapper_other', textId = "tweet_quote_text_other", authorId = 'tweet_quote_author_other', rootDom, quoteDom, intId, sinceId, handlerTextParser, handlerQuoteUpdate; 
	
	// Construct
	(function () {
		if( !window['jQuery'] ) {
			// Load jQuery from Google API
			document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>');
		}		
	})();
	
	
	// Initiate
	function init () { 
		try { 
			if( config.phrase || config.username || config.hashtag || config.advancedquery ) {
				
				document.write('<div id="' + rootId + '"></div>');
				rootDom = $("#" + rootId);
				
				if( config.classname.constructor === String ) {
					rootDom.addClass( config.classname );
				}
				
				if( config.defaulttext.constructor === String ) {
					rootDom.html( '<div id="' + wrapId + '"><span id="' + textId + '">' + config.defaulttext + '</span></div>' );
				}
				
			}
		} catch ( e ) {
			logError( e );
			return;
		}		
		
		getTweet();
		initRefresh();
	}
	
	function initRefresh () {
		intId = setInterval( getTweet, config.refreshRate );
	}
	
	function stopRefresh () {
		clearInterval( intId );
	}
	
	function getTweet () {
		// http://apiwiki.twitter.com/Search+API+Documentation
		var api = 'http://search.twitter.com/search.json?rpp=1&callback=tweetquoteOther.onApiCallback&lang=' + config.lang + "&"; // 140 character limit
		if( sinceId ) {
			api += 'since_id=' + sinceId + '&';
		}
		
		// Construct API call
		if( config.phrase ) {
			api += 'q=' + config.phrase;
		} else if ( config.username ) {
			api += 'q=from%3A' + config.username;
		} else if ( config.hashtag ) {
			api += 'q=%23' + config.hashtag;
		} else if ( config.advancedquery ) {
			api += 'q=' + config.advancedquery;
		}
		
		if( config.geocode ) {
			api += "&geocode=" + config.geocode;
		}
		
		try {
			rootDom.before('<script type="text/javascript" src="' + api + '"></script>');
		} catch ( e ) {
			logError ( e );
		}
	}
	
	function filterTweet ( json ) { 
		if( json.results && json.results.length > 0 ) {
									
					for(var i=0;i<json.results.length;i++)
					{				
									var tweet = json.results;
									
									sinceId = tweet[i].id;
									html = '<div id="' + wrapId + '">' + getTweetText( tweet[i] ) + '</div>';
									
									if(rootDom.html() != html) {
										
										rootDom.html( html );
										wrapperNode = $('#' + wrapId);
										wrapperNode.hide();
										
										switch( config.animationtype ) {
											case 'fade' :
												wrapperNode.fadeIn( config.animationspeed );
												break;
											case 'slide' :
												wrapperNode.slideIn( config.animationspeed );
												break;
											default :
												// Nothing
												wrapperNode.show();
												break;
										}
										
										
										if( handlerQuoteUpdate ) {
											handlerQuoteUpdate( wrapperNode );
										}
						}// end for loop
									
									
			}
		}
	}
	
	function getTweetText ( tweet ) {
		var text = tweet.text;
		if ( config.stripurls ) {
			var pattern_url = /https?:\/\/([\-\w\.]+)+(:\d+)?(\/([\w\/_\.]*(\?\S+)?)?)?/ig;
			text = text.replace( pattern_url , '');
		}
		
		if( config.stripTwitterTags ) { // Still working on
			var pattern_tags = /RT|\'|\"|/ig;
			text = text.replace( pattern_tags , '');
		}
		
		var html = '<span id="' + textId + '">' + text + '</span>';
		if( config.includeauthor ) {
			html += ' <span id="' + authorId + '">from ' + '<a href="http://www.twitter.com/' + tweet.from_user + '">' + tweet.from_user + '</a></span>';
		}		
		if( handlerTextParser ) {
			return( handlerTextParser( html, tweet ) );
		}
		return( html.replace(/^\s+|\s+$/g, '') );
	}
	
	function logError ( e ) {
		// For clued up Firefox/Safari developers
		if(console && console.log) {
			console.log("TQ: " + e);
		}
	}
	
	return {
		
		load : function ( options ) { 
			
			if( options.constructor === String) {
				// Assume basic string, add as phrase
				config.phrase = options;
			} else {
				$.extend(config, options);
			}
			
			init();
		},
		
		stop : function () {
			stopRefresh();
		},
		
		// Public method for Twitter search API (don't use)
		onApiCallback : function ( json ) {
			filterTweet( json );
		},
		
		// Custom callback handlers
		onTextParse : function ( func ) {
			handlerTextParser = func;
		},
		
		onQuoteUpdate : function ( func ) {
			handlerQuoteUpdate = func;
		}
		
	};
	
}();
window.tweetquoteOther = tweetquoteOther; // Ensure global scope