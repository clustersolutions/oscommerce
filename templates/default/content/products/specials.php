<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

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

    echo '    <td align="center" width="33%" class="smallText">';

    if (osc_empty($Qspecials->value('image')) === false) {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qspecials->value('products_keyword')) . '">' . $osC_Image->show($Qspecials->value('image'), $Qspecials->value('products_name')) . '</a><br />';
    }

    echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qspecials->value('products_keyword')) . '">' . $Qspecials->value('products_name') . '</a><br /><s>' . $osC_Currencies->displayPrice($Qspecials->value('products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($Qspecials->value('specials_new_products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</span></td>' . "\n";

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

  <?php echo $Qspecials->displayBatchLinksTotal($osC_Language->get('result_set_number_of_products')); ?>
</div>
