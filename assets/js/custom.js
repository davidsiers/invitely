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

    });

})(jQuery);