<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

// create column list
  $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);

  asort($define_list);

  $column_list = array();
  reset($define_list);
  while (list($key, $value) = each($define_list)) {
    if ($value > 0) $column_list[] = $key;
  }

  if ( ($Qlisting->numberOfRows() > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $Qlisting->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo $Qlisting->displayBatchLinksPullDown('page', tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }
?>
<div>
<?php
  if ($Qlisting->numberOfRows() > 0) {
?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
<?php
    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      $lc_key = false;
      $lc_align = '';

      switch ($column_list[$col]) {
        case 'PRODUCT_LIST_MODEL':
          $lc_text = TABLE_HEADING_MODEL;
          $lc_key = 'model';
          break;
        case 'PRODUCT_LIST_NAME':
          $lc_text = TABLE_HEADING_PRODUCTS;
          $lc_key = 'name';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $lc_text = TABLE_HEADING_MANUFACTURER;
          $lc_key = 'manufacturer';
          break;
        case 'PRODUCT_LIST_PRICE':
          $lc_text = TABLE_HEADING_PRICE;
          $lc_key = 'price';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $lc_text = TABLE_HEADING_QUANTITY;
          $lc_key = 'quantity';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $lc_text = TABLE_HEADING_WEIGHT;
          $lc_key = 'weight';
          $lc_align = 'right';
          break;
        case 'PRODUCT_LIST_IMAGE':
          $lc_text = TABLE_HEADING_IMAGE;
          $lc_align = 'center';
          break;
        case 'PRODUCT_LIST_BUY_NOW':
          $lc_text = TABLE_HEADING_BUY_NOW;
          $lc_align = 'center';
          break;
      }

      if ($lc_key !== false) {
        $lc_text = tep_create_sort_heading($lc_key, $lc_text);
      }

      echo '      <td align="' . $lc_align . '" class="productListing-heading">&nbsp;' . $lc_text . '&nbsp;</td>' . "\n";
    }
?>
    </tr>
<?php
    $rows = 0;

    while ($Qlisting->next()) {
      $rows++;

      echo '    <tr class="' . ((($rows/2) == floor($rows/2)) ? 'productListing-even' : 'productListing-odd') . '">' . "\n";

      $cur_row = sizeof($list_box_contents) - 1;

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $Qlisting->value('products_model') . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($_GET['manufacturers'])) {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_keyword') . '&amp;manufacturers=' . $_GET['manufacturers']) . '">' . $Qlisting->value('products_name') . '</a>';
            } else {
              $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_keyword') . ($cPath ? '&cPath=' . $cPath : '')) . '">' . $Qlisting->value('products_name') . '</a>&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers=' . $Qlisting->valueInt('manufacturers_id')) . '">' . $Qlisting->valueInt('manufacturers_name') . '</a>&nbsp;';
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_align = 'right';
            if (tep_not_null($Qlisting->value('specials_new_products_price'))) {
              $lc_text = '&nbsp;<s>' .  $osC_Currencies->displayPrice($Qlisting->value('products_price'), $Qlisting->valueInt('products_tax_class_id')) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($Qlisting->value('specials_new_products_price'), $Qlisting->valueInt('products_tax_class_id')) . '</span>&nbsp;';
            } else {
              $lc_text = '&nbsp;' . $osC_Currencies->displayPrice($Qlisting->value('products_price'), $Qlisting->valueInt('products_tax_class_id')) . '&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $Qlisting->valueInt('products_quantity') . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $osC_Weight->display($Qlisting->value('products_weight'), $Qlisting->value('products_weight_class')) . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_align = 'center';
            if (isset($_GET['manufacturers'])) {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_keyword') . '&amp;manufacturers=' . $_GET['manufacturers']) . '">' . tep_image(DIR_WS_IMAGES . $Qlisting->value('products_image'), $Qlisting->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>';
            } else {
              $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qlisting->value('products_keyword') . ($cPath ? '&cPath=' . $cPath : '')) . '">' . tep_image(DIR_WS_IMAGES . $Qlisting->value('products_image'), $Qlisting->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>&nbsp;';
            }
            break;
          case 'PRODUCT_LIST_BUY_NOW':
            $lc_align = 'center';
            $lc_text = '<a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action')) . 'action=buy_now&amp;products_id=' . $Qlisting->valueInt('products_id')) . '">' . tep_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
            break;
        }

        echo '      <td ' . ((empty($lc_align) === false) ? 'align="' . $lc_align . '" ' : '') . 'class="productListing-data">' . $lc_text . '</td>' . "\n";
      }

      echo '    </tr>' . "\n";
    }

    echo '  </table>' . "\n";
  } else {
?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php echo TEXT_NO_PRODUCTS; ?></td>
    </tr>
  </table>
<?php
  }
?>
</div>
<?php
  if ( ($Qlisting->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $Qlisting->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo $Qlisting->displayBatchLinksPullDown('page', tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }
?>
