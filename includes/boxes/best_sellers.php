<?php
/*
  $Id: best_sellers.php,v 1.23 2004/02/16 06:17:18 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (isset($current_category_id) && ($current_category_id > 0)) {
    $Qbestsellers = $osC_Database->query('select distinct p.products_id, pd.products_name from :table_products p, :table_products_description pd, :table_products_to_categories p2c, :table_categories c where p.products_status = 1 and p.products_ordered > 0 and p.products_id = pd.products_id and pd.language_id = :language_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and :current_category_id in (c.categories_id, c.parent_id) order by p.products_ordered desc, pd.products_name limit :max_display_bestsellers');
    $Qbestsellers->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qbestsellers->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qbestsellers->bindRaw(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qbestsellers->bindRaw(':table_categories', TABLE_CATEGORIES);
    $Qbestsellers->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qbestsellers->bindInt(':current_category_id', $current_category_id);
    $Qbestsellers->bindInt(':max_display_bestsellers', MAX_DISPLAY_BESTSELLERS);
    $Qbestsellers->execute();
  } else {
    $Qbestsellers = $osC_Database->query('select p.products_id, pd.products_name from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_ordered > 0 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_ordered desc, pd.products_name limit :max_display_bestsellers');
    $Qbestsellers->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qbestsellers->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qbestsellers->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qbestsellers->bindInt(':max_display_bestsellers', MAX_DISPLAY_BESTSELLERS);
    $Qbestsellers->execute();
  }

  if ($Qbestsellers->numberOfRows() >= MIN_DISPLAY_BESTSELLERS) {
?>
<!-- best_sellers //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);

    new infoBoxHeading($info_box_contents, false, false);

    $rows = 0;
    $bestsellers_list = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
    while ($Qbestsellers->next()) {
      $rows++;
      $bestsellers_list .= '<tr><td class="infoBoxContents" valign="top">' . tep_row_number_format($rows) . '.</td><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qbestsellers->valueInt('products_id')) . '">' . $Qbestsellers->value('products_name') . '</a></td></tr>';
    }
    $bestsellers_list .= '</table>';

    $info_box_contents = array();
    $info_box_contents[] = array('text' => $bestsellers_list);

    new infoBox($info_box_contents);

    $Qbestsellers->freeResult();
?>
            </td>
          </tr>
<!-- best_sellers_eof //-->
<?php
  }
?>
