function temp_show(noticeType, text) {

	if (noticeType == "info") {
		$("notice-info").html(text);
		$("#notice-info").show();
		time = setTimeout(function() {
			$("notice-info").hide();
		}, 2000);
	} else if (noticeType == "error") {
		$("notice-error").html(text);
		$("#notice-error").show();
		time = setTimeout(function() {
			$("notice-error").hide();
		}, 2000);
	} else if (noticeType == "success") {
		$("notice-success").html(text);
		$("#notice-success").show();
		time = setTimeout(function() {
			$("notice-success").hide();
		}, 2000);
	} else if (noticeType == "warning") {
		$("notice-warning").html(text);
		$("#notice-warning").show();
		time = setTimeout(function() {
			$("notice-warning").hide();
		}, 2000);
	}
}
function setOperation(div, state) {
	if (state == "loading") {
		$(div).addClass('loading');
		$(div).removeClass('error');
		$(div).removeClass('success');
	} else if (state == "error") {
		$(div).removeClass('loading');
		$(div).addClass('error');
		$(div).removeClass('success');
	} else if (state == "success") {
		$(div).removeClass('loading');
		$(div).removeClass('error');
		$(div).addClass('success');
	}
}
function isloading(state) {
	if (state) {
		$('#overlay').show();
	} else {
		$('.overlay-bg, .overlay-content').hide();
		$('#overlay').hide();
	}

}
function getForumDetails(forumId,courseId,userId){
  if(typeof forumId=='undefined'|| forumId<1){
    alert("forumId("+forumId+") not set");
    return false;
  }
  getContentDetails('content/discussion',forumId,courseId,userId);


}

function getDiscussionDetails(discussionId,forumId,courseId,userId){
  if(typeof forumId=='undefined'|| forumId<1){
    alert("forumId("+forumId+") not set");
    return false;
  }
  getContentDetails('content/discussion',forumId,courseId,userId,'&discussion='+discussionId);


}
function getContentDetails(contentType, contentId,courseId,userId, extraArgs){
  if(typeof contentType=='undefined'||contentType==""){
    alert("contentType("+contentType+") not defined");
    return false;
  }
  if(typeof contentId=='undefined' || contentId<1){
    alert("contentId("+contentId+") not set");
    return false;
  }
  if(typeof courseId=='undefined' || courseId<1){
    alert("courseId("+courseId+") not set");
    return false;
  }
  if(typeof userId=='undefined' || userId<1){
    alert("userId("+userId+") not set");
  }
  if(typeof extraArgs=='undefined'){
    extraArgs = "";
  }

  getPage(CONTEXT_ROOT+"/"+contentType+".php?id="+contentId+"&course="+courseId+"&user="+userId+extraArgs);
}

function submitForm(form,url,method){
	if (typeof form == 'undefined' || form == "") {
		alert('No form specified');
		return false;
	}else{
		if(typeof url == 'undefined' || url == ""){
                  url = $("#"+form)[0].action;
                }
                if(typeof method == 'undefined' || method ==""){
                method = $("#"+form)[0].method;
		}
	}
//console.log($("#"+form).serialize());
	isloading(true);
            
		$.ajax({
		url : CONTEXT_ROOT+"/"+url,
		type : method,
		dataType : "html",
		data : $("#"+form).serialize(),
		success : function(data) {
			var xmlDoc = $.parseXML(data);
			$xml = $(xmlDoc);
			console.log($xml);
			$("#main").replaceWith($xml.find('#main'));

			
			isloading(false);
		},
		fail : function() {
			temp_show('error', 'Unable to submit form');
			isloading(false);
		}
	});
	
}
function getDiv(div,url,method){
if(typeof method == 'undefined' || method ==""){
                method = 'GET';
}

        $.ajax({
                url : url,
                type : method,
                dataType : "html",
                cache : false,
                beforeSend : function() {
                        isloading(true);
                },
                success : function(data) {
                        var xmlDoc = $.parseXML(data);
                        $xml = $(xmlDoc);
                        console.log($xml);
                        if(div=="new"){
                          var new_div = document.createElement('div');
                          new_div.attr('id', 'overlay_div');
                          $("#"+main).append(new_div);
                          
                        }else{
                          $("#"+div).replaceWith($xml.find('#'+div));
                        }
                        isloading(false);

                },
                fail : function() {
alert('fail');
                        temp_show('error', 'Unable to Load '+url);
                        isloading(false);
                }
        });

}

function newPostForm(user,course,forum, discussion){
  if(typeof forum === 'undefined' || typeof user === 'undefined'
     || typeof course === 'undefined'){
    alert("Malformed call to create new Post Form");
    return;
  }
  if(typeof discussion === 'undefined'){
    discussion = 0;
  }
  var formHtml = "<div id='newPost' class='overlay-content'>"+
        "<form id='post_form' name='post_form' action=''>"+
        "<input type='hidden' name='id' id='id' value='"+forum+"' />"+
        "<input type='hidden' name='course' id='course' value='"+course+"'/>"+
        "<input type='hidden' name='user' id='user' value='"+user+"'/>"+
        "<input type='hidden' name='discussion' id='discussion' value='"+discussion+"' />"+
        "<input type='hidden' name='action' id='action' value='add' />"+
        "<div class='block form_item'>"+
        "<div class='block_head'>Add a new ";
       formHtml += ((discussion===0)?"Discussion":"Post");
       formHtml += "</div><textarea rows='5' name='text' id='text'></textarea>"+
        "<div class='form_item'>"+
	"<button type='button' id='submitP' name='submitP' onclick='submitForm(\"post_form\",\"content/discussion.php\",\"get\")'>Submit</button>"+
        "<button type='button' onclick='javascript:closeOverlay()' class='close-btn'>Close</button>"+
        "</div>"+
        "</form>"+
        "</div>";
$('#olcontent').html(formHtml);	

        var docHeight = $(document).height(); //grab the height of the page
        var scrollTop = $(window).scrollTop(); //grab the px value from the top of the page to where you're scrolling
        $('.overlay-bg').show().css({'height' : docHeight}); //display your popup and set height to the page height
        $('#newPost').show(); // show the appropriate popup
        $('#newPost').css({'top': scrollTop+20+'px'}); //set the content 20px from the window top


}

function getPage(url,method) {

if(typeof url =='undefined' || url ==""){
temp_show('error','No URL defined, redirecting to home');
url = "/";
}

if(typeof method =='undefined' || method ==""){
method = "GET";
}

	$.ajax({
		url : url,
		type : method,
		dataType : "html",
		cache : false,
		beforeSend : function() {
			// $('#main').hide(2000);
			isloading(true);
		},
		success : function(data) {
			var xmlDoc = $.parseXML(data);
			$xml = $(xmlDoc);
			//console.log($xml);
                        $("#nav_opts").replaceWith($xml.find('#nav_opts'));
			$("#main").replaceWith($xml.find('#main'));
                        // $('#main').html(value);
			// $('#main').show(2000);
			isloading(false);

		},
		fail : function() {
alert('fail');
			temp_show('error', 'Unable to Load '+url);
			// $('#main').show(2000);
			isloading(false);
		}
	});
}
function addAnsFields(){
count = parseInt($('#answers').find("#ans_count").val(),10);
ansHtml = "<div id='answer_"+count+"' name='answer_"+count+"'>"
          +  "<label>"+(count+1)+".</label>"
          +  "<div class='form_item'>"
          +  "<label>Answer</label>"
          +  "<input type='text' name='ans_text_"+count+"' id='ans_text_"+count+"' />"
          +  "</div>"
          +  "<div class='form_item'>"
          +  "<label>Fraction</label>"
          +  "<input type='text' name='ans_fraction_"+count+"' id='ans_fraction_"+count+"' />%"
          +  "</div>"
          +  "<div class='form_item'>"
          +  "<label>Feedback</label>"
          +  "<textarea id='ans_feedback_"+count+"' name='ans_feedback_"+count+"'></textarea>"
          +  "</div>"
          +  "</div>";


$('#answers').append(ansHtml);
$('#answers').find('#ans_count').val(count+1);
}
function toggleDiv(divId){
$('#'+divId).toggle();
}
/*$(function() {
$( "#tabs" ).tabs();
});
*/

/*
 * Create and display overlay forms
 */
function closeOverlay(){
 alert('CLOSING');
        $('.overlay-bg, .overlay-content').hide(); // hide the overlay
        $('#newPost').hide();
}
$(document).ready(function(){

    $('.close-btn').click(function(){
        alert('CLOSING');
        $('.overlay-bg, .overlay-content').hide(); // hide the overlay
	$('#newPost').hide();
    });
  
    // hides the popup if user clicks anywhere outside the container
//    $('.overlay-bg').click(function(){
//        $('.overlay-bg, .overlay-content').hide();
//    })
  
});

