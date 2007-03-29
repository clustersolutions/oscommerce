<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $Qproducts = osC_Product::getListingNew();

  if ($Qproducts->numberOfRows() > 0) {
    while ($Qproducts->next()) {
      if ($osC_Services->isStarted('specials') && ($new_price = $osC_Specials->getPrice($Qproducts->valueInt('products_id')))) {
        $products_price = '<s>' . $osC_Currencies->displayPrice($Qproducts->value('products_price'), $Qproducts->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_price, $Qproducts->valueInt('products_tax_class_id')) . '</span>';
      } else {
        $products_price = $osC_Currencies->displayPrice($Qproducts->value('products_price'), $Qproducts->valueInt('products_tax_class_id'));
      }
?>

  <tr>
    <td width="<?php echo $osC_Image->getWidth('thumbnails') + 10; ?>" valign="top" align="center">

<?php
      if (osc_empty($Qproducts->value('image')) === false) {
        echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qproducts->value('products_keyword')), $osC_Image->show($Qproducts->value('image'), $Qproducts->value('products_name')));
      }
?>

    </td>
    <td valign="top"><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qproducts->value('products_keyword')), '<b><u>' . $Qproducts->value('products_name') . '</u></b>') . '<br />' . $osC_Language->get('date_added') . ' ' . osC_DateTime::getLong($Qproducts->value('products_date_added')) . '<br />' . $osC_Language->get('manufacturer') . ' ' . $Qproducts->value('manufacturers_name') . '<br /><br />' . $osC_Language->get('price') . ' ' . $products_price; ?></td>
    <td align="right" valign="middle"><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $Qproducts->value('products_keyword') . '&action=cart_add'), osc_draw_image_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart'))); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>

<?php
    }
  } else {
?>

  <tr>
    <td><?php echo $osC_Language->get('no_new_products'); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>

<?php
  }
?>

</table>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qproducts->getBatchPageLinks('page', 'new'); ?></span>

  <?php echo $Qproducts->getBatchTotalPages($osC_Language->get('result_set_number_of_products')); ?>
</div>
