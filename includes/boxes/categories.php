<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- categories //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_CATEGORIES);

  new infoBoxHeading($info_box_contents, true, false);

  $osC_CategoryTree = new osC_CategoryTree;
  $osC_CategoryTree->setCategoryPath($cPath, '<b>', '</b>');
  $osC_CategoryTree->setParentGroupString('', '');
  $osC_CategoryTree->setParentString('', '->');
  $osC_CategoryTree->setChildString('', '<br>');
  $osC_CategoryTree->setSpacerString('&nbsp;', 2);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => $osC_CategoryTree->buildTree());

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- categories_eof //-->
