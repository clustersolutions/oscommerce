<?php
/*
  $Id: checkout_new_address.php,v 1.6 2004/05/17 01:03:30 hpdl Exp $

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
    <td class="main"><?php echo osc_draw_radio_field('gender', $gender_array, (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->gender : ''), '', (ACCOUNT_GENDER > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_FIRST_NAME; ?></td>
    <td class="main"><?php echo osc_draw_input_field('firstname', (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->first_name : ''), '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo ENTRY_LAST_NAME; ?></td>
    <td class="main"><?php echo osc_draw_input_field('lastname', (($osC_Customer->hasDefaultAddress() === false) ? $osC_Customer->last_name : ''), '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_COMPANY > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_COMPANY; ?></td>
    <td class="main"><?php echo osc_draw_input_field('company', '', '', (ACCOUNT_COMPANY > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_STREET_ADDRESS; ?></td>
    <td class="main"><?php echo osc_draw_input_field('street_address', '', '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_SUBURB > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_SUBURB; ?></td>
    <td class="main"><?php echo osc_draw_input_field('suburb', '', '', (ACCOUNT_SUBURB > 0)); ?></td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_POST_CODE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('postcode', '', '', true); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo ENTRY_CITY; ?></td>
    <td class="main"><?php echo osc_draw_input_field('city', '', '', true); ?></td>
  </tr>
<?php
  if (ACCOUNT_STATE > -1) {
?>
  <tr>
    <td class="main"><?php echo ENTRY_STATE; ?></td>
    <td class="main">
<?php
    if (isset($_POST['action']) && ($_POST['action'] == 'submit')) {
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
      echo osc_draw_input_field('state', '', '', (ACCOUNT_STATE > 0));
    }
?>
    </td>
  </tr>
<?php
  }
?>
  <tr>
    <td class="main"><?php echo ENTRY_COUNTRY; ?></td>
    <td class="main"><?php echo tep_get_country_list('country', STORE_COUNTRY, '', true); ?></td>
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
</table>
