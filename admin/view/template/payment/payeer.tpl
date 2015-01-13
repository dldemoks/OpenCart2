<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-liqpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
  
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="warning"><?php echo $error_warning; ?></div>
		<?php } ?>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payeer" class="form-horizontal">
			
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_status_help; ?>">
							<?php echo $entry_status; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<select name="payeer_status" class="form-control">
							<?php if ($payeer_status) { ?>
							<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
							<option value="0"><?php echo $text_disabled; ?></option>
							<?php } else { ?>
							<option value="1"><?php echo $text_enabled; ?></option>
							<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_order_status_help; ?>">
							<?php echo $entry_order_status; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<select name="payeer_order_status_id" class="form-control">
							<?php foreach ($order_statuses as $order_status) { ?>
							<?php if ($order_status['order_status_id'] == $payeer_order_status_id) { ?>
							<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<?php echo $entry_geo_zone; ?>
					</label>
					<div class="col-sm-10">
						<select name="payeer_geo_zone_id" class="form-control">
							<option value="0"><?php echo $text_all_zones; ?></option>
							<?php foreach ($geo_zones as $geo_zone) { ?>
							<?php if ($geo_zone['geo_zone_id'] == $payeer_geo_zone_id) { ?>
							<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_sort_order_help; ?>">
							<?php echo $entry_sort_order; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_sort_order" value="<?php echo $payeer_sort_order; ?>" size="1" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_url_help; ?>">
							<?php echo $entry_url; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_url" value="<?php echo $payeer_url; ?>" class="form-control" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_merchant_help; ?>">
							<?php echo $entry_merchant; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_merchant" value="<?php echo $payeer_merchant; ?>" class="form-control" />
						<?php if ($error_merchant) { ?>
						<span class="error"><?php echo $error_merchant; ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_security_help; ?>">
							<?php echo $entry_security; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_security" value="<?php echo $payeer_security; ?>" class="form-control" />
						<?php if ($error_security) { ?>
						<span class="error"><?php echo $error_security; ?></span>
						<?php } ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_order_desc_help; ?>">
							<?php echo $entry_order_desc; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_order_desc" value="<?php echo $payeer_order_desc; ?>" size="60" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_log_help; ?>">
							<?php echo $entry_log; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_log_value" value="<?php echo $payeer_log_value; ?>" size="60"  class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<span data-toggle="tooltip" title="<?php echo $entry_list_ip_help; ?>">
							<?php echo $entry_list_ip; ?>
						</span>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_list_ip" value="<?php echo $payeer_list_ip; ?>" size="60" class="form-control" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="">
						<?php echo $entry_admin_email; ?>
					</label>
					<div class="col-sm-10">
						<input type="text" name="payeer_admin_email" value="<?php echo $payeer_admin_email; ?>" size="60" class="form-control" />
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?> 