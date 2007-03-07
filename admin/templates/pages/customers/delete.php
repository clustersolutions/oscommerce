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

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . osc_output_string_protected($osC_ObjectInfo->get('customers_full_name')); ?></div>
<div class="infoBoxContent">
  <form name="cDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&cID=' . $osC_ObjectInfo->get('customers_id') . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=delete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_INTRO; ?></p>

  <p><?php echo '<b>' . osc_output_string_protected($osC_ObjectInfo->get('customers_full_name')) . '</b>'; ?></p>

<?php
  if ( $osC_ObjectInfo->get('total_reviews') > 0 ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_reviews', null, true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $osC_ObjectInfo->get('total_reviews')) . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
