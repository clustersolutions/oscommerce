<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $osC_Products = new osC_Products();
  $osC_Products->setSortBy('date_added', '-');

  $Qproducts = $osC_Products->execute();

  if ($Qproducts->numberOfRows() > 0) {
    while ($Qproducts->next()) {
      $osC_Product = new osC_Product($Qproducts->valueInt('products_id'));
?>

  <tr>
    <td width="<?php echo $osC_Image->getWidth('thumbnails') + 10; ?>" valign="top" align="center">

<?php
      if ( $osC_Product->hasImage() ) {
        echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()), $osC_Image->show($osC_Product->getImage(), $osC_Product->getTitle()));
      }
?>

    </td>
    <td valign="top"><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()), '<b><u>' . $osC_Product->getTitle() . '</u></b>') . '<br />' . $osC_Language->get('date_added') . ' ' . osC_DateTime::getLong($osC_Product->getDateAdded()) . '<br />' . $osC_Language->get('manufacturer') . ' ' . $osC_Product->getManufacturer() . '<br /><br />' . $osC_Language->get('price') . ' ' . $osC_Product->getPriceFormated(); ?></td>
    <td align="right" valign="middle"><?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword() . '&action=cart_add'), osc_draw_image_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart'))); ?></td>
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
