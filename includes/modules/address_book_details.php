<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<ol>

<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_gender'), null, 'fake', (ACCOUNT_GENDER > 0)) . osc_draw_radio_field('gender', $gender_array, (isset($Qentry) ? $Qentry->value('entry_gender') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getGender() : ''))); ?></li>

<?php
  }
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_first_name'), null, 'firstname', true) . osc_draw_input_field('firstname', (isset($Qentry) ? $Qentry->value('entry_firstname') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getFirstName() : ''))); ?></tli>
  <li><?php echo osc_draw_label($osC_Language->get('field_customer_last_name'), null, 'lastname', true) . osc_draw_input_field('lastname', (isset($Qentry) ? $Qentry->value('entry_lastname') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getLastName() : ''))); ?></li>

<?php
  if (ACCOUNT_COMPANY > -1) {
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_company'), null, 'company', (ACCOUNT_COMPANY > 0)) . osc_draw_input_field('company', (isset($Qentry) ? $Qentry->value('entry_company') : '')); ?></li>

<?php
  }
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_street_address'), null, 'street_address', true) . osc_draw_input_field('street_address', (isset($Qentry) ? $Qentry->value('entry_street_address') : '')); ?></li>

<?php
  if (ACCOUNT_SUBURB > -1) {
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_suburb'), null, 'suburb', (ACCOUNT_SUBURB > 0)) . osc_draw_input_field('suburb', (isset($Qentry) ? $Qentry->value('entry_suburb') : '')); ?></li>

<?php
  }
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_post_code'), null, 'postcode', true) . osc_draw_input_field('postcode', (isset($Qentry) ? $Qentry->value('entry_postcode') : '')); ?></li>
  <li><?php echo osc_draw_label($osC_Language->get('field_customer_city'), null, 'city', true) . osc_draw_input_field('city', (isset($Qentry) ? $Qentry->value('entry_city') : '')); ?></li>

<?php
  if (ACCOUNT_STATE > -1) {
?>

  <li>

<?php
    echo osc_draw_label($osC_Language->get('field_customer_state'), null, 'state', (ACCOUNT_STATE > 0));

    if ( (isset($_GET['new']) && ($_GET['new'] == 'save')) || (isset($_GET['edit']) && ($_GET['edit'] == 'save')) ) {
      if ($entry_state_has_zones === true) {
        $Qzones = $osC_Database->query('select zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
        $Qzones->bindRaw(':table_zones', TABLE_ZONES);
        $Qzones->bindInt(':zone_country_id', $_POST['country']);
        $Qzones->execute();

        $zones_array = array();
        while ($Qzones->next()) {
          $zones_array[] = array('id' => $Qzones->value('zone_name'), 'text' => $Qzones->value('zone_name'));
        }

        echo osc_draw_pull_down_menu('state', $zones_array);
      } else {
        echo osc_draw_input_field('state');
      }
    } else {
      echo osc_draw_input_field('state', (isset($Qentry) ? tep_get_zone_name($Qentry->valueInt('entry_country_id'), $Qentry->valueInt('entry_zone_id'), $Qentry->value('entry_state')) : ''));
    }
?>

  </li>

<?php
  }
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_country'), null, 'country', true) . tep_get_country_list('country', (isset($Qentry) ? $Qentry->valueInt('entry_country_id') : STORE_COUNTRY)); ?></li>

<?php
  if (ACCOUNT_TELEPHONE > -1) {
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_telephone_number'), null, 'telephone', (ACCOUNT_TELEPHONE > 0)) . osc_draw_input_field('telephone', (isset($Qentry) ? $Qentry->value('entry_telephone') : '')); ?></li>

<?php
  }

  if (ACCOUNT_FAX > -1) {
?>

  <li><?php echo osc_draw_label($osC_Language->get('field_customer_fax_number'), null, 'fax', (ACCOUNT_FAX > 0)) . osc_draw_input_field('fax', (isset($Qentry) ? $Qentry->value('entry_fax') : '')); ?></li>

<?php
  }

  if ($osC_Customer->hasDefaultAddress() && ((isset($_GET['edit']) && ($osC_Customer->getDefaultAddressID() != $_GET['address_book'])) || isset($_GET['new'])) ) {
?>

  <li><?php echo osc_draw_checkbox_field('primary', array(array('id' => 'on', 'text' => $osC_Language->get('set_as_primary'))), false); ?></li>

<?php
  }
?>

</ol>
