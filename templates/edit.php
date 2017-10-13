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
  <div class="form-group">
    <label class="col-md-2 control-label"><?php echo $this->text('Status'); ?></label>
    <div class="col-md-4">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-default<?php echo empty($filter['status']) ? '' : ' active'; ?>">
          <input name="filter[status]" type="radio" autocomplete="off" value="1"<?php echo empty($filter['status']) ? '' : ' checked'; ?>><?php echo $this->text('Enabled'); ?>
        </label>
        <label class="btn btn-default<?php echo empty($filter['status']) ? ' active' : ''; ?>">
          <input name="filter[status]" type="radio" autocomplete="off" value="0"<?php echo empty($filter['status']) ? ' checked' : ''; ?>><?php echo $this->text('Disabled'); ?>
        </label>
      </div>
      <div class="help-block">
        <?php echo $this->text('Disabled filters will be excluded from processing'); ?>
      </div>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('name', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Name'); ?></label>
    <div class="col-md-4">
      <input maxlength="255" name="filter[name]" class="form-control" value="<?php echo isset($filter['name']) ? $this->e($filter['name']) : ''; ?>">
      <div class="help-block">
        <?php echo $this->error('name'); ?>
        <div class="text-muted">
          <?php echo $this->text('Name of the filter for administrators'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('description', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Description'); ?></label>
    <div class="col-md-4">
      <input maxlength="255" name="filter[description]" class="form-control" value="<?php echo isset($filter['description']) ? $this->e($filter['description']) : ''; ?>">
      <div class="help-block">
        <?php echo $this->error('description'); ?>
        <div class="text-muted">
          <?php echo $this->text('Description of the filter for administrators'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group<?php echo $this->error('role_id', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Roles'); ?></label>
    <div class="col-md-4">
      <select name="filter[role_id][]" class="form-control" multiple>
        <?php foreach ($roles as $role) { ?>
        <?php if (isset($filter['role_id']) && in_array($role['role_id'], $filter['role_id'])) { ?>
        <option value="<?php echo $role['role_id']; ?>" selected><?php echo $this->e($role['name']); ?></option>
        <?php } else { ?>
        <option value="<?php echo $role['role_id']; ?>"><?php echo $this->e($role['name']); ?></option>
        <?php } ?>
        <?php } ?>
      </select>
      <div class="help-block">
        <?php echo $this->error('role_id'); ?>
        <div class="text-muted">
          <?php echo $this->text('Only users with the selected roles will be able to use this filter'); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group required<?php echo $this->error('data', ' has-error'); ?>">
    <label class="col-md-2 control-label"><?php echo $this->text('Configuration'); ?></label>
    <div class="col-md-10">
      <textarea name="filter[data]" rows="20" class="form-control"><?php echo isset($filter['data']) ? $this->e($filter['data']) : ''; ?></textarea>
      <div class="help-block">
        <?php echo $this->error('data'); ?>
        <div class="text-muted">
          <?php echo $this->text('JSON string containing configuration for this filter. See available options <a href="@url">here</a>', array('@url' => 'http://htmlpurifier.org/live/configdoc/plain.html')); ?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-10 col-md-offset-2">
      <div class="btn-toolbar">
        <?php if ($can_delete) { ?>
        <button class="btn btn-danger delete" name="delete" value="1" onclick="return confirm('<?php echo $this->text('Are you sure? It cannot be undone!'); ?>');">
          <?php echo $this->text('Delete'); ?>
        </button>
        <?php } ?>
        <a href="<?php echo $this->url('admin/module/settings/filter'); ?>" class="btn btn-default"><?php echo $this->text('Cancel'); ?></a>
        <?php if ($this->access('module_filter_edit')) { ?>
        <button class="btn btn-default save" name="save" value="1">
          <?php echo $this->text('Save'); ?>
        </button>
        <?php } ?>
      </div>
    </div>
  </div>
</form>


