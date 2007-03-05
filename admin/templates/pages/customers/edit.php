<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  if ( ACCOUNT_GENDER > -1 ) {
    $gender_array = array(array('id' => 'm', 'text' => MALE),
                          array('id' => 'f', 'text' => FEMALE));
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Customers_Admin::getData($_GET['cID']));
?>

<link type="text/css" rel="stylesheet" href="external/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="external/tabpane/js/tabpane.js"></script>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="tab-pane" id="mainTabPane">
  <script type="text/javascript"><!--
    var mainTabPane = new WebFXTabPane( document.getElementById( "mainTabPane" ) );
  //--></script>

  <div class="tab-page" id="tabData">
    <h2 class="tab"><?php echo CATEGORY_PERSONAL; ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabData" ) );
    //--></script>

    <form name="customers" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  if ( ACCOUNT_GENDER > -1 ) {
?>

      <tr>
        <td width="30%"><?php echo ENTRY_GENDER; ?></td>
        <td width="70%"><?php echo osc_draw_radio_field('gender', $gender_array, $osC_ObjectInfo->get('customers_gender')); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td width="30%"><?php echo ENTRY_FIRST_NAME; ?></td>
        <td width="70%"><?php echo osc_draw_input_field('firstname', $osC_ObjectInfo->get('customers_firstname')); ?></td>
      </tr>
      <tr>
        <td width="30%"><?php echo ENTRY_LAST_NAME; ?></td>
        <td width="70%"><?php echo osc_draw_input_field('lastname', $osC_ObjectInfo->get('customers_lastname')); ?></td>
      </tr>

<?php
  if ( ACCOUNT_DATE_OF_BIRTH == '1' ) {
?>

      <tr>
        <td width="30%"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
        <td width="70%"><?php echo osc_draw_date_pull_down_menu('dob', array('year' => $osC_ObjectInfo->get('customers_dob_year'), 'month' => $osC_ObjectInfo->get('customers_dob_month'), 'date' => $osC_ObjectInfo->get('customers_dob_date')), false, null, null, date('Y')-1901, -5); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
        <td width="70%"><?php echo osc_draw_input_field('email_address', $osC_ObjectInfo->get('customers_email_address')); ?></td>
      </tr>

<?php
  if ( ACCOUNT_NEWSLETTER == '1' ) {
?>

      <tr>
        <td width="30%"><?php echo ENTRY_NEWSLETTER; ?></td>
        <td width="70%"><?php echo osc_draw_checkbox_field('newsletter', null, ($osC_ObjectInfo->get('customers_newsletter') == '1')); ?></td>
      </tr>

<?php
  }
?>

      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo ENTRY_PASSWORD; ?></td>
        <td width="70%"><?php echo osc_draw_password_field('password'); ?></td>
      </tr>
      <tr>
        <td width="30%"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
        <td width="70%"><?php echo osc_draw_password_field('confirmation'); ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="30%"><?php echo ENTRY_STATUS; ?></td>
        <td width="70%"><?php echo osc_draw_checkbox_field('status', null, ($osC_ObjectInfo->get('customers_status') == '1')); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" />'; ?></p>

    </form>
  </div>

  <div class="tab-page" id="tabAddressBook">
    <h2 class="tab"><?php echo CATEGORY_ADDRESS_BOOK; ?></h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabAddressBook" ) );

<?php
  if ( isset($_GET['tabIndex']) && ( $_GET['tabIndex'] == 'tabAddressBook' ) ) {
    echo 'mainTabPane.setSelectedIndex( mainTabPane.pages.length - 1 );';
  }
?>

    //--></script>

    <p><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=saveAddress'), osc_icon('new.png') . ' New Address Book Entry'); ?></p>

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
          echo osc_icon('user_male.png', MALE) . '&nbsp;';
          break;

        case 'f':
          echo osc_icon('user_female.png', FEMALE) . '&nbsp;';
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
      echo '&nbsp;<i>(primary)</i>';
    }
?>

        </td>
        <td align="right">

<?php
    echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&abID=' . $Qaddresses->valueInt('address_book_id') . '&action=saveAddress'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $_GET['cID'] . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&abID=' . $Qaddresses->valueInt('address_book_id') . '&action=deleteAddress'), osc_icon('trash.png', IMAGE_DELETE)) . '&nbsp;';
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
      echo '<small><i>(no telephone number)</i></small>';
    }

    echo osc_icon('print.png', null, '16x16', 'style="margin-left: 16px;"') . '&nbsp;';

    if ( !osc_empty($Qaddresses->valueProtected('fax_number')) ) {
      echo $Qaddresses->valueProtected('fax_number');
    } else {
      echo '<small><i>(no fax number)</i></small>';
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

<p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" />'; ?></p>
