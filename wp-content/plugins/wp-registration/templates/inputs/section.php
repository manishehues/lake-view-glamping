<?php
/**
** WPR Template to render Section input
**/

    // not run if accessed directly
    if( ! defined("ABSPATH" ) )
        die("Not Allewed");

    $fm = new WPR_InputMeta($field_meta, 'section');
    
    $wpr_width = apply_filters('wpr_section_field_width', 12, $fm);
 ?>

<div class="col-md-<?php echo $wpr_width ?> wpr_field_wrapper"> 
    <h2 style="margin-top:10px;" class="wpr-section-title"><?php echo $fm->title; ?></h2>
    <h3 class="wpr-section-subtitle"><?php echo apply_filters('the_content', $fm->desc); ?></h3>
</div>