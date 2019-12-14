<?php
require_once(DIR_SYSTEM .'/library/mail/class.phpmailer.php');
require_once(DIR_SYSTEM . 'library/mail/class.smtp.php');  
error_reporting(0);
ini_set('max_execution_time', 30000);  //3000 seconds = 50 minutes

class ControllerReportbcmlRevenueAssurance extends Controller {
	public function index() {
		$this->load->language('report/reconciliation');

		$this->document->setTitle('Revenue Assurance');

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d');
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}


		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		if (isset($this->request->get['filter_unit'])) {
			$url .= '&filter_unit=' . $this->request->get['filter_unit'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Revenue Assurance',
			'href' => $this->url->link('reportbcml/revenue_assurance', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/reconciliation');
                		 $this->load->model('setting/store');

		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => $filter_store,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
                        'filter_company' => '2',
			'filter_unit'	     => $filter_unit,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
		$t1=$this->model_report_reconciliation->getTotalOrdersCompanywise($filter_data);
		$order_total = $t1["total"];
		$total_tagged_amount_all=$t1["total_tagged_amount"];
		$total_cash_amount_all=$t1["total_cash_amount"];
		$total_tagged_amount=0;
		$results = $this->model_report_reconciliation->getOrdersCompanywise($filter_data);

		foreach ($results as $result) {
                        		$total_tagged_amount=$total_tagged_amount+$result['tagged'];
								$total_cash_amount=$total_tagged_amount+$result['cash'];
                        $grower_info = $result['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                        $farmer_info=explode('-', $grower_info);
      
                $grower_id=@$farmer_info[0];
                $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                $father_name=ucwords(strtolower(@$farmer_info[2]));
	if(empty($grower_id))
	{
	$grower_id=$result['shipping_firstname'];
	}
	if(empty($farmer_name))
	{
	$farmer_name=$result['o_payment_address_1'];
	}
	  $inv_no=$result['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));
                if(empty($result['company']))
	 {
		$unit_name=$result['unit_name'];
	 }
	else
	{
		$unit_name=$result['company'];
	}
			$data['orders'][] = array(
				
				'date'       => date($this->language->get('date_format_short'), strtotime($result['date'])),
		  'order_id'   => $inv_no ,
			'inv_no'   => $result['order_id'],
                                'store_name' => $result['store_name'],
                                'store_id'   => $result['store_id'],
                                'total'      => $result['total'],
                                'tagged'     => $result['tagged'],
                                'grower_id'  => $grower_id,
                                'farmer_name'=> $farmer_name,
                                'unit'       => $unit_name
				
			);
		}
		//$data['orders']=usort($data['orders'], "cmp");
		$data['heading_title'] = 'Revenue Assurance';
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_products'] = $this->language->get('column_products');
		$data['column_tax'] = $this->language->get('column_tax');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/order_status');
                $data['stores'] = $this->model_setting_store->getStoresCompanyWise('2');
		
		$url = '';
                if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
                if (isset($this->request->get['filter_store'])) {
			$url .= '&filter_store=' . $this->request->get['filter_store'];
		}
		if (isset($this->request->get['filter_unit'])) {
			$url .= '&filter_unit=' . $this->request->get['filter_unit'];
		}


		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('reportbcml/revenue_assurance', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
                $data['filter_store'] = $filter_store;
		$data['filter_unit'] = $filter_unit;
		$data['total_tagged_amount'] =$total_tagged_amount;
		$data['total_cash_amount']=$total_cash_amount;
		$data['total_tagged_amount_All'] =$total_tagged_amount_all;
		$data['total_cash_amount_All'] =$total_cash_amount_All;
	
		$this->load->model('unit/unit');
		$data['units']=$this->model_unit_unit->getunit(array('filter_company'=>'2'));
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		if (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}
		
		//print_r($data['units']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('reportbcml/revenue_assurance.tpl', $data));
	}
function cmp($a, $b)
{
    if ($a["order_id"] == $b["order_id"]) {
        return 0;
    }
    return ($a["order_id"] < $b["order_id"]) ? -1 : 1;
}

        public function download_pdf() { 
            $this->load->language('report/reconciliation');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}
		$this->load->model('report/reconciliation');
                 $this->load->model('setting/store');

		$data['orders'] = array();

		$filter_data = array(
                                          'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
                        'filter_company' => '2',
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end
		);
                $data['store']="";
                $data['unit']="";
		$order_total = $this->model_report_reconciliation->getTotalOrdersCompanywise($filter_data);

		$results = $this->model_report_reconciliation->getOrdersCompanywise($filter_data);

		foreach ($results as $result) {
                        /*
                        $grower_info = $this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                        $farmer_info=explode('-', $grower_info);
      
                $grower_id=@$farmer_info[0];
                $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                $father_name=ucwords(strtolower(@$farmer_info[2]));
	$inv_no=ucwords(strtolower(@$farmer_info[3]));
*/
                $grower_info = $result['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                $farmer_info=explode('-', $grower_info);
      
                $grower_id=@$farmer_info[0];
                $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                $father_name=ucwords(strtolower(@$farmer_info[2]));
	  $inv_no=$result['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));
			$data['orders'][] = array(
				
				'date'       => date($this->language->get('date_format_short'), strtotime($result['date'])),
				'order_id'   => $inv_no ,
			'inv_no'   => $result['order_id'],
                                'store_name' => $result['store_name'],
                                'store_id'   => $result['store_id'],
                                'total'      => $result['total'],
                                'tagged'     => $result['tagged'],
                                'grower_id'  => $grower_id,
                                'farmer_name'=> $farmer_name,
                                'unit'       => $result['company']
				
			);
                        $data['store']=$result['store_name'];
                        $data['unit']=$result['company'];
		}
		if($filter_store==0)
                {
                    
                  $data['store']='';
                  $data['unit']='';  
                } 
		$data['start_date'] = date($this->language->get('date_format_short'), strtotime($filter_date_start));
                $data['end_date']  = date($this->language->get('date_format_short'), strtotime($filter_date_end));
		
                require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
                
                $html = $this->load->view('report/reconciliation_pdf.tpl',$data);
                //$this->response->setOutput($this->load->view('tag/order_invoice.tpl', $data));
               
                $base_url = HTTP_CATALOG;
                
                //$mpdf = new mPDF($mode, $format, $font_size, $font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);
                $mpdf = new mPDF('c', 'A4', '', '', '5', '5', '25', '25', '5', '1', '');
                $header = '<div class="header" style="">
                   
<div class="logo" style="width: 100%;" >
<img src="../image/letterhead_text.png" style="height: 40px; width: 121px;" />
<img src="../image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />

                         </div>
<img src="../image/letterhead_topline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 

</div>';
    //$header='';
                $mpdf->SetHTMLHeader($header, 'O', false);
                    
                $footer = '<div class="footer">
                        
                        <img src="../image/letterhead_bottomline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 
                        <div class="address"><img src="../image/letterhead_footertext.png" style="height: 10px; margin-left: -40px;width: 120% !important;" /> </div>'
                        . '</div>';

                //$footer = '<div class="footer"><div class="address"><b>Akshamaala Solutions Pvt. Ltd. : </b>'.$data['company_address'].'</div><div class="pageno">{PAGENO}</div></div>';
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($html);
/*$mpdf->AddPage();

                $mpdf->WriteHTML('<div>
<br/><br/>
<b>Our Bank Details</b>
<br/><br/>
A/C Number - 03292020000923
<br/>
Bank Name - HDFC Bank/ Current Account
<br/>
IFSC Code - HDFC0000329
<br/><br/>
Checked and Submitted by : 


</div>
        ');
                */
                $filename='Reconciliation Report'.$order_id.'.pdf';
                //$mpdf->Output(DIR_UPLOAD.$filename,'F');
                $mpdf->Output($filename,'D');
                
                
		$this->response->setOutput($this->load->view('report/reconciliation_pdf.tpl', $data));
        }
        public function download_excel() {
            
               if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
                           if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}

		

		$this->load->model('report/reconciliation');
                

		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
                     'filter_company' => '2',
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end
		);

		//$order_total = $this->model_report_reconciliation->getTotalOrdersCompanywise($filter_data);

		$results = $this->model_report_reconciliation->getOrdersCompanywise($filter_data);

    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        'Unit',
        'Store Name',
        'Store ID',
        'Order ID',
        'Inv no.',
        'Grower ID',
        'Name',
        'Date',
        'Amount',
        'Tagged',
		'Cash'
    );
   
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
    
    $row = 2;
    
    foreach($results as $data)
    { 
        
        $col = 0;
		$grower_info = $data['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
        $farmer_info=explode('-', $grower_info);
      
                $grower_id=@$farmer_info[0];
                $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                $father_name=ucwords(strtolower(@$farmer_info[2]));
	if(empty($grower_id))
	{
	$grower_id=$data['shipping_firstname'];
	}
	if(empty($farmer_name))
	{
	$farmer_name=$data['o_payment_address_1'];
	}
	  $inv_no=$data['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));
     if(empty($data['company']))
	 {
		$unit_name=$data['unit_name'];
	 }
	else
	{
		$unit_name=$data['company'];
	}
				
	  

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, @$unit_name );
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['store_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['store_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row,  $inv_no);
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['order_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $grower_id);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $farmer_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, date('Y-m-d',strtotime($data['date'])));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, number_format((float)$data['total'], 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, number_format((float)$data['tagged'], 2, '.', ''));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, number_format((float)$data['cash'], 2, '.', ''));  
        $row++;
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="reconciliation_report_'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    
    }

//download item

public function download_item_excel() {
            
               if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}

		

		$this->load->model('report/reconciliation');
                

		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => $filter_store,
                        'filter_unit'	     => $filter_unit,
                        'filter_company' => '2',
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end
		);

		$order_total = $this->model_report_reconciliation->getTotalOrdersCompanywise($filter_data);

		$results = $this->model_report_reconciliation->getOrdersCompanywise($filter_data);
    //exit;
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        'Unit',
        'Store Name',
        'Store ID',
        'Order ID',
	'Invoice_No',
        'Grower ID',
        'Name',
        'Date',
	'Product Name',
	'Quantity',
	'Price',
	'Tax',
	'Total (Cash+Tagged)'
    );
   
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
    
    $row = 2;
    
    foreach($results as $data)
    { 
        
        $col = 0;
   /*     
         $grower_info = $this->model_report_reconciliation->getGrowerDetails($data['order_id']);
         $farmer_info=explode('-', $grower_info);
         $grower_id=@$farmer_info[0];
         $farmer_name=ucwords(strtolower(@$farmer_info[1]));
         $father_name=ucwords(strtolower(@$farmer_info[2]));
		$inv_nos=ucwords(strtolower(@$farmer_info[3]));
*/
 $grower_info = $data['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                $farmer_info=explode('-', $grower_info);
      
                $grower_id=@$farmer_info[0];
                $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                $father_name=ucwords(strtolower(@$farmer_info[2]));
	  $inv_nos=$data['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));

	//get product details row		                
	$orderinfos=$this->model_report_reconciliation->getOrder_detail($data['order_id']);
	foreach($orderinfos as $orderinfo){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, @$data['company'] );
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['store_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['store_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $inv_nos);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['order_id']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $grower_id);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $farmer_name);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, date('Y-m-d',strtotime($data['date'])));
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, number_format((float)$data['total'], 2, '.', ''));
        //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, number_format((float)$data['tagged'], 2, '.', ''));           
	//new row           
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $orderinfo['name']);           
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $orderinfo['quantity']);           
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $orderinfo['price']); 
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $orderinfo['tax']);           
	$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $row,number_format((float)($orderinfo['total']+($orderinfo['tax']*$orderinfo['quantity'])),2,'.',''));           
          

        $row++;
	}

    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="reconciliation_report_'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
    
    }




     public function email_excel() {
        
        

				
		$this->load->model('report/sale');
                
		$data['orders'] = array();

		$filter_data = array(
                        'filter_store'	     => '',
			'filter_date_start'	     => date('Y-m-d'),
			'filter_date_end'	     => date('Y-m-d'),
			
			'filter_order_status_id' => '5'
		);

		

		$results = $this->model_report_sale->getOrders($filter_data);

		

        
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        'Date Start',
        'Date End',
        'No. Orders',
        'Store',
        'Total'
    );
   
    $col = 0;
    foreach ($fields as $field)
    {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
        $col++;
    }
     
    // Fetching the table data
    //$this->load->model('report/searchattendance');
    //$results = $this->model_report_searchattendance->getmdoattendance($filter_data);
    
    $row = 2;
    
    foreach($results as $data)
    { 
        $col = 0;
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, date('Y-m-d',strtotime($data['date_start'])));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, date('Y-m-d',strtotime($data['date_end'])));
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['orders']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['store_name']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['total']);            
        

        $row++;
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    
    // Sending headers to force the user to download the file
    //header('Content-Type: application/vnd.ms-excel');
    //header('Content-Disposition: attachment;filename="tagged_report_'.date('dMy').'.xls"');
    //header('Cache-Control: max-age=0');

    //$objWriter->save('php://output');
    $filename='sale_order_report_'.date('ymdhis').'.xls';
    $objWriter->save(DIR_UPLOAD.$filename );
    //
    $mail             = new PHPMailer();

                $body = "<p>Akshamaala Solution Pvt. Ltd.</p>";
                
                $mail->IsSMTP();
                $mail->Host       = "mail.akshamaala.in";
                                                           
                $mail->SMTPAuth   = false;                 
                $mail->SMTPSecure = "";                 
                $mail->Host       = "mail.akshamaala.in";      
                $mail->Port       = 25;                  
                $mail->Username   = "mis@akshamaala.in";  
                $mail->Password   = "mismis";            
		//$mail->From = "mail.akshamaala.in";
		//$mail->FromName = "Support Team";
                $mail->SetFrom('mail.akshamaala.in', 'Akshamaala');

                $mail->AddReplyTo('mail.akshamaala.in','Akshamaala');

                $mail->Subject    = "Sale orders Report";

                $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

                $mail->MsgHTML($body);
                
                //to get the email of supplier
                
                $mail->AddAddress('chetan.singh@akshamaala.com', "Chetan Singh");
                //$mail->AddCC('ashok.prasad@akshamaala.com', "Ashok Prasad");
                

                $mail->AddAttachment(DIR_UPLOAD.$filename);
                
                if(!$mail->Send())
                {
                  echo "Mailer Error: " . $mail->ErrorInfo;
                }
                else
                {
                  if(!unlink(DIR_UPLOAD.$filename))
                  {
                      echo ("Error deleting ");
                  }
                  else
                  {
                     echo ("Deleted ");
                  }
                }
    }
///////////create bill start here//////////////
public function create_bill() { 
            $this->load->language('report/reconciliation');

		echo 'here';
		$log=new Log("create_bill_bcml-".date('Y-m-d').".log"); 
		if (isset($this->request->post['filter_date'])) {
			$filter_date = $this->request->post['filter_date'];
		} else {
			$filter_date= date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		

		if (isset($this->request->post['filter_store_2'])) {
			$filter_store = $this->request->post['filter_store_2'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->post['filter_unit_2'])) {
			$filter_unit = $this->request->post['filter_unit_2'];
		} else {
			$filter_unit = 0;
		}
		$this->load->model('report/reconciliation');
        $this->load->model('setting/store');
		$this->load->model('pos/bcml');
						
		$data['orders'] = array();

		$filter_data = array(
            'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
			'filter_date'	     => $filter_date,
			'start'				 =>0,
			'limit'				 =>1000
		);
		$log->write($filter_data);
        $file_a_array=$this->model_report_reconciliation->get_file_data_tagged($filter_data);
		$log->write($file_a_array);
        $file_in_database=""; 
		$file_server= "";
		if(count($file_a_array)>0)
		{
			$file_in_database="yes"; 
			$path = "../system/upload/tagged_pdf";
                                          $files = scandir($path);
                                          $file_server="";
                                          foreach ($files as &$value) 
                                          {
                                            
                                            if(trim($file_a_array[0]["file_name"])==trim($value))
                                           { 
                                               $file_server= "yes";
		
                                             }
                 
                                          }
			
		}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$isatbcmldone="";
			    if(($file_in_database=="yes") && ($file_server=="yes"))
			    {
					$log->write('file_in_database==yes && file_server==yes');
					
					$file_url = DIR_UPLOAD.'tagged_pdf/'.$file_a_array[0]["file_name"];
                                                                      header('Content-Type: application/pdf');
                                                                      header("Content-Transfer-Encoding: Binary");
                                                                      header("Content-disposition: attachment; filename=".$file_a_array[0]["file_name"]);
                                                                      readfile($file_url);
				              header('location: '.$_SERVER['HTTP_REFERER']);
				             exit;
			}
			else ///if file is already generated for same filter////////  
			{
                           $data['store']="";
                           $data['unit']="";
                
				 //echo $file_server;
                 if(($file_a_array[0]["file_name"]!="") && ($file_server!="yes"))//////means data in database but file is not available at server////////////
                 {
                    $filename=$file_a_array[0]["file_name"];
					$data["file_aspl"]=$file_a_array[0]["sid"];
					$log->write('means data in database but file is not available at server');
                 } 
				else
				{ 
					$log->write('in else');
					$filename='tagged_order_'.$filter_date.'_'.$filter_store.'_'.$filter_unit.'.pdf';  
					$data["file_aspl"]=$this->model_report_reconciliation->add_file_data_tagged($filter_data,$filename);//'1065';//
					$log->write($data["file_aspl"]);
					$resultsb = $this->model_report_reconciliation->getOrdersBcml($filter_data);
					
					$this->load->model('user/user');
					$this->user->getId();
					$user_info = $this->model_user_user->getUser($this->user->getId());
					$user_firstname = $user_info['firstname'];
					$user_lastname = $user_info['lastname'];
					
					$arrays = array_chunk($resultsb, 10);
					//print_r($arrays);
					foreach ($arrays as $array_num => $array) 
					{ 
								$data['DebitNoteDetail']=array();
								
								foreach ($array as $item_num => $resultb) 
								{	
									
								$inv_nob=$resultb['requisition_id'];
								$data['DebitNoteDetail'][] = array(
								'debitnoteno'=> $data["file_aspl"],
								'indentno'   => $inv_nob ,
                                'invoiceno'   => $resultb['order_id'],
                                'invoicedate'       => date('Ymd', strtotime($resultb['date'])),
                                'username'       => $user_firstname.' '.$user_lastname,
								'taggedvalue'=>number_format((float)$resultb['tagged'], 2, '.', '')
                                );
								
								}
								$DebitNoteDetail=json_encode($data['DebitNoteDetail']);
								$dbdata=array();
								$dbdata['DebitNoteDetail']=$DebitNoteDetail;
								$dbdata['unitid']=$filter_unit;
								
								$resultbcbcl=1;//$this->model_pos_bcml->CreateDebitNote('CreateDebitNote', $dbdata);
								$log->write($resultbcbcl);
								if($resultbcbcl==1)
								{
									$this->model_report_reconciliation->update_bcml_upload($data['DebitNoteDetail']);
									$isatbcmldone='yes';									
								}
								else
								{
									$isatbcmldone='no';
								}
								//exit;
					}
					//print_r($resultsb);
					//exit;
					//echo $isatbcmldone;
					$log->write($isatbcmldone);
					if($isatbcmldone=='no')
					{
								$update_total=$this->model_report_reconciliation->delete_file_data_tagged($data["file_aspl"]);
								$this->session->data['error']='Oops! Some error at server';
								header('Location: '.$_SERVER['HTTP_REFERER']);
								exit;
					}
					
						
						
						
				}
	   
	   
				//echo count($results);
				//exit;
				$results = $this->model_report_reconciliation->getOrders($filter_data);
                $total_amount=0;
				foreach ($results as $result) 
				{
                      
					$grower_info = $result['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                    $farmer_info=explode('-', $grower_info);
      
                    $grower_id=@$farmer_info[0];
                    $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                    $father_name=ucwords(strtolower(@$farmer_info[2]));
                    if(empty($grower_id))
                    {
                        $grower_id=$result['shipping_firstname'];
                    }
                    if(empty($farmer_name))
                    {
                        $farmer_name=$result['o_payment_address_1'];
                    }
                    $inv_no=$result['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));
                    if(empty($result['company']))
                    {
                           $unit_name=$result['unit_name'];
                    }
							else
							{
                            $unit_name=$result['company'];
							}
							$data['orders'][] = array(
								'date'       => date($this->language->get('date_format_short'), strtotime($result['date'])),
                                'order_id'   => $inv_no ,
                                'inv_no'   => $result['order_id'],
                                'store_name' => $result['store_name'],
                                'store_id'   => $result['store_id'],
                                'total'      => number_format((float)$result['total'], 2, '.', ''),//$result['total'],
                                'tagged'     => number_format((float)$result['tagged'], 2, '.', ''),//$result['tagged'],
                                'grower_id'  => $grower_id,
                                'farmer_name'=> $farmer_name,
                                'unit'       => $unit_name,
                                'selected'  =>$selected
				
							);
                        $total_amount=$total_amount+$result['tagged'];
                        $data['store']=$result['store_name'];
                        $data['unit']=$result['company'];
				}
				if($filter_store==0)
                {
                    
                  $data['store']='';
                  $data['unit']='';  
                } 
				$data['start_date'] = date($this->language->get('date_format_short'), strtotime($filter_date));
                $data['end_date']  = date($this->language->get('date_format_short'), strtotime($filter_date));
		
                require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
                
                $html = $this->load->view('report/reconciliation_pdf.tpl',$data);
                
               
                $base_url = HTTP_CATALOG;
                
                
                $mpdf = new mPDF('c', 'A4', '', '', '5', '5', '25', '25', '5', '1', '');
                $header = '<div class="header" style="">
                   
<div class="logo" style="width: 100%;" >
<img src="../image/letterhead_text.png" style="height: 40px; width: 121px;" />
<img src="../image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />

                         </div>
<img src="../image/letterhead_topline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 

</div>';
   
                $mpdf->SetHTMLHeader($header, 'O', false);
                    
                $footer = '<div class="footer">
                        
                        <img src="../image/letterhead_bottomline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 
                        <div class="address"><img src="../image/letterhead_footertext.png" style="height: 10px; margin-left: -40px;width: 120% !important;" /> </div>'
                        . '</div>';

                
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($html);


                //$filename='tagged_order_'.$filter_date.'_'.$filter_store.'_'.$filter_unit.'.pdf';    
	  //$this->model_report_reconciliation->add_file_data_tagged($filter_data,$filename); 
               // $mpdf->Output(DIR_UPLOAD.'tagged_pdf/'.$filename,'F');  
	   $this->load->model('user/user'); 
	  $logged_user = $this->user->getId();
	  $update_total=$this->model_report_reconciliation->update_file_data_tagged($filter_data,$data["file_aspl"],$total_amount,$logged_user); 
                $mpdf->Output($filename,'D');
	  header('location: '.$_SERVER['HTTP_REFERER']);
	  echo "File created successfully"; 
           }
        }

/////////////create bill end here//////////////////

////////////////get bill start here/////////////////////////
public function get_bill()
{
                            if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}
		$this->load->model('report/reconciliation');
                            $this->load->model('setting/store');

		$data['orders'] = array();

		$filter_data = array(
                                          'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end
		);
                           $file_a_array=$this->model_report_reconciliation->get_file_data_tagged_within($filter_data);
		if(count($file_a_array)>0)
	              {
                              echo "1";
		}
		else
		{
		 echo "0";
		}

}
///////////////get bill end here///////////////
///////////////////////////create tag bill pdf in one click start here/////////////////////////////
    public function create_bill_one_click() {
		$this->load->language('tag/order');

		$data['title'] = $this->language->get('text_invoice');
		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}

		if (isset($this->request->get['filter_store'])) {
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                        $filter_data = array(
                                          'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,
			
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$data['text_invoice'] = $this->language->get('text_invoice');
		$data['text_order_detail'] = $this->language->get('text_order_detail');
		$data['text_order_id'] = $this->language->get('text_order_id');
		$data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_website'] = $this->language->get('text_website');
		$data['text_to'] = $this->language->get('text_to');
		$data['text_ship_to'] = $this->language->get('text_ship_to');
		$data['text_payment_method'] = $this->language->get('text_payment_method');
		$data['text_shipping_method'] = $this->language->get('text_shipping_method');

		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_comment'] = $this->language->get('column_comment');

		$this->load->model('tag/order');

		$this->load->model('setting/setting');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
                $mcrypt=new MCrypt();    
               
                require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
				$this->load->model('report/reconciliation');
				//$file_a_array=$this->model_report_reconciliation->get_file_data_tagged($filter_data);
				$order_id_array=$this->model_report_reconciliation->getOrder_ids_tagged($filter_data);

				
               
                $data['orders'] = array();
                foreach ($order_id_array as $order_id)
                {
                        
                   $order_info = $this->model_tag_order->getOrder($order_id); 

                if ($order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);

				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_tag_order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_tag_order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

							if ($upload_info) {
								$value = $upload_info['name'];
							} else {
								$value = '';
							}
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}
                                
                                $voucher_data = array();

				$vouchers = $this->model_tag_order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data = array();

				$totals = $this->model_tag_order->getOrderTotals($order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}
                       ///////////products from order_product_leads end here///////////////
                                $bill_id = $this->model_tag_order->getReqID($order_id);
                                $product_data_2 = array();
                         		 	$products_2 = $this->model_tag_order->getOrderProducts_2($bill_id);

				foreach ($products_2 as $product_2) {
					$option_data_2 = array();

					$options_2 = $this->model_tag_order->getOrderOptions_2($bill_id, $product_2['order_product_id']);

					foreach ($options_2 as $option_2) {
						if ($option_2['type'] != 'file') {
							$value_2 = $option_2['value'];
						} else {
							$upload_info_2 = $this->model_tool_upload->getUploadByCode($option_2['value']);

							if ($upload_info_2) {
								$value_2 = $upload_info_2['name'];
							} else {
								$value_2 = '';
							}
						}

						$option_data_2[] = array(
							'name_2'  => $option_2['name'],
							'value_2' => $value_2
						);
					}

					$product_data_2[] = array(
						'name_2'     => $product_2['name'],
						'model_2'    => $product_2['model'],
						'option_2'   => $option_data_2,
						'quantity_2' => $product_2['quantity'],
						'price_2'    => $this->currency->format($product_2['price'] + ($this->config->get('config_tax') ? $product_2['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total_2'    => $this->currency->format($product_2['total'] + ($this->config->get('config_tax') ? ($product_2['tax'] * $product_2['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}
                                
                                $voucher_data_2 = array();

				$vouchers_2 = $this->model_tag_order->getOrderVouchers_2($bill_id);

				foreach ($vouchers_2 as $voucher_2) {
					$voucher_data_2[] = array(
						'description_2' => $voucher_2['description'],
						'amount_2'      => $this->currency->format($voucher_2['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$total_data_2 = array();

				$totals_2 = $this->model_tag_order->getOrderTotals_2($bill_id);

				foreach ($totals_2 as $total_2) {
					$total_data_2[] = array(
						'title_2' => $total_2['title'],
						'text_2'  => $this->currency->format($total_2['value'], $order_info['currency_code'], $order_info['currency_value']),
					);
				}
                                
                                ///////////products from order_product end here///////////////
	           $data["farmer_info_string"]=$order_info["payment_address_1"];
                
                         $farmer_info=explode('-', $data["farmer_info_string"]);
               
	        $file_first_part=$bill_id."_".$farmer_info[0];
	        $path = "../system/upload";
                $files = scandir($path);
                $profile_pic="";
                foreach ($files as &$value) 
                {
                    $file_array=explode(".",$value);
                    $file_name=$mcrypt->decrypt($file_array[0]);
                    
                    if(trim($file_name)==trim($file_first_part))
                    { 
                     $profile_pic= $value;
		
                    }
                 
                }
             if($profile_pic=="")
	     {
	       foreach ($files as &$value) 
            {
                    $file_array=explode(".",$value);
                    $file_name=$mcrypt->decrypt($file_array[0]);
                   
	             $file_bill_id=explode("_",trim($file_name));
	            //print_r($file_array);echo $file_name.",".$file_bill_id[0]."<br/>"; 
                    if(trim($file_bill_id[0])==trim($bill_id))
                    { 
                     $profile_pic= $value;
		
                    }
               }
	      }  
                $oc_order_array=$this->model_tag_order->getOrderDetailsFromOrder($bill_id);
                $bank_num_1=explode('-',$oc_order_array["shipping_address_2"]); 
               
 	            $bank_num_2=explode('-',$oc_order_array["shipping_city"]); 
                
                // print_r($farmer_info);
				$data['orders'][] = array(
					'order_id'	         => $order_id,
					'invoice_no'         => $bill_id,
					'date_added'         => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'         => $order_info['store_name'],
					'store_url'          => rtrim($order_info['store_url'], '/'),
					'store_address'      => nl2br($store_address),
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'email'              => $order_info['email'],
					'telephone'          => $order_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'payment_method'     => $order_info['payment_method'],
					'product'            => $product_data,
                                                                     'voucher'            => $voucher_data,
					'total'              => $total_data,
                                                                      'product_2'          => $product_data_2,
                                                                      'voucher_2'          => $voucher_data_2,
					'total_2'            => $total_data_2,
					'comment'            => nl2br($order_info['comment']),
					'grower_id'          => $farmer_info[0],
					'farmer_name'        => ucwords(strtolower($farmer_info[1])),
					'father_name'        => ucwords(strtolower($farmer_info[2])),
					'village_name'       => ucwords(strtolower($order_info["shipping_firstname"])),
                                                                      'bank_id_number'     => $bank_num_1[1],
                                                                     'identity_type'       => $bank_num_2[0],
                                                                     'identity_number'    => $bank_num_2[1],
					'profile_pic'        => $profile_pic
				);
			}


		
                  
                }
$html = $this->load->view('tag/order_invoice_all_pdf.tpl',$data);
//$this->response->setOutput($this->load->view('tag/order_invoice_all_pdf.tpl', $data));    
//print_r($data['orders']); 
                //  exit; 
                $base_url = HTTP_CATALOG;
                
                $mpdf = new mPDF('c','A4','','' , 5 , 5 , 25 , 10 , 5 , 7);
                //$mpdf = new mPDF($mode, $format, $font_size, $font, $margin_left, $margin_right, $margin_top, $margin_bottom, $margin_header, $margin_footer, $orientation);
                $header = '<div style="max-height: 1000px;min-height: 1000px;"><div class="header">
		<div class="logo">
		<img style="float: right;margin-right: 40px;height: 30px;" src="'.$base_url.'image/catalog/logo.png"  />
		</div>
 		
		<img src="../image/letterhead_topline.png" style="height: 10px; margin-left: -16px;width: 120%;" /> 
	        </div>';
    
                $mpdf->SetHTMLHeader($header, 'O', false);
                    
                $footer = '<div class="footer">
                        
                        <img src="../image/letterhead_bottomline.png" style="height: 10px; margin-left: -40px;width: 150% !important;" /> 
                        <div class="address"><img src="../image/letterhead_footertext.png" style="margin-left: -40px;width: 120% !important;" /> </div>'
                        . '</div>';

                       	 //$footer = '<div class="footer"><div class="address"><b>Akshamaala Solutions Pvt. Ltd. : </b>'.$data['company_address'].'</div><div class="pageno">{PAGENO}</div></div></div>';
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
    
                $mpdf->WriteHTML($html);
                
                $filename='tagged_order_'.$filter_date_start.'_'.$filter_date_end.'_'.$filter_store.'_'.$filter_unit.'.pdf';    
	  //$this->model_report_reconciliation->add_file_data_tagged($filter_data,$filename); 
                $mpdf->Output(DIR_UPLOAD.'tagged_pdf/'.$filename,'F');
	  
                //$mpdf->Output($filename,'D');
	  //$this->response->setOutput($this->load->view('tag/order_invoice.tpl', $data));
	  echo "File created successfully"; 
				
				
	}



///////////////////////////////////create tag bill in one click end here//////////////////////     
public function get_store_unit()
{
$store_id=$this->request->get['store_id'];
if($store_id=="20")
{
 echo "<option value='' selected='selected'>SELECT UNIT</option><option value='03'>HARIYAWAN</option><option value='04' >LONI</option>";
}
else
{
$this->load->model('report/reconciliation');
$get_data=$this->model_report_reconciliation->get_store_unit($store_id);
//print_r($get_data[0]);
echo "<option value='' >SELECT UNIT</option><option value='".$get_data[0]['unit_id']."' selected='selected'>".$get_data[0]["unit_name"]."</option>";
}

}
public function getdebitnotedetail() {
		$this->load->language('report/reconciliation');

		$this->document->setTitle('Get Debit Note Detail');

		if (isset($this->request->get['filter_letter_number'])) {
			$filter_letter_number = $this->request->get['filter_letter_number'];
		} else {
			$filter_letter_number = '';
		}
		if (isset($this->request->get['filter_unit'])) {
			$filter_unit = $this->request->get['filter_unit'];
		} else {
			$filter_unit = '';
		}
		

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_letter_number'])) {
			$url .= '&filter_letter_number=' . $this->request->get['filter_letter_number'];
		}
		if (isset($this->request->get['filter_unit'])) {
			$url .= '&filter_unit=' . $this->request->get['filter_unit'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => 'Get Debit Note Detail',
			'href' => $this->url->link('reportbcml/reconciliation/getdebitnotedetail', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$this->load->model('report/reconciliation');
		$this->load->model('pos/bcml');
        	$this->load->model('setting/store');

		$data['orders'] = array();

		$filter_data = array(
            		'DebitNoteNo'	     => $filter_letter_number,
			'unitid'	     => $filter_unit,
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);
		//$t1=$this->model_report_reconciliation->getTotalOrdersCompanywise($filter_data);
		$order_total = 0;//$t1["total"];
		//$total_tagged_amount_all=$t1["total_tagged_amount"];
		//$total_tagged_amount=0;
			
		if(!empty($filter_letter_number))
		{
			$results = $this->model_pos_bcml->GetDebitNoteDetail('GetDebitNoteDetail',$filter_data);
		}
		
		foreach ($results as $result) {
                        		
			$data['orders'][] = array(
				
								'InvoiceDate'       => $result['InvoiceDate'],
								
								'DebitNoteNo'   => $result['DebitNoteNo'],
                                'IndentNo' => $result['IndentNo'],
                                'InvoiceNo'   => $result['InvoiceNo'],
                                'TaggedValue'      => $result['TaggedValue'],
								'DebitNoteStatus'=>$result['DebitNoteStatus'],
								'UserName'=>$result['UserName']
				
			);
		}
		/*
		$data['DebitNoteDetail'][] = array(
								'debitnoteno'=> $data["file_aspl"],
								'indentno'   => $inv_nob ,
                                'invoiceno'   => $resultb['order_id'],
                                'invoicedate'       => date('Ymd', strtotime($resultb['date'])),
                                'username'       => $user_firstname.' '.$user_lastname,
								'taggedvalue'=>number_format((float)$resultb['tagged'], 2, '.', '')
                                );
		*/
		$data['heading_title'] = 'Get Debit Note Detail';
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_orders'] = $this->language->get('column_orders');
		$data['column_products'] = $this->language->get('column_products');
		$data['column_tax'] = $this->language->get('column_tax');
		$data['column_total'] = $this->language->get('column_total');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		

		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/order_status');
                $data['stores'] = $this->model_setting_store->getStoresCompanyWise('2');
		
		$url = '';
        if (isset($this->request->get['filter_letter_number'])) {
			$url .= '&filter_letter_number=' . $this->request->get['filter_letter_number'];
		}
		if (isset($this->request->get['filter_unit'])) {
			$url .= '&filter_unit=' . $this->request->get['filter_unit'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('reportbcml/reconciliation', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['filter_letter_number'] = $filter_letter_number;
		
		$data['filter_unit'] = $filter_unit;
		$data['total_tagged_amount'] =$total_tagged_amount;
		$data['total_tagged_amount_All'] =$total_tagged_amount_all;

		$this->load->model('unit/unit');
		$data['units']=$this->model_unit_unit->getunit(array('filter_company'=>'2'));
		//print_r($data['units']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('reportbcml/reconciliation_get_payment_indent.tpl', $data));
	}

	///////////create bill duplicate start here//////////////
public function create_bill_duplicate() { 
            $this->load->language('report/reconciliation'); 

		

		if (isset($this->request->post['filter_date'])) {
			$filter_date = $this->request->post['filter_date'];
		} else {
			$filter_date= date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		

		if (isset($this->request->post['filter_store_2'])) {
			$filter_store = $this->request->post['filter_store_2'];
		} else {
			$filter_store = 0;
		}
		if (isset($this->request->post['filter_unit_2'])) {
			$filter_unit = $this->request->post['filter_unit_2'];
		} else {
			$filter_unit = 0;
		}
		$this->load->model('report/reconciliation');
        $this->load->model('setting/store');
		$this->load->model('pos/bcml');
						
		$data['orders'] = array();

		$filter_data = array(
            'filter_store'	     => $filter_store,
			'filter_unit'	     => $filter_unit,
			'filter_date'	     => $filter_date
		);

        $file_a_array=$this->model_report_reconciliation->get_file_data_tagged($filter_data);
        $file_in_database=""; 
		$file_server= "";
		if(count($file_a_array)>0)
		{
			$file_in_database="yes"; 
			$path = "../system/upload/tagged_pdf";
                                          $files = scandir($path);
                                          $file_server="";
                                          foreach ($files as &$value) 
                                          {
                                            
                                            if(trim($file_a_array[0]["file_name"])==trim($value))
                                           { 
                                               $file_server= "yes";
		
                                             }
                 
                                          }
			
		}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$isatbcmldone="";
			    if(($file_in_database=="yes1") && ($file_server=="yes1"))
			    {
					
					$file_url = DIR_UPLOAD.'tagged_pdf/'.$file_a_array[0]["file_name"];
                                                                      header('Content-Type: application/pdf');
                                                                      header("Content-Transfer-Encoding: Binary");
                                                                      header("Content-disposition: attachment; filename=".$file_a_array[0]["file_name"]);
                                                                      readfile($file_url);
				              header('location: '.$_SERVER['HTTP_REFERER']);
				             exit;
			}
			else ///if file is already generated for same filter////////  
			{
                           $data['store']="";
                           $data['unit']="";
                
				 //echo $file_server;
                 if(($file_a_array[0]["file_name"]!="") && ($file_server!="yes"))//////means data in database but file is not available at server////////////
                 {
                    //echo $filename=$file_a_array[0]["file_name"];exit;
					//$data["file_aspl"]=$file_a_array[0]["sid"];
                 } 
				//else
				{ 
					$filename='tagged_order_'.$filter_date.'_'.$filter_store.'_'.$filter_unit.'.pdf';  
					$data["file_aspl"]='1296'; //$this->model_report_reconciliation->add_file_data_tagged($filter_data,$filename);//'1065';// 
					
					$resultsb = $this->model_report_reconciliation->getOrdersBcml($filter_data);
					//print_r($resultsb);

					//exit;
					$this->load->model('user/user');
					$this->user->getId();
					$user_info = $this->model_user_user->getUser($this->user->getId());
					$user_firstname = $user_info['firstname'];
					$user_lastname = $user_info['lastname'];
					
					$arrays = array_chunk($resultsb, 10);
					//print_r($arrays);
					foreach ($arrays as $array_num => $array) 
					{ 
								$data['DebitNoteDetail']=array();
								
								foreach ($array as $item_num => $resultb) 
								{	
									
								$inv_nob=$resultb['requisition_id'];
								$data['DebitNoteDetail'][] = array(
								'debitnoteno'=> $data["file_aspl"],
								'indentno'   => $inv_nob ,
                                'invoiceno'   => $resultb['order_id'],
                                'invoicedate'       => date('Ymd', strtotime($resultb['date'])),
                                'username'       => $user_firstname.' '.$user_lastname,
								'taggedvalue'=>number_format((float)$resultb['tagged'], 2, '.', '')
                                );
								
								}
								$DebitNoteDetail=json_encode($data['DebitNoteDetail']);
								$dbdata=array();
								$dbdata['DebitNoteDetail']=$DebitNoteDetail;
								$dbdata['unitid']=$filter_unit;
								
								$resultbcbcl=$this->model_pos_bcml->CreateDebitNote('CreateDebitNote', $dbdata);
								if($resultbcbcl==1)
								{
									$this->model_report_reconciliation->update_bcml_upload($data['DebitNoteDetail']);
									$isatbcmldone='yes';									
								}
								else
								{
									$isatbcmldone='no';
								}
								//exit;
					}
					//echo $isatbcmldone;
					if($isatbcmldone=='no')
					{
								$update_total=$this->model_report_reconciliation->delete_file_data_tagged($data["file_aspl"]);
								header('Location: '.$_SERVER['HTTP_REFERER']);
								exit;
					}
					
						
						
						
				}
	   
	   
				//echo count($results);
				//exit;
				$results = $this->model_report_reconciliation->getOrders($filter_data);
                $total_amount=0;
				foreach ($results as $result) 
				{
                      
					$grower_info = $result['payment_address_1'];//$this->model_report_reconciliation->getGrowerDetails($result['order_id']);
                    $farmer_info=explode('-', $grower_info);
      
                    $grower_id=@$farmer_info[0];
                    $farmer_name=ucwords(strtolower(@$farmer_info[1]));
                    $father_name=ucwords(strtolower(@$farmer_info[2]));
                    if(empty($grower_id))
                    {
                        $grower_id=$result['shipping_firstname'];
                    }
                    if(empty($farmer_name))
                    {
                        $farmer_name=$result['o_payment_address_1'];
                    }
                    $inv_no=$result['requisition_id'];//ucwords(strtolower(@$farmer_info[3]));
                    if(empty($result['company']))
                    {
                           $unit_name=$result['unit_name'];
                    }
							else
							{
                            $unit_name=$result['company'];
							}
							$data['orders'][] = array(
								'date'       => date($this->language->get('date_format_short'), strtotime($result['date'])),
                                'order_id'   => $inv_no ,
                                'inv_no'   => $result['order_id'],
                                'store_name' => $result['store_name'],
                                'store_id'   => $result['store_id'],
                                'total'      => number_format((float)$result['total'], 2, '.', ''),//$result['total'],
                                'tagged'     => number_format((float)$result['tagged'], 2, '.', ''),//$result['tagged'],
                                'grower_id'  => $grower_id,
                                'farmer_name'=> $farmer_name,
                                'unit'       => $unit_name,
                                'selected'  =>$selected
				
							);
                        $total_amount=$total_amount+$result['tagged'];
                        $data['store']=$result['store_name'];
                        $data['unit']=$result['company'];
				}
				if($filter_store==0)
                {
                    
                  $data['store']='';
                  $data['unit']='';  
                } 
				$data['start_date'] = date($this->language->get('date_format_short'), strtotime($filter_date));
                $data['end_date']  = date($this->language->get('date_format_short'), strtotime($filter_date));
		
                require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
                
                $html = $this->load->view('report/reconciliation_pdf.tpl',$data);
                
               
                $base_url = HTTP_CATALOG;
                
                
                $mpdf = new mPDF('c', 'A4', '', '', '5', '5', '25', '25', '5', '1', '');
                $header = '<div class="header" style="">
                   
<div class="logo" style="width: 100%;" >
<img src="../image/letterhead_text.png" style="height: 40px; width: 121px;" />
<img src="../image/letterhead_log.png" style="height: 55px; width: 121px;float: right;" />

                         </div>
<img src="../image/letterhead_topline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 

</div>';
   
                $mpdf->SetHTMLHeader($header, 'O', false);
                    
                $footer = '<div class="footer">
                        
                        <img src="../image/letterhead_bottomline.png" style="height: 10px; margin-left: -46px;width: 120% !important;" /> 
                        <div class="address"><img src="../image/letterhead_footertext.png" style="height: 10px; margin-left: -40px;width: 120% !important;" /> </div>'
                        . '</div>';

                
                $mpdf->SetHTMLFooter($footer);
                    
                $mpdf->SetDisplayMode('fullpage');
    
                $mpdf->list_indent_first_level = 0;
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->setAutoBottomMargin = 'stretch';
                $mpdf->WriteHTML($html);


                //$filename='tagged_order_'.$filter_date.'_'.$filter_store.'_'.$filter_unit.'.pdf';    
	  //$this->model_report_reconciliation->add_file_data_tagged($filter_data,$filename); 
               // $mpdf->Output(DIR_UPLOAD.'tagged_pdf/'.$filename,'F');  
	   $this->load->model('user/user'); 
	  $logged_user = $this->user->getId();
	  $update_total=$this->model_report_reconciliation->update_file_data_tagged($filter_data,$data["file_aspl"],$total_amount,$logged_user); 
                $mpdf->Output($filename,'D');
	  header('location: '.$_SERVER['HTTP_REFERER']);
	  echo "File created successfully"; 
           }
        }

/////////////create bill duplicate end here//////////////////

}