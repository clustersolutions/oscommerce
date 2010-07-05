<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
?>

<div style="float: right;"><?php echo osc_link_object(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle(), 'hspace="5" vspace="5"', 'mini')); ?></div>

<h1><?php echo $OSCOM_Template->getPageTitle() . ($OSCOM_Product->hasModel() ? '<br /><span class="smallText">' . $OSCOM_Product->getModel() . '</span>' : ''); ?></h1>

<div style="clear: both;"></div>

<?php
  if ( $OSCOM_MessageStack->exists('Reviews') ) {
    echo $OSCOM_MessageStack->get('Reviews');
  }
?>

<form name="reviews_write" action="<?php echo OSCOM::getLink(null, null, 'Reviews&Process&' . $OSCOM_Product->getID()); ?>" method="post" onsubmit="return checkForm(this);">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('new_review_title'); ?></h6>

  <div class="content">
    <ol>

<?php
  if ( $OSCOM_Customer->isLoggedOn() === false ) {
?>

      <li><?php echo osc_draw_label(ENTRY_NAME, null, 'customer_name') . osc_draw_input_field('customer_name'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_email_address'), null, 'customer_email_address') . osc_draw_input_field('customer_email_address'); ?></li>

<?php
  }
?>

      <li><?php echo osc_draw_textarea_field('review', null, null, 15, 'style="width: 98%;"'); ?></li>
      <li><?php echo OSCOM::getDef('field_review_rating') . ' ' . OSCOM::getDef('review_lowest_rating_title') . ' ' . osc_draw_radio_field('rating', array('1', '2', '3', '4', '5')) . ' ' . OSCOM::getDef('review_highest_rating_title'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, 'Reviews&' . $OSCOM_Product->getID()), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

</form>
