<?php
/**
 * @package Filter
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post" class="form-horizontal">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th><?php echo $this->text('Filter'); ?></th>
          <th><?php echo $this->text('Description'); ?></th>
          <th><?php echo $this->text('Status'); ?></th>
          <th><?php echo $this->text('Role'); ?></th>
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
            <input name="settings[status][<?php echo $this->e($filter_id); ?>]" type="hidden" value="0">
            <input name="settings[status][<?php echo $this->e($filter_id); ?>]" type="checkbox" value="1"<?php echo empty($settings['status'][$filter_id]) ? '' : ' checked'; ?>>
          </td>
          <td>
            <select name="settings[role_id][<?php echo $this->e($filter_id); ?>][]" class="form-control" multiple>
              <?php foreach ($roles as $role_id => $role) { ?>
              <option value="<?php echo $this->e($role_id); ?>"<?php echo isset($settings['role_id'][$filter_id]) && in_array($role_id, $settings['role_id'][$filter_id]) ? ' selected' : ''; ?>>
                <?php echo $this->e($role['name']); ?>
              </option>
              <?php } ?>
              <option value="0"<?php echo isset($settings['role_id'][$filter_id]) && in_array(0, $settings['role_id'][$filter_id]) ? ' selected' : ''; ?>>
                <?php echo $this->text('None'); ?> (<?php echo $this->text('Anonymous'); ?>)
              </option>
            </select>
          </td>
        </tr>
        <tr class="collapse active" id="filter-details-<?php echo $this->e($filter_id); ?>">
          <td colspan="4">
            <pre><?php echo $filter['rendered_config']; ?></pre>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <div class="btn-toolbar">
    <a class="btn btn-default" href="<?php echo $this->url("admin/module/list"); ?>">
      <?php echo $this->text("Cancel"); ?>
    </a>
    <button class="btn btn-default save" name="save" value="1">
      <?php echo $this->text("Save"); ?>
    </button>
  </div>
</form>