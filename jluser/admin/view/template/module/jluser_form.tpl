<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons">
      <a onclick="jl_check_before_submit()" class="button">
        <span><?php echo $button_save; ?></span>
      </a>
      <a onclick="location = '<?php echo $cancel; ?>';" class="button">
        <span><?php echo $button_cancel; ?></span>
      </a>
  </div>
  </div>
  <div class="content">
	<div id="tabs" class="htabs">
	  <a href="#tab-profile"><?php echo $tab_profile; ?></a>
	  <a href="#tab-associated_customer"><?php echo $tab_associated_customer; ?></a>
	</div>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	  <div id="tab-profile">
		  <table class="form">
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_username; ?></td>
			  <td>
			<input type="text" name="distributor_username" value="<?php echo $distributor_username; ?>">
			<?php if ($error_distributor_username) { ?>
				  <span class="error"><?php echo $error_distributor_username; ?></span>
				<?php } ?>
			  </td>
			</tr>
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_firstname; ?></td>
			  <td>
				<input type="text" name="distributor_firstname" value="<?php echo $distributor_firstname; ?>">
			<?php if ($error_distributor_firstname) { ?>
				  <span class="error"><?php echo $error_distributor_firstname; ?></span>
				<?php } ?>
			  </td>
			</tr>
			<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_lastname; ?></td>
			  <td>
			<input type="text" name="distributor_lastname" value="<?php echo $distributor_lastname; ?>">
			<?php if ($error_distributor_lastname) { ?>
				  <span class="error"><?php echo $error_distributor_lastname; ?></span>
				<?php } ?>
			  </td>
		  </td>
			</tr>
		<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_password; ?></td>
			  <td>
			<input type="text" name="distributor_password" value="aaaa<?php //echo $distributor_password; ?>">
			<?php if ($error_distributor_password) { ?>
				  <span class="error"><?php echo $error_distributor_password; ?></span>
				<?php } ?>
			  </td>
			</tr>
		<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_confirm; ?></td>
			  <td>
			<input type="text" name="distributor_confirm" value="aaaa<?php //echo $distributor_confirm; ?>">
			<?php if ($error_distributor_confirm) { ?>
				  <span class="error"><?php echo $error_distributor_confirm; ?></span>
				<?php } ?>
			  </td>
			</tr>
		<tr>
			  <td><span class="required">*</span> <?php echo $entry_distributor_email; ?></td>
			  <td>
			<input type="text" name="distributor_email" value="a@a.com<?php //echo $distributor_email; ?>">
			<?php if ($error_distributor_email) { ?>
				  <span class="error"><?php echo $error_distributor_email; ?></span>
				<?php } ?>
			  </td>
			</tr>
			<tr>
			  <td><?php echo $entry_distributor_address; ?></td>
			  <td><input type="text" name="distributor_address" value="<?php echo $distributor_address; ?>"></td>
			</tr>
			<tr>
			  <td><?php echo $entry_distributor_telephone; ?></td>
			  <td><input type="text" name="distributor_telephone" value="<?php echo $distributor_telephone; ?>"></td>
			</tr>
			<tr>
			  <td><?php echo $entry_distributor_vip_card_number_list; ?></td>
			  <td>
			<table id='jl_table_card'>
			  <thead>
			  <tr>
				<th><?php echo $text_number; ?></th>
				<th><?php echo $text_rate; ?></th>
				<th>&nbsp;</th>
			  </tr>
			  </thead>
			  <tbody>
			  <?php if (count($distributor_vip_card_number_list) == 0) { ?>
				  <tr>
					<td><input type="text" value="" onblur="this.value = (isNaN(parseInt(this.value))) ? 0 : parseInt(this.value)"></td>
					<td><input type="text" value="" onblur="this.value = (isNaN(parseFloat(this.value))) ? 0 : parseFloat(this.value)"></td>
					<td><input type="button" onclick="jl_delete_row(this)" value="<?php echo $text_delete ?>" />
				  </tr>
			  <?php } else { ?>
				<?php foreach ($distributor_vip_card_number_list as $d) { ?>
				  <tr>
					<td><input <?php echo ($error) ? '' : 'disabled="disabled"' ?>" type="text" value="<?php echo $d['card_id']; ?>" onblur="this.value = (isNaN(parseInt(this.value))) ? 0 : parseInt(this.value)"></td>
					<td><input type="text" value="<?php echo $d['card_rate']; ?>" onblur="this.value = (isNaN(parseFloat(this.value))) ? 0 : parseFloat(this.value)"></td>
					<td><input type="button" onclick="jl_delete_row(this)" value="<?php echo $text_delete ?>" />
				  </tr>
				<?php } ?>
			  <?php } ?>
			  </tbody>
			</table>
			<input type="button" value="<?php echo $text_add; ?>" onclick="jl_add_row()" />
			</tr>
			<tr>
			  <td><?php echo $entry_distributor_point_sale; ?></td>
			  <td>
			<select name="distributor_point_sale">
				<?php if ($distributor_point_sale) { ?>
				  <option value="0"><?php echo $text_no; ?></option>
				  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
				<?php } else { ?>
				  <option value="0" selected="selected"><?php echo $text_no; ?></option>
				  <option value="1"><?php echo $text_yes; ?></option>
				<?php } ?>
				</select>
		  </td>
			</tr>
			<tr>
			  <td><?php echo $entry_distributor_cash_collector; ?></td>
		  <td>
			<select name="distributor_cash_collector">
				<?php if ($distributor_cash_collector) { ?>
				  <option value="0"><?php echo $text_no; ?></option>
				  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
				<?php } else { ?>
				  <option value="0" selected="selected"><?php echo $text_no; ?></option>
				  <option value="1"><?php echo $text_yes; ?></option>
				<?php } ?>
				</select>
		  </td>
			</tr>
		  </table>
		  <input type="hidden" id="distributor_vip_card_number_list" name="distributor_vip_card_number_list" value="" />
		</div>
		<div id="tab-associated_customer">
			<table class="list">
				<thead>
					<tr>
						<td width="1" style="text-align: center;">
							<input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" />
						</td>
						<td class="left">
							<a href="#"><?php echo $text_column_firstname; ?></a>
						</td>
						<td class="left">
							<a href="#"><?php echo $text_column_lastname; ?></a>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php if (is_array($distributor_associated_customer)) { ?>
					<?php foreach ($distributor_associated_customer as $ac) { ?>
					<tr>
						<td style="text-align: center;">
						<?php if ($ac['selected']) { ?>
							<input type="checkbox" name="distributor_associated_customer[]" value="<?php echo $ac['customer_id']; ?>" checked="checked" />
						<?php } else { ?>
							<input type="checkbox" name="distributor_associated_customer[]" value="<?php echo $ac['customer_id']; ?>" />
						<?php } ?>
					</td>
					<td><?php echo $ac['firstname']; ?></td>
					<td><?php echo $ac['lastname']; ?></td>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
    </form>
  </div>
</div>
<script type="text/javascript">

$('#tabs a').tabs(); 

function _setCardList() {
	var card_list = '';
	$('#jl_table_card > tbody  > tr').each(function(i) {
		var number = $(this).find('td').eq(0).find('input').val();
    	var rate = $(this).find('td').eq(1).find('input').val();
		if (number != '') {
			value_submit += number+';'+rate+'#';
			if ($(this).find('td').eq(0).find('input').prop('disabled') === false) {
				value_card_id_to_check += number+';';
			}
		}
	});
}

function jl_check_before_submit() {
	var success = false;
	var value_submit = '';
	var value_card_id_to_check = '';
	$('#jl_table_card > tbody  > tr').each(function(i) {
		var number = $(this).find('td').eq(0).find('input').val();
    	var rate = $(this).find('td').eq(1).find('input').val();
		if (number != '') {
			value_submit += number+';'+rate;
			if ($(this).find('td').eq(0).find('input').prop('disabled') === false) {
				value_submit += ';1';
				value_card_id_to_check += number+';';
			} else if ($(this).css("background-color") != 'transparent') {
				value_submit += ';-1';
			} else {
				value_submit += ';0';
			}
			value_submit += '#';
		}
	});
	$.ajax({
		type: 'post',
		url: 'index.php?route=module/jluser/check_card_id&token=<?php echo $this->session->data["token"]; ?>',
		data: {value: value_card_id_to_check},
		dataType: 'json',
		async: false,
		success: function(response) {
			if (response.success) {
				success = response.success;
				$('#distributor_vip_card_number_list').val(value_submit);
				$('#form').submit();
			} else {
				var message = 'Le(s) numéro(s) de carte suivant existe déjà';
				for (var i=0; i<response.card_id_already_present.length; i++) {
					message += "\n-" + response.card_id_already_present[i];
				}
				alert(message);
			}
		}
	});
}

function jl_add_row()
{
	var text = '<tr>';
	text += '<td><input type="text" value="" onblur="this.value = (isNaN(parseInt(this.value))) ? 0 : parseInt(this.value)"></td>';
	text += '<td><input type="text" value="" onblur="this.value = (isNaN(parseFloat(this.value))) ? 0 : parseFloat(this.value)"s></td>';
	text += '<td><input type="button" onclick="jl_delete_row(this)" value="<?php echo $text_delete; ?>" /></td>';
	text += '</tr>';
	$('#jl_table_card tr:last').after(text);
}

function jl_delete_row(row)
{
	var i = row.parentNode.parentNode.rowIndex;
	if ($('#jl_table_card tr').eq(i).css("background-color") != 'transparent') {
		$('#jl_table_card tr').eq(i).removeAttr("style");
	} else {
		$('#jl_table_card tr').eq(i).css("background-color", "red");
	}
}
</script>
<?php echo $footer; ?>