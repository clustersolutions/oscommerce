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

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Customers_Admin::getAddressBookData($_GET['cID'], $_GET['abID']));
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('edit.png') . ' ' . osc_output_string_protected(osC_Customers_Admin::getData($_GET['cID'], 'customers_firstname')) . ' ' . osc_output_string_protected(osC_Customers_Admin::getData($_GET['cID'], 'customers_lastname')); ?></div>
<div class="infoBoxContent">
  <form name="customers" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&abID=' . $_GET['abID'] . '&action=saveAddress'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_gender'); ?></td>
      <td width="70%"><?php echo osc_draw_radio_field('ab_gender', $gender_array, $osC_ObjectInfo->get('gender')); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_first_name'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_firstname', $osC_ObjectInfo->get('firstname')); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_last_name'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_lastname', $osC_ObjectInfo->get('lastname')); ?></td>
    </tr>

<?php
  if ( ACCOUNT_COMPANY > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_company'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_company', $osC_ObjectInfo->get('company')); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_street_address'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_street_address', $osC_ObjectInfo->get('street_address')); ?></td>
    </tr>

<?php
  if ( ACCOUNT_SUBURB > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_suburb'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_suburb', $osC_ObjectInfo->get('suburb')); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_post_code'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_postcode', $osC_ObjectInfo->get('postcode')); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_city'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_city', $osC_ObjectInfo->get('city')); ?></td>
    </tr>

<?php
  if ( ACCOUNT_STATE > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_state'); ?></td>
      <td width="70%">

<?php
    if ( isset($_POST['subaction']) && ($_POST['subaction'] == 'confirm') && isset($entry_state_has_zones) && ($entry_state_has_zones === true) ) {
      $Qzones = $osC_Database->query('select zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $_POST['ab_country']);
      $Qzones->execute();

      $zones_array = array();

      while ( $Qzones->next() ) {
        $zones_array[] = array('id' => $Qzones->value('zone_name'),
                               'text' => $Qzones->value('zone_name'));
      }

      echo osc_draw_pull_down_menu('ab_state', $zones_array);
    } else {
      echo osc_draw_input_field('ab_state', (!osc_empty($osC_ObjectInfo->get('zone_id')) ? osC_Address::getZoneName($osC_ObjectInfo->get('zone_id')) : $osC_ObjectInfo->get('state')));
    }
?>

      </td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_country'); ?></td>
      <td width="70%">

<?php
  $countries_array = array();

  foreach ( osC_Address::getCountries() as $country ) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }

  echo osc_draw_pull_down_menu('ab_country', $countries_array, $osC_ObjectInfo->get('country_id'));
?>

      </td>
    </tr>

<?php
  if ( ACCOUNT_TELEPHONE > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_telephone_number'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_telephone', $osC_ObjectInfo->get('telephone_number')); ?></td>
    </tr>

<?php
  }

  if ( ACCOUNT_FAX > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_fax_number'); ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_fax', $osC_ObjectInfo->get('fax_number')); ?></td>
    </tr>

<?php
  }

  if ( osC_Customers_Admin::getData($_GET['cID'], 'customers_default_address_id') != $_GET['abID'] ) {
?>

    <tr>
      <td width="30%"><?php echo $osC_Language->get('field_set_as_primary'); ?></td>
      <td width="70%"><?php echo osc_draw_checkbox_field('ab_primary'); ?></td>
    </tr>

<?php
  }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_save') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save&tabIndex=tabAddressBook') . '\';" />'; ?></p>

  </form>
</div>
