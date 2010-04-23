<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<div id="adminMenu">
  <span style="float: left;"><?php echo osc_link_object(OSCOM::getLink(OSCOM::getSite(), 'Index'), osc_image(OSCOM::getPublicSiteLink('images/oscommerce_icon.png'), null, 16, 16, 'style="padding: 4px;"')); ?></span>

  <ul class="apps">

<?php
  if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
    echo '<li><a href="#"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>Applications</a><ul>';

    foreach ( osC_Access::getLevels() as $group => $links ) {
      echo '<li><a href="' . OSCOM::getLink(null, $links[array_shift(array_keys($links))]['module']) . '"><span class="ui-icon ui-icon-triangle-1-e" style="float: right;"></span>' . osC_Access::getGroupTitle($group) . '</a><ul>';

      foreach ( $links as $link ) {
        echo '<li><a href="' . OSCOM::getLink(null, $link['module']) . '">' . $link['title'] . '</a></li>';
      }

      echo '</ul></li>';
    }

    echo '</ul></li>';
  }

  echo '<li><a href="' . OSCOM::getLink('Shop', 'Index', null, 'NONSSL', false) . '" target="_blank">' . OSCOM::getDef('header_title_online_catalog') . '</a></li>' .
       '<li><a href="http://www.oscommerce.com" target="_blank"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>' . OSCOM::getDef('header_title_help') . '</a><ul>' .
       '<li><a href="http://www.oscommerce.com" target="_blank">osCommerce Support Site</a></li>' .
       '<li><a href="http://www.oscommerce.info" target="_blank">Online Documentation</a></li>' .
       '<li><a href="http://forums.oscommerce.com" target="_blank">Community Support Forums</a></li>' .
       '<li><a href="http://addons.oscommerce.com" target="_blank">Add-Ons Site</a></li>' .
       '<li><a href="http://svn.oscommerce.com/jira" target="_blank">Bug Reporter</a></li></ul></li>';
?>

  </ul>

<?php
  if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
    echo '<ul class="apps" style="float: right;"><li><a href="#"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>' . osc_output_string_protected($_SESSION[OSCOM::getSite()]['username']) . '</a><ul><li><a href="' . OSCOM::getLink(null, 'Login', 'action=Logoff') . '">' . OSCOM::getDef('header_title_logoff') . '</a></li></ul></li></ul>';
  }
?>

</div>

<script type="text/javascript">
  $('#adminMenu .apps').droppy({speed: 0});
</script>
