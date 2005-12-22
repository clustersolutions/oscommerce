<?php
/*
  $Id: index.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>

<?php
  $Qspecials = osC_Specials::getListing();

  $row = 0;

  while ($Qspecials->next()) {
    $row++;

    echo '    <td align="center" width="33%" class="smallText"><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qspecials->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qspecials->value('products_image'), $Qspecials->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br /><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qspecials->valueInt('products_id')) . '">' . $Qspecials->value('products_name') . '</a><br /><s>' . $osC_Currencies->displayPrice($Qspecials->value('products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</s><br /><span class="productSpecialPrice">' . $osC_Currencies->displayPrice($Qspecials->value('specials_new_products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</span></td>' . "\n";

    if ((($row / 3) == floor($row / 3))) {
?>

  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
  <tr>

<?php
    }
  }
?>

  </tr>
</table>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qspecials->displayBatchLinksPullDown(); ?></span>

  <?php echo $Qspecials->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?>
</div>
