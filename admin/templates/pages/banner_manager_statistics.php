<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $type = (isset($_GET['type']) ? $_GET['type'] : '');

  $Qbanner = $osC_Database->query('select banners_title from :table_banners where banners_id = :banners_id');
  $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
  $Qbanner->bindInt(':banners_id', $_GET['bID']);
  $Qbanner->execute();

  $years_array = array();
  $Qyears = $osC_Database->query('select distinct year(banners_history_date) as banner_year from :table_banners_history where banners_id = :banners_id');
  $Qyears->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
  $Qyears->bindInt(':banners_id', $_GET['bID']);
  $Qyears->execute();

  while ($Qyears->next()) {
    $years_array[] = array('id' => $Qyears->valueInt('banner_year'),
                           'text' => $Qyears->valueInt('banner_year'));
  }

  $Qyears->freeResult();

  $months_array = array();
  for ($i=1; $i<13; $i++) {
    $months_array[] = array('id' => $i,
                            'text' => strftime('%B', mktime(0,0,0,$i)));
  }

  $type_array = array(array('id' => 'daily',
                            'text' => STATISTICS_TYPE_DAILY),
                      array('id' => 'monthly',
                            'text' => STATISTICS_TYPE_MONTHLY),
                      array('id' => 'yearly',
                            'text' => STATISTICS_TYPE_YEARLY));
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE . ': ' . $Qbanner->value('banners_title'); ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo '<form name="type" action="' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()) . ' method="get">' . osc_draw_hidden_field('page', $_GET['page']) . osc_draw_hidden_field('bID', $_GET['bID']) . osc_draw_hidden_field('action', 'statistics') .
       TITLE_TYPE . ' ' . osc_draw_pull_down_menu('type', $type_array, 'daily', 'onchange="this.form.submit();"');

  switch ($type) {
    case 'yearly': break;
    case 'monthly':
      echo TITLE_YEAR . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');
      break;
    default:
    case 'daily':
      echo TITLE_MONTH . ' ' . osc_draw_pull_down_menu('month', $months_array, date('n'), 'onchange="this.form.submit();"') . TITLE_YEAR . ' ' . osc_draw_pull_down_menu('year', $years_array, date('Y'), 'onchange="this.form.submit();"');
      break;
  }

  echo '</form>';
?>
    </td>
  </tr>
</table>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<?php
  if (is_dir('images/graphs') && is_writeable('images/graphs') && !empty($osC_Template->image_extension)) {
    switch ($type) {
      case 'yearly':
        include('includes/graphs/banner_yearly.php');
        echo '<p align="center">' . osc_image('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $image_extension) . '</p>';
        break;
      case 'monthly':
        include('includes/graphs/banner_monthly.php');
        echo '<p align="center">' . osc_image('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $image_extension) . '</p>';
        break;
      default:
      case 'daily':
        include('includes/graphs/banner_daily.php');
        echo '<p align="center">' . osc_image('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $image_extension) . '</p>';
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
  if (isset($stats)) {
    for ($i=0, $n=sizeof($stats); $i<$n; $i++) {
      echo '    <tr>' . "\n" .
           '      <td>' . $stats[$i][0] . '</td>' . "\n" .
           '      <td>' . number_format($stats[$i][1]) . '</td>' . "\n" .
           '      <td>' . number_format($stats[$i][2]) . '</td>' . "\n" .
           '    </tr>' . "\n";
    }
  }
?>
  </tbody>
</table>

<p align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&bID=' . $_GET['bID']) . '\';">'; ?></p>
