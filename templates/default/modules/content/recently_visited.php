<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if ($osC_RecentlyVisited->hasHistory()) {
?>

<div class="moduleBox">
  <div class="outsideHeading">Your Recent History</div>

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

        if (SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == 'True') {
          echo '              <td width="' . ((SMALL_IMAGE_WIDTH * 0.5) + 5) . '"><a href="' . tep_href_link(FILENAME_PRODUCTS, $product['keyword']) . '">' . tep_image(DIR_WS_IMAGES . $product['image'], $product['name'], SMALL_IMAGE_WIDTH*0.5, SMALL_IMAGE_HEIGHT*0.5) . '</a></td>' . "\n";
        }

        echo '              <td class="main"><a href="' . tep_href_link(FILENAME_PRODUCTS, $product['keyword']) . '">' . $product['name'] . '</a><br />';

        if (SERVICE_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True') {
          echo $product['price'] . '&nbsp;';
        }

        echo '<i>(in <a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $product['category_path']) . '">' . $product['category_name'] . '</a>)</i></td>' . "\n" .
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

        if (SERVICE_RECENTLY_VISITED_SHOW_CATEGORY_IMAGES == 'True') {
          echo '              <td width="' . ((SMALL_IMAGE_WIDTH * 0.5) + 5) . '"><a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['path']) . '">' . tep_image(DIR_WS_IMAGES . $category['image'], $category['name'], SMALL_IMAGE_WIDTH*0.5, SMALL_IMAGE_HEIGHT*0.5) . '</a></td>' . "\n";
        }

        echo '            <td class="main"><a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['path']) . '">' . $category['name'] . '</a>';

        if (empty($category['parent_id']) === false) {
          echo '&nbsp;<i>(in <a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $category['parent_id']) . '">' . $category['parent_name'] . '</a>)</i>';
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
             '            <td class="main"><a href="' . tep_href_link(FILENAME_SEARCH, 'keywords=' . $searchphrase['keywords']) . '">' . $searchphrase['keywords'] . '</a> <i>(' . number_format($searchphrase['results']) . ' results)</i></td>' . "\n" .
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
