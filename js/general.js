// JavaScript Document
function run_timer()
{
    var today = new Date();
    var ndate=today.toString();	//April 23, 2009, 02:04:25 pm
    ndate=ndate.split('GMT');
    document.getElementById("TimeDiv").style.border='transparent';
    document.getElementById("TimeDiv").innerHTML=ndate[0];	
    setTimeout(run_timer,1000);
}

function getHeightWidht()
{
    var viewportwidth;
    var viewportheight;
    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight 
    if (typeof window.innerWidth != 'undefined')
    {
        viewportwidth = window.innerWidth,
        viewportheight = window.innerHeight
    }
    // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    else if (typeof document.documentElement != 'undefined'   && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0)
    {
        viewportwidth = document.documentElement.clientWidth;
        viewportheight = document.documentElement.clientHeight
    } 
    // older versions of IE 
    else
    {
        viewportwidth = document.getElementsById('bodyid').clientWidth;
        viewportheight = document.getElementsById('bodyid').clientHeight;
    }
    return parseInt(viewportwidth);
}

function checkall(thisid)
{
    for(var i=1;document.getElementById('check'+i);i++){
        if(document.getElementById(thisid.id).checked==true){	

            document.getElementById('check'+i).checked = true;
        }
        if(document.getElementById(thisid.id).checked==false){	

            document.getElementById('check'+i).checked = false;
        }
    }	
}

function check_check(spanid,checkid){ 
    var chkchkstat = true;
    for(var i=1; document.getElementById('check'+i); i++){
        if(document.getElementById('check'+i).checked == false){
            chkchkstat = false;   
            break;
        } 
    } 
    //alert(chkchkstat);
    if(chkchkstat == false){
        $('#'+spanid).html('');
        document.getElementById(checkid).checked = false;  
    } else {
        document.getElementById(checkid).checked = true;  
    }
    return true;
}
function checknummsp(e)
{

    evt=e || window.event;
    var keypressed=evt.which || evt.keyCode;
    //alert(keypressed);
    if(keypressed!="48" &&  keypressed!="49" && keypressed!="50" && keypressed!="51" && keypressed!="52" && keypressed!="53" && keypressed!="54" && keypressed!="55" && keypressed!="8" && keypressed!="56" && keypressed!="57" && keypressed!="45" && keypressed!="46" && keypressed!="37" && keypressed!="39" && keypressed!="9")
    {
        return false;
    }	
}

function showhidedata(show)
{
    if(show==1)
    {
        if(document.getElementById('firstdiv').style.display=="block")
        {
            document.getElementById('firstdiv').style.display="none";
        }
        else
        {
            document.getElementById('firstdiv').style.display="block";
        }
    }
}


function showhidediscp(show)
{
    if(show==1)
    {
        if(document.getElementById('dispdiv').style.display=="block")
        {
            document.getElementById('dispdiv').style.display="none";
		
        }
        else
        {
            document.getElementById('dispdiv').style.display="block";
        }
    }
}

function hrefHandler()
{
    var a=window.location.pathname;
    var b=a.match(/[\/|\\]([^\\\/]+)$/);
    var ans=confirm("Are you sure? You want to delete.");
    return ans;
}
function EmptyListbox(listBoxId)
{
    var elSel = document.getElementById(listBoxId);
    for (i = elSel.length - 1; i>=0; i--) {
        elSel.remove(i);   
    }
}
function AddOptiontoListBox(listBoxId,Value,Text)
{
    var elSel = document.getElementById(listBoxId);	
    var opt = document.createElement("option");
    elSel.options.add(opt);
    opt.text=Text;
    opt.value=Value;
}
function CollaspeExpand(divName)
{
    $('#Child'+divName).slideToggle("slow");

/*if(document.getElementById('Child'+divName).style.display=="block")
	{
		document.getElementById('Child'+divName).style.display="none";
	}
	else
	{
		document.getElementById('Child'+divName).style.display="block";
	}*/
}
//phone no
function checkLen(charlen,e)
{
    var mb= document.getElementById('phone_number').value;
    //alert(mb.length);
    evt=e || window.event;
    var keypressed=evt.which || evt.keyCode;
    //alert(keypressed);
    if(keypressed!="48" &&  keypressed!="49" && keypressed!="50" && keypressed!="51" && keypressed!="52" && keypressed!="53" && keypressed!="54" && keypressed!="55" && keypressed!="8" && keypressed!="56" && keypressed!="57" && keypressed!="45" && keypressed!="46" && keypressed!="37" && keypressed!="39" && keypressed!="9")
    {
        return false;
    }
    else
    {
	
        if(mb.length<3)
        {
            return charlen;
        }
        else if(mb.length==3 || mb.length==7 )
        {	
            var dashchar="-";
            var charlen1=charlen+dashchar;
            var charlen2=document.getElementById('phone_number').value=charlen1;
            //var charlen2=charlen1.join("-");
            //alert(charlen2);
            return charlen1;
        //return charlen1;
        }
    }
	

}


//print_r

function print_r(arr,level) {
    var dumped_text = "";
    if(!level) level = 0;

    //The padding given at the beginning of the line.
    var level_padding = "";
    for(var j=0;j<level+1;j++) level_padding += "    ";

    if(typeof(arr) == 'object') { //Array/Hashes/Objects 
        for(var item in arr) {
            var value = arr[item];

            if(typeof(value) == 'object') { //If it is an array,
                dumped_text += level_padding + "'" + item + "' ...\n";
                dumped_text += print_r(value,level+1);
            } else {
                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }
    } else { //Stings/Chars/Numbers etc.
        dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
    }
    return dumped_text;
}

function makepropertyinsurance(){
    //check if checkbox is checked
    console.log($('#insurance'),$('#insurance').attr('checked'));
    if($('#insurance').attr('checked')=='checked'){
        $('#is_insured').val('true');
    }else{
        $('#is_insured').val('false');
    }
}

//loading overlay
//element - jquery object [ $('#element') ]
//image_path - path to image i.e. absolute url **:- omit trailing slash in path
function loading(element,image_path){
        element.append('<div class="question-overlay question-overlay-box"></div><img src="'+image_path+'/fancybox_loading2x.gif" class="question-overlay loading-image">')
}

function loadingComplete(){
    $('.question-overlay').remove();
}
function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}