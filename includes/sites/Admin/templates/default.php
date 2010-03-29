<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo '<?xml version="1.0" encoding="utf-8"?>'; // short_open_tag compatibility ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>" />

<?php
  if ( $request_type == 'SSL' ) {
    echo '<link rel="shortcut icon" href="images/favicon_ssl.ico" type="image/x-icon" />';
  } else {
    echo '<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />';
  }
?>

<title><?php echo STORE_NAME . ': ' . $osC_Language->get('administration_title') . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

<meta name="generator" value="osCommerce Online Merchant" />
<meta name="robots" content="noindex,nofollow" />

<script type="text/javascript" src="../ext/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../ext/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="../ext/jquery/jquery.json-1.3.min.js"></script>
<script type="text/javascript" src="../ext/jquery/jquery.tinysort.min.js"></script>
<script type="text/javascript" src="../ext/jquery/jquery.ocupload-1.1.2.packed.js"></script>

<link rel="stylesheet" type="text/css" href="../ext/jquery/ui/themes/smoothness/jquery-ui-1.7.2.custom.css" />
<script type="text/javascript" src="../ext/jquery/ui/jquery-ui-1.7.2.custom.min.js"></script>

<script type="text/javascript" src="../ext/alexei/sprintf.js"></script>

<script type="text/javascript" src="includes/sites/Admin/includes/general.js"></script>
<script type="text/javascript" src="includes/sites/Admin/js/datatable.js"></script>

<link rel="stylesheet" type="text/css" href="includes/sites/Admin/templates/default/stylesheet.css" />

<script type="text/javascript">
  var pageURL = '<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()); ?>';
  var pageModule = '<?php echo $osC_Template->getModule(); ?>';

  var batchSize = parseInt('<?php echo MAX_DISPLAY_SEARCH_RESULTS; ?>');
  var batchTotalPagesText = '<?php echo addslashes($osC_Language->get('batch_results_number_of_entries')); ?>';
  var batchCurrentPageset = '<?php echo addslashes($osC_Language->get('result_set_current_page')); ?>';
  var batchIconNavigationBack = '<?php echo osc_icon('nav_back.png'); ?>';
  var batchIconNavigationBackGrey = '<?php echo osc_icon('nav_back_grey.png'); ?>';
  var batchIconNavigationForward = '<?php echo osc_icon('nav_forward.png'); ?>';
  var batchIconNavigationForwardGrey = '<?php echo osc_icon('nav_forward_grey.png'); ?>';
  var batchIconProgress = '<?php echo osc_icon('progress_ani.gif'); ?>';

  var taxDecimalPlaces = parseInt('<?php echo TAX_DECIMAL_PLACES; ?>');

  $(function() {
//all hover and click logic for buttons
    $('.fg-button:not(.ui-state-disabled)').hover(function() {
      $(this).addClass('ui-state-hover');
    }, function() {
      $(this).removeClass('ui-state-hover');
    }).mousedown(function() {
      $(this).parents('.fg-buttonset-single:first').find('.fg-button.ui-state-active').removeClass('ui-state-active');
      if ( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ) {
        $(this).removeClass('ui-state-active');
      } else {
        $(this).addClass('ui-state-active');
      }
    }).mouseup(function() {
      if (! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ) {
        $(this).removeClass('ui-state-active');
      }
    });
  });
</script>

</head>

<body>

<?php
  if ( $osC_Template->hasPageHeader() ) {
    include(OSCOM::BASE_DIRECTORY . '/includes/sites/Admin/templates/default/header.php');
  }

  if ( isset($_SESSION['admin']) && !in_array($osC_Template->getModule(), array('index', 'login')) ) {
?>

<div id="appsPane">
  <h4><?php echo osC_Access::getGroupTitle(osC_Access::getGroup($osC_Template->getModule())); ?></h4>

<?php
    foreach ( osC_Access::getLevels(osC_Access::getGroup($osC_Template->getModule())) as $group => $links ) {
      echo '<ul>';

      foreach ( $links as $link ) {
        echo '<li' . ( $link['module'] == $osC_Template->getModule() ? ' class="selected"' : '') . '><span>' . osc_icon($link['icon'], $link['title']) . '</span> <a href="' . osc_href_link_admin(FILENAME_DEFAULT, $link['module']) . '">' . $link['title'] . '</a>';

        if ( is_array($link['subgroups']) && !empty($link['subgroups']) ) {
          echo '<ul' . ($link['module'] == $osC_Template->getModule() ? ' style="display: block;"' : '') . '>';

          foreach ( $link['subgroups'] as $subgroup ) {
            echo '<li><a href="' . osc_href_link_admin(FILENAME_DEFAULT, $link['module'] . '&' . $subgroup['identifier']) . '">' . $subgroup['title'] . '</a></li>';
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
  if ( $osC_MessageStack->exists('header') ) {
    echo $osC_MessageStack->get('header');
  }

  require(OSCOM::BASE_DIRECTORY . '/includes/sites/Admin/includes/applications/' . $osC_Template->getModule() . '/pages/' . $osC_Template->getPageContentsFilename());
?>

</div>

<?php
  if ( isset($_SESSION['admin']) && !in_array($osC_Template->getModule(), array('index', 'login')) ) {
?>

<script type="text/javascript">
  $('#appContent').css('marginLeft', '190px');
</script>

<?php
  }

  if ( $osC_Template->hasPageFooter() ) {
?>

<div id="footer">
  <?php include(OSCOM::BASE_DIRECTORY . '/includes/sites/Admin/templates/default/footer.php'); ?>
</div>

<?php
  }
?>

</body>

</html>
