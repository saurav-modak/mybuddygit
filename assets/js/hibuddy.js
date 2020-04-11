$(document).ready(function() {

	//Button for profile post
	$('#submit_profile_post').click(function(){
		
		$.ajax({
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: $('form.profile_post').serialize(),
			success: function(msg) {
				$("#post_form").modal('hide');
				location.reload();
			},
			error: function() {
				alert('Failure');
			}
		});
	});

	$('#search').click(function(){
        if(window.matchMedia("(min-width: 200px)").matches){
            $('#search_text_input').animate({width: '250px'},500);
        }
    });
    $('.button_holder').on('click',function(){
        document.search_form.submit();
    });
});

$message_dropdown_status = "hide";

$(document).click(function(e){
	//This hides the search result dropdown when clicked outside
	if(e.target.class != "search_results" && e.target.id != "search_text_input"){
		$('.search_results').html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}

	//This hides the navbar message dropdown when clicked outside
	if(e.target.class != "dropdown_data_window"){
		$('.dropdown_data_window').html("");
		$('.dropdown_data_window').css({"padding" : "0px", "height" : "0px"});
		$(".see_all_messages").html("");
		$message_dropdown_status = "hide";

	}
})

function getUsers(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}

//message badge
function getDropdownData(user, type) {
	

	
	//if($(".dropdown_data_window").css("height") == "2px") {
	if($message_dropdown_status == "hide") {

		var pageName;
		var see_all = "";

		if(type == 'notification') {
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
			see_all = "<br>";
		}
		else if (type == 'message') {
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
			see_all = "<a href='messages.php'><div class='all_msg_txt'>See All Messages</div></a>"
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,
			success: function(response) {
				
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "10 10 30 10", "height": "450px", "border" : "1px solid #DADADA"});
				$("#dropdown_data_type").val(type);
				$(".see_all_messages").html(see_all);
				$message_dropdown_status = "show";
			}
			

		});

	}
	else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "2px", "border" : "none"});
		$(".see_all_messages").html("");
		$message_dropdown_status = "hide";
	}

}

function getLiveSearchUsers(value, user) {
	$.post("includes/handlers/ajax_search.php",{query:value, userLoggedIn:user}, function(data) {

		//alert('Typed:'+value+'User:'+user)

		if($(".search_results_footer_empty")[0]){
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}


		$('.search_results').html(data)
		$('.search_results_footer').html("<a href='search.php?q="+value+"'>See All Results</a>")

		if(data == ""){
			$('.search_results_footer').html("");
			$('.search_results_footer').toggleClass("search_results_footer_empty");
			$('.search_results_footer').toggleClass("search_results_footer");

		}
	});
}