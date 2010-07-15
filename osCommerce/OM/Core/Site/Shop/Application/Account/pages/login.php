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
  if ( $OSCOM_MessageStack->exists('LogIn') ) {
    echo $OSCOM_MessageStack->get('LogIn');
  }
?>

<div class="moduleBox" style="width: 49%; float: right;">
  <form name="login" action="<?php echo OSCOM::getLink(null, null, 'LogIn&Process', 'SSL'); ?>" method="post">

  <h6><?php echo OSCOM::getDef('login_returning_customer_heading'); ?></h6>

  <div class="content">
    <p><?php echo OSCOM::getDef('login_returning_customer_text'); ?></p>

    <ol>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_email_address'), 'email_address') . osc_draw_input_field('email_address'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password'), 'password') . osc_draw_password_field('password'); ?></li>
    </ol>

    <p><?php echo sprintf(OSCOM::getDef('login_returning_customer_password_forgotten'), OSCOM::getLink(null, null, 'PasswordForgotten', 'SSL')); ?></p>

    <p align="right"><?php echo osc_draw_image_submit_button('button_login.gif', OSCOM::getDef('button_sign_in')); ?></p>
  </div>

  </form>
</div>

<div class="moduleBox" style="width: 49%;">
  <div class="outsideHeading">
    <h6><?php echo OSCOM::getDef('login_new_customer_heading'); ?></h6>
  </div>

  <div class="content">
    <p><?php echo OSCOM::getDef('login_new_customer_text'); ?></p>

    <p align="right"><?php echo osc_link_object(OSCOM::getLink(null, null, 'Create', 'SSL'), osc_draw_image_button('button_continue.gif', OSCOM::getDef('button_continue'))); ?></p>
  </div>
</div>
