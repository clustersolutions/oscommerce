<?php
/*
  $Id: shipping.php 5 2005-01-31 01:40:15Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $osC_CategoryTree->reset();
  $osC_CategoryTree->setShowCategoryProductCount(false);
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_specials.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>
  <div style="float: left; width: 50%;"><?php echo $osC_CategoryTree->buildTree(); ?></div>

  <div style="float: right; width: 50%;">
    <ul>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_ACCOUNT . '</a>'; ?></li>
        <ul>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'edit', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_ACCOUNT_EDIT . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_ADDRESS_BOOK . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'orders', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_ACCOUNT_HISTORY . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'newsletters', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_ACCOUNT_NOTIFICATIONS . '</a>'; ?></li>
        </ul>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_SHOPPING_CART . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . TEXT_INFO_SITEMAP_PAGE_CHECKOUT_SHIPPING . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_SEARCH) . '">' . TEXT_INFO_SITEMAP_PAGE_ADVANCED_SEARCH . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'new') . '">' . TEXT_INFO_SITEMAP_PAGE_PRODUCTS_NEW . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'specials') . '">' . TEXT_INFO_SITEMAP_PAGE_SPECIALS . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews') . '">' . TEXT_INFO_SITEMAP_PAGE_REVIEWS . '</a>'; ?></li>
      <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO) . '">' . BOX_HEADING_INFORMATION . '</a>'; ?></li>
        <ul>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'shipping') . '">' . BOX_INFORMATION_SHIPPING . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'privacy') . '">' . BOX_INFORMATION_PRIVACY . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'conditions') . '">' . BOX_INFORMATION_CONDITIONS . '</a>'; ?></li>
          <li><?php echo '<a href="' . tep_href_link(FILENAME_INFO, 'contact') . '">' . BOX_INFORMATION_CONTACT . '</a>'; ?></li>
        </ul>
    </ul>
  </div>
</div>
