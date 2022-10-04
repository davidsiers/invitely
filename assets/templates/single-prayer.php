<?php
/**
 * The template for displaying single prayer.
 *
 */

 get_template_part( invitely_include('assets/templates/header.php') );
  
if ( NULL !== get_post() ) {
  $prayer = get_post();
} else {

  $args = array(
    'post_type' =>'prayers',
    'post_status' => 'publish',
    'posts_per_page' => 1
);
$prayers = wp_get_recent_posts($args, OBJECT);
$prayer = $prayers[0];

}

 $fields = get_fields($prayer->ID);
      foreach( $fields as $name => $value ) {
        $prayer->$name = $value;
      }
  $featured_image = wp_get_attachment_url(get_post_thumbnail_id($prayer->ID));
?>

<div class="prayer-container container">

    <div class="d-none prayer-author row">
      <div class="col-6">
        <strong>Prayer First Name</strong>
        <p><?php echo $prayer->first_name ?></p>
      </div>
      <div class="col-6">
        <strong>Prayer Last Name</strong>
        <p><?php echo $prayer->last_name ?></p>
      </div>

      <div class="col-6">
        <strong>Prayer Email</strong>
        <p><?php echo $prayer->email ?></p>
      </div>
      <div class="col-6">
        <strong>Prayer UID</strong>
        <p><?php echo $prayer->uid ?></p>
      </div>

      <div class="col-6">
        <strong>Prayer Number of Prayers</strong>
        <p><?php echo $prayer->number_of_prayers ?></p>
      </div>
      <div class="col-6">
        <strong>Prayer Answered Timestamp</strong>
        <p><?php echo $prayer->answered_timestamp ?></p>
      </div>

      <div class="col-6">
        <strong>Prayer Subject</strong>
        <p><?php echo $prayer->subject ?></p>
      </div>
      <div class="col-6">
        <strong>Prayer Description</strong>
        <p><?php echo $prayer->description ?></p>
      </div>
    </div>

    <div class="row align-items-center" style="min-height: 90vh">
      <div class="col-12">  
        <div class="card" style="width: 97%;max-width: 500px;margin: 0 auto;">
          <div class="card-body">
            <h5 class="card-title mt-0"><?php echo $prayer->subject ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $prayer->first_name ?> <?php echo $prayer->last_name ?></h6>
            <p class="card-text"><?php echo $prayer->description ?></p>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#prayerModal">I Prayed for this Prayer Request</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="prayerModal" tabindex="-1" aria-labelledby="prayerModallabel" aria-hidden="true">
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" id="sendprayer" class="btn btn-primary">Encourage</button>
        </form>
      </div>
    </div>
  </div>
</div>
        
</div>

    <?php 
    
    get_template_part( invitely_include('assets/templates/footer.php') );

    

    