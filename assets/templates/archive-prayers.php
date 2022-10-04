<?php
/**
 * The template for displaying single prayer.
 *
 */

 get_template_part( invitely_include('assets/templates/header.php') );
  
// if ( NULL !== get_post() ) {
//   $prayer = get_post();
// } else {

//   $args = array(
//     'post_type' =>'prayers',
//     'post_status' => 'publish',
//     'posts_per_page' => 10
// );
// $prayers = wp_get_recent_posts($args, OBJECT);
// $prayer = $prayers[0];

// }

//  $fields = get_fields($prayer->ID);
//       foreach( $fields as $name => $value ) {
//         $prayer->$name = $value;
//       }
  // $featured_image = wp_get_attachment_url(get_post_thumbnail_id($prayer->ID));
?>

<div class="prayer-container container">
<div class="row">

<?php
    $loop = new WP_Query( array( 'post_type' => 'prayers', 'paged' => $paged ) );
    if ( $loop->have_posts() ) :
    while ( $loop->have_posts() ) : $loop->the_post(); ?>

    <?php
      $prayer = new stdClass();
      $prayer->ID = get_the_ID();
      $fields = get_fields($prayer->ID);
      $first_name = get_field("first_name");
      
      foreach( $fields as $name => $value ) {
        $prayer->$name = $value;
      }

      $featured_image = wp_get_attachment_url(get_post_thumbnail_id($prayer->ID));
    ?>

      <div class="col-md-12 prayer-items">
        <div class="card" style="width: 97%;max-width: 500px;margin: 0 auto;">
          <div class="card-body">
          <h5 class="card-title mt-0"><?php echo $prayer->subject ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $first_name ?> <?php echo $prayer->last_name ?> - <?php echo get_the_date( 'F j, Y' ); ?></h6>
            <p class="card-text"><?php echo $prayer->description ?></p>
            <a href="/?p=<?php echo $prayer->ID ?>" class="btn btn-primary">Open Prayer Request</a>
            <!-- <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#prayerModal-<?php echo $prayer->ID ?>">I Prayed for this Prayer Request</a> -->
          </div>
        </div>
      </div> 

      <div class="modal fade" id="prayerModal-<?php echo $prayer->ID ?>" tabindex="-1" aria-labelledby="prayerModallabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-body">
              <div class="container-fluid">
                <div class="row">

                  <div class="col-12">
                    <h5 class="modal-title mt-0 mb-0" id="prayerModallabel">Encourage <?php echo $prayer->first_name ?></h5>
                    <p class="mb-0 mt-0">Would you like to notify <?php echo $prayer->first_name ?> that you prayed?</p>
                    <hr class="mb-2 mt-3">
                  </div>

                </div>

              <form id="sendPrayerModalForm">
                
                <div class="d-none">
                  <input type="text" class="form-control" id="prayerID" placeholder="Prayer ID" aria-label="Prayer ID" value="<?php echo $prayer->ID ?>">
                </div> 

                <div class="row mb-4 mt-3">
                  <div class="col-12">
                    <div class="mb-6">
                      <input type="text" class="form-control" id="prayerFirstName" placeholder="What is Your Name?" aria-label="first name" required>
                    </div>
                  </div>
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" id="sendprayer" class="btn btn-primary">Encourage</button>
                </div>

              </form>
              </div>
            </div>
          </div>
        </div>
      </div>

        <?php endwhile;
        if (  $loop->max_num_pages > 1 ) : ?>
            <div id="nav-below" class="navigation">
                <div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Previous', 'domain' ) ); ?></div>
                <div class="nav-next"><?php previous_posts_link( __( 'Next <span class="meta-nav">&rarr;</span>', 'domain' ) ); ?></div>
            </div>
        <?php endif;
    endif;
    wp_reset_postdata();
?>

</div>


    <?php 
    
    get_template_part( invitely_include('assets/templates/footer.php') );

    

    