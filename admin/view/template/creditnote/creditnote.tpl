<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Credit Note</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>">creditnote</a></li>
        <?php } ?>
      </ul>
      <a href="<?php echo $redirect; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i></a>
      </button>
      
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i>creditnote</h3>
        
<!--        <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            Download Excel</button>
        
        <button type="button" id="button-pdf" class="btn btn-primary pull-right" style="margin-top: -8px !important; margin-right: 10px !important;">
            Download PDF</button> 
        <button type="button" id="button-bill" class="btn btn-primary pull-right" onclick="open_model()" style="margin-top: -8px !important; margin-right: 10px !important;">
            Create Bill</button>
        <button type="button" id="button-details" class="btn btn-primary pull-right" style="margin-top: -8px !important; margin-right: 10px !important;">
            Download Itemized Excel</button> -->
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date" id="date_from">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date" id="date_to">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
            
                      
                  <select name="filter_store" id="input-store" style="width: 100%;" class="select2 form-control">
	<option value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) { ?>
                  <?php if ($store['store_id'] == $filter_store) { ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              
              </div>


              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">                         
                       
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left">S.No.</td>
                <td class="text-left">Date</td>
                <td class="text-left">Store</td>
                <td class="text-left">Invoice No</td>
                <td class="text-left">Total Amount</td>
                 <td class="text-left">Action</td>
                
                
            </thead>
            <tbody>
              <?php if ($orders) { if($_GET["page"]=="") {$aa=1;} else if($_GET["page"]=="1") {$aa=1;}
              else{ $aa=(($_GET["page"]-1)*20)+1; } ?>
              <?php foreach ($orders as $order) { //print_r($order); ?>
              <tr>
                <td class="text-left"><?php echo $aa; ?></td>
                <td class="text-left"><?php echo $order['cr_date']; ?></td>
                <td class="text-left"><?php echo $order['store_name']; ?></td>
                <td class="text-right"><?php echo $order['credit_no']; ?></td>
                <td class="text-left"><?php echo $order['total_amount_formated']; ?></td>
              
                <td class="text-right">
                    <a href="<?php echo 'index.php?route=creditnote/creditnote/download_creditnote&token='.$token . '&credit_no='.$order['id']; ?>" data-toggle="tooltip" title="Download Creditnote" style="margin-left: 5px;" class="btn btn-info">
                    <i class="fa fa-download"></i>
                    </a> </td>
	      
              </tr>              <?php $aa++; } ?>
              <?php  } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right">
	
		<?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>


<!-- Modal -->
  <div class="modal fade" id="myModal_create_bill" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="partner_cncl_btn2" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Create Bill</h4>
        </div>
        <div class="modal-body">
        <form action="index.php?route=report/reconciliation/create_bill&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" onsubmit="return myFunction()" >
            <div class="form-group">
                <label class="control-label" for="input-date">Date</label>
                <div class="input-group date" id="date_cr">
                  <input required type="text" name="filter_date"  placeholder="" data-date-format="YYYY-MM-DD" id="input-date" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            <div class="form-group">
            <label for="input-username">Store</label>
            <div class="input-group"><span class="input-group-addon"><i class="fa fa-building-o" aria-hidden="true"></i></span>
                
               <select name="filter_store_2" id="input-store" class="form-control" required onchange="get_unit(this.value)">
	    <option value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) { ?>
                  <?php if (($store['store_id'] == $filter_store) & $filter_store="0") { ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>

            </div>
            </div>
            <div class="form-group">
            <label for="input-username">Unit</label>
            <div class="input-group"><span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                <select name="filter_unit_2" id="input-unit_2" class="form-control" required >
                         <option value="" >SELECT UNIT</option>

                                          <option value="01" <?php if ('01' == $filter_unit) { ?> selected="selected" <?php } ?>>AJBAPUR</option>
			<option value="02" <?php if ('02' == $filter_unit) { ?> selected="selected" <?php } ?>>RUPAPUR</option>
			<option value="03" <?php if ('03' == $filter_unit) { ?> selected="selected" <?php } ?>>HARIYAWAN</option>
			<option value="04" <?php if ('04' == $filter_unit) { ?> selected="selected" <?php } ?>>LONI</option>
                                   
                </select>
            </div>
            </div>
            
            
            <div class="text-right">
                <input type="submit" id="partner_sbmt_btn"  class="btn btn-primary" value="Submit" />
                <button type="button" id="partner_cncl_btn" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </form>
        </div>
        <!--<div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>-->
      </div>
      
    </div>
  </div>




  <script type="text/javascript">
$("#input-store").select2();
function get_unit(store_id)
{
url = 'index.php?route=report/reconciliation/get_store_unit&token=<?php echo $token; ?>&store_id='+store_id;
$.ajax({
              url: url,
              // dataType: 'json',
               success: function(json) 
               {
                           //alert(json);	
		$("#input-unit_2").html(json);  
               }
                       
              });

}

function open_model()
{
$('#myModal_create_bill').modal('show');
$('input[name=\'filter_date\']').val('');
$('select[name=\'filter_store_2\']').val('');
$('select[name=\'filter_unit_2\']').val('');

return false;
}
function myFunction() {
    
             var filter_date = $('input[name=\'filter_date\']').val();
	
              var filter_store = $('select[name=\'filter_store_2\']').val();
	
	var filter_unit = $('select[name=\'filter_unit_2\']').val();
	
	if ( (filter_date!= '') && (filter_store != '') && (filter_unit != '') ){
		
		$('#myModal_create_bill').modal('hide');
		
		return true;
	}

   
   return false;
    
}
<!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=creditnote/creditnote&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	
              var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	var filter_unit = $('select[name=\'filter_unit\']').val();
	
	if (filter_unit != '') {
		url += '&filter_unit=' + encodeURIComponent(filter_unit);
	}	

	location = url;
});
//--></script> 
 <script type="text/javascript"><!--
$('#button-download').on('click', function() {
	url = 'index.php?route=report/reconciliation/download_excel&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_unit = $('select[name=\'filter_unit\']').val();
	
	if (filter_unit != '') {
		url += '&filter_unit=' + encodeURIComponent(filter_unit);
	}	

	
         var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}	
        window.open(url, '_blank');
	//location = url;
});
//--></script> 
        
<script type="text/javascript"><!--
$('#button-pdf').on('click', function() {
	url = 'index.php?route=report/reconciliation/download_pdf&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_unit =$('select[name=\'filter_unit\']').val();
	
	if (filter_unit != '') {
		url += '&filter_unit=' + encodeURIComponent(filter_unit);
	}	

         var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}	
        window.open(url, '_blank');
	//location = url;
});


$('#button-details').on('click', function() {
	url = 'index.php?route=report/reconciliation/download_item_excel&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_unit = $('select[name=\'filter_unit\']').val(); 
	
	if (filter_unit != '') {
		url += '&filter_unit=' + encodeURIComponent(filter_unit);
	}	

         var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}	
        window.open(url, '_blank');
	//location = url;
});

//--></script> 
        <script type="text/javascript">
/*
    var maxDate =  new Date();
    //var minDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), +1); //one day next before month
$("#date_cr").datetimepicker({
  timepicker: false,
  pickTime: false,
  maxDate: maxDate,
  closeOnDateSelect: true
}); 
  $("#date_from").datetimepicker({
  timepicker: false,
  pickTime: false,
  maxDate: maxDate,
  closeOnDateSelect: true
}).on('dp.change', function (ev) {
   
   change_to_date();
});
$("#date_to").datetimepicker({
  showClear: true,
  timepicker: false,
  pickTime: false,
  
  maxDate: maxDate,
  closeOnDateSelect: true
}).on('dp.change', function (ev) {
   
   change_to_equal_date();
});
function change_to_equal_date()
{
var frm=$("#input-date-start").val(); 
var too=$("#input-date-end").val(); 
var fromTime = new Date(frm);
var minDate = new Date(fromTime.getFullYear(), fromTime.getMonth(), +1); //one day next before month
var maxDate =  new Date(fromTime.getFullYear(), fromTime.getMonth() +1, +0); // one day before next month
var date_to=convert(maxDate);
var toTime = new Date(too);

var millisecondsPerDay = 1000 * 60 * 60 * 24;
var millisBetween = toTime.getTime()-fromTime.getTime();
var days = millisBetween / millisecondsPerDay;

//alert(fromTime+' && '+toTime+' && '+ days);

    if(new Date(frm).getTime()>new Date(too).getTime())
    {
        $("#input-date-end").val(date_to);
        alertify.error('End date can not be less then start date');
    }
    if(days>31)
    {
        $("#input-date-end").val(date_to);
        alertify.error('There can be maximum 1 month difference between start date and end date');
    }
}
function change_to_date()
{
var frm=$("#input-date-start").val();
var fromTime = new Date(frm);
var minDate = new Date(fromTime.getFullYear(), fromTime.getMonth(), +1); //one day next before month
var maxDate =  new Date(fromTime.getFullYear(), fromTime.getMonth() +1, +0); // one day before next month
var date_to=convert(maxDate);

$("#input-date-end").val(date_to);
//$("#date_to").datetimepicker('update', "2017/09/20");
//$("#date_to").removeClass("date");

$("#date_to").datetimepicker({
  showClear: true,
  timepicker: false,
  pickTime: false,
  minDate: minDate, 
  maxDate: maxDate,
  closeOnDateSelect: true
});
    
}
$('#date_from').change(function(){
   $(this).next('input.datetimepicker').destroy(); 

   $("#date_to").next('input.datetimepicker').datetimepicker({
       minDate:$(this).val()
   });
});
function convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}
*/
</script>
  <script type="text/javascript">

$('.date').datetimepicker({
	pickTime: false,
	maxDate: new Date()
});

</script></div>
<?php echo $footer; ?>