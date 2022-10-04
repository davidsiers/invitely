<?php
/**
 * The template for displaying single devotion.
 *
 */

 get_template_part( invitely_include('assets/templates/header.php') );
  
if ( NULL !== get_post() ) {
  $devotion = get_post();
} else {

  $args = array(
    'post_type' =>'devotions',
    'post_status' => 'publish',
    'posts_per_page' => 1
);
$devotions = wp_get_recent_posts($args, OBJECT);
$devotion = $devotions[0];

}

 $fields = get_fields($devotion->ID);
      foreach( $fields as $name => $value ) {
        $devotion->$name = $value;
      }
      if ($devotion->devotional_author) {
        $devotional_author_fields = get_fields($devotion->devotional_author->ID);
        foreach( $devotional_author_fields as $name => $value ) {
          $devotion->devotional_author->$name = $value;
        }
    }
  $featured_image = wp_get_attachment_url(get_post_thumbnail_id($devotion->ID));
?>

<div class="devo-container container">
    <div class="devotion-image">
        <img class="featured-image" src="<?php echo $featured_image ?>">
    </div>

    <div class="devotion-card passage">
      <a href="<?php echo $devotion->passage_link ?>" target="_blank">
        <h5>Today’s Verse</h5>
        <div class="content">
            <p class="passage"><?php echo $devotion->passage_text ?></p>
            <p class="reference"><?php echo $devotion->passage_reference ?></p>
        </div>
      </a>
    </div>

    <h5 class="devo-date"><?php echo get_the_date("l, F j", $devotion) ?></h5>

    <div class="devotional-post-content"><?php echo apply_filters('the_content',$devotion->post_content); ?></div>

    <div class="devotion-card memory-verse">
      <a href="<?php echo $devotion->memory_verse_link ?>" target="_blank">
        <h5>Weekly Memory Verse</h5>
        <div class="content">
            <p class="passage"><?php echo $devotion->memory_verse_text ?></p>
            <p class="reference"><?php echo $devotion->memory_verse_reference ?></p>
        </div>
      </a>
    </div>

    <div class="devotion-quote">
        <div class="quote-text">"<?php echo $devotion->quote_text ?>"</div>
        <div class="quote-author">- <?php echo $devotion->quote_author ?></div>
    </div>


    <div class="devotional-author row">
      <div class="col-4 author-image">
        <img class="img-circle" src="<?php echo $devotion->devotional_author->profile_image["url"] ?>">
      </div>
      <div class="col-8 author-title">
        <h4><?php echo $devotion->devotional_author->nickname ?></h4>
        <p>Devotional Author</p>

        <div class="row">
          <div class="col-6">
          <a type="button" class="btn btn-primary btn-block" href="mailto:<?php echo $devotion->devotional_author->email ?>">Email</a>
          </div>

          <div class="col-6">
          <a type="button" class="btn btn-primary btn-block" href="sms:<?php echo $devotion->devotional_author->phone ?>">Phone</a>
          </div>
        </div>

      </div>
    </div>


    <a type="button" class="btn btn-primary btn-block response-button"><?php echo $devotion->feedback_button_text ?></a>

        
</div>

    <?php 
    
    get_template_part( invitely_include('assets/templates/footer.php') );

    

    