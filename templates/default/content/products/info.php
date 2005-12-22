<?php
/*
  $Id: index.php 199 2005-09-22 17:56:13 +0200 (Do, 22 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1 style="float: right;"><?php echo $osC_Product->getPriceFormated(true); ?></h1>

<h1><?php echo $osC_Template->getPageTitle() . ($osC_Product->hasModel() ? '<br /><span class="smallText">' . $osC_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ($osC_Product->hasImage()) {
?>

<div style="float: right;">
  <script type="text/javascript"><!--
    document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $osC_Product->getID()) , '\\\')">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), addslashes($osC_Product->getTitle()), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
  //--></script>
  <noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $osC_Product->getImage()) . '" target="_blank">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), $osC_Product->getTitle(), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br />' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
  </noscript>
</div>

<?php
  }
?>

<?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCTS, tep_get_all_get_params(array('action')) . 'action=add_product')); ?>

<p><?php echo $osC_Product->getDescription(); ?></p>

<?php
  if ($osC_Product->hasAttributes()) {
?>

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main" colspan="2"><?php echo TEXT_PRODUCT_OPTIONS; ?></td>
  </tr>

<?php
    foreach ($osC_Product->getAttributes() as $options => $values) {
      if (isset($_SESSION['cart']->contents[$osC_Product->getID()]['attributes'][$options])) {
        $selected_attribute = $_SESSION['cart']->contents[$osC_Product->getID()]['attributes'][$options];
      } else {
        $selected_attribute = false;
      }
?>

  <tr>
    <td class="main"><?php echo $values['options_name'] . ':'; ?></td>
    <td class="main"><?php echo osc_draw_pull_down_menu('id[' . $options . ']', $values['data'], $selected_attribute); ?></td>
  </tr>

<?php
    }
?>

</table>

<?php
  }

  if ($osC_Services->isStarted('reviews') && osC_Reviews::exists(tep_get_prid($osC_Product->getID()))) {
?>

<p><?php echo TEXT_CURRENT_REVIEWS . ' ' . osC_Reviews::getTotal(tep_get_prid($osC_Product->getID())); ?></p>

<?php
  }

  if ($osC_Product->hasURL()) {
?>

<p><?php echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&amp;goto=' . urlencode($osC_Product->getURL()), 'NONSSL', true, false)); ?></p>

<?php
  }

  if ($osC_Product->getDateAvailable() > date('Y-m-d H:i:s')) {
?>

<p align="center"><?php echo sprintf(TEXT_DATE_AVAILABLE, tep_date_long($osC_Product->getDateAvailable())); ?></p>

<?php
  }
?>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo osc_draw_hidden_field('products_id', tep_get_prid($osC_Product->getID())) . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART); ?></span>

<?php
  if ($osC_Services->isStarted('reviews')) {
    echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews&amp;' . tep_get_all_get_params()) . '">' . tep_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS) . '</a>';
  }
?>
</div>

</form>
