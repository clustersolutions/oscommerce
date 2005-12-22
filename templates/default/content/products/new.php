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
    <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qproducts->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qproducts->value('products_image'), $Qproducts->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
    <td valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qproducts->valueInt('products_id')) . '"><b><u>' . $Qproducts->value('products_name') . '</u></b></a><br />' . TEXT_DATE_ADDED . ' ' . tep_date_long($Qproducts->value('products_date_added')) . '<br />' . TEXT_MANUFACTURER . ' ' . $Qproducts->value('manufacturers_name') . '<br /><br />' . TEXT_PRICE . ' ' . $products_price; ?></td>
    <td align="right" valign="middle" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=buy_now&amp;products_id=' . $Qproducts->value('products_id')) . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a>'; ?></td>
  </tr>
  <tr>
    <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>

<?php
    }
  } else {
?>

  <tr>
    <td class="main"><?php echo TEXT_NO_NEW_PRODUCTS; ?></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>

<?php
  }
?>

</table>

<div class="listingPageLinks">
  <span style="float: right;"><?php echo $Qproducts->displayBatchLinksPullDown('page', 'new'); ?></span>

  <?php echo $Qproducts->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?>
</div>
