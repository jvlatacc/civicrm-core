<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2015
 * $Id$
 *
 */
class CRM_Group_Page_Group extends CRM_Core_Page_Basic {
  protected $_sortByCharacter;

  /**
   * @return string
   */
  public function getBAOName() {
    return 'CRM_Contact_BAO_Group';
  }

  /**
   * Define action links.
   *
   *   self::$_links array of action links
   */
  public function &links() {
  }

  /**
   * Return class name of edit form.
   *
   * @return string
   */
  public function editForm() {
    return 'CRM_Group_Form_Edit';
  }

  /**
   * Return name of edit form.
   *
   * @return string
   */
  public function editName() {
    return ts('Edit Group');
  }

  /**
   * Return name of delete form.
   *
   * @return string
   */
  public function deleteName() {
    return 'Delete Group';
  }

  /**
   * Return user context uri to return to.
   *
   * @param null $mode
   *
   * @return string
   */
  public function userContext($mode = NULL) {
    return 'civicrm/group';
  }

  /**
   * Return user context uri params.
   *
   * @param null $mode
   *
   * @return string
   */
  public function userContextParams($mode = NULL) {
    return 'reset=1&action=browse';
  }

  /**
   * Make sure that the user has permission to access this group.
   *
   * @param int $id
   *   The id of the object.
   * @param int $title
   *   Name or title of the object.
   *
   * @return string
   *   the permission that the user has (or null)
   */
  public function checkPermission($id, $title) {
    return CRM_Contact_BAO_Group::checkPermission($id, $title);
  }

  /**
   * We need to do slightly different things for groups vs saved search groups, hence we
   * reimplement browse from Page_Basic
   *
   * @param int $action
   *
   * @return void
   */
  public function browse($action = NULL) {
    $groupPermission = CRM_Core_Permission::check('edit groups') ? CRM_Core_Permission::EDIT : CRM_Core_Permission::VIEW;
    $this->assign('groupPermission', $groupPermission);

    $showOrgInfo = FALSE;

    // CRM-9936
    $reservedPermission = CRM_Core_Permission::check('administer reserved groups') ? CRM_Core_Permission::EDIT : CRM_Core_Permission::VIEW;
    $this->assign('reservedPermission', $reservedPermission);

    if (CRM_Core_Permission::check('administer Multiple Organizations') &&
      CRM_Core_Permission::isMultisiteEnabled()
    ) {
      $showOrgInfo = TRUE;
    }
    $this->assign('showOrgInfo', $showOrgInfo);

    // Refresh smart group cache
    if (!empty($_GET['update_smart_groups'])) {
      CRM_Contact_BAO_GroupContactCache::loadAll();
    }
    else {
      CRM_Contact_BAO_GroupContactCache::fillIfEmpty();
    }

    $this->search();
  }

  public function search() {
    if ($this->_action & (CRM_Core_Action::ADD |
        CRM_Core_Action::UPDATE |
        CRM_Core_Action::DELETE
      )
    ) {
      return;
    }

    $form = new CRM_Core_Controller_Simple('CRM_Group_Form_Search', ts('Search Groups'), CRM_Core_Action::ADD);
    $form->setEmbedded(TRUE);
    $form->setParent($this);
    $form->process();
    $form->run();
  }

}
