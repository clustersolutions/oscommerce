<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

  require('includes/application_top.php');

  $dir_fs_www_root = dirname(__FILE__);

  if (isset($_GET['action']) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
      case 'getImages':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        $array = array();

        $Qimages = $osC_Database->query('select id, image, default_flag from :table_products_images where products_id = :products_id order by sort_order');
        $Qimages->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qimages->bindInt(':products_id', $_GET['pID']);
        $Qimages->execute();

        while ($Qimages->next()) {
          foreach ($osC_Image->getGroups() as $group) {
            $pass = true;

            if (isset($_GET['filter']) && (($_GET['filter'] == 'originals') && ($group['id'] != '1'))) {
              $pass = false;
            } elseif (isset($_GET['filter']) && (($_GET['filter'] == 'others') && ($group['id'] == '1'))) {
              $pass = false;
            }

            if ($pass === true) {
              $element = array($Qimages->valueInt('id'),
                               $group['id'],
                               $Qimages->value('image'),
                               $group['code'],
                               osc_href_link($osC_Image->getAddress($Qimages->value('image'), $group['code']), null, 'NONSSL', false, false, true),
                               number_format(@filesize(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimages->value('image'))),
                               $Qimages->valueInt('default_flag'));

              $array[] = implode('[-]', $element);
            }
          }
        }

        if (empty($array)) {
          echo '0:osCRPC:' . $_GET['pID'];
        } else {
          echo '1:osCRPC:' . implode('[x]', $array);
        }

        exit;
        break;

      case 'setDefaultImage':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->setAsDefault($_GET['image'])) {
          echo '1:osCRPC:' . $_GET['image'];
        } else {
          echo '0:osCRPC:' . $_GET['image'];
        }

        exit;
        break;

      case 'reorderImages':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->reorderImages($_GET['imagesOriginal'])) {
          echo '1:osCRPC:' . $_GET['pID'];
        } else {
          echo '0:osCRPC:' . $_GET['pID'];
        }

        exit;
        break;

      case 'deleteProductImage':
        include('includes/classes/image.php');
        $osC_Image = new osC_Image_Admin();

        if ($osC_Image->delete($_GET['image'])) {
          echo '1:osCRPC:' . $_GET['image'];
        } else {
          echo '0:osCRPC:' . $_GET['image'];
        }

        exit;
        break;

      case 'getLocalImages':
        $osC_DirectoryListing = new osC_DirectoryListing('../images/products/_upload', true);
        $osC_DirectoryListing->setCheckExtension('gif');
        $osC_DirectoryListing->setCheckExtension('jpg');
        $osC_DirectoryListing->setCheckExtension('png');
        $osC_DirectoryListing->setIncludeDirectories(false);

        $array = array();

        foreach ($osC_DirectoryListing->getFiles() as $file) {
          $array[] = $file['name'];
        }

        echo '1:osCRPC:' . implode('#', $array);

        exit;
        break;
    }
  }

  echo '-100:osCRPC:noActionError';
?>