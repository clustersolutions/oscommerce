<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Customers_Admin::getData($_GET['cID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ADDRESS_BOOK_ENTRY; ?></div>
<div class="infoBoxContent">
  <form name="customers" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=saveAddress'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
?>

    <tr>
      <td width="30%"><?php echo ENTRY_GENDER; ?></td>
      <td width="70%"><?php echo osc_draw_radio_field('ab_gender', $gender_array, $osC_ObjectInfo->get('customers_gender')); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo ENTRY_FIRST_NAME; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_firstname', $osC_ObjectInfo->get('customers_firstname')); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo ENTRY_LAST_NAME; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_lastname', $osC_ObjectInfo->get('customers_lastname')); ?></td>
    </tr>

<?php
  if ( ACCOUNT_COMPANY > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_COMPANY; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_company'); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo ENTRY_STREET_ADDRESS; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_street_address'); ?></td>
    </tr>

<?php
  if ( ACCOUNT_SUBURB > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_SUBURB; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_suburb'); ?></td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo ENTRY_POST_CODE; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_postcode'); ?></td>
    </tr>
    <tr>
      <td width="30%"><?php echo ENTRY_CITY; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_city'); ?></td>
    </tr>

<?php
  if ( ACCOUNT_STATE > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_STATE; ?></td>
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
      echo osc_draw_input_field('ab_state');
    }
?>

      </td>
    </tr>

<?php
  }
?>

    <tr>
      <td width="30%"><?php echo ENTRY_COUNTRY; ?></td>
      <td width="70%">

<?php
  $countries_array = array();

  foreach ( osC_Address::getCountries() as $country ) {
    $countries_array[] = array('id' => $country['id'],
                               'text' => $country['name']);
  }

  echo osc_draw_pull_down_menu('ab_country', $countries_array, STORE_COUNTRY);
?>

      </td>
    </tr>

<?php
  if ( ACCOUNT_TELEPHONE > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_telephone'); ?></td>
    </tr>

<?php
  }

  if ( ACCOUNT_FAX > -1 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_FAX_NUMBER; ?></td>
      <td width="70%"><?php echo osc_draw_input_field('ab_fax'); ?></td>
    </tr>

<?php
  }

  if ( $osC_ObjectInfo->get('customers_default_address_id') > 0 ) {
?>

    <tr>
      <td width="30%"><?php echo ENTRY_SET_AS_PRIMARY; ?></td>
      <td width="70%"><?php echo osc_draw_checkbox_field('ab_primary'); ?></td>
    </tr>

<?php
  }
?>

  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save&tabIndex=tabAddressBook') . '\';" />'; ?></p>

  </form>
</div>
