<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License

  osCommerce Copyright Policy:
  http://www.oscommerce.com/about/copyright

  osCommerce Trademark Policy:
  http://www.oscommerce.com/about/trademark

  GNU General Public License:
  http://www.gnu.org/licenses/gpl.html
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

<base href="<?php echo tep_href_link('', '', 'AUTO', false); ?>" />

<link rel="stylesheet" type="text/css" href="templates/<?php echo $osC_Template->getCode(); ?>/stylesheet.css" />

<?php
  if ($osC_Template->hasPageTags()) {
    echo $osC_Template->getPageTags();
  }

  if ($osC_Template->hasJavascript()) {
    $osC_Template->getJavascript();
  }
?>

</head>

<body>

<div id="pageBlockLeft">
  <div id="pageContent">

<?php
  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }

  if ($osC_Template->hasPageContentModules()) {
    foreach ($osC_Services->getCallBeforePageContent() as $service) {
      $$service[0]->$service[1]();
    }

    foreach ($osC_Template->getContentModules('before') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        include('templates/default/modules/content/' . $osC_Box->getCode() . '.php');
      }

      unset($osC_Box);
    }
  }

  include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());

  if ($osC_Template->hasPageContentModules()) {
    foreach ($osC_Services->getCallAfterPageContent() as $service) {
      $$service[0]->$service[1]();
    }

    foreach ($osC_Template->getContentModules('after') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        include('templates/default/modules/content/' . $osC_Box->getCode() . '.php');
      }

      unset($osC_Box);
    }
  }
?>

  </div>

<?php
  if ($osC_Template->hasPageBoxModules()) {
?>

  <div id="pageColumnLeft">
    <div class="boxGroup">

<?php
    foreach ($osC_Template->getBoxModules('left') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        include('templates/default/modules/boxes/' . $osC_Box->getCode() . '.php');
      }

      unset($osC_Box);
    }
?>

    </div>
  </div>

<?php
  }
?>

</div>

<?php
  if ($osC_Template->hasPageBoxModules()) {
?>

<div id="pageColumnRight">
  <div class="boxGroup">

<?php
    foreach ($osC_Template->getBoxModules('right') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        include('templates/default/modules/boxes/' . $osC_Box->getCode() . '.php');
      }

      unset($osC_Box);
    }
?>

  </div>
</div>

<?php
  }

  if ($osC_Template->hasPageHeader()) {
?>

<div id="pageHeader">
  <div id="header-content">
    <div id="headerLogo">

<?php
    echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>';
?>

    </div>

    <div id="headerIcons">

<?php
    echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', $osC_Language->get('my_account')) . '</a>&nbsp;&nbsp;
          <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', $osC_Language->get('cart_contents')) . '</a>&nbsp;&nbsp;
          <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', $osC_Language->get('checkout')) . '</a>';
?>

    </div>

    <div id="headerBar">
      <div id="breadcrumb" style="float: left;">

<?php
    if ($osC_Services->isStarted('breadcrumb')) {
      echo $breadcrumb->trail(' &raquo; ');
    }
?>

      </div>
      <div style="text-align: right;">

<?php
    if ($osC_Customer->isLoggedOn()) {
      echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'logoff', 'SSL') . '" class="headerNavigation">' . $osC_Language->get('sign_out') . '</a> &nbsp;|&nbsp; ';
    }

    echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . $osC_Language->get('my_account') . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '" class="headerNavigation">' . $osC_Language->get('cart_contents') . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="headerNavigation">' . $osC_Language->get('checkout') . '</a>';
?>

      </div>
    </div>
  </div>
</div>

<?php
  } // if ($osC_Template->hasPageHeader())

  if ($osC_Template->hasPageFooter()) {
?>

<div id="pageFooter">
  <div id="footerBar">
    <div><?php echo osC_DateTime::getLong(); ?></div>
  </div>

  <?php echo '<p align="center">' . sprintf($osC_Language->get('footer'), date('Y'), tep_href_link(FILENAME_DEFAULT), STORE_NAME) . '</p>'; ?>
</div>

<?php
    if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
      echo '<p align="center">' . $osC_Banner->display() . '</p>';
    }
  }
?>

</body>

</html>
