<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Access;
?>

<div id="adminMenu">
  <ul class="apps">
    <li class="shortcuts"><?php echo osc_link_object(OSCOM::getLink(OSCOM::getSite(), 'Index'), osc_image(OSCOM::getPublicSiteLink('images/oscommerce_icon.png'), null, 16, 16)); ?></li>

<?php
  if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
    echo '  <li><a href="#"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>Applications</a>' .
         '    <ul>';

    foreach ( Access::getLevels() as $group => $links ) {
      $application = current($links);

      echo '      <li><a href="' . OSCOM::getLink(null, $application['module']) . '"><span class="ui-icon ui-icon-triangle-1-e" style="float: right;"></span>' . Access::getGroupTitle($group) . '</a>' .
           '        <ul>';

      foreach ( $links as $link ) {
        echo '          <li><a href="' . OSCOM::getLink(null, $link['module']) . '">' . $OSCOM_Template->getIcon(16, $link['icon']) . '&nbsp;' . $link['title'] . '</a></li>';
      }

      echo '        </ul>' .
           '      </li>';
    }

    echo '    </ul>' .
         '  </li>';
  }

  echo '  <li><a href="' . OSCOM::getLink('Shop', 'Index', null, 'NONSSL', false) . '" target="_blank">' . OSCOM::getDef('header_title_online_catalog') . '</a></li>' .
       '  <li><a href="http://www.oscommerce.com" target="_blank"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>' . OSCOM::getDef('header_title_help') . '</a>' .
       '    <ul>' .
       '      <li><a href="http://www.oscommerce.com" target="_blank">osCommerce Support Site</a></li>' .
       '      <li><a href="http://www.oscommerce.info" target="_blank">Online Documentation</a></li>' .
       '      <li><a href="http://forums.oscommerce.com" target="_blank">Community Support Forums</a></li>' .
       '      <li><a href="http://addons.oscommerce.com" target="_blank">Add-Ons Site</a></li>' .
       '      <li><a href="http://svn.oscommerce.com/jira" target="_blank">Bug Reporter</a></li>' .
       '    </ul>' .
       '  </li>';
?>

  </ul>

<?php
  if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
    echo '<ul class="apps" style="float: right;">';

    if ( $OSCOM_Application->canLinkTo() ) {
      if ( Access::isShortcut(OSCOM::getSiteApplication()) ) {
        echo '  <li class="shortcuts">' . osc_link_object(OSCOM::getLink(null, 'Index', 'RemoveShortcut&shortcut=' . OSCOM::getSiteApplication()), osc_icon('shortcut_remove.png')) . '</li>';
      } else {
        echo '  <li class="shortcuts">' . osc_link_object(OSCOM::getLink(null, 'Index', 'AddShortcut&shortcut=' . OSCOM::getSiteApplication()), osc_icon('shortcut_add.png')) . '</li>';
      }
    }

    if ( Access::hasShortcut() ) {
      echo '  <li class="shortcuts">';

      foreach ( Access::getShortcuts() as $shortcut ) {
        echo '<a href="' . OSCOM::getLink(null, $shortcut['module']) . '" id="shortcut-'.$shortcut['module'].'">' . $OSCOM_Template->getIcon(16, $shortcut['icon'], $shortcut['title']) . '<div class="jewel">0</div></a>';
      }

      echo '  </li>';
      if ( Access::hasShortcutCallback() ) {
       echo '<script type="text/javascript">

               function updateJewels(){
		jQuery.ajax({
		   type: "GET",
		   url: "index.php?RPC&Admin&Index&ShortcutAjax",
		   success: function(ret){
		    ret = jQuery.parseJSON(ret);
		    jQuery.each(ret, function(key, val) { 
	             if(jQuery("#shortcut-" + key + " .jewel").html != val && val > 0){
	              jQuery("#shortcut-" + key + " .jewel").html(val).show();
	              jQuery("#shortcut-" + key).click(function(){
		       jQuery.ajax({
		        type: "GET",
		        url: "index.php?RPC&Admin&Index&ShortcutAjaxVisit",
		        data: "module=" + key,
		        success: function(ret){}
		       });
	              });
	             }
		    });
		   }
		 });
		}

		jQuery(document).ready(function() {
		 updateJewels();
		 setInterval("updateJewels()", 10000);
		});

       </script>';
      }
    }

    echo '  <li><a href="#"><span class="ui-icon ui-icon-triangle-1-s" style="float: right;"></span>' . osc_output_string_protected($_SESSION[OSCOM::getSite()]['username']) . '</a>' .
         '    <ul>' .
         '      <li><a href="' . OSCOM::getLink(null, 'Login', 'Logoff') . '">' . OSCOM::getDef('header_title_logoff') . '</a></li>' .
         '    </ul>' .
         '  </li>' .
         '</ul>';
  }
?>

</div>

<script type="text/javascript">
  $('#adminMenu .apps').droppy({speed: 0});
  $('#adminMenu .apps li img').tipsy();
</script>
