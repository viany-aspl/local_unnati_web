<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><!--<button onclick ="print_order()" data-toggle="tooltip" title="<?php echo "Print Order"; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>--><!--<a href="<?php echo $shipping; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></a> <a href="<?php echo $edit; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>--> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel Button"; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo "Orders"; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="panel panel-default" id = "print_div">
	<div class="panel-heading">
		<h3 class="panel-title">
			<i class="fa fa-info-circle"></i>
			<?php echo "Order # " . $order_id; ?>
		</h3>
	</div>
      <form method="post" action="">
	<div class="panel-body">
	    <div class="row">
		<div class="col-lg-6">
		Akshamaala Solutions Pvt Ltd F 35, 1st Floor, Sector 8,<br/>
                Noida Noida, Uttar Pradesh 201301 01204040170
                <br/>
                accounts@unnati.com
                <br/>
                www.unnati.world
                <br/>
                PAN No : AAICA9806D
                <br/>
                CIN: U72200DL2010PTC209266
                <br/>
                GSTN : 09AAICA9806D1ZM
		</div>
                <div class="col-lg-6">
		Purchase Order No : <?php echo $order_id; ?>
                <br/>
                Date : <?php echo date('Y-m-d'); ?>
                <br/>
                Invoice No : <?php echo $order_information['order_info']['po_invoice_prefix']; ?>/<?php echo $created_po; ?>
                <br/><br/> 
                Warehouse : 
	<?php if(empty($created_po)){ ?> 
                <select required name="ware_house" style="float: right;width: 80%;" class="form-control">
                    <option value="">SELECT</option>
                    <?php foreach($order_information['ware_houses'] as $ware_houses)
			{
                    ?>
                    <option <?php if($order_information['order_info']['po_ware_house']==$ware_houses['store_id']){?> selected="selected"<?php }  ?> value="<?php echo $ware_houses['store_id']; ?>"><?php echo $ware_houses['name']; ?></option> 
                    <?php
                    
                        } 
                    ?>
                </select>
	<?php }else { ?>
	<?php foreach($order_information['ware_houses'] as $ware_houses)
			{
                    ?>
                   <?php if($order_information['order_info']['po_ware_house']==$ware_houses['store_id']){?> <?php echo $ware_houses['name']; ?><?php }  ?> 
                    <?php
                    
                        } 
                    ?>
	<?php } ?>
		</div>
	    </div>
            <br/>
            <div class="row" style="height: 1px;background-color: silver;width:100%;margin-left: 0%"></div>
            <br/>
            <div class="row">
                <div class="col-lg-6">
                    <b>Party Details :</b>
                    <br/>
                    <?php echo $order_information['products'][0]['store_address']; ?>
                    <br/>
                    PAN No : <?php echo $order_information['products'][0]['store_pan']; ?>
                    <br/>
                    GSTN : <?php echo $order_information['products'][0]['store_gst']; ?>
                </div>
                
            </div>
            <br/><br/>
            
		<table class="table table-bordered" id="print_table" border="1">
          <thead>
                <tr>
                          <td class="text-left" style="width: 11.11%;">S.No.</td>
                          <td class="text-left" style="width: 11.11%;">Product Name</td>
                          <td class="text-left" style="width: 11.11%;">HSN</td>
			  <td class="text-left" style="width: 11.11%;">Rate</td>
			  <td class="text-left" style="width: 11.11%;">Tax Type</td>
                          <td class="text-left" style="width: 11.11%;">Tax (Per Unit)</td>
			  <td class="text-left" style="width: 11.11%;">Quantity</td>
			  <td class="text-left" style="width: 11.11%;">Amount</td>
                          <?php if(empty($created_po)){ ?> 
                          <td class="text-left" style="width: 11.11%;">Action</td>
                          <?php } ?>
                </tr>
          </thead>
          <tbody id="t_body">
          
              <input type="hidden" name="order_id" value="<?php echo $order_information['order_info']['id']; ?>" />
              <input type="hidden" name="store_id" value="<?php echo $order_information['products'][0]['store_id']; ?>" />
		  <?php
			$grand_total = 0;$a=1;
                        $p_count=count($order_information['products']);
			foreach($order_information['products'] as $product)
			{ //print_r($product);
		  ?>
            <tr id="tr_<?php echo  $a; ?>">
                <input name="product_id[]" id="p_id_<?php echo  $a; ?>" type="hidden" value="<?php echo  $product['product_id'];?>" />
                <input name="product_hsn[]" id="p_hsn_<?php echo  $a; ?>" type="hidden" value="<?php echo  $product['product_hsn'];?>" />
                <input class="form-control" name="p_tax_type[]" id="p_tax_type_<?php echo  $a; ?>" type="hidden" value="<?php echo  $product['product_tax_type'];?>" />
                <input class="form-control" name="p_tax_rate[]" id="p_tax_rate_<?php echo  $a; ?>" type="hidden" value="<?php echo  round($product['product_tax_rate'],PHP_ROUND_HALF_UP);?>" />
                <input class="form-control" name="p_amount[]" id="p_amount_<?php echo  $a; ?>" type="hidden" value="<?php echo (round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']; ?>" />
                     
                <td class="text-left" id="td_sid_<?php echo  $a; ?>"><?php echo  $a;?></td>
                          <td class="text-left" id="td_p_name_<?php echo  $a; ?>">
                               <?php if(empty($created_po)){ ?> 
                              <input required="required" class="form-control" name="product_name[]" id="p_name_<?php echo  $a; ?>" type="text" value="<?php echo  $product['product_name'];?>" />
                               <?php }else{ ?>
			<?php echo  $product['product_name'];?>
		<?php } ?>
 
                          </td>
			  <td id="td_p_hsn_<?php echo  $a; ?>" class="text-left">
                              <?php echo $product['product_hsn'];?>
                          </td>
			  <td class="text-left" id="td_p_price_<?php echo  $a; ?>">
		 <?php if(empty($created_po)){ ?> 
                              <input required="required"  class="form-control"  onkeypress="return remove_zero(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_price(this.value,<?php echo $a; ?>);" name="p_price[]" id="p_price_<?php echo  $a; ?>" type="text" value="<?php echo round($product['product_price'],PHP_ROUND_HALF_UP);?>" />
		<?php }else{ ?>
			<?php echo round($product['product_price'],PHP_ROUND_HALF_UP);?>
		<?php } ?>
                           </td>
			  
			  <td class="text-left" id="td_p_tax_type_<?php echo  $a; ?>">
			  <?php echo $product['product_tax_type'];?>
                              
			  </td>
                          <td class="text-left" id="td_p_tax_rate_<?php echo  $a; ?>">
			  <?php echo round($product['product_tax_rate'],PHP_ROUND_HALF_UP);?>
                              
			  </td>
                          
			  <td class="text-left" id="td_p_qnty_<?php echo  $a; ?>">
			   <?php if(empty($created_po)){ ?> 
                              <input  required="required" onkeypress="return remove_zero_q(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_q(this.value,<?php echo $a; ?>);" class="form-control" name="p_qnty[]" id="p_qnty_<?php echo  $a; ?>" type="text" value="<?php echo  $product['product_quantity'];?>" />
			<?php }else{ ?>
			<?php echo  $product['product_quantity'];?>
		<?php } ?>
			  </td>
			  <td class="text-left" id="td_p_amount_<?php echo  $a; ?>">
			  <?php //+$product['product_tax_rate']
                          echo number_format((float)((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']), 2, '.', '');
                          $grand_total=$grand_total+((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']);
                          ?>
                              
			  </td>
                          <?php if(empty($created_po)){ ?> 
                          <td class="text-left">
                             <?php if(empty($created_po)){ ?> 
                              
                            <button <?php if($a==$p_count){ ?> style="display: inline-block;" <?php }else{ ?> style="display:none;" <?php } ?> type="button" id="add_btn_<?php echo  $a; ?>" class="btn btn-primary add_btn" onclick="return add_row(<?php echo $a; ?>);">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button <?php if($a==$p_count){ ?> style="display: inline-block;" <?php }else{ ?> style="display:none;" <?php } ?> type="button" id="remove_btn_<?php echo  $a; ?>" class="btn btn-danger remove_btn" onclick="return remove_row(<?php echo $a; ?>);">
                                <i class="fa fa-minus"></i>
                            </button>
                             <?php } ?>
                               
                          </td>
                          <?php } ?> 
			</tr>
		<?php
                        $a++;
			}
		?>
			
          
</tbody>
        </table>
            <table class="table table-bordered" id="print_table_2" border="1">
                <tr>
                    <td class="text-right" style="width: 78%;" id="set_colspan" ><b>Sub Total:</b></td>
				<td style="width: 22%;" class ="text-left" id="td_sub_total">
                                    <?php if($grand_total == 0) { echo ''; }
                                else { echo number_format((float)$grand_total, 2, '.', ''); } ?>
                                
                                </td>
                                <input type="hidden" id="sub_total" name="sub_total" value="<?php echo $grand_total; ?>" />
			</tr>
                <?php
			$grand_total;
                        $p_count=count($order_information['products']);
                        $a=1;
                        $gst_5_array=array();
                        
			foreach($order_information['products'] as $product)
			{ //print_r($product);
		  ?>
                <?php if(trim($product['product_tax_type'])=="GST@5%"){ 
                $cgst="2.5";
                
                $sgst="2.5";
                $gst_5_array[]=array(number_format((float)(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$cgst/100), 2, '.', ''));
                
                 }
                 else if(trim($product['product_tax_type'])=="GST@12%"){ 
                $cgst="6";
                $sgst="6";
                 }
                 else if(trim($product['product_tax_type'])=="GST@18%"){ 
                $cgst="9";
                $sgst="9";
                 }?>
                <!--<tr id="tr_scgst_<?php echo  $a; ?>">
                    <td class="text-right" style="width: 78%;" ><b>
                            <span id="span_cgst_txt_<?php echo  $a; ?>" >
                            CGST @<?php echo $cgst; ?>%
                            </span>
                            <br/>
                            <span id="span_sgst_txt_<?php echo  $a; ?>" >
                            SGST @<?php echo $sgst; ?>%
                            </span>
                        </b></td>
                    <td style="width: 22%;" class ="text-left" >
                        <span id="span_cgst_<?php echo  $a; ?>">
                        <?php echo number_format((float)(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$cgst/100), 2, '.', ''); ?>
                        </span>
                        <br/>
                        <span id="span_sgst_<?php echo  $a; ?>">
                        <?php echo number_format((float)(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$sgst/100), 2, '.', ''); ?>
                        </span>
                    </td>
	            
		
		</tr>-->
                        <?php $a++;
                     $grand_total= $grand_total+(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$cgst/100)+(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$sgst/100);  
                 }
                $total_gst_5=0;
                foreach($gst_5_array as $gst_5)
                {
                   $total_gst_5=$total_gst_5+$gst_5[0]; 
                }
                
                 ?>
               <tr id="tr_scgst_<?php echo  $a; ?>">
                    <td class="text-right" style="width: 78%;" ><b>
                            <span id="span_cgst_txt_<?php echo  $a; ?>" >
                            CGST @<?php echo $cgst; ?>%
                            </span>
                            <br/>
                            <span id="span_sgst_txt_<?php echo  $a; ?>" >
                            SGST @<?php echo $sgst; ?>%
                            </span>
                        </b></td>
                    <td style="width: 22%;" class ="text-left" >
                        <span id="span_cgst_<?php echo  $a; ?>">
                        <?php echo number_format((float)(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$cgst/100), 2, '.', ''); ?>
                        </span>
                        <br/>
                        <span id="span_sgst_<?php echo  $a; ?>">
                        <?php echo number_format((float)(((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity'])*$sgst/100), 2, '.', ''); ?>
                        </span>
                    </td>
	            
		
		</tr>         
            </table>
            <table class="table table-bordered" id="print_table_3" border="1">
                <tr>
                    <td class="text-right" style="width: 78%;" id="set_colspan" ><b>Grand Total:</b></td>
				<td style="width: 22%;" class ="text-left" id="td_grand_total">
                                    <?php if($grand_total == 0) { echo ''; }
                                else { echo $grand_total; } ?>
                                 
                                </td>
                                <input type="hidden" id="grand_total" name="grand_total" value="<?php echo $grand_total; ?>" />
			</tr>
                       
			
            </table>
         <?php if(empty($created_po)){ ?>
			<input type="submit" class="btn btn-primary pull-right" value="Create Invoice" />
         <?php }
         else
         {
         ?>
                        <input onclick="send_to_download(<?php echo $order_id; ?>)" type="button" class="btn btn-primary pull-right" value="Download Invoice" />             
         <?php } ?>
		<!--
  <a id="download_pdf" href="<?php echo $pdf_export . '&export=1'; ?>" target="_blank">
			<input type="submit" class="btn btn-primary pull-right"><?php echo "Download as pdf"; ?> />
		</a>
  -->
	</div>
	</form>
  </div>
  
</div>
<script type="text/javascript">
function send_to_download(order_id)
{
    //alert(order_id);
    url = 'index.php?route=purchase/purchase_order/download_invoice&token=<?php echo $token; ?>&order_id='+order_id;
    //location = url;
    window.open(url, '_blank');
}
//////////
function add_row(a)
{
    var count = parseFloat($('#print_table tr').length);//
    //var last_id=$("#last_id").val();
    //alert(count);
    var last_row= parseFloat(count)-parseFloat(1);
    if($("#p_name_"+last_row).val()=="")
    {
        alertify.error('Please fill the details in row'); 
        $("#p_name_"+last_row).focus();
        return false;
    }
    var html='<tr id="tr_'+count+'">'+
                '<input name="product_id[]" id="p_id_'+count+'" value="" type="hidden">'+
                '<input name="product_hsn[]" id="p_hsn_'+count+'" value="" type="hidden">'+
                '<input class="form-control" id="p_tax_type_'+count+'" name="p_tax_type[]" value="" type="hidden">'+
                '<input class="form-control" id="p_tax_rate_'+count+'" name="p_tax_rate[]" value="" type="hidden">'+
                '<input class="form-control" name="p_amount[]" id="p_amount_'+count+'" value="" type="hidden">'+
                '<td class="text-left" id="td_sid_'+count+'">'+count+'</td>'+
                          '<td class="text-left" id="td_p_name_'+count+'">'+
                              
                              '<input autocomplete="off" required="required" class="form-control" name="product_name[]" id="p_name_'+count+'" value="" type="text">'+
                              
                          '</td>'+
			  '<td id="td_p_hsn_'+count+'" class="text-left"></td>'+
			  '<td class="text-left" id="td_p_price_'+count+'">'+
                              '<input required="required" class="form-control" onkeypress="return remove_zero(this.value,'+count+',event);" onkeyup="return update_by_price(this.value,'+count+');" name="p_price[]" id="p_price_'+count+'" value="" type="text">'+
                           '</td>'+
			  
			  '<td class="text-left" id="td_p_tax_type_'+count+'">'+
			  '</td>'+
                          '<td class="text-left" id="td_p_tax_rate_'+count+'">'+                         
			  '</td>'+
                          
			  '<td class="text-left" id="td_p_qnty_'+count+'">'+
			  
                              '<input required="required" onkeypress="return remove_zero_q(this.value,'+count+',event);" onkeyup="return update_by_q(this.value,'+count+');" class="form-control" name="p_qnty[]" id="p_qnty_'+count+'" value="0" type="text">'+
			  '</td>'+
			  '<td class="text-left" id="td_p_amount_'+count+'">'+
			  '</td><td>'+
                          '<button  style="display:inline-block;" type="button" id="add_btn_'+count+'" class="btn btn-primary add_btn" onclick="return add_row('+count+');">'+
                                '<i class="fa fa-plus"></i>'+
                            '</button>'+
                            '<button  style="display:inline-block;" type="button" id="remove_btn_'+count+'" class="btn btn-danger remove_btn" onclick="return remove_row('+count+');">'+
                                '<i class="fa fa-minus"></i>'+
                            '</button>'+
                          '</td>'+
			'</tr>';
                var html2='<tr id="tr_scgst_'+count+'">'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt_'+count+'">'+
                            '</span>'+
                            '<br>'+
                            '<span id="span_sgst_txt_'+count+'">'+
                            '</span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst_'+count+'">'+
                        '</span>'+
                        '<br>'+
                        '<span id="span_sgst_'+count+'">'+
                        '</span>'+
                    '</td>'+
	            '</tr>';
                $("#t_body").append(html);
                $("#print_table_2").append(html2);
                $('.add_btn').hide();
                $('.remove_btn').hide();
                $('#add_btn_'+count).show();
                $('#remove_btn_'+count).show();
                setautoserarch('p_name_'+count);
}
///////////////
function remove_row(a)
{
    if(a==1)
    {
        alertify.error('You can not delete all products '); 
        return false;
    }
    else
    {
    var aa=parseFloat(a)-parseFloat(1) ;
    //alert(a);
    //alert(aa);
    alertify.confirm('Are you sure ! you want to delete this product ?',
                function(e){ 
                    if(e){
                     
                     var row_amount=$('#p_amount_'+a).val();
                     var stotal=$('#sub_total').val();
                     var gtotal=$('#grand_total').val();
        //alert(stotal+"-"+row_amount);
        if(row_amount=="")
        {
            row_amount=0;
        }
        var sub_total = parseFloat(stotal) - parseFloat(row_amount);
        sub_total=parseFloat(sub_total).toFixed(2);
        $("#tr_scgst_"+a).remove();
        $("#tr_"+a).remove();
        var total_tax=0;
        var grand_total=0;
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
        
        
        $('.add_btn').hide();
        $('.remove_btn').hide();
        $('#add_btn_'+aa).show();
        $('#remove_btn_'+aa).show();
                }else{
                    alertify.error('You canceled this action !'); 
                    return false;
                }
            }
                    
                        );
                 $("#alertify-ok").html('Continue');
    }
    
}
function remove_zero_q(valuee,a,evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46)
          {
             if(valuee==0)
    {
    $("#p_qnty_"+a).val('');
    
    }
    return true;
          }
          if (charCode > 31 && (charCode < 48 || charCode > 57))
          {
             return false;
          }
          
    if(valuee==0)
    {
    $("#p_qnty_"+a).val('');
    }
}
function update_by_q(valuee,a)
{
    if(valuee=="")
    { 
       $("#p_qnty_"+a).val('0');
    } 
    if(valuee=="")
    {
        valuee=0;
        //return false;
    }
    //valuee=valuee;
    var tax_rate=parseFloat($("#p_tax_rate_"+a).val());
    var p_price=parseFloat($("#p_price_"+a).val());
    //alert(p_price+'+'+tax_rate);+tax_rate
    var total_amount=(p_price)*(valuee);
    total_amount=parseFloat(total_amount).toFixed(2);
    $('#p_amount_'+a).val(total_amount);
    $('#td_p_amount_'+a).html(total_amount);
    var tax_type=$('#td_p_tax_type_'+a).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(tax_type+","+cgst+"+"+sgst);
    $("#span_cgst_txt_"+a).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+a).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+a).html(tax_cgst);
    $("#span_sgst_"+a).html(tax_sgst);
    
    var sub_total=0;
    var count = parseFloat($('#print_table tr').length);//
    for(c=1;c<=count;c++)
    {
        
        var p_amount=$('#p_amount_<?php //echo $c; ?>'+c).val();
        if(p_amount!=undefined)
        {
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
    }
        sub_total=parseFloat(sub_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        var total_tax=0;
        var grand_total=0;
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
        
        //$('#td_grand_total').html(grand_total);
        //$('#grand_total').val(grand_total);
        return false;
}
    
function remove_zero(valuee,a,evt)
{
    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46)
          {
             if(valuee==0)
    {
    $("#p_price_"+a).val('');
    
    }
    return true;
          }
          if (charCode > 31 && (charCode < 48 || charCode > 57))
          {
             return false;
          }
          
    if(valuee==0)
    {
    $("#p_price_"+a).val('');
    }
}
function update_by_price(valuee,a)
{
    if(valuee=="")
    { 
       $("#p_price_"+a).val('0');
    } 
    if(valuee=="")
    {
        valuee=0;
    }
    valuee=parseFloat(valuee);
    var tax_rate=parseFloat($("#p_tax_rate_"+a).val());
    //+(tax_rate)
    var total_amount=((valuee))*($("#p_qnty_"+a).val());
    total_amount=parseFloat(total_amount).toFixed(2);
    $('#p_amount_'+a).val(total_amount);
    $('#td_p_amount_'+a).html(total_amount);
    
    var tax_type=$('#td_p_tax_type_'+a).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+a).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+a).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+a).html(tax_cgst);
    $("#span_sgst_"+a).html(tax_sgst);
    
    var grand_total=0;
    var sub_total=0;
    var total_tax=0;
    var count = parseFloat($('#print_table tr').length);//
    for(c=1;c<=count;c++)
    {
        
        var p_amount= $('#p_amount_<?php //echo $c; ?>'+c).val()
        if(p_amount!=undefined)
        {
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
    }
    var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        
        grand_total=parseFloat(grand_total).toFixed(2);
        sub_total=parseFloat(sub_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        return false;
}
</script>

 <script type="text/javascript">
   $('input[name=\'product_name[]\']').autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchase/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                console.log(JSON.stringify(json));
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['product_id'],
                        price: item['price'],
                        price_wo_t: item['price_wo_t'],
                        hstn: item['hstn'],
                        product_tax_type: item['product_tax_type'],
                        product_tax_rate: item['product_tax_rate'],
                        
                    }
                }));
            }
        });
    },
    'select': function(item) {
        
        var this_id=(this.id).split('_');
        var thisid=this_id.pop();
        
        $('#p_name_'+thisid).val(item['label']);
        $('#p_id_'+thisid).val(item['value']);
        $('#td_p_tax_type_'+thisid).html(item['product_tax_type']);
        $('#td_p_hsn_'+thisid).html(item['hstn']);
        $('#p_hsn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
      
        var total_amount=(parseFloat(item['price_wo_t']))*($("#p_qnty_"+thisid).val());
        total_amount=parseFloat(total_amount).toFixed(2);
        $('#p_amount_'+thisid).val(total_amount);
        $('#td_p_amount_'+thisid).html(total_amount);
        $('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
        $('#td_p_tax_rate_'+thisid).html(item['product_tax_rate']);
        
    var tax_type=$('#td_p_tax_type_'+thisid).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+thisid).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+thisid).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+thisid).html(tax_cgst);
    $("#span_sgst_"+thisid).html(tax_sgst);
        
        
        var grand_total=0;
        var sub_total=0;
        var total_tax=0;
        var count = parseFloat($('#print_table tr').length);//
        for(c=1;c<=count;c++)
        {
        
        var p_amount=$('#p_amount_'+c).val();
        if(p_amount!=undefined)
        {
        
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
        }
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        sub_total=parseFloat(sub_total).toFixed(2);
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
    }
});


function setautoserarch(id)
{
       $('#'+id).autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchase/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { //alert(JSON.stringify(json));
                console.log(JSON.stringify(json));
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['product_id'],
                        price: item['price'],
                        price_wo_t: item['price_wo_t'],
                        hstn: item['hstn'],
                        product_tax_type: item['product_tax_type'],
                        product_tax_rate: item['product_tax_rate'],
                        
                    }
                }));
            }
        });
    },
    'select': function(item) {
        
        var this_id=(this.id).split('_');
        var thisid=this_id.pop();
       
        $('#p_name_'+thisid).val(item['label']);
        $('#p_id_'+thisid).val(item['value']);
        $('#td_p_tax_type_'+thisid).html(item['product_tax_type']);
        $('#p_tax_type_'+thisid).val(item['product_tax_type']);
        $('#td_p_hsn_'+thisid).html(item['hstn']);
        $('#p_hsn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
        //+parseFloat(item['product_tax_rate'])
        var total_amount=(parseFloat(item['price_wo_t']))*($("#p_qnty_"+thisid).val());
        total_amount=parseFloat(total_amount).toFixed(2);
        $('#p_amount_'+thisid).val(total_amount);
        $('#td_p_amount_'+thisid).html(total_amount);
        $('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
        $('#td_p_tax_rate_'+thisid).html(item['product_tax_rate']);
        
        var tax_type=$('#td_p_tax_type_'+thisid).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+thisid).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+thisid).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+thisid).html(tax_cgst);
    $("#span_sgst_"+thisid).html(tax_sgst);
        
        var grand_total=0;
        var sub_total=0;
        var total_tax=0;
        var count = parseFloat($('#print_table tr').length);//
        for(c=1;c<=count;c++)
        {
        
        var p_amount=$('#p_amount_'+c).val();
        if(p_amount!=undefined)
        {
        //alert(p_amount);
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
        }
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        sub_total=parseFloat(sub_total).toFixed(2);
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
    }
});
    
}
</script>


<script type="text/javascript">
	function print_order()
	{
		var prtContent = document.getElementById("print_div");
		var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
		
		WinPrint.document.writeln('<!DOCTYPE html>');
                WinPrint.document.writeln('<html><head><title></title>');
                WinPrint.document.writeln('<style>table{border:1px; border-collapse:collapse;}');
                WinPrint.document.writeln('table, td, th {border: 1px solid black;}');
		WinPrint.document.writeln('label{font-weight:bold}');
		WinPrint.document.writeln('.text-right{text-align:right;}');
		WinPrint.document.writeln('.remaining_quantity{display:none;}');
		document.getElementById('set_colspan').setAttribute('colspan','7');
		WinPrint.document.writeln('</style></head><body>');
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.writeln('</body></html>');
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}
	
	function download_pdf()
	{
		var doc = new jsPDF();
		doc.fromHTML($('#print_div').get(0),20,20,{
			'width':5000
		});
		doc.save('test.pdf');
	}
	
	function print_order()
	{
		document.getElementById("download_pdf").style.display = "none";
                var printContents = document.getElementById('print_div').innerHTML;
                var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
		document.getElementById("download_pdf").style.display = "block";
    }
	
	
	function demoFromHTML() 
        {
            var pdf = new jsPDF('l', 'pt', 'letter',true);
            //pdf.setFontSize(8);
            source = $('#print_div')[0];
            specialElementHandlers = {
            '#bypassme': function (element, renderer) {
            return true
        }
    };
    margins = {
        top: 100,
        bottom: 80,
        left: 80,
        width:1000
    };
    pdf.fromHTML(
    source,
    margins.left,
    margins.top, {
        'width': margins.width,
        'elementHandlers': specialElementHandlers
    },

    function (dispose) {
        pdf.save('Test.pdf');
    }
	);
}
</script>
<?php echo $footer; ?>