<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['cID']) && is_numeric($_GET['cID'])) {
    $Qaccount = $osC_Database->query('select customers_id, customers_gender, customers_firstname, customers_lastname, dayofmonth(customers_dob) as customers_dob_day, month(customers_dob) as customers_dob_month, year(customers_dob) as customers_dob_year, customers_email_address, customers_newsletter, customers_default_address_id, customers_status from :table_customers where customers_id = :customers_id');
    $Qaccount->bindTable(':table_customers', TABLE_CUSTOMERS);
    $Qaccount->bindInt(':customers_id', $_GET['cID']);
    $Qaccount->execute();

    if ($Qaccount->value('customers_default_address_id') > 0) {
      $Qab = $osC_Database->query('select entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_zone_id, entry_country_id, entry_telephone, entry_fax from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
      $Qab->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
      $Qab->bindInt(':customers_id', $_GET['cID']);
      $Qab->bindInt(':address_book_id', $Qaccount->valueInt('customers_default_address_id'));
      $Qab->execute();
    }
  }

  if (ACCOUNT_GENDER > -1) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('customers', FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . (isset($_GET['cID']) ? '&cID=' . $_GET['cID'] : '') . '&action=save'); ?>

<p class="formAreaTitle"><?php echo CATEGORY_PERSONAL; ?></p>

<div class="formArea">
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
<?php
  if (ACCOUNT_GENDER > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_GENDER; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_radio_field('gender', $gender_array, (isset($Qaccount) ? $Qaccount->value('customers_gender') : ''), '', (ACCOUNT_GENDER > 0)); ?></td>
    </tr>
<?php
  }
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_FIRST_NAME; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('firstname', (isset($Qaccount) ? $Qaccount->value('customers_firstname') : ''), '', true); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_LAST_NAME; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('lastname', (isset($Qaccount) ? $Qaccount->value('customers_lastname') : ''), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_DATE_OF_BIRTH > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
      <td class="main" width="70%"><?php echo tep_draw_date_pull_down_menu('dob', (isset($Qaccount) ? adodb_mktime(0, 0, 0, $Qaccount->valueInt('customers_dob_month'), $Qaccount->valueInt('customers_dob_day'), $Qaccount->valueInt('customers_dob_year')) : ''), false, true, true, date('Y')-1901, -5) . '&nbsp;<span class="inputRequirement">*</span>'; ?></td>
    </tr>
<?php
  }
?>
    <tr>
      <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('email_address', (isset($Qaccount) ? $Qaccount->value('customers_email_address') : ''), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_NEWSLETTER > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_NEWSLETTER; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_checkbox_field('newsletter', '', (isset($Qaccount) && ($Qaccount->valueInt('customers_newsletter') === 1) ? true : false)); ?></td>
    </tr>
<?php
  }
?>
    <tr>
     <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_PASSWORD; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_password_field('password', '', '', true); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_password_field('confirmation', '', '', true); ?></td>
    </tr>
    <tr>
     <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_STATUS; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_checkbox_field('status', '', (isset($Qaccount) && ($Qaccount->valueInt('customers_status') === 1) ? true : false)); ?></td>
    </tr>
  </table>
</div>

<p class="formAreaTitle"><?php echo CATEGORY_ADDRESS; ?></p>

<div class="formArea">
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
<?php
  if (ACCOUNT_GENDER > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_GENDER; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_radio_field('ab_gender', $gender_array, (isset($Qab) ? $Qab->value('entry_gender') : ''), '', (ACCOUNT_GENDER > 0)); ?></td>
    </tr>
<?php
  }
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_FIRST_NAME; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_firstname', (isset($Qab) ? $Qab->value('entry_firstname') : ''), '', true); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_LAST_NAME; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_lastname', (isset($Qab) ? $Qab->value('entry_lastname') : ''), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_COMPANY > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_COMPANY; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_company', (isset($Qab) ? $Qab->value('entry_company') : ''), '', (ACCOUNT_COMPANY > 0)); ?></td>
    </tr>
<?php
  }
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_STREET_ADDRESS; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_street_address', (isset($Qab) ? $Qab->value('entry_street_address') : ''), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_SUBURB > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_SUBURB; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_suburb', (isset($Qab) ? $Qab->value('entry_suburb') : ''), '', (ACCOUNT_SUBURB > 0)); ?></td>
    </tr>
<?php
  }
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_POST_CODE; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_postcode', (isset($Qab) ? $Qab->value('entry_postcode') : ''), '', true); ?></td>
    </tr>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_CITY; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_city', (isset($Qab) ? $Qab->value('entry_city') : ''), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_STATE > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_STATE; ?></td>
      <td class="main" width="70%">
<?php
    if ( ($action == 'save') && isset($entry_state_has_zones) && ($entry_state_has_zones === true) ) {
      $Qzones = $osC_Database->query('select zone_name from :table_zones where zone_country_id = :zone_country_id order by zone_name');
      $Qzones->bindTable(':table_zones', TABLE_ZONES);
      $Qzones->bindInt(':zone_country_id', $_POST['ab_country']);
      $Qzones->execute();

      $zones_array = array();
      while ($Qzones->next()) {
        $zones_array[] = array('id' => $Qzones->value('zone_name'), 'text' => $Qzones->value('zone_name'));
      }

      echo osc_draw_pull_down_menu('ab_state', $zones_array, '', '', (ACCOUNT_STATE > 0));
    } else {
      echo osc_draw_input_field('ab_state', (isset($Qab) ? tep_get_zone_name($Qab->value('entry_country_id'), $Qab->value('entry_zone_id'), $Qab->value('entry_state')) : ''), '', (ACCOUNT_STATE > 0));
    }
?>
      </td>
    </tr>
<?php
  }
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_COUNTRY; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_pull_down_menu('ab_country', tep_get_countries(), (isset($Qab) ? $Qab->value('entry_country_id') : STORE_COUNTRY), '', true); ?></td>
    </tr>
<?php
  if (ACCOUNT_TELEPHONE > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_telephone', (isset($Qab) ? $Qab->value('entry_telephone') : ''), '', (ACCOUNT_TELEPHONE > 0)); ?></td>
    </tr>
<?php
  }

  if (ACCOUNT_FAX > -1) {
?>
    <tr>
      <td class="main" width="30%"><?php echo ENTRY_FAX_NUMBER; ?></td>
      <td class="main" width="70%"><?php echo osc_draw_input_field('ab_fax', (isset($Qab) ? $Qab->value('entry_fax') : ''), '', (ACCOUNT_FAX > 0)); ?></td>
    </tr>
<?php
  }
?>
  </table>
</div>

<p align="right"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" class="operationButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . (isset($_GET['cID']) ? '&cID=' . $_GET['cID'] : '')) . '\';">'; ?></p>

</form>
