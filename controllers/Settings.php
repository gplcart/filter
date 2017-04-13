<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\filter\controllers;

use gplcart\core\models\Module as ModuleModel,
    gplcart\core\models\Filter as FilterModel,
    gplcart\core\models\UserRole as UserRoleModel;
use gplcart\core\controllers\backend\Controller as BackendController;

/**
 * Handles incoming requests and outputs data related to Filter module
 */
class Settings extends BackendController
{

    /**
     * Module model instance
     * @var \gplcart\core\models\Module $module
     */
    protected $module;

    /**
     * Filter model instance
     * @var \gplcart\core\models\Filter $filter
     */
    protected $filter;

    /**
     * User role model instance
     * @var \gplcart\core\models\UserRole $role
     */
    protected $role;

    /**
     * @param ModuleModel $module
     * @param FilterModel $filter
     * @param UserRoleModel $role
     */
    public function __construct(ModuleModel $module, FilterModel $filter,
            UserRoleModel $role)
    {
        parent::__construct();

        $this->role = $role;
        $this->module = $module;
        $this->filter = $filter;
    }

    /**
     * Route page callback to display the module settings page
     */
    public function editSettings()
    {
        $this->controlAccess('module_filter_edit');

        $this->setTitleEditSettings();
        $this->setBreadcrumbEditSettings();

        $this->setData('roles', $this->role->getList());
        $this->setData('filters', $this->getFiltersSettings());
        $this->setData('settings', $this->config->module('filter'));

        $this->submitSettings();
        $this->outputEditSettings();
    }

    /**
     * Returns an array of prepared filters
     * @return array
     */
    protected function getFiltersSettings()
    {
        $filters = $this->filter->getList();
        foreach ($filters as &$filter) {
            $filter['rendered_config'] = print_r($filter['config'], true);
        }
        return $filters;
    }

    /**
     * Set title on the module settings page
     */
    protected function setTitleEditSettings()
    {
        $vars = array('%name' => $this->text('Filter'));
        $title = $this->text('Edit %name settings', $vars);
        $this->setTitle($title);
    }

    /**
     * Set breadcrumbs on the module settings page
     */
    protected function setBreadcrumbEditSettings()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $breadcrumbs[] = array(
            'text' => $this->text('Modules'),
            'url' => $this->url('admin/module/list')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Saves the submitted settings
     */
    protected function submitSettings()
    {
        if ($this->isPosted('save') && $this->validateSettings()) {
            $this->updateSettings();
        }
    }

    /**
     * Validate submitted module settings
     */
    protected function validateSettings()
    {
        $this->setSubmitted('settings');
        return !$this->hasErrors();
    }

    /**
     * Update module settings
     */
    protected function updateSettings()
    {
        $this->controlAccess('module_edit');
        $this->controlAccess('module_filter_edit');

        $this->module->setSettings('filter', $this->getSubmitted());
        $this->redirect('', $this->text('Settings have been updated'), 'success');
    }

    /**
     * Render and output the module settings page
     */
    protected function outputEditSettings()
    {
        $this->output('filter|settings');
    }

}
