<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

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

<html <?php echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />

<title><?php echo TITLE . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

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

  include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());

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
?>

  </div>

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
</div>

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

<div id="pageHeader">
  <div id="header-content">
    <div id="headerLogo">

<?php
  echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>';
?>

    </div>

    <div id="headerIcons">

<?php
  echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>&nbsp;&nbsp;
        <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>&nbsp;&nbsp;
        <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>';
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
    echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'logoff', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_LOGOFF . '</a> &nbsp;|&nbsp; ';
  }

  echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_MY_ACCOUNT . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CART_CONTENTS . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CHECKOUT . '</a>';
?>

      </div>
    </div>
  </div>
</div>

<div id="pageFooter">
  <div id="footerBar">
    <div style="float: left;"><?php echo strftime(DATE_FORMAT_LONG); ?></div>

    <div style="text-align: right;"><?php if ($messageStack->size('counter')) echo $messageStack->outputPlain('counter'); ?></div>
  </div>

  <?php echo '<p align="center">' . FOOTER_TEXT_BODY . '</p>'; ?>
</div>

<?php
  if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
    echo '<p align="center">' . $osC_Banner->display() . '</p>';
  }
?>

</body>

</html>
