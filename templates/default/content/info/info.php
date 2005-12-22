<?php
/*
  $Id: account.php 95 2005-03-28 21:56:29Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_ACCOUNT_TITLE; ?></div>

  <div class="content">
    <div style="float: left; width: 130px;"><?php echo tep_image(DIR_WS_IMAGES . 'account_personal.gif'); ?></div>

    <ul style="list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif'); ?>);">
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'shipping') . '">' . BOX_INFORMATION_SHIPPING . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'privacy') . '">' . BOX_INFORMATION_PRIVACY . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'conditions') . '">' . BOX_INFORMATION_CONDITIONS . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">' . BOX_INFORMATION_CONTACT . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'sitemap') . '">' . BOX_INFORMATION_SITEMAP . '</a>'; ?></li>
    </ul>
  </div>
</div>
