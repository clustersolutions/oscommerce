<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\CoreUpdate\Model;

  use \Phar;
  use \RecursiveIteratorIterator;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class applyPackage {
    protected static $_to_version;

    public static function execute() {
      $phar_can_open = true;

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');

        $meta = $phar->getMetadata();

        self::$_to_version = $meta['version_to'];

// reset the log
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') ) {
          unlink(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt');
        }

// first delete files before extracting new files
        if ( isset($meta['delete']) ) {
          foreach ( $meta['delete'] as $file ) {
            if ( substr($file, 0, 14) == 'osCommerce/OM/' ) {
              if ( file_exists(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) ) {
                if ( is_dir(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) ) {
// first delete files inside
                  $DL = new DirectoryListing(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  $DL->setRecursive(true);
                  $DL->setAddDirectoryToFilename(true);
                  $DL->setIncludeDirectories(false);

                  foreach ( $DL->getFiles() as $f ) {
                    if ( unlink(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']) ) {
                      self::log('Deleted: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']);
                    } else {
                      self::log('*** Could Not Delete: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']);
                    }
                  }

// then empty directories inside
                  $DL = new DirectoryListing(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  $DL->setRecursive(true);
                  $DL->setAddDirectoryToFilename(true);
                  $DL->setIncludeFiles(false);

                  foreach ( $DL->getFiles() as $f ) {
                    if ( rmdir(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']) ) {
                      self::log('Deleted: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']);
                    } else {
                      self::log('*** Could Not Delete: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file . '/' . $f['name']);
                    }
                  }

// lastly the (now) empty directory itself
                  if ( rmdir(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) ) {
                    self::log('Deleted: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  } else {
                    self::log('*** Could Not Delete: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  }
                } else {
                  if ( unlink(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) ) {
                    self::log('Deleted: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  } else {
                    self::log('*** Could Not Delete: ' . realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
                  }
                }
              }
            } elseif ( substr($file, 0, 7) == 'public/' ) {
              if ( file_exists(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file) ) {
                if ( is_dir(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file) ) {
// first delete files inside
                  $DL = new DirectoryListing(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  $DL->setRecursive(true);
                  $DL->setAddDirectoryToFilename(true);
                  $DL->setIncludeDirectories(false);

                  foreach ( $DL->getFiles() as $f ) {
                    if ( unlink(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']) ) {
                      self::log('Deleted: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']);
                    } else {
                      self::log('*** Could Not Delete: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']);
                    }
                  }

// then empty directories inside
                  $DL = new DirectoryListing(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  $DL->setRecursive(true);
                  $DL->setAddDirectoryToFilename(true);
                  $DL->setIncludeFiles(false);

                  foreach ( $DL->getFiles() as $f ) {
                    if ( rmdir(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']) ) {
                      self::log('Deleted: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']);
                    } else {
                      self::log('*** Could Not Delete: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file . '/' . $f['name']);
                    }
                  }

// lastly the (now) empty directory itself
                  if ( rmdir(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file) ) {
                    self::log('Deleted: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  } else {
                    self::log('*** Could Not Delete: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  }
                } else {
                  if ( unlink(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file) ) {
                    self::log('Deleted: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  } else {
                    self::log('*** Could Not Delete: ' . realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
                  }
                }
              }
            }
          }
        }

// loop through each file individually as extractTo() does not work with
// directories (see http://bugs.php.net/bug.php?id=54289)
        foreach ( new RecursiveIteratorIterator($phar) as $iteration ) {
          if ( ($pos = strpos($iteration->getPathName(), 'update.phar')) !== false ) {
            $file = substr($iteration->getPathName(), $pos+12);

            if ( substr($file, 0, 14) == 'osCommerce/OM/' ) {
              if ( file_exists(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file) ) {
                unlink(realpath(OSCOM::BASE_DIRECTORY . '../../') . '/' . $file);
              }

              if ( $phar->extractTo(realpath(OSCOM::BASE_DIRECTORY . '../../'), $file, true) ) {
                self::log('Extracted: ' . $file);
              } else {
                self::log('*** Could Not Extract: ' . $file);
              }
            } elseif ( substr($file, 0, 7) == 'public/' ) {
              if ( file_exists(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file) ) {
                unlink(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../') . '/' . $file);
              }

              if ( $phar->extractTo(realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../'), $file, true) ) {
                self::log('Extracted: ' . $file);
              } else {
                self::log('*** Could Not Extract: ' . $file);
              }
            }
          }
        }
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        trigger_error($e->getMessage());
      }

      return $phar_can_open;
    }

    protected static function log($message) {
      if ( is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt', $message . "\n", FILE_APPEND);
      }
    }
  }
?>
