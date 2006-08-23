<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if ($osC_RecentlyVisited->hasHistory()) {
?>

<div class="moduleBox">
  <h6>Your Recent History</h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>

<?php
    if ($osC_RecentlyVisited->hasProducts()) {
?>

        <td valign="top">
          <div class="tableHeading"><?php echo $osC_Language->get('recently_visited_title'); ?></div>

          <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      foreach ($osC_RecentlyVisited->getProducts() as $product) {
        echo '            <tr>' . "\n";

        if (SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == '1') {
          echo '              <td width="' . ($osC_Image->getWidth('mini') + 10) . '" align="center">' . osc_link_object(tep_href_link(FILENAME_PRODUCTS, $product['keyword']), $osC_Image->show($product['image'], $product['name'], '', 'mini')) . '</td>' . "\n";
        }

        echo '              <td>' . osc_link_object(tep_href_link(FILENAME_PRODUCTS, $product['keyword']), $product['name']) . '<br />';

        if (SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == '1') {
          echo $product['price'] . '&nbsp;';
        }

        echo '<i>(in ' . osc_link_object(tep_href_link(FILENAME_DEFAULT, 'cPath=' . $product['category_path']), $product['category_name']) . ')</i></td>' . "\n" .
             '            </tr>' . "\n";
      }
?>

          </table>
        </td>

<?php
    }

    if ($osC_RecentlyVisited->hasCategories()) {
?>

        <td valign="top">
          <div class="tableHeading"><b>Recent Categories</b></div>

          <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      foreach ($osC_RecentlyVisited->getCategories() as $category) {
        echo '          <tr>' . "\n";

        if (SERVICE_RECENTLY_VISITED_SHOW_CATEGORY_IMAGES == '1') {
          echo '              <td width="' . ((SMALL_IMAGE_WIDTH * 0.5) + 5) . '">' . osc_link_object(tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['path']), tep_image(DIR_WS_IMAGES . $category['image'], $category['name'], SMALL_IMAGE_WIDTH*0.5, SMALL_IMAGE_HEIGHT*0.5)) . '</td>' . "\n";
        }

        echo '            <td>' . osc_link_object(tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['path']), $category['name']);

        if (empty($category['parent_id']) === false) {
          echo '&nbsp;<i>(in ' . osc_link_object(tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['parent_id']), $category['parent_name']) . ')</i>';
        }

        echo '</td>' . "\n" .
             '          </tr>' . "\n";
      }
?>

          </table>
        </td>

<?php
    }

    if ($osC_RecentlyVisited->hasSearches()) {
?>

        <td valign="top">
          <div class="tableHeading"><b>Recent Searches</b></div>

          <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      foreach ($osC_RecentlyVisited->getSearches() as $searchphrase) {
        echo '          <tr>' . "\n" .
             '            <td>' . osc_link_object(tep_href_link(FILENAME_SEARCH, 'keywords=' . $searchphrase['keywords']), $searchphrase['keywords']) . ' <i>(' . number_format($searchphrase['results']) . ' results)</i></td>' . "\n" .
             '          </tr>' . "\n";
      }
?>

          </table>
        </td>

<?php
    }
?>

      </tr>
    </table>
  </div>
</div>

<?php
  }
?>
