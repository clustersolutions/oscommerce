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
  if ( $OSCOM_MessageStack->exists('Create') ) {
    echo $OSCOM_MessageStack->get('Create');
  }
?>

<form name="create" action="<?php echo OSCOM::getLink(null, null, 'Create&Process', 'SSL'); ?>" method="post" onsubmit="return check_form(create);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo OSCOM::getDef('form_required_information'); ?></em>

  <h6><?php echo OSCOM::getDef('my_account_title'); ?></h6>

  <div class="content">
    <ol>

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => OSCOM::getDef('gender_male')),
                          array('id' => 'f', 'text' => OSCOM::getDef('gender_female')));
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_gender'), 'fake', null, (ACCOUNT_GENDER > 0)) . osc_draw_radio_field('gender', $gender_array); ?></li>

<?php
  }
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_first_name'), 'firstname', null, true) . osc_draw_input_field('firstname'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_last_name'), 'lastname', null, true) . osc_draw_input_field('lastname'); ?></li>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_date_of_birth'), 'dob_days', null, true) . osc_draw_date_pull_down_menu('dob', null, false, null, null, date('Y')-1901, -5); ?></li>

<?php
  }
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_email_address'), 'email_address', null, true) . osc_draw_input_field('email_address'); ?></li>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_newsletter'), 'newsletter') . osc_draw_checkbox_field('newsletter', '1'); ?></li>

<?php
  }
?>

      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password'), 'password', null, true) . osc_draw_password_field('password'); ?></li>
      <li><?php echo osc_draw_label(OSCOM::getDef('field_customer_password_confirmation'), 'confirmation', null, true) . osc_draw_password_field('confirmation'); ?></li>
    </ol>
  </div>
</div>

<?php
  if ( DISPLAY_PRIVACY_CONDITIONS == '1' ) {
?>

<div class="moduleBox">
  <h6><?php echo OSCOM::getDef('create_account_terms_heading'); ?></h6>

  <div class="content">
    <?php echo sprintf(OSCOM::getDef('create_account_terms_description'), OSCOM::getLink(null, 'Info', 'Privacy', 'AUTO')) . '<br /><br /><ol><li>' . osc_draw_checkbox_field('privacy_conditions', array(array('id' => 1, 'text' => OSCOM::getDef('create_account_terms_confirm')))) . '</li></ol>'; ?>
  </div>
</div>

<?php
  }
?>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_image_submit_button('button_continue.gif', OSCOM::getDef('button_continue')); ?></span>

  <?php echo osc_link_object(OSCOM::getLink(null, null, null, 'SSL'), osc_draw_image_button('button_back.gif', OSCOM::getDef('button_back'))); ?>
</div>

</form>
