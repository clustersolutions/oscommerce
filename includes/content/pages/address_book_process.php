<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['edit'])) {
    $Qentry = $osC_Database->query('select entry_gender, entry_company, entry_firstname, entry_lastname, entry_street_address, entry_suburb, entry_postcode, entry_city, entry_state, entry_zone_id, entry_country_id, entry_telephone, entry_fax from :table_address_book where customers_id = :customers_id and address_book_id = :address_book_id');
    $Qentry->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
    $Qentry->bindInt(':customers_id', $osC_Customer->id);
    $Qentry->bindInt(':address_book_id', $_GET['address_book']);
    $Qentry->execute();

    if ($Qentry->numberOfRows() < 1) {
      $messageStack->add('address_book', ERROR_NONEXISTING_ADDRESS_BOOK_ENTRY, 'error');
    }

    $page_heading_title = HEADING_TITLE_ADDRESS_BOOK_EDIT_ENTRY;
  } else {
    if (($counter = tep_count_customer_address_book_entries()) >= MAX_ADDRESS_BOOK_ENTRIES) {
      $messageStack->add('address_book', ERROR_ADDRESS_BOOK_FULL);
    }

    $page_heading_title = HEADING_TITLE_ADDRESS_BOOK_ADD_ENTRY;
  }

  require('includes/form_check.js.php');
?>

<div class="pageHeading">
  <span class="pageHeadingImage"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_address_book.gif', $page_heading_title, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></span>

  <h1><?php echo $page_heading_title; ?></h1>
</div>

<?php
  if ($messageStack->size('address_book') > 0) {
    echo $messageStack->output('address_book');
  }

  if ( ($osC_Customer->hasDefaultAddress() === false) || (isset($_GET['new']) && ($counter < MAX_ADDRESS_BOOK_ENTRIES)) || (isset($Qentry) && ($Qentry->numberOfRows() === 1)) ) {
?>

<form name="address_book" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'address_book=' . $_GET['address_book'] . '&' . (isset($_GET['edit']) ? 'edit' : 'new') . '=save', 'SSL'); ?>" method="post" onSubmit="return check_form(address_book);">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo FORM_REQUIRED_INFORMATION; ?></span>

    <?php echo NEW_ADDRESS_TITLE; ?>
  </div>

  <div class="content">

<?php
  require('includes/modules/address_book_details.php');
?>

  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

<?php
    if (sizeof($navigation->snapshot) > 0) {
      $back_link = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array($osC_Session->name)), $navigation->snapshot['mode']);
    } elseif ($osC_Customer->hasDefaultAddress() === false) {
      $back_link = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
    } else {
      $back_link = tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL');
    }

    echo '<a href="' . $back_link . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>';
?>

</div>

</form>

<?php
  } else {
?>

<div class="submitFormButtons">
  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

<?php
  }
?>
