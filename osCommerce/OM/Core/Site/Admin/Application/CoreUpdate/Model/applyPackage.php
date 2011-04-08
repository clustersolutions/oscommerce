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
  use osCommerce\OM\Core\DateTime;
  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\OSCOM;

  class applyPackage {
    protected static $_to_version;

    public static function execute() {
      $phar_can_open = true;

      $pro_hart = array();

      try {
        $phar = new Phar(OSCOM::BASE_DIRECTORY . 'Work/CoreUpdate/update.phar');

        $meta = $phar->getMetadata();

        self::$_to_version = $meta['version_to'];

// reset the log
        if ( file_exists(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') && is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt') ) {
          unlink(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt');
        }

        self::log('##### UPDATE TO ' . self::$_to_version . ' STARTED');

// first delete files before extracting new files
        if ( isset($meta['delete']) ) {
          foreach ( $meta['delete'] as $file ) {
            $directory = (substr($file, 0, 14) == 'osCommerce/OM/' ? realpath(OSCOM::BASE_DIRECTORY . '../../') : realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../')) . '/';

            if ( file_exists($directory . $file) ) {
              if ( is_dir($directory . $file) ) {
// first delete files inside
                $DL = new DirectoryListing($directory . $file);
                $DL->setRecursive(true);
                $DL->setAddDirectoryToFilename(true);
                $DL->setIncludeDirectories(false);

                foreach ( $DL->getFiles() as $f ) {
                  if ( rename($directory . $file . '/' . $f['name'], $directory . $file . '/' . dirname($f['name']) . '/.CU_' . basename($f['name'])) ) {
                    $pro_hart[] = array('type' => 'file',
                                        'where' => $directory,
                                        'path' => $file . '/' . dirname($f['name']) . '/.CU_' . basename($f['name']),
                                        'log' => true);
                  }
                }

// then empty directories inside
                $DL = new DirectoryListing($directory . $file);
                $DL->setRecursive(true);
                $DL->setAddDirectoryToFilename(true);
                $DL->setIncludeFiles(false);

                foreach ( $DL->getFiles() as $f ) {
                  if ( rename($directory . $file . '/' . $f['name'], $directory . $file . '/' . dirname($f['name']) . '/.CU_' . basename($f['name'])) ) {
                    $pro_hart[] = array('type' => 'directory',
                                        'where' => $directory,
                                        'path' => $file . '/' . dirname($f['name']) . '/.CU_' . basename($f['name']),
                                        'log' => true);
                  }
                }

// lastly the (now) empty directory itself
                if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                  $pro_hart[] = array('type' => 'directory',
                                      'where' => $directory,
                                      'path' => dirname($file) . '/.CU_' . basename($file),
                                      'log' => true);
                }
              } else {
                if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                  $pro_hart[] = array('type' => 'file',
                                      'where' => $directory,
                                      'path' => dirname($file) . '/.CU_' . basename($file),
                                      'log' => true);
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

            $directory = (substr($file, 0, 14) == 'osCommerce/OM/' ? realpath(OSCOM::BASE_DIRECTORY . '../../') : realpath(OSCOM::getConfig('dir_fs_public', 'OSCOM') . '../')) . '/';

            if ( file_exists($directory . $file) ) {
              if ( rename($directory . $file, $directory . dirname($file) . '/.CU_' . basename($file)) ) {
                $pro_hart[] = array('type' => 'file',
                                    'where' => $directory,
                                    'path' => dirname($file) . '/.CU_' . basename($file),
                                    'log' => false);
              }
            }

            if ( $phar->extractTo($directory, $file, true) ) {
              self::log('Extracted: ' . $file);
            } else {
              self::log('*** Could Not Extract: ' . $file);
            }
          }
        }

        self::log('##### CLEANUP');

        foreach ( array_reverse($pro_hart, true) as $mess ) {
          if ( $mess['type'] == 'directory' ) {
            if ( rmdir($mess['where'] . $mess['path']) ) {
              if ( $mess['log'] === true ) {
                self::log('Deleted: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            } else {
              if ( $mess['log'] === true ) {
                self::log('*** Could Not Delete: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            }
          } else {
            if ( unlink($mess['where'] . $mess['path']) ) {
              if ( $mess['log'] === true ) {
                self::log('Deleted: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            } else {
              if ( $mess['log'] === true ) {
                self::log('*** Could Not Delete: ' . str_replace('/.CU_', '/', $mess['path']));
              }
            }
          }
        }

        self::log('##### UPDATE TO ' . self::$_to_version . ' COMPLETED');
      } catch ( \Exception $e ) {
        $phar_can_open = false;

        self::log('##### ERROR: ' . $e->getMessage());

        self::log('##### REVERTING');

        foreach ( array_reverse($pro_hart, true) as $mess ) {
          if ( $mess['type'] == 'directory' ) {
            if ( file_exists($mess['where'] . str_replace('/.CU_', '/', $mess['path'])) ) {
              rmdir($mess['where'] . str_replace('/.CU_', '/', $mess['path']));
            }
          } else {
            if ( file_exists($mess['where'] . str_replace('/.CU_', '/', $mess['path'])) ) {
              unlink($mess['where'] . str_replace('/.CU_', '/', $mess['path']));
            }
          }

          if ( file_exists($mess['where'] . $mess['path']) ) {
            rename($mess['where'] . $mess['path'], $mess['where'] . str_replace('/.CU_', '/', $mess['path']));
          }

          self::log('Reverted: ' . str_replace('/.CU_', '/', $mess['path']));
        }

        self::log('##### REVERTING COMPLETED');
        self::log('##### UPDATE TO ' . self::$_to_version . ' FAILED');

        trigger_error($e->getMessage());
        trigger_error('Please review the update log at: ' . OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt');
      }

      return $phar_can_open;
    }

    protected static function log($message) {
      if ( is_writable(OSCOM::BASE_DIRECTORY . 'Work/Logs') ) {
        file_put_contents(OSCOM::BASE_DIRECTORY . 'Work/Logs/update-' . self::$_to_version . '.txt', '[' . DateTime::getNow('d-M-Y H:i:s') . '] ' . $message . "\n", FILE_APPEND);
      }
    }
  }
?>
