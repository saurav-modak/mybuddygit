$(document).ready(function(){
    //on click singup, hide login and show registration form
    $("#signup").click(function(){
        $("#first").slideUp("slow",function(){
            $("#second").slideDown("slow");
            //$("#third").slideUp("slow");
        });
    });

    //on click singup, hide registration and show login form
    $("#signin").click(function(){
        $("#second").slideUp("slow",function(){
            $("#first").slideDown("slow");
            //$("#third").slideUp("slow");
        });
    });

    //on click reset pass
    $("#forgotpass").click(function(){
        $("#first").slideUp("slow",function(){
            $("#third").slideDown("slow");
        });
    });

    $("#rememberpass").click(function(){
        $("#third").slideUp("slow",function(){
            $("#first").slideDown("slow");
        });
    });
});
