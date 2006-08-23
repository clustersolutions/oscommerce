<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1 style="float: right;"><?php echo $osC_Product->getPriceFormated(true); ?></h1>

<h1><?php echo $osC_Template->getPageTitle() . ($osC_Product->hasModel() ? '<br /><span class="smallText">' . $osC_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ($osC_Product->hasImage()) {
?>

<div style="float: right; text-align: center;">
  <?php echo osc_link_object(osc_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword()), $osC_Image->show($osC_Product->getImage(), $osC_Product->getTitle(), 'hspace="5" vspace="5"', 'product_info') . '<br />' . $osC_Language->get('enlarge_image'), 'target="_blank" onclick="window.open(\'' . osc_href_link(FILENAME_PRODUCTS, 'images&' . $osC_Product->getKeyword()) . '\', \'popUp\', \'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=' . (($osC_Product->numberOfImages() > 1) ? $osC_Image->getWidth('large') + ($osC_Image->getWidth('thumbnails') * 2) + 70 : $osC_Image->getWidth('large') + 20) . ',height=' . ($osC_Image->getHeight('large') + 20) . '\'); return false;"'); ?>
</div>

<?php
  }
?>

<form name="cart_quantity" action="<?php echo osc_href_link(FILENAME_PRODUCTS, osc_get_all_get_params(array('action')) . '&action=add_product'); ?>" method="post">

<p><?php echo $osC_Product->getDescription(); ?></p>

<?php
  if ($osC_Product->hasAttributes()) {
?>

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2"><?php echo $osC_Language->get('product_attributes'); ?></td>
  </tr>

<?php
    foreach ($osC_Product->getAttributes() as $options => $values) {
?>

  <tr>
    <td><?php echo $values['options_name'] . ':'; ?></td>
    <td><?php echo osc_draw_pull_down_menu('id[' . $options . ']', $values['data']); ?></td>
  </tr>

<?php
    }
?>

</table>

<?php
  }

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

  if ($osC_Product->getDateAvailable() > date('Y-m-d H:i:s')) {
?>

<p align="center"><?php echo sprintf($osC_Language->get('date_availability'), osC_DateTime::getLong($osC_Product->getDateAvailable())); ?></p>

<?php
  }
?>

<div class="submitFormButtons" style="text-align: right;">

<?php
  if ($osC_Services->isStarted('reviews')) {
    echo '<span style="float: left;">' . osc_link_object(osc_href_link(FILENAME_PRODUCTS, 'reviews&' . osc_get_all_get_params()), osc_draw_image_button('button_reviews.gif', $osC_Language->get('button_reviews'))) . '</span>';
  }
?>
  <?php echo osc_draw_hidden_field('products_id', osc_get_product_id($osC_Product->getID())) . osc_draw_image_submit_button('button_in_cart.gif', $osC_Language->get('button_add_to_cart')); ?>
</div>

</form>
