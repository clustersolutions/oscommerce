<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $type = ( isset($_GET['type']) ? $_GET['type'] : '' );

  $Qyears = $osC_Database->query('select distinct year(banners_history_date) as banner_year from :table_banners_history where banners_id = :banners_id');
  $Qyears->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
  $Qyears->bindInt(':banners_id', $_GET['bID']);
  $Qyears->execute();

  $years_array = array();

  while ( $Qyears->next() ) {
    $years_array[] = array('id' => $Qyears->valueInt('banner_year'),
                           'text' => $Qyears->valueInt('banner_year'));
  }

  $Qyears->freeResult();

  $months_array = array();

  for ( $i = 1; $i < 13; $i++ ) {
    $months_array[] = array('id' => $i,
                            'text' => strftime('%B', mktime(0,0,0,$i)));
  }

  $type_array = array(array('id' => 'daily',
                            'text' => STATISTICS_TYPE_DAILY),
                      array('id' => 'monthly',
                            'text' => STATISTICS_TYPE_MONTHLY),
                      array('id' => 'yearly',
                            'text' => STATISTICS_TYPE_YEARLY));

  $osC_ObjectInfo = new osC_ObjectInfo(osC_BannerManager_Admin::getData($_GET['bID']));
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<form name="type" action="<?php echo osc_href_link(FILENAME_DEFAULT); ?>" method="get">
  
<?php
  echo osc_draw_hidden_field($osC_Template->getModule()) .
       osc_draw_hidden_field('page', $_GET['page']) .
       osc_draw_hidden_field('bID', $_GET['bID']) .
       osc_draw_hidden_field('action', 'statistics');
?>

<p align="right">

<?php
  echo TITLE_TYPE . ' ' . osc_draw_pull_down_menu('type', $type_array, 'daily', 'onchange="this.form.submit();"') . ' ';

  switch ( $type ) {
    case 'yearly':
      break;

    case 'monthly':
      echo TITLE_YEAR . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');

      break;

    case 'daily':
    default:
      echo TITLE_MONTH . ' ' . osc_draw_pull_down_menu('month', $months_array, date('n'), 'onchange="this.form.submit();"') . ' ' .
           TITLE_YEAR . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');

      break;
  }

  echo '&nbsp;<input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '\';" />';
?>

</p>

</form>

<?php
  if ( is_dir('images/graphs') && is_writeable('images/graphs') && !empty($osC_Template->image_extension) ) {
    switch ( $type ) {
      case 'yearly':
        include('includes/graphs/banner_yearly.php');

        echo '<p align="center">' . osc_image('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $osC_Template->image_extension) . '</p>';

        break;

      case 'monthly':
        include('includes/graphs/banner_monthly.php');

        echo '<p align="center">' . osc_image('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $osC_Template->image_extension) . '</p>';

        break;

      case 'daily':
      default:
        include('includes/graphs/banner_daily.php');

        echo '<p align="center">' . osc_image('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $osC_Template->image_extension) . '</p>';

        break;
    }
  }
?>

<table border="0" width="600" cellspacing="0" cellpadding="2" class="dataTable" align="center">
  <thead>
    <tr>
      <th><?php echo TABLE_HEADING_SOURCE; ?></th>
      <th><?php echo TABLE_HEADING_VIEWS; ?></th>
      <th><?php echo TABLE_HEADING_CLICKS; ?></th>
    </tr>
  </thead>
  <tbody>

<?php
  if ( isset($stats) ) {
    for ( $i = 0, $n = sizeof($stats); $i < $n; $i++ ) {
      echo '    <tr>' .
           '      <td>' . $stats[$i][0] . '</td>' .
           '      <td>' . number_format($stats[$i][1]) . '</td>' .
           '      <td>' . number_format($stats[$i][2]) . '</td>' .
           '    </tr>';
    }
  }
?>

  </tbody>
</table>
