<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<script language="javascript" src="external/jscookmenu/JSCookMenu.js"></script>
<link rel="stylesheet" href="external/jscookmenu/ThemeOffice/theme.css" type="text/css">
<script language="javascript" src="external/jscookmenu/ThemeOffice/theme.js"></script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT), osc_image('images/oscommerce.jpg', 'osCommerce, 3.0 Alpha 4')); ?></td>
    <td width="150" align="right" class="smallText">
<?php
  if ($request_type == 'SSL') {
    echo sprintf(BOX_CONNECTION_PROTECTED, (isset($_SERVER['SSL_CIPHER_ALGKEYSIZE']) ? $_SERVER['SSL_CIPHER_ALGKEYSIZE'] . '-bit' : '<i>' . BOX_CONNECTION_UNKNOWN . '</i>')) . osc_icon('locked.png', ICON_LOCKED);
  } else {
    echo BOX_CONNECTION_UNPROTECTED . ' ' . osc_icon('unlocked.png', ICON_UNLOCKED);
  }
?>
    </td>
  </tr>
</table>

<div id="administrationMenu" class="ThemeOfficeMainItem">
  <ul style="visibility: hidden">

<?php
  $access = array();

  if (isset($_SESSION['admin'])) {
    $access = osC_Access::getLevels();
  }

  ksort($access);

  foreach ($access as $group => $links) {
    echo '    <li><span></span><span>' . osC_Access::getGroupTitle( $group ) . '</span>' .
         '      <ul>';

    ksort( $links );

    foreach ($links as $link) {
      echo '        <li><span>' . osc_icon($link['icon']) . '</span><a href="' . osc_href_link_admin(FILENAME_DEFAULT, $link['module']) . '">' . $link['title'] . '</a>';

      if ( is_array($link['subgroups']) && !empty($link['subgroups']) ) {
        echo '          <ul>';

        foreach ($link['subgroups'] as $subgroup) {
          echo '            <li><span>' . osc_icon($subgroup['icon']) . '</span><a href="' . osc_href_link_admin(FILENAME_DEFAULT, $link['module'] . '&' . $subgroup['identifier']) . '">' . $subgroup['title'] . '</a></li>';
        }

        echo '          </ul>';
      }

      echo '        </li>';
    }

    echo '      </ul>' .
         '    </li>' .
         '    <li></li>';
  }

  echo '    <li><span></span><span>' . HEADER_TITLE_HELP . '</span>' .
       '      <ul>' .
       '        <li><span>' . osc_icon('oscommerce.png') . '</span><span>' . HEADER_TITLE_OSCOMMERCE_SUPPORT_SITE . '</span>' .
       '          <ul>' .
       '            <li><span>' . osc_icon('oscommerce.png') . '</span><a href="http://www.oscommerce.com" target="_blank">Support Site</a></li>' .
       '            <li><span>' . osc_icon('log.png') . '</span><a href="http://www.oscommerce.info" target="_blank">Knowledge Base</a></li>' .
       '            <li><span>' . osc_icon('people.png') . '</span><a href="http://forums.oscommerce.com" target="_blank">Community Forums</a></li>' .
       '            <li><span>' . osc_icon('run.png') . '</span><a href="http://www.oscommerce.com/community/contributions" target="_blank">Contributions</a></li>' .
       '            <li><span>' . osc_icon('configure.png') . '</span><a href="http://www.oscommerce.com/community/bugs" target="_blank">Bug Reporter</a></li>' .
       '          </ul>' .
       '        </li>' .
       '        <li><span>' . osc_icon('locale.png') . '</span><span>' . HEADER_TITLE_LANGUAGES . '</span>' .
       '          <ul>';

  foreach ($osC_Language->getAll() as $l) {
    echo '            <li><span>' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image']) . '</span><a href="' . osc_href_link_admin(FILENAME_DEFAULT, 'language=' . $l['code']) . '">' . $l['name'] . '</a></li>';
  }

  echo '          </ul>' .
       '        </li>' .
       '        <li><span>' . osc_icon('home.png') . '</span><a href="' . osc_href_link(null, null, 'NONSSL', false, false, true) . '">' . HEADER_TITLE_ONLINE_CATALOG . '</a></li>' .
       '      </ul>' .
       '    </li>';

  if (isset($_SESSION['admin'])) {
    echo '    <li><span></span><a href="' . osc_href_link_admin(FILENAME_DEFAULT, 'login&action=logoff') . '">' . BOX_HEADING_LOGOFF . '</a></li>';
  }
?>

  </ul>
</div>

<script type="text/javascript"><!--
  cmDrawFromText('administrationMenu', 'hbr', cmThemeOffice, 'ThemeOffice');
//--></script>

<?php
  if ($osC_MessageStack->size('header') > 0) {
    echo $osC_MessageStack->output('header');
  }
?>
