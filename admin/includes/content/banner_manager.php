<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Banner_manager extends osC_Template {

/* Public variables */
    var $image_extension;

/* Private variables */

    var $_module = 'banner_manager',
        $_page_title,
        $_page_contents = 'banner_manager.php';

/* Class constructor */

    function osC_Content_Banner_manager() {
      global $osC_MessageStack;

      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      $this->image_extension = osc_dynamic_image_extension();

// check if the graphs directory exists
      if (!empty($this->image_extension)) {
        if (is_dir('images/graphs')) {
          if (!is_writeable('images/graphs')) {
            $osC_MessageStack->add($this->_module, ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE, 'error');
          }
        } else {
          $osC_MessageStack->add($this->_module, ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST, 'error');
        }
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'statistics':
            $this->_page_contents = 'banner_manager_statistics.php';
            break;

          case 'save':
            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

      $banner_error = false;

      if (empty($_POST['banners_title'])) {
        $osC_MessageStack->add($this->_module, ERROR_BANNER_TITLE_REQUIRED, 'error');
        $banner_error = true;
      }

      if (empty($_POST['banners_group']) && empty($_POST['new_banners_group'])) {
        $osC_MessageStack->add($this->_module, ERROR_BANNER_GROUP_REQUIRED, 'error');
        $banner_error = true;
      }

      if (empty($_POST['banners_html_text'])) {
        if (empty($_POST['banners_image_local'])) {
          $banners_image = new upload('banners_image', realpath('../images/' . $_POST['banners_image_target']));

          if ($banners_image->exists()) {
            if (!($banners_image->parse() && $banners_image->save())) {
              $banner_error = true;
            }
          }
        }
      }

      if ($banner_error === false) {
        $db_image_location = (!empty($_POST['banners_image_local'])) ? $_POST['banners_image_local'] : $_POST['banners_image_target'] . $banners_image->filename;

        if (isset($_GET['bID']) && is_numeric($_GET['bID'])) {
          $Qbanner = $osC_Database->query('update :table_banners set banners_title = :banners_title, banners_url = :banners_url, banners_image = :banners_image, banners_group = :banners_group, banners_html_text = :banners_html_text, expires_date = :expires_date, expires_impressions = :expires_impressions, date_scheduled = :date_scheduled, status = :status where banners_id = :banners_id');
          $Qbanner->bindInt(':banners_id', $_GET['bID']);
        } else {
          $Qbanner = $osC_Database->query('insert into :table_banners (banners_title, banners_url, banners_image, banners_group, banners_html_text, expires_date, expires_impressions, date_scheduled, status, date_added) values (:banners_title, :banners_url, :banners_image, :banners_group, :banners_html_text, :expires_date, :expires_impressions, :date_scheduled, :status, now())');
        }
        $Qbanner->bindTable(':table_banners', TABLE_BANNERS);
        $Qbanner->bindValue(':banners_title', $_POST['banners_title']);
        $Qbanner->bindValue(':banners_url', $_POST['banners_url']);
        $Qbanner->bindValue(':banners_image', $db_image_location);
        $Qbanner->bindValue(':banners_group', (!empty($_POST['new_banners_group']) ? $_POST['new_banners_group'] : $_POST['banners_group']));
        $Qbanner->bindValue(':banners_html_text', $_POST['banners_html_text']);

        if (empty($_POST['date_expires'])) {
          $Qbanner->bindRaw(':expires_date', 'null');
          $Qbanner->bindInt(':expires_impressions', $_POST['expires_impressions']);
        } else {
          $Qbanner->bindValue(':expires_date', $_POST['date_expires']);
          $Qbanner->bindInt(':expires_impressions', '0');
        }

        if (empty($_POST['date_scheduled'])) {
          $Qbanner->bindRaw(':date_scheduled', 'null');
          $Qbanner->bindInt(':status', ((isset($_POST['status']) && ($_POST['status'] == 'on')) ? 1 : 0));
        } else {
          $Qbanner->bindValue(':date_scheduled', $_POST['date_scheduled']);
          $Qbanner->bindInt(':status', ($_POST['date_scheduled'] > date('Y-m-d') ? 0 : ((isset($_POST['status']) && ($_POST['status'] == 'on')) ? 1 : 0)));
        }

        $Qbanner->execute();

        if ($osC_Database->isError() === false) {
          if (isset($_GET['bID']) && is_numeric($_GET['bID'])) {
            $banners_id = $_GET['bID'];
          } else {
            $banners_id = $osC_Database->nextID();
          }

          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page'] . '&bID=' . $banners_id));
      }
    }

    function _delete() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['bID']) && is_numeric($_GET['bID'])) {
        if (isset($_POST['delete_image']) && ($_POST['delete_image'] == 'on')) {
          $Qimage = $osC_Database->query('select banners_image from :table_banners where banners_id = :banners_id');
          $Qimage->bindTable(':table_banners', TABLE_BANNERS);
          $Qimage->bindInt(':banners_id', $_GET['bID']);
          $Qimage->execute();

          if (is_file('../images/' . $Qimage->value('banners_image'))) {
            if (is_writeable('../images/' . $Qimage->value('banners_image'))) {
              unlink('../images/' . $Qimage->value('banners_image'));
            } else {
              $osC_MessageStack->add_session($this->_module, ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $osC_MessageStack->add_session($this->_module, ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }
        }

        $Qdelete = $osC_Database->query('delete from :table_banners where banners_id = :banners_id');
        $Qdelete->bindTable(':table_banners', TABLE_BANNERS);
        $Qdelete->bindInt(':banners_id', $_GET['bID']);
        $Qdelete->execute();

        $Qdelete = $osC_Database->query('delete from :table_banners_history where banners_id = :banners_id');
        $Qdelete->bindTable(':table_banners_history', TABLE_BANNERS_HISTORY);
        $Qdelete->bindInt(':banners_id', $_GET['bID']);
        $Qdelete->execute();

        if (!empty($this->image_extension)) {
          if (is_file('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $this->image_extension)) {
            if (is_writeable('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $this->image_extension)) {
              unlink('images/graphs/banner_yearly-' . $_GET['bID'] . '.' . $this->image_extension);
            }
          }

          if (is_file('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $this->image_extension)) {
            if (is_writeable('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $this->image_extension)) {
              unlink('images/graphs/banner_monthly-' . $_GET['bID'] . '.' . $this->image_extension);
            }
          }

          if (is_file('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $this->image_extension)) {
            if (is_writeable('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $this->image_extension)) {
              unlink('images/graphs/banner_daily-' . $_GET['bID'] . '.' . $this->image_extension);
            }
          }
        }

        $osC_MessageStack->add_session($this->_module, SUCCESS_BANNER_REMOVED, 'success');

        osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
      }
    }
  }
?>
