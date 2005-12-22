<?php
/*
  $Id: shipping.php 5 2005-01-31 01:40:15Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_specials.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<table border="0" width="40%" cellspacing="0" cellpadding="0" align="right">
  <tr>
    <td><?php new infoBoxHeading(array(array('text' => BOX_INFO_SSL_CHECK_HEADING))); ?></td>
  </tr>
  <tr>
    <td><?php new infoBox(array(array('text' => BOX_INFO_SSL_CHECK_CONTENT))); ?></td>
  </tr>
</table>

<p><?php echo TEXT_INFO_SSL_CHECK; ?></p>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_INFO) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></span>
</div>
