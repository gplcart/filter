<?php

/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\filter\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\core\models\UserRole;

/**
 * Handles incoming requests and outputs data related to Filter module
 */
class Filter extends Controller
{

    /**
     * User role model instance
     * @var \gplcart\core\models\UserRole $role
     */
    protected $role;

    /**
     * An array of filter data
     * @var array
     */
    protected $data_filter;

    /**
     * Filter constructor.
     * @param UserRole $role
     */
    public function __construct(UserRole $role)
    {
        parent::__construct();

        $this->role = $role;
    }

    /**
     * Displays the filter overview page
     */
    public function listFilter()
    {
        $this->setTitleListFilter();
        $this->setBreadcrumbListFilter();

        $this->setData('roles', $this->role->getList());
        $this->setData('filters', $this->getListFilter());

        $this->outputListFilter();
    }

    /**
     * Set titles on the filter overview page
     */
    protected function setTitleListFilter()
    {
        $this->setTitle($this->text('Filters'));
    }

    /**
     * Set breadcrumbs on the filter overview page
     */
    protected function setBreadcrumbListFilter()
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

        $breadcrumbs[] = array(
            'text' => $this->text('Filters'),
            'url' => $this->url('admin/module/settings/filter')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Render and output the filter overview page
     */
    protected function outputListFilter()
    {
        $this->output('filter|list');
    }

    /**
     * Returns an array of prepared filters
     * @return array
     */
    protected function getListFilter()
    {
        $roles = $this->role->getList();
        $filters = $this->filter->getHandlers();

        foreach ($filters as $id => &$filter) {

            if (!isset($filter['module']) || $filter['module'] !== 'filter') {
                unset($filters[$id]);
                continue;
            }

            $names = array();

            foreach ($filter['role_id'] as $role_id) {
                if (isset($roles[$role_id]['name'])) {
                    $names[] = $roles[$role_id]['name'];
                }
            }

            $filter['role_name'] = $names;
            $filter['rendered_config'] = json_encode($filter['data'], JSON_PRETTY_PRINT);
        }

        return $filters;
    }

    /**
     * Displays the filter edit page
     * @param string $filter_id
     */
    public function editFilter($filter_id)
    {
        $this->setFilterFilter($filter_id);
        $this->setTitleEditFilter();
        $this->setBreadcrumbEditFilter();

        $this->setData('filter', $this->data_filter);
        $this->setData('roles', $this->role->getList());
        $this->setData('can_delete', $this->canDeleteFilter());

        $this->submitEditFilter();
        $this->setDataEditFilter();
        $this->outputEditFilter();
    }

    /**
     * Sets template data
     */
    protected function setDataEditFilter()
    {
        $data = $this->getData('filter.data');

        if (is_array($data)) {
            $this->setData('filter.data', json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Sets the current filter
     * @param string $filter_id
     */
    protected function setFilterFilter($filter_id)
    {
        $this->data_filter = $this->filter->get($filter_id);

        if (empty($this->data_filter['module']) || $this->data_filter['module'] !== 'filter') {
            $this->setHttpStatus(404);
        }
    }

    /**
     * Set title on the filter edit page
     */
    protected function setTitleEditFilter()
    {
        $title = $this->text('Edit %name', array('%name' => $this->data_filter['name']));
        $this->setTitle($title);
    }

    /**
     * Set breadcrumbs on the filter edit page
     */
    protected function setBreadcrumbEditFilter()
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

        $breadcrumbs[] = array(
            'text' => $this->text('Filters'),
            'url' => $this->url('admin/module/settings/filter')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Saves the submitted filter
     */
    protected function submitEditFilter()
    {
        if ($this->isPosted('delete')) {
            $this->deleteFilter();
        } else if ($this->isPosted('save') && $this->validateEditFilter()) {
            $this->updateFilter();
        }
    }

    /**
     * Validate submitted filter
     */
    protected function validateEditFilter()
    {
        $this->setSubmitted('filter', null, false);
        $this->setSubmittedBool('status');

        $this->validateElement('name', 'required');
        $this->validateElement('description', 'length', array(0, 255));

        $array = json_decode($this->getSubmitted('data'), true);

        if (is_array($array)) {
            $this->setSubmitted('data', $array);
        } else {
            $this->setError('data', $this->text('Invalid configuration'));
        }

        return !$this->hasErrors();
    }

    /**
     * Update a filter
     */
    protected function updateFilter()
    {
        $saved = $this->config->get('module_filter_filters', array());
        $saved[$this->data_filter['filter_id']] = $this->getSubmitted();
        $this->config->set('module_filter_filters', $saved);

        $this->redirect("admin/module/settings/filter", $this->text('Filter has been updated'), 'success');
    }

    /**
     * Delete a saved filter
     */
    protected function deleteFilter()
    {
        $saved = $this->config->get('module_filter_filters', array());
        unset($saved[$this->data_filter['filter_id']]);
        $this->config->set('module_filter_filters', $saved);

        $this->redirect("admin/module/settings/filter", $this->text('Filter has been deleted'), 'success');
    }

    /**
     * Whether the filter can be deleted
     * @return bool
     */
    protected function canDeleteFilter()
    {
        $saved = $this->config->get('module_filter_filters', array());
        return isset($saved[$this->data_filter['filter_id']]) && $this->access('module_filter_delete');
    }

    /**
     * Render and output the filter edit page
     */
    protected function outputEditFilter()
    {
        $this->output('filter|edit');
    }

}
