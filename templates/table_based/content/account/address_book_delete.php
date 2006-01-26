<?php
/*
  $Id:address_book_delete.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if ($_GET['address_book'] == $osC_Customer->getDefaultAddressID()) {
    $messageStack->add('address_book', $osC_Language->get('warning_primary_address_deletion'), 'warning');
  } else {
    if (osC_AddressBook::checkEntry($_GET['address_book'])) {
      $Qentry = osC_AddressBook::getEntry($_GET['address_book']);
    } else {
      $messageStack->add('address_book', $osC_Language->get('error_address_book_entry_non_existing'), 'error');
    }
  }
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_address_book.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('address_book') > 0) {
    echo $messageStack->output('address_book');
  }

  if ( ($_GET['address_book'] != $osC_Customer->getDefaultAddressID()) && isset($Qentry) ) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('address_book_delete_address_title'); ?></div>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo tep_address_label($osC_Customer->getID(), $_GET['address_book'], true, ' ', '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('selected_address_title') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <?php echo $osC_Language->get('address_book_delete_address_description'); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book=' . $_GET['address_book'] . '&delete=confirm', 'SSL') . '">' . tep_image_button('button_delete.gif', $osC_Language->get('button_delete')) . '</a>'; ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>

<?php
  } else {
?>

<div class="submitFormButtons">
  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>

<?php
  }
?>
