<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use \RecursiveIteratorIterator;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\DirectoryListing;

  class getPackageContents {
    public static function execute() {
      $result = array('entries' => array());

      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        trigger_error($e->getMessage());
      }

      if ( $phar_can_open === true ) {
        $update_pkg = array();
        $core_fs = array();

        foreach ( new RecursiveIteratorIterator($phar) as $iteration ) {
          if ( ($pos = strpos($iteration->getPathName(), 'update.phar')) !== false ) {
            $update_pkg[] = substr($iteration->getPathName(), $pos+12);
          }
        }

        natcasesort($update_pkg);

        $DL = new DirectoryListing(OSCOM::BASE_DIRECTORY);
        $DL->setRecursive(true);
        $DL->setIncludeDirectories(false);
        $DL->setAddDirectoryToFilename(true);
        $DL->setStats(false);

        foreach ( $DL->getFiles() as $file ) {
          $core_fs[] = 'osCommerce/OM/' . $file['name'];
        }

        $counter = 0;

        foreach ( $update_pkg as $update_file ) {
          $result['entries'][] = array('key' => $counter,
                                       'name' => $update_file,
                                       'exists' => in_array($update_file, $core_fs),
                                       'writable' => self::isWritable(realpath(OSCOM::BASE_DIRECTORY . '/../../') . '/' . $update_file));

          $counter++;
        }
      }

      $result['total'] = count($result['entries']);

      return $result;
    }

    public static function isWritable($location) {
      if ( !file_exists($location) ) {
        while ( true ) {
          $location = dirname($location);

          if ( file_exists($location) ) {
            break;
          }
        }
      }

      return is_writable($location);
    }
  }
?>
