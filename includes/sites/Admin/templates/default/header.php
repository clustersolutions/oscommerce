<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<div id="header">
  <div style="float: left; padding: 5px;"><?php echo osc_link_object(OSCOM::getLink(OSCOM::getSite(), 'Index'), osc_image(OSCOM::getPublicSiteLink('images/oscommerce.jpg'), PROJECT_VERSION)); ?></div>
  <div style="float: right; width: 150px; text-align: center;">

<?php
  if ( OSCOM::getRequestType() == 'SSL' ) {
    echo '<div class="reqSSL">' . OSCOM::getDef('ssl_protection') . '</div>';
  } else {
    echo '<div class="reqNONSSL">' . OSCOM::getDef('ssl_unprotected') . '</div>';
  }
?>

  </div>
</div>

<div id="adminMenu">
  <ul class="levelTop">

<?php
  foreach ( osC_Access::getLevels() as $group => $links ) {
    echo '<li' . ($group == osC_Access::getGroup(OSCOM::getSiteApplication()) ? ' class="activeGreen"' : ' class="hoverGreen"') . '><span><a href="' . OSCOM::getLink(null, $links[array_shift(array_keys($links))]['module']) . '">' . osC_Access::getGroupTitle($group) . '</a></span><ul class="levelSub">';

    foreach ( $links as $link ) {
      echo '<li><a href="' . OSCOM::getLink(null, $link['module']) . '">' . $link['title'] . '</a></li>';
    }

    echo '</ul></li>';
  }

  echo '<li class="hoverGreen"><span><a href="http://www.oscommerce.com" target="_blank">' . OSCOM::getDef('header_title_help') . '</a></span><ul class="levelSub">' .
       '<li><a href="http://www.oscommerce.com" target="_blank">osCommerce Support Site</a></li>' .
       '<li><a href="http://www.oscommerce.info" target="_blank">Online Documentation</a></li>' .
       '<li><a href="http://forums.oscommerce.com" target="_blank">Community Support Forums</a></li>' .
       '<li><a href="http://addons.oscommerce.com" target="_blank">Add-Ons Site</a></li>' .
       '<li><a href="http://svn.oscommerce.com/jira" target="_blank">Bug Reporter</a></li></ul></li>' .
       '<li class="hoverGreen"><a href="' . OSCOM::getLink('Shop', 'Index', null, 'NONSSL', false) . '" target="_blank">' . OSCOM::getDef('header_title_online_catalog') . '</a></li>';

  if ( isset($_SESSION['admin']) ) {
    echo '<li class="hoverRed"><a href="' . OSCOM::getLink(null, 'Login', 'action=Logoff') . '">' . OSCOM::getDef('header_title_logoff') . '</a></li>';
  }
?>

  </ul>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('ul.levelTop li span').hover(function() {
      $(this).parent().find('ul.levelSub').stop().slideDown('fast').show('fast', function() {
        $(this).height('auto');
      });

      $(this).parent().hover(function() {}, function() {
        $(this).parent().find('ul.levelSub').stop().slideUp('fast');
      });
    }).hover(function() {
      $(this).addClass('subhover');
    }, function() {
      $(this).removeClass('subhover');
    });
  });
</script>
