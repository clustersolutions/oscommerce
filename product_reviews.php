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

  $Qproduct = $osC_Database->query('select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from :table_products p, :table_products_description pd where p.products_id = :products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id');
  $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
  $Qproduct->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qproduct->bindInt(':products_id', $_GET['products_id']);
  $Qproduct->bindInt(':language_id', $osC_Session->value('languages_id'));
  $Qproduct->execute();

  if ($Qproduct->numberOfRows() < 1) {
    tep_redirect(tep_href_link(FILENAME_REVIEWS));
  }

  if ( ($osC_Services->isStarted('specials')) && ($new_price = $osC_Specials->getPrice($Qproduct->valueInt('products_id'))) ) {
    $products_price = '<s>' . $osC_Currencies->displayPrice($Qproduct->value('products_price'), $Qproduct->valueInt('products_tax_class_id')) . '</s> <span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_price, $Qproduct->valueInt('products_tax_class_id')) . '</span>';
  } else {
    $products_price = $osC_Currencies->displayPrice($Qproduct->value('products_price'), $Qproduct->valueInt('products_tax_class_id'));
  }

  if (tep_not_null($Qproduct->value('products_model'))) {
    $products_name = $Qproduct->value('products_name') . '<br><span class="smallText">[' . $Qproduct->value('products_model') . ']</span>';
  } else {
    $products_name = $Qproduct->value('products_name');
  }

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  require(DIR_WS_LANGUAGES . $osC_Session->value('language') . '/' . FILENAME_PRODUCT_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
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
            <td class="pageHeading" valign="top"><?php echo $products_name; ?></td>
            <td class="pageHeading" align="right" valign="top"><?php echo $products_price; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('reviews') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('reviews'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $Qreviews = $osC_Database->query('select r.reviews_id, left(r.reviews_text, 100) as reviews_text, r.reviews_rating, r.date_added, customers_name from :table_reviews r where r.products_id = :products_id and languages_id = :languages_id and r.reviews_status = 1 order by reviews_id desc');
  $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
  $Qreviews->bindInt(':products_id', $Qproduct->valueInt('products_id'));
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
                    <td align="right" class="smallText"><?php echo $Qreviews->displayBatchLinksPullDown('page', tep_get_all_get_params(array('page', 'info'))); ?></td>
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
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $Qproduct->valueInt('products_id') . '&reviews_id=' . $Qreviews->valueInt('reviews_id')) . '"><u><b>' . sprintf(TEXT_REVIEW_BY, $Qreviews->valueProtected('customers_name')) . '</b></u></a>'; ?></td>
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
                        <td valign="top" class="main"><?php echo tep_break_string($Qreviews->valueProtected('reviews_text'), 60, '-<br>') . ((strlen($Qreviews->value('reviews_text')) >= 100) ? '..' : '') . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $Qreviews->valueInt('reviews_rating') . '.gif', sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))), sprintf(TEXT_OF_5_STARS, $Qreviews->valueInt('reviews_rating'))) . '</i>'; ?></td>
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
?>
<?php
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

  if (($Qreviews->numberOfRows() > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $Qreviews->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td align="right" class="smallText"><?php echo $Qreviews->displayBatchLinksPullDown('page', tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params()) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                        <td class="main" align="right">
<?php
  if ($osC_Reviews->is_enabled === true) {
    echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, tep_get_all_get_params()) . '">' . tep_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a>';
  }
?>
                        </td>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="right" valign="top"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td align="center" class="smallText">
<?php
  if (tep_not_null($Qproduct->value('products_image'))) {
?>
<script language="javascript"><!--
document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $Qproduct->valueInt('products_id')) . '\\\')">' . tep_image(DIR_WS_IMAGES . $Qproduct->value('products_image'), addslashes($Qproduct->value('products_name')), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
//--></script>
<noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $Qproduct->value('products_image')) . '" target="_blank">' . tep_image(DIR_WS_IMAGES . $Qproduct->value('products_image'), $Qproduct->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
</noscript>
<?php
  }

  echo '<p><a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action')) . 'action=buy_now') . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a></p>';
?>
                </td>
              </tr>
            </table>
          </td>
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
