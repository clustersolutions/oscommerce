<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('information_title'); ?></h6>

  <div class="content">
    <?php echo tep_image(DIR_WS_IMAGES . 'account_personal.gif', $osC_Language->get('information_title'), null, null, 'style="float: left;"'); ?>

    <ul style="padding-left: 100px; list-style-image: url(<?php echo tep_href_link(DIR_WS_IMAGES . 'arrow_green.gif'); ?>);">
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'shipping') . '">' . $osC_Language->get('box_information_shipping') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'privacy') . '">' . $osC_Language->get('box_information_privacy') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'conditions') . '">' . $osC_Language->get('box_information_conditions') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">' . $osC_Language->get('box_information_contact') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'sitemap') . '">' . $osC_Language->get('box_information_sitemap') . '</a>'; ?></li>
    </ul>

    <div style="clear: both;"></div>
  </div>
</div>
