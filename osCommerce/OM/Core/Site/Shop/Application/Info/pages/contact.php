<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Contact') ) {
    echo $OSCOM_MessageStack->get('Contact');
  }
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('contact_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo nl2br(STORE_NAME_ADDRESS); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . OSCOM::getDef('contact_store_address_title') . '</b><br />' . osc_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo OSCOM::getDef('contact'); ?></p>

    <div style="clear: both;"></div>
  </div>
</div>

<form name="contact" action="<?php echo OSCOM::getLink(null, null, 'Contact&Process'); ?>" method="post">

<div class="moduleBox">
  <div class="content">
    <ol>
      <li><?php echo osc_draw_label(OSCOM::getDef('contact_name_title'), 'name') . osc_draw_input_field('name'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('contact_email_address_title'), 'email') . osc_draw_input_field('email'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('contact_enquiry_title'), 'enquiry') . osc_draw_textarea_field('enquiry', null, 50, 15); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?>
</div>

</form>
