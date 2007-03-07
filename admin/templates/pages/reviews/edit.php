<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_ObjectInfo = new osC_ObjectInfo(osC_Reviews_Admin::getData($_GET['rID']));

  $rating_array = array();

  for ($i=1; $i<=5; $i++) {
    $rating_array[] = array('id' => $i, 'text' => '');
  }
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $osC_ObjectInfo->get('products_name'); ?></div>
<div class="infoBoxContent">
  <form name="review" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&rID=' . $_GET['rID'] . '&action=save'); ?>" method="post">

  <p><?php echo '<b>' . ENTRY_PRODUCT . '</b><br />' . $osC_ObjectInfo->get('products_name'); ?></p>
  <p><?php echo '<b>' . ENTRY_FROM . '</b><br />' . osc_output_string_protected($osC_ObjectInfo->get('customers_name')); ?></p>
  <p><?php echo '<b>' . ENTRY_DATE . '</b><br />' . osC_DateTime::getShort($osC_ObjectInfo->get('date_added')); ?></p>
  <p><?php echo '<b>' . ENTRY_REVIEW . '</b><br />' . osc_draw_textarea_field('reviews_text', $osC_ObjectInfo->get('reviews_text')) . '<br />' . ENTRY_REVIEW_TEXT; ?></p>
  <p><?php echo '<b>' . ENTRY_RATING . '</b><br />' . TEXT_BAD . '&nbsp;' . osc_draw_radio_field('reviews_rating', $rating_array, $osC_ObjectInfo->get('reviews_rating')) . '&nbsp;' . TEXT_GOOD; ?></p>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
