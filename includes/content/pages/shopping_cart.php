<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_cart.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($cart->count_contents() > 0) {
    $any_out_of_stock = 0;
    $products = $cart->get_products();
?>

<form name="shopping_cart" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'action=update_product', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <div class="outsideHeading"><?php echo HEADING_TITLE_CHECKOUT_SHOPPING_CART; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $products_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '"><b>' . $products[$i]['name'] . '</b></a> (Top Category)';

// Push all attributes information in an array
      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
        while (list($option, $value) = each($products[$i]['attributes'])) {
          $products_name .= osc_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);

          $Qattributes = $osC_Database->query('select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from :table_products_options popt, :table_products_options_values poval, :table_products_attributes pa where pa.products_id = :products_id and pa.options_id = :options_id and pa.options_id = popt.products_options_id and pa.options_values_id = :options_values_id and pa.options_values_id = poval.products_options_values_id and popt.language_id = :language_id and poval.language_id = :language_id');
          $Qattributes->bindTable(':table_products_options', TABLE_PRODUCTS_OPTIONS);
          $Qattributes->bindTable(':table_products_options_values', TABLE_PRODUCTS_OPTIONS_VALUES);
          $Qattributes->bindTable(':table_products_attributes', TABLE_PRODUCTS_ATTRIBUTES);
          $Qattributes->bindInt(':products_id', $products[$i]['id']);
          $Qattributes->bindInt(':options_id', $option);
          $Qattributes->bindInt(':options_values_id', $value);
          $Qattributes->bindInt(':language_id', $osC_Session->value('languages_id'));
          $Qattributes->bindInt(':language_id', $osC_Session->value('languages_id'));
          $Qattributes->execute();

          $products[$i][$option]['products_options_name'] = $Qattributes->value('products_options_name');
          $products[$i][$option]['options_values_id'] = $value;
          $products[$i][$option]['products_options_values_name'] = $Qattributes->value('products_options_values_name');
          $products[$i][$option]['options_values_price'] = $Qattributes->value('options_values_price');
          $products[$i][$option]['price_prefix'] = $Qattributes->value('price_prefix');
        }
      }

      if (STOCK_CHECK == 'true') {
        $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
        if (tep_not_null($stock_check)) {
          $any_out_of_stock = 1;

          $products_name .= $stock_check;
        }
      }

      if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
        reset($products[$i]['attributes']);
        while (list($option, $value) = each($products[$i]['attributes'])) {
          $products_name .= '<br>- ' . $products[$i][$option]['products_options_name'] . ': ' . $products[$i][$option]['products_options_values_name'];
        }
      }
?>
      <tr>
        <td valign="top" width="60"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'action=cartRemove&products_id=' . $products[$i]['id'], 'SSL') . '">' . tep_image_button('small_delete.gif', SMALL_IMAGE_BUTTON_DELETE) . '</a>'; ?></td>
        <td valign="top"><?php echo $products_name; ?></td>
        <td valign="top"><?php echo osc_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . osc_draw_hidden_field('products_id[]', $products[$i]['id']); ?></td>
        <td valign="top" align="right"><?php echo '<b>' . $osC_Currencies->displayPrice($products[$i]['final_price'], $products[$i]['tax_class_id'], $products[$i]['quantity']) . '</b>'; ?></td>
      </tr>

<?php
    }
?>
    </table>
  </div>

  <p style="text-align: right; padding-right: 7px;"><b><?php echo SUB_TITLE_SUB_TOTAL; ?> <?php echo $osC_Currencies->format($cart->show_total()); ?></b></p>

<?php
    if ($any_out_of_stock == 1) {
      if (STOCK_ALLOW_CHECKOUT == 'true') {
        echo '<p class="stockWarning" align="center">' . OUT_OF_STOCK_CAN_CHECKOUT . '</p>';
      } else {
        echo '<p class="stockWarning" align="center">' . OUT_OF_STOCK_CANT_CHECKOUT . '</p>';
      }
    }
?>

</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT) . '</a>'; ?></span>

  <?php echo tep_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART); ?>
</div>

</form>

<?php
  } else {
?>

<div class="moduleBox">
  <div class="content">
    <?php echo TEXT_CART_EMPTY; ?>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></span>
</div>

<?php
  }
?>

<p>&nbsp;</p>

<div class="moduleBox">
  <div class="outsideHeading">Continue Shopping</div>

  <div class="content">
    [The previously shown categories and products should be listed here ala amazon style. This replaces the need for a continue shopping button.]
  </div>
</div>
