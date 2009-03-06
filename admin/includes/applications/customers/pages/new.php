<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png') . ' ' . $osC_Language->get('action_heading_new_customer'); ?></div>
<div class="infoBoxContent">
  <form name="customers" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_gender'); ?></td>
      <td width="70%"><?php echo osc_draw_radio_field('gender', $gender_array); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_first_name'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('firstname'); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_last_name'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('lastname'); ?></td>
    </tr>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_date_of_birth'); ?></td>
      <td width="70%"><?php echo osc_draw_date_pull_down_menu('dob', null, false, null, null, date('Y')-1901, -5); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_email_address'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('email_address'); ?></td>
    </tr>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_newsletter_subscription'); ?></td>
      <td width="70%"><?php echo osc_draw_checkbox_field('newsletter'); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_password'); ?></td>
      <td width="70%"><?php echo osc_draw_password_field('password'); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_password_confirmation'); ?></td>
      <td width="70%"><?php echo osc_draw_password_field('confirmation'); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_status'); ?></td>
      <td width="70%"><?php echo osc_draw_checkbox_field('status', 'on', true); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" />'; ?></p>

  </form>
</div>
