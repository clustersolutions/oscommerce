<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $osC_CategoryTree->reset();
  $osC_CategoryTree->setShowCategoryProductCount(false);
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_specials.gif', $osC_Template->getPageTitle(), null, null, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>
  <div style="float: right; width: 49%;">
    <ul>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . $osC_Language->get('sitemap_account') . '</a>'; ?>
        <ul>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'edit', 'SSL') . '">' . $osC_Language->get('sitemap_account_edit') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . $osC_Language->get('sitemap_address_book') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">' . $osC_Language->get('sitemap_account_history') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'newsletters', 'SSL') . '">' . $osC_Language->get('sitemap_account_notifications') . '</a>'; ?></li>
        </ul>
      </li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . $osC_Language->get('sitemap_shopping_cart') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . $osC_Language->get('sitemap_checkout_shipping') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_SEARCH) . '">' . $osC_Language->get('sitemap_advanced_search') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'new') . '">' . $osC_Language->get('sitemap_products_new') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'specials') . '">' . $osC_Language->get('sitemap_specials') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews') . '">' . $osC_Language->get('sitemap_reviews') . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO) . '">' . $osC_Language->get('box_information_heading') . '</a>'; ?>
        <ul>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'shipping') . '">' . $osC_Language->get('box_information_shipping') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'privacy') . '">' . $osC_Language->get('box_information_privacy') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'conditions') . '">' . $osC_Language->get('box_information_conditions') . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">' . $osC_Language->get('box_information_contact') . '</a>'; ?></li>
        </ul>
      </li>
    </ul>
  </div>

  <div style="width: 49%;">
    <?php echo $osC_CategoryTree->buildTree(); ?>
  </div>
</div>
