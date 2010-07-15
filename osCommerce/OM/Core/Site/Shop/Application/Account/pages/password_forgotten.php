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
  if ( $OSCOM_MessageStack->exists('PasswordForgotten') ) {
    echo $OSCOM_MessageStack->get('PasswordForgotten');
  }
?>

<form name="password_forgotten" action="<?php echo OSCOM::getLink(null, null, 'PasswordForgotten&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(password_forgotten);">

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('password_forgotten_heading'); ?></h6>

  <div class="content">
    <p><?php echo OSCOM::getDef('password_forgotten'); ?></p>

    <ol>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_email_address'), 'email_address') . osc_draw_input_field('email_address'); ?></li>
    </ol>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, null, 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

</form>
