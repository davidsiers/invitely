(function($) {

    $(document).ready(function() {
        // Flip Card
        if (document.getElementById('panel-flip')) {
            $('#panel-flip').on('click', function() {
                $('.flippable').toggleClass('flipped');
            });
            $('.flippable').on('click', function() {
                $('.flippable').toggleClass('flipped');
            });
            console.log('panel-flip');
        }
        if (document.getElementById('sendprayer')) {
            console.log('send prayer fuctioin is working');
            var prayerID = $('#prayerID').val();
            $("#sendPrayerModalForm").validate({
                rules: {
                    prayerFirstName: "required",
                },
                submitHandler: function(form) {
                    var firstName = $('#prayerFirstName').val();
                    // var lastName = $('#prayerLastName').val();
                    var data = {
                        "first_name": firstName,
                        "last_name": ''
                    };
                    addData(data, prayerID);
                }
            });
            $('#sendprayer').on('click', function() {});
        }


        function addData(data, prayerID) { // pass your data in method
            $.ajax({
                type: "PUT",
                url: "//www.tucsonbaptist.com/wp-json/invitely/v1/prayer-request/" + prayerID + "/prayed",
                data: JSON.stringify(data), // now data come in this function
                contentType: "application/json; charset=utf-8",
                crossDomain: true,
                dataType: "json",
                cache: false,
                async: false,
                success: function(data, status, jqXHR) {

                    alert("You have successfully notified them."); // write success in " "
                    location.reload();
                },

                error: function(jqXHR, status) {
                    // error handler
                    console.log(jqXHR);
                    alert('fail' + status.code);
                }
            });
        }

    });

})(jQuery);