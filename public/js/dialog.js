$(document).ready(function() {

        $("#dialog").dialog({
                autoOpen: false
        });
        $("#button").on("click", function(e) {
            e.preventDefault();
            alert('clicked');
            $.get('/pms/client/edit', function(data) {
                $('#dialog').html(data);
            });
            $("#dialog").dialog("open");
        });

    
    // Validating Form Fields.....
    $("#submit").click(function(e) {
//        var email = $("#email").val();
//        var name = $("#name").val();
//        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
//        if (email === '' || name === '') {
//                alert("Please fill all fields...!!!!!!");
//                e.preventDefault();
//        } else if (!(email).match(emailReg)) {
//                alert("Invalid Email...!!!!!!");
//                e.preventDefault();
//        } else {
//                alert("Form Submitted Successfully......");
//        }
    });
});


