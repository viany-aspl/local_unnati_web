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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
        <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px;"> Download</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
                <div class="input-group date">
                  <?php //echo $filter_store; //print_r($stores);//exit; ?>
                  <span class="input-group-btn">
                      
                  <select name="filter_store" id="input-store" class="form-control">
                   <option selected="selected" value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) { //echo $store['store_id'];  ?>
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
                
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left">SI ID </td>
	<td class="text-left">Store Name </td>
                <td class="text-left">Product ID </td>
                <td class="text-right">Product Name </td>
                <td class="text-right">Qnty </td>
                <!--<td class="text-right">Price </td> 
                <td class="text-right">Amount </td>-->
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) {  if($_GET["page"]=="") {$aa=1;} else if($_GET["page"]=="1") {$aa=1;}
              else{ $aa=(($_GET["page"]-1)*20)+1; }?>
              <?php foreach ($orders as $order) { ?>
              <tr>
                <td class="text-left"><?php echo $aa; ?></td>
	<td class="text-left"><?php echo $order['store_name']; ?></td>
                <td class="text-left"><?php echo $order['product_id']; ?></td>
                <td class="text-right"><?php echo $order['product_name']; ?></td>
                <td class="text-right"><?php echo $order['qnty']; ?></td>
                <!--<td class="text-right"><?php echo $order['price']; ?></td>
                <td class="text-right"><?php echo ($order['price']*$order['qnty']); ?></td>-->
              </tr>
              <?php 
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
             
              <?php echo $results; ?>  </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=reportdscl/inventory_report/linked_product&token=<?php echo $token; ?>';
	
        var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store!="") {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	location = url;
});
//--></script> 
<script type="text/javascript"><!--
$('#button-download').on('click', function() {
    url = 'index.php?route=reportdscl/inventory_report/linked_product_download_excel&token=<?php echo $token; ?>';
    
        var filter_store = $('select[name=\'filter_store\']').val();
    if (filter_store!="") {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

    
       
    //location = url;
        window.open(url, '_blank');
});
//-->

</script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>