<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (!$osC_Services->isStarted('reviews')) {
    tep_redirect(tep_href_link(FILENAME_DEFAULT));
  }

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_REVIEWS));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_reviews_new.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $Qreviews = $osC_Database->query('select r.reviews_id, left(r.reviews_text, 100) as reviews_text, r.reviews_rating, r.date_added, p.products_id, pd.products_name, p.products_image, r.customers_name from :table_reviews r, :table_products p, :table_products_description pd where p.products_status = 1 and p.products_id = r.products_id and p.products_id = pd.products_id and pd.language_id = :language_id and r.languages_id = :languages_id and r.reviews_status = 1 order by r.reviews_id desc');
  $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
  $Qreviews->bindTable(':table_products', TABLE_PRODUCTS);
  $Qreviews->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qreviews->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qreviews->bindInt(':languages_id', $osC_Session->value('languages_id'));
  $Qreviews->setBatchLimit($_GET['page'], MAX_DISPLAY_NEW_REVIEWS);
  $Qreviews->execute();

  if ($Qreviews->numberOfRows() > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                <td align="right" class="smallText"><?php echo $Qreviews->displayBatchLinksPullDown(); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
    }

    while ($Qreviews->next()) {
?>
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $Qreviews->valueInt('products_id') . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '"><u><b>' . $Qreviews->value('products_name') . '</b></u></a> <span class="smallText">' . sprintf(TEXT_REVIEW_BY, $Qreviews->valueProtected('customers_name')) . '</span>'; ?></td>
                <td class="smallText" align="right"><?php echo sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($Qreviews->value('date_added'))); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="center" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $Qreviews->valueInt('products_id') . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qreviews->value('products_image'), $Qreviews->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
                    <td valign="top" class="main"><?php echo tep_break_string($Qreviews->valueProtected('reviews_text'), 60, '-<br>') . ((strlen($Qreviews->valueProtected('reviews_text')) >= 100) ? '..' : '') . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))), sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))) . '</i>'; ?></td>
                    <td width="10" align="right"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
    }

    if ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                <td align="right" class="smallText"><?php echo $Qreviews->displayBatchLinksPullDown(); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
    }
  } else {
?>
          <tr>
            <td><?php new infoBox(array(array('text' => TEXT_NO_REVIEWS))); ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
  }
?>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
