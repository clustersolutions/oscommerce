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

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>

<?php
  if ($osC_Product->hasImage()) {
?>

  <div style="float: left; text-align: center; padding: 0 10px 10px 0; width: <?php echo $osC_Image->getWidth('product_info'); ?>px;">
    <?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword()), $osC_Image->show($osC_Product->getImage(), $osC_Product->getTitle(), null, 'product_info'), 'target="_blank" onclick="window.open(\'' . osc_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword()) . '\', \'popUp\', \'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=' . (($osC_Product->numberOfImages() > 1) ? $osC_Image->getWidth('large') + ($osC_Image->getWidth('thumbnails') * 2) + 70 : $osC_Image->getWidth('large') + 20) . ',height=' . ($osC_Image->getHeight('large') + 20) . '\'); return false;"'); ?>
  </div>

<?php
  }
?>

  <div style="<?php if ( $osC_Product->hasImage() ) { echo 'margin-left: ' . ($osC_Image->getWidth('product_info') + 20) . 'px; '; } ?>min-height: <?php echo $osC_Image->getHeight('product_info'); ?>px;">
    <form name="cart_quantity" action="<?php echo osc_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword() . '&action=cart_add'); ?>" method="post">

    <div style="float: right;">
      <?php echo osc_draw_image_submit_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart')); ?>
    </div>

    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="productInfoKey">Price:</td>
        <td class="productInfoValue"><span id="productInfoPrice"><?php echo $osC_Product->getPriceFormated(true); ?></span> (plus <?php echo osc_link_object(osc_href_link(FILENAME_INFO, 'shipping'), 'shipping'); ?>)</td>
      </tr>

<?php
  if ( $osC_Product->hasAttribute('shipping_availability') ) {
?>

      <tr>
        <td class="productInfoKey">Availability:</td>
        <td class="productInfoValue" id="productInfoAvailability"><?php echo $osC_Product->getAttribute('shipping_availability'); ?></td>
      </tr>

<?php
  }
?>

    </table>

<?php
  if ( $osC_Product->hasVariants() ) {
?>

    <div id="variantsBlock">
      <div id="variantsBlockTitle"><?php echo $osC_Language->get('product_attributes'); ?></div>

      <div id="variantsBlockData">

<?php
    foreach ( $osC_Product->getVariants() as $group_id => $value ) {
      echo osC_Variants::parse($value['module'], $value);
    }

    echo osC_Variants::defineJavascript($osC_Product->getVariants(false));
?>

      </div>
    </div>

<?php
  }
?>

    </form>
  </div>
</div>

<div style="clear: both;"></div>

<table border="0" cellspacing="0" cellpadding="0">

<?php
  if ( $osC_Product->hasAttribute('manufacturers') ) {
?>

  <tr>
    <td class="productInfoKey">Manufacturer:</td>
    <td class="productInfoValue"><?php echo $osC_Product->getAttribute('manufacturers'); ?></td>
  </tr>

<?php
  }
?>

  <tr>
    <td class="productInfoKey">Model:</td>
    <td class="productInfoValue"><span id="productInfoModel"><?php echo $osC_Product->getModel(); ?></span></td>
  </tr>

<?php
  if ( $osC_Product->hasAttribute('date_available') ) {
?>

  <tr>
    <td class="productInfoKey">Date Available:</td>
    <td class="productInfoValue"><?php echo osC_DateTime::getShort($osC_Product->getAttribute('date_available')); ?></td>
  </tr>

<?php
  }
?>

</table>

<?php
  if ( $osC_Product->hasVariants() ) {
?>

<script language="javascript" type="text/javascript">
  var originalPrice = '<?php echo $osC_Product->getPriceFormated(true); ?>';
  var productInfoNotAvailable = '<span id="productVariantCombinationNotAvailable">Not available in this combination. Please select another combination for your order.</span>';
  var productInfoAvailability = '<?php if ( $osC_Product->hasAttribute('shipping_availability') ) { echo addslashes($osC_Product->getAttribute('shipping_availability')); } ?>';

  refreshVariants();
</script>

<?php
  }
?>

<div>
  <?php echo $osC_Product->getDescription(); ?>
</div>

<?php
  if ($osC_Services->isStarted('reviews') && osC_Reviews::exists(osc_get_product_id($osC_Product->getID()))) {
?>

<p><?php echo $osC_Language->get('number_of_product_reviews') . ' ' . osC_Reviews::getTotal(osc_get_product_id($osC_Product->getID())); ?></p>

<?php
  }

  if ($osC_Product->hasURL()) {
?>

<p><?php echo sprintf($osC_Language->get('go_to_external_products_webpage'), osc_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($osC_Product->getURL()), 'NONSSL', null, false)); ?></p>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">

<?php
  if ($osC_Services->isStarted('reviews')) {
    echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, 'reviews&' . osc_get_all_get_params()), osc_draw_image_button('button_reviews.gif', $osC_Language->get('button_reviews')));
  }
?>

</div>

</form>
