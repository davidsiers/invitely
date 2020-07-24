<?php
/**
 * The template for displaying single invite.
 *
 */

 get_template_part( invitely_include('assets/templates/header.php') );

 $token = sanitize_text_field( get_query_var( 'token-id' ) );
 if ($token != get_the_ID()) {
    get_template_part( invitely_include('assets/templates/access-denied.php') );
    exit;
}

?>

<?php
$args = array(
    'post_type' => 'invites',
    'posts_per_page' => 1,
);

$events = wp_get_object_terms( get_the_ID(), 'invite_event' );
$event_id = $events[0]->term_id;
$event_description = $events[0]->description;

$loop = new WP_Query($args);
while ( $loop->have_posts() ) {
    $loop->the_post();

    ?>

<meta property="og:image" content="<?php the_field('event_social_image', 'term_'.$event_id) ?>">

<div class="text-center">
    <?php if ( is_user_logged_in() ) {
        echo '<div class="m-y-1"><span class="flag-wrapper flag-check-in">
                <a href="" title="Click to check in this delegate" class="flag flag-action flag-link-toggle flag-processed" rel="nofollow">
                    <div class="row-flex">
                        <i class="fa fa-square-o fa-2x uncheck m-r-1"></i> 
                        <span>Click here to Check In</span>
                    </div>
                </a>
                </span>
                </div>';
        }
    ?>
    <a class="btn my-4" id="panel-flip">
        <span class="fa-stack">
            <i class="fa fa-circle-thin fa-stack-2x"></i>
            <i class="fa fa-info fa-stack-1x"></i>
        </span> Event Info</a>
</div>

<div class="container">

    <div class="event-ticket panel row mx-auto pt-3 pt-2 flippable" style="background-color: <?php the_field('event_background_color', 'term_'.$event_id)?>;color: <?php the_field('event_foreground_color', 'term_'.$event_id)?>;">
        
            <div class="col-6 logo pl-2">
                <img class="img-fluid" src="<?php the_field('event_logo', 'term_'.$event_id)?>" alt="">
                <h5 class="logo-text">Ticket</h5>
            </div>
            <div class="col-6 event-title text-right pr-3">
                <strong>EVENT</strong><br>
                <h5 class="mt-0"><?php the_field('event_logo_text', 'term_'.$event_id)?></h5>
            </div>

        <div class="col-12 py-2 strip-img">
            <img class="img-fluid" src="<?php the_field('event_strip_image', 'term_'.$event_id)?>" width="782" height="205" alt="">
        </div>

            <div class="col-6 ticket-holder pl-3">
                <strong>TICKET HOLDER</strong><br>
                <span><?php the_field('first_name')?> <?php the_field('last_name')?></span>
            </div> 
            <div class="col-6 event-time text-right pr-3">
                <strong>WHEN</strong><br>
                <span><? the_field('service_time')?></span>
            </div> 


            <div class="col-2 guest-number pl-3 my-3">
                <strong>GUESTS</strong><br>
                <span><?php the_field('number_of_guests')?></span>
            </div>
            <div class="col-10 event-location text-right pr-3 my-3">
                <strong>LOCATION</strong><br>
                <span><?php the_field('event_location', 'term_'.$event_id)?></span>
            </div>


        <div class="col-12 text-center pt-4 pb-2" id="qrcode"></div>
        <div class="col-12 text-center code-instructions pb-4"><?php the_field('event_barcode_instruction_text', 'term_'.$event_id)?></div>

    </div>

    <div class="event-ticket panel row p-2 flippable event-info mb-3 mx-auto flipped">

        <div class="event-website">
            <strong>EVENT WEBSITE</strong>
            <div><a href="<?php the_field('event_website', 'term_'.$event_id)?>">Learn More</a></div>  
        </div>  
        <div class="event-description">
            <strong>ABOUT THE EVENT</strong>
            <div><?php echo $event_description ?></div>
        </div>  
        <div class="invited-by">
            <strong>INVITED BY</strong>
            <div><? the_field('invited_by_first_name')?> <? the_field('invited_by_last_name')?><br>
            <a href="tel:<? the_field('invited_by_phone')?>"><? the_field('invited_by_phone')?></a><br>
            <a href="mailto:<? the_field('invited_by_email')?>"><? the_field('invited_by_email')?></a>
            </div>  
        </div>  
        <div class="event-time-back">
            <strong>WHEN</strong>
            <div><? the_field('service_time')?></div>
        </div>  
        <div class="event-address">
            <strong>LOCATION</strong>
            <div><?php the_field('event_address', 'term_'.$event_id)?></div>
        </div>  
        <div class="small-group">
            <strong>GROWTH GROUP</strong>
            <div><?php the_field('growth_group')?></div>
        </div>  
        <div class="event-phone">
            <strong>VENUE PHONE</strong>
            <div>
                <a href="tel:<?php the_field('event_phone', 'term_'.$event_id)?>"><?php the_field('event_phone', 'term_'.$event_id)?></a>
            </div>
        </div>
    </div>

    </div>
    </div>
    </div>

    <script>
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "invite-<?php echo get_the_ID() ?>",
            width: 150,
            height: 150,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
    <?php 
    
    get_template_part( invitely_include('assets/templates/footer.php') );

}