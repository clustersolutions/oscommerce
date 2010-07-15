<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Customers_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<script type="text/javascript">
$(document).ready(function(){
  $("#customerTabs").tabs( { selected: <?php echo ( isset($_GET['tabIndex']) && ( $_GET['tabIndex'] == 'tabAddressBook' ) ? 1 : 0 ); ?> } );
});
</script>

<div id="customerTabs">
  <ul>
    <li><?php echo osc_link_object('#section_personal_content', $osC_Language->get('section_personal')); ?></li>
    <li><?php echo osc_link_object('#section_address_book_content', $osC_Language->get('section_address_book')); ?></li>
  </ul>

  <div id="section_personal_content">
    <form name="customers" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( ACCOUNT_GENDER > -1 ) {
?>

      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_gender'); ?></td>
        <td width="70%"><?php echo osc_draw_radio_field('gender', $gender_array, $osC_ObjectInfo->get('customers_gender')); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_first_name'); ?></td>
        <td width="70%"><?php echo osc_draw_input_field('firstname', $osC_ObjectInfo->get('customers_firstname')); ?></td>
      </tr>
      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_last_name'); ?></td>
        <td width="70%"><?php echo osc_draw_input_field('lastname', $osC_ObjectInfo->get('customers_lastname')); ?></td>
      </tr>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_date_of_birth'); ?></td>
        <td width="70%"><?php echo osc_draw_date_pull_down_menu('dob', array('year' => $osC_ObjectInfo->get('customers_dob_year'), 'month' => $osC_ObjectInfo->get('customers_dob_month'), 'date' => $osC_ObjectInfo->get('customers_dob_date')), false, null, null, date('Y')-1901, -5); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_email_address'); ?></td>
        <td width="70%"><?php echo osc_draw_input_field('email_address', $osC_ObjectInfo->get('customers_email_address')); ?></td>
      </tr>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_newsletter_subscription'); ?></td>
        <td width="70%"><?php echo osc_draw_checkbox_field('newsletter', null, ($osC_ObjectInfo->get('customers_newsletter') == '1')); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_new_password'); ?></td>
        <td width="70%"><?php echo osc_draw_password_field('password'); ?></td>
      </tr>
      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_new_password_confirmation'); ?></td>
        <td width="70%"><?php echo osc_draw_password_field('confirmation'); ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo $osC_Language->get('field_status'); ?></td>
        <td width="70%"><?php echo osc_draw_checkbox_field('status', null, ($osC_ObjectInfo->get('customers_status') == '1')); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" />'; ?></p>

    </form>
  </div>

  <div id="section_address_book_content">
    <p><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=saveAddress'), osc_icon('new.png') . ' ' . $osC_Language->get('operation_new_address_book_entry')); ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $Qaddresses = osC_Customers_Admin::getAddressBookData($_GET['cID']);

  while ( $Qaddresses->next() ) {
?>

      <tr>
        <td>

<?php
    if ( ACCOUNT_GENDER > -1 ) {
      switch ( $Qaddresses->value('gender') ) {
        case 'm':
          echo osc_icon('user_male.png') . '&nbsp;';
          break;

        case 'f':
          echo osc_icon('user_female.png') . '&nbsp;';
          break;

        default:
          echo osc_icon('people.png') . '&nbsp;';
          break;
      }
    } else {
      echo osc_icon('people.png') . '&nbsp;';
    }

    echo osC_Address::format($Qaddresses->toArray(), ', ');

    if ( $osC_ObjectInfo->get('customers_default_address_id') == $Qaddresses->valueInt('address_book_id') ) {
      echo '&nbsp;<i>(' . $osC_Language->get('primary_address') . ')</i>';
    }
?>

        </td>
        <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&abID=' . $Qaddresses->valueInt('address_book_id') . '&action=saveAddress'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&abID=' . $Qaddresses->valueInt('address_book_id') . '&action=deleteAddress'), osc_icon('trash.png')) . '&nbsp;';
?>

        </td>
      </tr>
      <tr>
        <td colspan="2">

<?php
    echo osc_icon('telephone.png', null, '16x16', 'style="margin-left: 16px;"') . '&nbsp;';

    if ( !osc_empty($Qaddresses->valueProtected('telephone_number')) ) {
      echo $Qaddresses->valueProtected('telephone_number');
    } else {
      echo '<small><i>(' . $osC_Language->get('no_telephone_number') . ')</i></small>';
    }

    echo osc_icon('print.png', null, '16x16', 'style="margin-left: 16px;"') . '&nbsp;';

    if ( !osc_empty($Qaddresses->valueProtected('fax_number')) ) {
      echo $Qaddresses->valueProtected('fax_number');
    } else {
      echo '<small><i>(' . $osC_Language->get('no_fax_number') . ')</i></small>';
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </table>
  </div>
</div>

<p align="right"><?php echo '<input type="button" value="' . $osC_Language->get('button_back') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" />'; ?></p>
