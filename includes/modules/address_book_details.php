<?php
/*
  $Id: address_book_details.php,v 1.15 2004/06/13 18:04:40 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
?>
  <tr>
    <td class="main"><?php echo ENTRY_GENDER; ?></td>
    <td class="main"><?php echo osc_draw_radio_field('gender', $gender_array, (isset($entry['entry_gender']) ? $entry['entry_gender'] : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->gender : '')), '', (ACCOUNT_GENDER > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
    <td class="main"><?php echo osc_draw_input_field('firstname', (isset($entry['entry_firstname']) ? $entry['entry_firstname'] : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->first_name : '')), '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
    <td class="main"><?php echo osc_draw_input_field('lastname', (isset($entry['entry_lastname']) ? $entry['entry_lastname'] : (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->last_name : '')), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_COMPANY > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_COMPANY; ?></td>
    <td class="main"><?php echo osc_draw_input_field('company', (isset($entry['entry_company']) ? $entry['entry_company'] : ''), '', (ACCOUNT_COMPANY > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_STREET_ADDRESS; ?></td>
    <td class="main"><?php echo osc_draw_input_field('street_address', (isset($entry['entry_street_address']) ? $entry['entry_street_address'] : ''), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_SUBURB > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_SUBURB; ?></td>
    <td class="main"><?php echo osc_draw_input_field('suburb', (isset($entry['entry_suburb']) ? $entry['entry_suburb'] : ''), '', (ACCOUNT_SUBURB > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_POST_CODE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('postcode', (isset($entry['entry_postcode']) ? $entry['entry_postcode'] : ''), '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo ENTRY_CITY; ?></td>
    <td class="main"><?php echo osc_draw_input_field('city', (isset($entry['entry_city']) ? $entry['entry_city'] : ''), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_STATE > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_STATE; ?></td>
    <td class="main">
<?php
    if (isset($_POST['action']) && (($_POST['action'] == 'process') || ($_POST['action'] == 'update'))) {
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
      echo osc_draw_input_field('state', (isset($entry['entry_country_id']) ? tep_get_zone_name($entry['entry_country_id'], $entry['entry_zone_id'], $entry['entry_state']) : ''), '', (ACCOUNT_STATE > 0));
    }
?>
    </td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_COUNTRY; ?></td>
    <td class="main"><?php echo tep_get_country_list('country', (isset($entry['entry_country_id']) ? $entry['entry_country_id'] : STORE_COUNTRY), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_TELEPHONE > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
    <td class="main"><?php echo osc_draw_input_field('telephone', (isset($entry['entry_telephone']) ? $entry['entry_telephone'] : ''), '', (ACCOUNT_TELEPHONE > 0)); ?></td>
  </tr>
<?php
  }
?>
<?php
  if (ACCOUNT_FAX > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_FAX_NUMBER; ?></td>
    <td class="main"><?php echo osc_draw_input_field('fax', (isset($entry['entry_fax']) ? $entry['entry_fax'] : ''), '', (ACCOUNT_FAX > 0)); ?></td>
  </tr>
<?php
  }
?>
<?php
  if ($osC_Customer->hasDefaultAddress() && ((isset($_GET['edit']) && ($osC_Customer->default_address_id != $_GET['edit'])) || (isset($_GET['edit']) == false)) ) {
?>
  <tr>
    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
  <tr>
    <td colspan="2" class="main"><?php echo osc_draw_checkbox_field('primary', 'on', false, 'id="primary"') . '<label for="primary">&nbsp;' . SET_AS_PRIMARY . '</label>'; ?></td>
  </tr>
<?php
  }
?>
</table>
