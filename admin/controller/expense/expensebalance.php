<?php
	//require_once(DIR_SYSTEM .'/library/mpdf/mpdf.php');
	class ControllerExpenseExpensebalance extends Controller {
		public function index($return_orders = array()) {
			$url = '';
			
			$this->load->model('purchase/return_orders');
			$this->document->setTitle('Expense Balace');
			$data['heading_title'] = 'Expense Balace';
			$data['text_list'] = 'Expense Balace';
			
			
			
			$data['button_filter'] = $this->language->get('button_filter');
			$data['button_clear'] = $this->language->get('button_clear');
			
			$data['token'] = $this->session->data['token'];
			
		
                        $this->load->model('expense/expensebalance');
                        $this->load->model('setting/store');
                        $data["units"] = $this->model_expense_expensebalance->getAllUnits();
                        $data["store"] = $this->model_expense_expensebalance->getStores();
                        
                        if ($this->request->server['REQUEST_METHOD'] == 'POST')
                        {
			//print_r($this->request->post);exit;
                        $this->load->model('expense/expensebalance');
                        $data['logged_user_data'] = $this->user->getId();
                        $this->model_expense_expensebalance->insrtexpensebalancedtl($this->request->post,$data['logged_user_data']);


                        $this->session->data['success'] = 'Expense balance done successfully';
                        $this->response->redirect($this->url->link('expense/expensebalance/expenselist', 'token=' . $this->session->data['token'] , 'SSL'));
                        }
                        
                        
                     
			
			$url ='';
			
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => 'Expense balance',
				'href' => $this->url->link('expense/expensebalance', 'token=' . $this->session->data['token'] . $url, true)
			);
			$data['cancel']=$this->url->link('expense/expensebalance/expenselist', 'token=' . $this->session->data['token'], 'SSL');
			$data['header'] = $this->load->controller('common/header');
			
			
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
				$this->response->setOutput($this->load->view('expense/expensedtl.tpl', $data));
			
		}
        
		public function expenselist()
{
$this->load->language('report/product_purchased');

$this->document->setTitle("Expense Balance List");

$this->load->model('expense/expensebalance');


$this->getList();
}
protected function getList() {

if (isset($this->request->get['filter_date_start'])) {
$filter_date_start = $this->request->get['filter_date_start'];
} else {
$data['filter_date_start']=$filter_date_start = date('Y-m').'-01';
}

if (isset($this->request->get['filter_date_end'])) {
$data['filter_date_end']=$filter_date_end = $this->request->get['filter_date_end'];
} else {
$filter_date_end = date('Y-m-d');
}

if (isset($this->request->get['filter_stores_id'])) {
$filter_stores_id = $this->request->get['filter_stores_id'];
} else {
$filter_stores_id = 0;
}

$data['filter_date_start']=$filter_date_start;
$data['filter_date_end']=$filter_date_end;
$data['filter_stores_id']=$filter_stores_id;

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

if (isset($this->request->get['filter_stores_id'])) {
$url .= '&filter_stores_id=' . $this->request->get['filter_stores_id'];
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
'text' => $this->language->get('heading_title'),
'href' => $this->url->link('expense/expensebalance/expenselist', 'token=' . $this->session->data['token'], 'SSL')
);
$data['cancel']=$this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
$this->load->model('expense/expensebalance');
$data["order_stores"] = $this->model_expense_expensebalance->getStores();
$data['expense'] = array();

$filter_data = array(
'filter_date_start' => $filter_date_start,
'filter_date_end' => $filter_date_end,
'filter_stores_id' => $filter_stores_id,
'start' => ($page - 1) * $this->config->get('config_limit_admin'),
'limit' => $this->config->get('config_limit_admin')
);

$product_total = $this->model_expense_expensebalance->getTotalexpenseList($filter_data);

$results = $this->model_expense_expensebalance->getexpenseList($filter_data);

foreach ($results as $result) { //print_r($result);
$data['expense'][] = array(
'store' => $result['store'],
'amount' => $result['amount'],
'totalamount' => $result['expense_balance'],
'create_date' => $result['create_date'],
'payment_method' => $result['payment_method'],
'transaction_no' => $result['transaction_no'],
'name' => $result['name'],
'firstname' => $result['firstname'],
'lastname' => $result['lastname']

);
}

$data['heading_title'] = $this->language->get('heading_title');

$data['text_list'] = $this->language->get('text_list');
$data['text_no_results'] = $this->language->get('text_no_results');


$data['column_name'] = $this->language->get('column_name');
$data['column_model'] = $this->language->get('column_model');
$data['column_quantity'] = $this->language->get('column_quantity');
$data['column_total'] = $this->language->get('column_total');

$data['entry_date_start'] = $this->language->get('entry_date_start');
$data['entry_date_end'] = $this->language->get('entry_date_end');
$data['entry_status'] = $this->language->get('entry_status');


if (isset($this->error['warning'])) {
$data['error_warning'] = $this->error['warning'];
} else {
$data['error_warning'] = '';
}

if (isset($this->session->data['success'])) {
$data['success'] = $this->session->data['success'];

unset($this->session->data['success']);
} else {
$data['success'] = '';
}

$data['button_filter'] = $this->language->get('button_filter');

$data['token'] = $this->session->data['token'];



if (isset($this->request->get['filter_date_start'])) {
$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
}

if (isset($this->request->get['filter_date_end'])) {
$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
}

if (isset($this->request->get['filter_stores_id'])) {
$url .= '&filter_stores_id=' . $this->request->get['filter_stores_id'];
}

$pagination = new Pagination();
$pagination->total = $product_total;
$pagination->page = $page;
$pagination->limit = $this->config->get('config_limit_admin');
$pagination->url = $this->url->link('report/store_lazer', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

$data['pagination'] = $pagination->render();
$data['filter_stores_id']=$filter_stores_id;
$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

$data['filter_date_start'] = $filter_date_start;
$data['filter_date_end'] = $filter_date_end;
$data['filter_order_status_id'] = $filter_order_status_id;

$data['header'] = $this->load->controller('common/header');
$data['column_left'] = $this->load->controller('common/column_left');
$data['footer'] = $this->load->controller('common/footer');

$this->response->setOutput($this->load->view('expense/expenselist.tpl', $data));
}


public function download_excel() {
if (isset($this->request->get['filter_date_start'])) {
$filter_date_start = $this->request->get['filter_date_start'];
} else {
$data['filter_date_start']=$filter_date_start = date('Y-m').'-01';
}

if (isset($this->request->get['filter_date_end'])) {
$data['filter_date_end']=$filter_date_end = $this->request->get['filter_date_end'];
} else {
$filter_date_end = date('Y-m-d');
}

if (isset($this->request->get['filter_stores_id'])) {
$filter_stores_id = $this->request->get['filter_stores_id'];
} else {
$filter_stores_id = 0;
}

$data['filter_date_start']=$filter_date_start;
$data['filter_date_end']=$filter_date_end;
$data['filter_stores_id']=$filter_stores_id;

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

if (isset($this->request->get['filter_stores_id'])) {
$url .= '&filter_stores_id=' . $this->request->get['filter_stores_id'];
}

if (isset($this->request->get['page'])) {
$url .= '&page=' . $this->request->get['page'];
}

		

		$filter_data = array(
'filter_date_start' => $filter_date_start,
'filter_date_end' => $filter_date_end,
'filter_stores_id' => $filter_stores_id,

);


        $this->load->model('expense/expensebalance');
        
		$data['orders'] = array();

		
        $results = $this->model_expense_expensebalance->getexpenseList($filter_data);

                
		                
    include_once '../system/library/PHPExcel.php';
    include_once '../system/library/PHPExcel/IOFactory.php';
    $objPHPExcel = new PHPExcel();
    
    $objPHPExcel->createSheet();
    
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");

    $objPHPExcel->setActiveSheetIndex(0);

    // Field names in the first row
    $fields = array(
        'Store Name',
        
        'Amount',
        'Total Amount',
        'Transaction No',
        'Payment Method',
        'Create Date'
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
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $data['store']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data['expense_balance']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data['amount']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data['transaction_no']); 
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data['payment_method']); 
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data['create_date']); 
          
             
   
        $row++;
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // Sending headers to force the user to download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Expense_Balance_'.date('dMy').'.xls"');
    header('Cache-Control: max-age=0');

    $objWriter->save('php://output');
        
    }
		
	}
?>