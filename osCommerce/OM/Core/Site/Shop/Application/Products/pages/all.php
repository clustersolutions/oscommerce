<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Site\Shop\Products;
  use osCommerce\OM\Core\Site\Shop\Product;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DateTime;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $OSCOM_Products = new Products();
  $OSCOM_Products->setSortBy('date_added', '-');

  $Qproducts = $OSCOM_Products->execute();

  if ( $Qproducts->numberOfRows() > 0 ) {
    while ( $Qproducts->next() ) {
      $OSCOM_Product = new Product($Qproducts->valueInt('products_id'));
?>

  <tr>
    <td width="<?php echo $OSCOM_Image->getWidth('thumbnails') + 10; ?>" valign="top" align="center">

<?php
      if ( $OSCOM_Product->hasImage() ) {
        echo osc_link_object(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), $OSCOM_Image->show($OSCOM_Product->getImage(), $OSCOM_Product->getTitle()));
      }
?>

    </td>
    <td valign="top"><?php echo osc_link_object(OSCOM::getLink(null, null, $OSCOM_Product->getKeyword()), '<b><u>' . $OSCOM_Product->getTitle() . '</u></b>') . '<br />' . OSCOM::getDef('date_added') . ' ' . DateTime::getLong($OSCOM_Product->getDateAdded()) . '<br />' . OSCOM::getDef('manufacturer') . ' ' . $OSCOM_Product->getManufacturer() . '<br /><br />' . OSCOM::getDef('price') . ' ' . $OSCOM_Product->getPriceFormated(); ?></td>
    <td align="right" valign="middle"><?php echo osc_link_object(OSCOM::getLink(null, 'Cart', 'Add&' . $OSCOM_Product->getKeyword()), osc_draw_image_button('button_in_cart.gif', OSCOM::getDef('button_add_to_cart'))); ?></td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>

<?php
    }
  } else {
?>

  <tr>
    <td><?php echo OSCOM::getDef('no_new_products'); ?></td>
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

  <?php echo $Qproducts->getBatchTotalPages(OSCOM::getDef('result_set_number_of_products')); ?>
</div>
