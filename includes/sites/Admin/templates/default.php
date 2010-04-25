<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo '<?xml version="1.0" encoding="utf-8"?>'; // short_open_tag compatibility ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $OSCOM_Language->getTextDirection(); ?>" xml:lang="<?php echo $OSCOM_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $OSCOM_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ': ' . OSCOM::getDef('administration_title') . ($OSCOM_Template->hasPageTitle() ? ': ' . $OSCOM_Template->getPageTitle() : ''); ?></title>

<link rel="icon" type="image/png" href="<?php echo OSCOM::getPublicSiteLink('images/oscommerce_icon.png'); ?>" />

<meta name="generator" value="osCommerce Online Merchant" />
<meta name="robots" content="noindex,nofollow" />

<script type="text/javascript" src="public/external/jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.json-1.3.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.tinysort.min.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.ocupload-1.1.2.packed.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" src="public/external/jquery/jquery.droppy.js"></script>

<link rel="stylesheet" type="text/css" href="public/external/jquery/ui/themes/smoothness/jquery.ui.all.css" />
<script type="text/javascript" src="public/external/jquery/ui/jquery-ui-1.8.custom.min.js"></script>

<script type="text/javascript" src="ext/alexei/sprintf.js"></script>

<script type="text/javascript" src="<?php echo OSCOM::getPublicSiteLink('javascript/general.js'); ?>"></script>
<script type="text/javascript" src="<?php echo OSCOM::getPublicSiteLink('javascript/datatable.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo OSCOM::getPublicSiteLink('templates/default/stylesheets/general.css'); ?>" />

<script type="text/javascript">
  var pageURL = '<?php echo OSCOM::getLink(); ?>';
  var pageModule = '<?php echo OSCOM::getSiteApplication(); ?>';

  var batchSize = parseInt('<?php echo MAX_DISPLAY_SEARCH_RESULTS; ?>');
  var batchTotalPagesText = '<?php echo addslashes(OSCOM::getDef('batch_results_number_of_entries')); ?>';
  var batchCurrentPageset = '<?php echo addslashes(OSCOM::getDef('result_set_current_page')); ?>';
  var batchIconNavigationBack = '<?php echo osc_icon('nav_back.png'); ?>';
  var batchIconNavigationBackGrey = '<?php echo osc_icon('nav_back_grey.png'); ?>';
  var batchIconNavigationForward = '<?php echo osc_icon('nav_forward.png'); ?>';
  var batchIconNavigationForwardGrey = '<?php echo osc_icon('nav_forward_grey.png'); ?>';
  var batchIconProgress = '<?php echo osc_icon('progress_ani.gif'); ?>';

  var taxDecimalPlaces = parseInt('<?php echo TAX_DECIMAL_PLACES; ?>');
</script>

</head>

<body>

<?php
  if ( $OSCOM_Template->hasPageHeader() ) {
    include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/templates/default/header.php');
  }

  if ( isset($_SESSION[OSCOM::getSite()]['id']) && !in_array(OSCOM::getSiteApplication(), array('Index', 'Login')) ) {
?>

<div id="appsPane">
  <h4><?php echo osC_Access::getGroupTitle(osC_Access::getGroup(OSCOM::getSiteApplication())); ?></h4>

<?php
    foreach ( osC_Access::getLevels(osC_Access::getGroup(OSCOM::getSiteApplication())) as $group => $links ) {
      echo '<ul>';

      foreach ( $links as $link ) {
        echo '<li' . ( $link['module'] == OSCOM::getSiteApplication() ? ' class="selected"' : '') . '>' . $OSCOM_Template->getIcon(16, $link['icon']) . '&nbsp;<a href="' . OSCOM::getLink(null, $link['module']) . '">' . $link['title'] . '</a>';

        if ( is_array($link['subgroups']) && !empty($link['subgroups']) ) {
          echo '<ul' . ($link['module'] == OSCOM::getSiteApplication() ? ' style="display: block;"' : '') . '>';

          foreach ( $link['subgroups'] as $subgroup ) {
            echo '<li><a href="' . OSCOM::getLink(null, $link['module'], $subgroup['identifier']) . '">' . $subgroup['title'] . '</a></li>';
          }

          echo '</ul>';
        }

        echo '</li>';
      }

      echo '</ul>';
    }
?>

</div>

<?php
  }
?>

<div id="appContent">

<?php
  if ( OSCOM_Registry::get('MessageStack')->exists('header') ) {
    echo OSCOM_Registry::get('MessageStack')->get('header');
  }

  require(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/applications/' . OSCOM::getSiteApplication() . '/pages/' . $OSCOM_Template->getPageContentsFilename());
?>

</div>

<?php
  if ( isset($_SESSION[OSCOM::getSite()]['id']) && !in_array(OSCOM::getSiteApplication(), array('Index', 'Login')) ) {
?>

<script type="text/javascript">
  $('#appContent').css('marginLeft', '190px');
</script>

<?php
  }

  if ( $OSCOM_Template->hasPageFooter() ) {
?>

<div id="footer">
  <?php include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/templates/default/footer.php'); ?>
</div>

<?php
  }
?>

</body>

</html>
