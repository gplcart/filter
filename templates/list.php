<?php
/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<div class="table-responsive">
  <table class="table">
    <thead>
      <tr>
        <th><?php echo $this->text('Filter'); ?></th>
        <th><?php echo $this->text('Description'); ?></th>
        <th><?php echo $this->text('Status'); ?></th>
        <th><?php echo $this->text('Role'); ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($filters as $filter_id => $filter) { ?>
      <tr>
        <td class="middle">
          <a data-toggle="collapse" href="#filter-details-<?php echo $this->e($filter_id); ?>">
            <?php echo $this->e($filter['name']); ?>
          </a>
        </td>
        <td class="middle">
            <?php echo $this->truncate($this->e($filter['description']), 200); ?>
        </td>
        <td class="middle">
          <?php if (empty($filter['status'])) { ?>
          <i class="fa fa-square-o"></i>
          <?php } else { ?>
          <i class="fa fa-check-square-o"></i>
          <?php } ?>
        </td>
        <td>
          <?php if (empty($filter['role_name'])) { ?>
          --
          <?php } else { ?>
          <?php echo $this->e(implode(', ', (array) $filter['role_name'])); ?>
          <?php } ?>
        </td>
        <td class="middle">
          <?php if ($this->access('module_filter_edit')) { ?>
          <ul class="list-inline">
            <li>
              <a href="<?php echo $this->url("admin/module/settings/filter/edit/$filter_id"); ?>">
                <?php echo $this->lower($this->text('Edit')); ?>
              </a>
            </li>
          </ul>
          <?php } ?>
        </td>
      </tr>
      <tr class="collapse active" id="filter-details-<?php echo $this->e($filter_id); ?>">
        <td colspan="5">
          <pre><?php echo $filter['rendered_config']; ?></pre>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>