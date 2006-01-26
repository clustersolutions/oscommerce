<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => $osC_Language->get('gender_male')),
                          array('id' => 'f', 'text' => $osC_Language->get('gender_female')));
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_gender'); ?></td>
    <td class="main"><?php echo osc_draw_radio_field('gender', $gender_array, (isset($Qentry) ? $Qentry->value('entry_gender') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getGender() : '')), '', (ACCOUNT_GENDER > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_first_name'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('firstname', (isset($Qentry) ? $Qentry->value('entry_firstname') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getFirstName() : '')), '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_last_name'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('lastname', (isset($Qentry) ? $Qentry->value('entry_lastname') : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->getLastName() : '')), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_COMPANY > -1) {
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_company'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('company', (isset($Qentry) ? $Qentry->value('entry_company') : ''), '', (ACCOUNT_COMPANY > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_street_address'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('street_address', (isset($Qentry) ? $Qentry->value('entry_street_address') : ''), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_SUBURB > -1) {
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_suburb'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('suburb', (isset($Qentry) ? $Qentry->value('entry_suburb') : ''), '', (ACCOUNT_SUBURB > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_post_code'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('postcode', (isset($Qentry) ? $Qentry->value('entry_postcode') : ''), '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_city'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('city', (isset($Qentry) ? $Qentry->value('entry_city') : ''), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_STATE > -1) {
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_state'); ?></td>
    <td class="main">
<?php
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

        echo osc_draw_pull_down_menu('state', $zones_array, '', '', (ACCOUNT_STATE > 0));
      } else {
        echo osc_draw_input_field('state', '', '', (ACCOUNT_STATE > 0));
      }
    } else {
      echo osc_draw_input_field('state', (isset($Qentry) ? tep_get_zone_name($Qentry->valueInt('entry_country_id'), $Qentry->valueInt('entry_zone_id'), $Qentry->value('entry_state')) : ''), '', (ACCOUNT_STATE > 0));
    }
?>
    </td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_country'); ?></td>
    <td class="main"><?php echo tep_get_country_list('country', (isset($Qentry) ? $Qentry->valueInt('entry_country_id') : STORE_COUNTRY), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_TELEPHONE > -1) {
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_telephone_number'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('telephone', (isset($Qentry) ? $Qentry->value('entry_telephone') : ''), '', (ACCOUNT_TELEPHONE > 0)); ?></td>
  </tr>
<?php
  }
?>
<?php
  if (ACCOUNT_FAX > -1) {
?>
  <tr>
    <td class="main"><?php echo $osC_Language->get('field_customer_fax_number'); ?></td>
    <td class="main"><?php echo osc_draw_input_field('fax', (isset($Qentry) ? $Qentry->value('entry_fax') : ''), '', (ACCOUNT_FAX > 0)); ?></td>
  </tr>
<?php
  }
?>
<?php
  if ($osC_Customer->hasDefaultAddress() && ((isset($_GET['edit']) && ($osC_Customer->getDefaultAddressID() != $_GET['address_book'])) || isset($_GET['new'])) ) {
?>
  <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
  <tr>
    <td colspan="2" class="main"><?php echo osc_draw_checkbox_field('primary', 'on', false, 'id="primary"') . '<label for="primary">&nbsp;' . $osC_Language->get('set_as_primary') . '</label>'; ?></td>
  </tr>
<?php
  }
?>
</table>
