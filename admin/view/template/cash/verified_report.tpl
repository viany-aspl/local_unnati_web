<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <?php if ($success) {  ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
        <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            Download</button>
      </div>
        
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              	  <div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
                <div class="input-group ">
                 
                  <span class="input-group-btn">
                      
                  <select name="filter_store" id="filter_store" class="form-control">
                   <option selected="selected" value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) {   ?>
                  <?php if ($store['store_id'] == $filter_store) {
                      if($filter_store!=""){
                      ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                      <?php }} else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <span style="font-weight: bold;"> Total Amount : <?php echo $total_amount; ?></span> <br/> <br/>
          <table class="table table-bordered">
            <thead>
              <tr>
	<td class="text-left">SID</td>
	<td class="text-left">Store Name <?php //echo $column_title; ?></td>
                <td class="text-left">Store ID <?php //echo $column_date_end; ?>
                
                
                <td class="text-right">Transaction Type <?php //echo //$column_orders; ?></td>
                <td class="text-right">Deposit Date <?php //echo $column_total; ?></td>
                <td class="text-right">Transaction Number <?php //echo $column_total; ?></td>
                <td class="text-left">Deposit Amount<?php //echo $column_date_end; ?></td>
                <td class="text-left">Branch Code <?php //echo $column_date_end; ?></td>
                <td class="text-left">Branch Location<?php //echo $column_date_end; ?></td>
	<td class="text-left">Verified by<?php //echo $column_date_end; ?></td>
                <td class="text-left">Remarks<?php //echo $column_date_end; ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) { $total=0; if($_GET["page"]=="1"){ $aa=1; }elseif($_GET["page"]==""){$aa=1;} else { $aa=@$_GET["page"]+20-1; } ?>
              <?php foreach ($orders as $order) { ?>
                
              <tr>
                	<td class="text-left"><?php echo $aa; ?></td>
                
                <td class="text-left"><?php echo $order['store_name']; ?></td>
	<td class="text-left"><?php echo $order['store_id']; ?></td>
                <td class="text-right"><?php echo $order['bank_name']; ?></td>
                <td class="text-right"><?php echo $order['deposit_date']; ?></td>
                <td class="text-right"><?php echo $order['transaction_number']; ?></td>
                <td class="text-left"><?php echo $order['amount']; ?></td>
                <td class="text-left"><?php echo $order['branch_code']; ?></td>
                <td class="text-left"><?php echo $order['branch_location']; ?></td>
	<td class="text-left"><?php echo $order['Verified_By']; ?></td>
                <td class="text-left" style="max-width: 100px;"><?php echo $order['remarks']; ?></td>
              </tr>
              <?php $total=$total+$order['amount'];
              		$aa++;
              } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"> 
              <span style="font-weight: bold;">Page Total Amount : <?php echo $total; ?></span> <br/>
              <?php echo $results; ?>  </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=cash/verify/verify_list&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_store = $('#filter_store').val();
	
	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	location = url;
});
//--></script> 
    <script type="text/javascript"><!--
$('#button-download').on('click', function() {
	url = 'index.php?route=cash/verify/verify_list_download&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
             var filter_store = $('#filter_store').val();
	
	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
        window.open(url, '_blank');
});
//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>