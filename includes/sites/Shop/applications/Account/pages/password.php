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

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists('Password') ) {
    echo $OSCOM_MessageStack->get('Password');
  }
?>

<form name="account_password" action="<?php echo OSCOM::getLink(null, null, 'Password', 'SSL'); ?>" method="post" onsubmit="return check_form(account_edit);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('my_password_title'); ?></h6>

  <div class="content">
    <ol>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password_current'), 'password_current', null, true) . osc_draw_password_field('password_current'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password_new'), 'password_new', null, true) . osc_draw_password_field('password_new'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password_confirmation'), 'password_confirmation', null, true) . osc_draw_password_field('password_confirmation'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_hidden_field('subaction', 'process') . osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, null, 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

</form>
