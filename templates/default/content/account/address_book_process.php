<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['edit'])) {
    $Qentry = osC_AddressBook::getEntry($_GET['address_book']);
  } else {
    if (osC_AddressBook::numberOfEntries() >= MAX_ADDRESS_BOOK_ENTRIES) {
      $messageStack->add('address_book', $osC_Language->get('error_address_book_full'));
    }
  }
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_address_book.gif', $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('address_book') > 0) {
    echo $messageStack->output('address_book');
  }

  if ( ($osC_Customer->hasDefaultAddress() === false) || (isset($_GET['new']) && (osC_AddressBook::numberOfEntries() < MAX_ADDRESS_BOOK_ENTRIES)) || (isset($Qentry) && ($Qentry->numberOfRows() === 1)) ) {
?>

<form name="address_book" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'address_book=' . $_GET['address_book'] . '&' . (isset($_GET['edit']) ? 'edit' : 'new') . '=save', 'SSL'); ?>" method="post" onsubmit="return check_form(address_book);">

<div class="moduleBox">
  <em style="float: right; margin-top: 10px;"><?php echo $osC_Language->get('form_required_information'); ?></em>

  <h6><?php echo $osC_Language->get('address_book_new_address_title'); ?></h6>

  <div class="content">

<?php
    include('includes/modules/address_book_details.php');
?>

  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?></span>

<?php
    if ($osC_NavigationHistory->hasSnapshot()) {
      $back_link = $osC_NavigationHistory->getSnapshotURL();
    } elseif ($osC_Customer->hasDefaultAddress() === false) {
      $back_link = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
    } else {
      $back_link = tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL');
    }

    echo osc_link_object($back_link, tep_image_button('button_back.gif', $osC_Language->get('button_back')));
?>

</div>

</form>

<?php
  } else {
?>

<div class="submitFormButtons">
  <?php osc_link_object(tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL'), tep_image_button('button_back.gif', $osC_Language->get('button_back'))); ?>
</div>

<?php
  }
?>
