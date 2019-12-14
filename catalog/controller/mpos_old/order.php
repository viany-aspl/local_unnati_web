<?php
class ControllermposOrder extends Controller {
    public function adminmodel($model) {
      
      $admin_dir = DIR_SYSTEM;
      $admin_dir = str_replace('system/','admin/',$admin_dir);
      $file = $admin_dir . 'model/' . $model . '.php';      
      //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
      $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
      
      if (file_exists($file)) {
         include_once($file);
         
         $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
      } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
      }
   }

	public function addOrder() {
		
$log=new Log("order.log");
$log->write($this->request->post);

		 $mcrypt=new MCrypt();
		$keys = array(
			'store_id',
			'payment_method',
			'customer_id',
			'affiliate_id',
			'user_id',
			'prddtl'
		);

foreach ($keys as $key) {
            

                $this->request->post[$key] =$mcrypt->decrypt($this->request->post[$key]) ;
            
        }

$prds=json_decode($this->request->post[prddtl],true);

$this->session->data['user_id']="1";//$this->request->post['user_id'] ;
//add data to cart
foreach($prds as $prd)
{
$log->write($prd['product_id']);
$log->write($this->addToCart($prd['product_id']));
}

$log->write("after product submit");
		unset($this->session->data['shipping_method']);
                 
                $data = array();
                
                //validation 
                $errors = '';
                
                $payment_method = $this->request->post['payment_method'];
                $is_guest = $this->request->post['is_guest'];
                $customer_id ="1"; //$this->request->post['customer_id'];
                $card_no = $this->request->post['card_no'];
                $data['comment'] = $this->request->post['comment'];
                
                if($is_guest=='false' && $customer_id==''){
                    $errors .= 'Select the customer.<br />';
                }
                
                if(($payment_method  == 'Card') && $card_no==''){
                    $errors .= 'Enter the card number.<br />';
                }
                
                if($errors != ''){                   
                    $data['errors'] = $errors;
                    $this->response->setOutput(json_encode($data));
                    return;
                }

                                    $this-> adminmodel('pos/pos');

                
		$data['store_id'] = "2";//$this->request->post['store_id'];
		
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
                
                $data['shipping_country_id'] = $default_country_id;
		$data['shipping_zone_id'] = $default_zone_id;
		$data['payment_country_id'] = $default_country_id;
		$data['payment_zone_id'] = $default_zone_id;
		$data['customer_id'] = 0;
		$data['customer_group_id'] = 1;
		$data['firstname'] = 'Walkin';
		$data['lastname'] = "Customer";
		$data['email'] = '';
		$data['telephone'] = '';
		$data['fax'] = '';
                
		$data['payment_firstname'] = 'Walkin';
		$data['payment_lastname'] = "Customer";
		$data['payment_company'] = '';
		$data['payment_company_id'] = '';
		$data['payment_tax_id'] = '';
		$data['payment_address_1'] = '';
		$data['payment_address_2'] = '';
		$data['payment_city'] = '';
		$data['payment_postcode'] = '';
		$data['payment_country_id'] = '';
		$data['payment_zone_id'] = '';
		$data['payment_method'] = $payment_method;
		$data['payment_code'] = 'in_store';
		$data['shipping_firstname'] = '';
		$data['shipping_lastname'] = '';
		$data['shipping_company'] = '';
		$data['shipping_address_1'] = '';
		$data['shipping_address_2'] = '';
		$data['shipping_city'] = '';
		$data['shipping_postcode'] = '';
		$data['shipping_country_id'] = '';
		$data['shipping_zone_id'] = '';
		$data['shipping_method'] = 'Pickup From Store';
		$data['shipping_code'] = 'pickup.pickup';		
		$data['order_status_id'] = 5;
		$data['affiliate_id'] = isset( $this->request->post['affiliate_id'])? $this->request->post['affiliate_id']:0;
                $data['card_no'] = $card_no;
		$data['user_id'] = $this->user->getId();
                $log->write("user id");
                $log->write($customer_id);
		$is_guest='false';
                //override for customer 
                if($is_guest=='false'){
                    
                $log->write("false");
                    $customer = $this->model_pos_pos->getCustomer($customer_id);
                    
                    $data['customer_id'] = $customer_id;
                    $data['customer_group_id'] = $customer['customer_group_id'];
                    $data['firstname'] = $customer['firstname'];
                    $data['lastname'] = $customer['lastname'];
                    $data['email'] = $customer['email'];
                    $data['telephone'] = $customer['telephone'];
                    $data['fax'] = $customer['fax'];

                    $data['payment_firstname'] = $customer['firstname'];
                    $data['payment_lastname'] = $customer['lastname'];
                }				
                
                //get product list 
                $this->load->library('customer');
                $this->customer = new Customer($this->registry);
                
                $this->load->library('tax');//
                $this->tax = new Tax($this->registry);
            
                $this->load->library('pos_cart');//
                $this->cart = new Pos_cart($this->registry);
                
                $data['order_product'] = array();
                
                foreach ($this->cart->getProducts() as $product) {
			
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];	
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);

					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}				

				$option_data[] = array(								   
					'product_option_id'  => $option['product_option_id'],
                                        'product_option_value_id'  => $option['product_option_value_id'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
					'type'  => $option['type'],
                                        'name'  => $option['name'],
				);
			}

			$data['order_product'][] = array(
                                'product_id'   => $product['product_id'],
				'name'         => $product['name'],
				'model'        => $product['model'], 
				'quantity'     => $product['quantity'],                            
				'price'        => $product['price'],
				'total'        => $product['price']*$product['quantity'],
                                'tax'          => $this->tax->getTax($product['price'], $product['tax_class_id']),
                                'reward'       => $product['reward'],
				'order_option' => $option_data,
			);
		}//foreach products 
                
	
	                                    $this-> adminmodel('pos/extension');



                $total_data = array();					
                $total = 0;
                $taxes = $this->cart->getTaxes();
$log->write("near");
                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                        $sort_order = array(); 

                        $results = $this->model_pos_extension->getExtensions('total');

                        foreach ($results as $key => $value) {
                                $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                        }

                        array_multisort($sort_order, SORT_ASC, $results);

                        foreach ($results as $result) {
                                if ($this->config->get($result['code'] . '_status')) {

	                                    $this-> adminmodel('pos/' . $result['code']);

                                        $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                                }

                                $sort_order = array(); 

                                foreach ($total_data as $key => $value) {
                                        $sort_order[$key] = $value['sort_order'];
                                }

                                array_multisort($sort_order, SORT_ASC, $total_data);			
                        }
                }
$log->write("near1");
                $data['order_total'] = $total_data;
                
                if(isset($this->session->data['voucher'])){
                    $data['order_voucher'] = $this->session->data['voucher'];
                }
                
                //end of order total 
                $json['customer_name'] = $data['firstname'].' '.$data['lastname'];
$log->write($json);
$log->write("near2");

$this->load->model('checkout/order');
	
//				$order_id= $this->model_checkout_order->addOrder($order_data);



                $order_id = $this->model_pos_pos->addOrder($data);
                $log->write("near3");
                unset($this->session->data['discount_amount']);
                
                //recore for counter payment 
                if($payment_method  == 'Card'){                    
                    $cash = 0;
                    $card = $total;
                }else{
                    $cash = $total;
                    $card = 0;
                }
                
                $data = array(
                 'user_id' => $this->user->getId(),
                 'cash' => $cash,
                 'card' => $card,                 
                );
                
                $this->model_pos_pos->addPayment($data);
                
                $json['order_id'] = $order_id;
                $log->write($order_id);
                $balance = $this->model_pos_pos->get_user_balance($this->user->getId());
                $json['cash'] = $this->currency->format($balance['cash']);
                $json['card'] = $this->currency->format($balance['card']);

// Set the order history
				if (isset($this->request->post['order_status_id'])) {
					$order_status_id = $this->request->post['order_status_id'];
				} else {
					$order_status_id = $this->config->get('config_order_status_id');
				}
	
				$this->model_checkout_order->addOrderHistory($json['order_id'], $order_status_id);
	

 
                $json['success'] = 'Success: new order placed with ID: '.$order_id;
                
                $this->response->setOutput(json_encode($json));	
	}//END add order 






	public function edit() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				// Customer
				if (!isset($this->session->data['customer'])) {
					$json['error'] = $this->language->get('error_customer');
				}

				// Payment Address
				if (!isset($this->session->data['payment_address'])) {
					$json['error'] = $this->language->get('error_payment_address');
				}

				// Payment Method
				if (!isset($this->session->data['payment_method'])) {
					$json['error'] = $this->language->get('error_payment_method');
				}

				// Shipping
				if ($this->cart->hasShipping()) {
					// Shipping Address
					if (!isset($this->session->data['shipping_address'])) {
						$json['error'] = $this->language->get('error_shipping_address');
					}

					// Shipping Method
					if (!isset($this->request->post['shipping_method'])) {
						$json['error'] = $this->language->get('error_shipping_method');
					}
				} else {
					unset($this->session->data['shipping_address']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				// Cart
				if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
					$json['error'] = $this->language->get('error_stock');
				}

				// Validate minimum quantity requirements.
				$products = $this->cart->getProducts();

				foreach ($products as $product) {
					$product_total = 0;

					foreach ($products as $product_2) {
						if ($product_2['product_id'] == $product['product_id']) {
							$product_total += $product_2['quantity'];
						}
					}

					if ($product['minimum'] > $product_total) {
						$json['error'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);

						break;
					}
				}

				if (!$json) {
					$order_data = array();

					// Store Details
					$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
					$order_data['store_id'] = $this->config->get('config_store_id');
					$order_data['store_name'] = $this->config->get('config_name');
					$order_data['store_url'] = $this->config->get('config_url');

					// Customer Details
					$order_data['customer_id'] = $this->session->data['customer']['customer_id'];
					$order_data['customer_group_id'] = $this->session->data['customer']['customer_group_id'];
					$order_data['firstname'] = $this->session->data['customer']['firstname'];
					$order_data['lastname'] = $this->session->data['customer']['lastname'];
					$order_data['email'] = $this->session->data['customer']['email'];
					$order_data['telephone'] = $this->session->data['customer']['telephone'];
					$order_data['fax'] = $this->session->data['customer']['fax'];
					$order_data['custom_field'] = $this->session->data['customer']['custom_field'];

					// Payment Details
					$order_data['payment_firstname'] = $this->session->data['payment_address']['firstname'];
					$order_data['payment_lastname'] = $this->session->data['payment_address']['lastname'];
					$order_data['payment_company'] = $this->session->data['payment_address']['company'];
					$order_data['payment_address_1'] = $this->session->data['payment_address']['address_1'];
					$order_data['payment_address_2'] = $this->session->data['payment_address']['address_2'];
					$order_data['payment_city'] = $this->session->data['payment_address']['city'];
					$order_data['payment_postcode'] = $this->session->data['payment_address']['postcode'];
					$order_data['payment_zone'] = $this->session->data['payment_address']['zone'];
					$order_data['payment_zone_id'] = $this->session->data['payment_address']['zone_id'];
					$order_data['payment_country'] = $this->session->data['payment_address']['country'];
					$order_data['payment_country_id'] = $this->session->data['payment_address']['country_id'];
					$order_data['payment_address_format'] = $this->session->data['payment_address']['address_format'];
					$order_data['payment_custom_field'] = $this->session->data['payment_address']['custom_field'];

					if (isset($this->session->data['payment_method']['title'])) {
						$order_data['payment_method'] = $this->session->data['payment_method']['title'];
					} else {
						$order_data['payment_method'] = '';
					}

					if (isset($this->session->data['payment_method']['code'])) {
						$order_data['payment_code'] = $this->session->data['payment_method']['code'];
					} else {
						$order_data['payment_code'] = '';
					}

					// Shipping Details
					if ($this->cart->hasShipping()) {
						$order_data['shipping_firstname'] = $this->session->data['shipping_address']['firstname'];
						$order_data['shipping_lastname'] = $this->session->data['shipping_address']['lastname'];
						$order_data['shipping_company'] = $this->session->data['shipping_address']['company'];
						$order_data['shipping_address_1'] = $this->session->data['shipping_address']['address_1'];
						$order_data['shipping_address_2'] = $this->session->data['shipping_address']['address_2'];
						$order_data['shipping_city'] = $this->session->data['shipping_address']['city'];
						$order_data['shipping_postcode'] = $this->session->data['shipping_address']['postcode'];
						$order_data['shipping_zone'] = $this->session->data['shipping_address']['zone'];
						$order_data['shipping_zone_id'] = $this->session->data['shipping_address']['zone_id'];
						$order_data['shipping_country'] = $this->session->data['shipping_address']['country'];
						$order_data['shipping_country_id'] = $this->session->data['shipping_address']['country_id'];
						$order_data['shipping_address_format'] = $this->session->data['shipping_address']['address_format'];
						$order_data['shipping_custom_field'] = $this->session->data['shipping_address']['custom_field'];

						if (isset($this->session->data['shipping_method']['title'])) {
							$order_data['shipping_method'] = $this->session->data['shipping_method']['title'];
						} else {
							$order_data['shipping_method'] = '';
						}

						if (isset($this->session->data['shipping_method']['code'])) {
							$order_data['shipping_code'] = $this->session->data['shipping_method']['code'];
						} else {
							$order_data['shipping_code'] = '';
						}
					} else {
						$order_data['shipping_firstname'] = '';
						$order_data['shipping_lastname'] = '';
						$order_data['shipping_company'] = '';
						$order_data['shipping_address_1'] = '';
						$order_data['shipping_address_2'] = '';
						$order_data['shipping_city'] = '';
						$order_data['shipping_postcode'] = '';
						$order_data['shipping_zone'] = '';
						$order_data['shipping_zone_id'] = '';
						$order_data['shipping_country'] = '';
						$order_data['shipping_country_id'] = '';
						$order_data['shipping_address_format'] = '';
						$order_data['shipping_custom_field'] = array();
						$order_data['shipping_method'] = '';
						$order_data['shipping_code'] = '';
					}

					// Products
					$order_data['products'] = array();

					foreach ($this->cart->getProducts() as $product) {
						$option_data = array();

						foreach ($product['option'] as $option) {
							$option_data[] = array(
								'product_option_id'       => $option['product_option_id'],
								'product_option_value_id' => $option['product_option_value_id'],
								'option_id'               => $option['option_id'],
								'option_value_id'         => $option['option_value_id'],
								'name'                    => $option['name'],
								'value'                   => $option['value'],
								'type'                    => $option['type']
							);
						}

						$order_data['products'][] = array(
							'product_id' => $product['product_id'],
							'name'       => $product['name'],
							'model'      => $product['model'],
							'option'     => $option_data,
							'download'   => $product['download'],
							'quantity'   => $product['quantity'],
							'subtract'   => $product['subtract'],
							'price'      => $product['price'],
							'total'      => $product['total'],
							'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
							'reward'     => $product['reward']
						);
					}

					// Gift Voucher
					$order_data['vouchers'] = array();

					if (!empty($this->session->data['vouchers'])) {
						foreach ($this->session->data['vouchers'] as $voucher) {
							$order_data['vouchers'][] = array(
								'description'      => $voucher['description'],
								'code'             => substr(md5(mt_rand()), 0, 10),
								'to_name'          => $voucher['to_name'],
								'to_email'         => $voucher['to_email'],
								'from_name'        => $voucher['from_name'],
								'from_email'       => $voucher['from_email'],
								'voucher_theme_id' => $voucher['voucher_theme_id'],
								'message'          => $voucher['message'],
								'amount'           => $voucher['amount']
							);
						}
					}

					// Order Totals
					$this->load->model('extension/extension');

					$order_data['totals'] = array();
					$total = 0;
					$taxes = $this->cart->getTaxes();

					$sort_order = array();

					$results = $this->model_extension_extension->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get($result['code'] . '_status')) {
							$this->load->model('total/' . $result['code']);

							$this->{'model_total_' . $result['code']}->getTotal($order_data['totals'], $total, $taxes);
						}
					}

					$sort_order = array();

					foreach ($order_data['totals'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $order_data['totals']);

					if (isset($this->request->post['comment'])) {
						$order_data['comment'] = $this->request->post['comment'];
					} else {
						$order_data['comment'] = '';
					}

					$order_data['total'] = $total;

					if (isset($this->request->post['affiliate_id'])) {
						$subtotal = $this->cart->getSubTotal();

						// Affiliate
						$this->load->model('affiliate/affiliate');

						$affiliate_info = $this->model_affiliate_affiliate->getAffiliate($this->request->post['affiliate_id']);

						if ($affiliate_info) {
							$order_data['affiliate_id'] = $affiliate_info['affiliate_id'];
							$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
						} else {
							$order_data['affiliate_id'] = 0;
							$order_data['commission'] = 0;
						}
					} else {
						$order_data['affiliate_id'] = 0;
						$order_data['commission'] = 0;
					}

					$this->model_checkout_order->editOrder($order_id, $order_data);

					// Set the order history
					if (isset($this->request->post['order_status_id'])) {
						$order_status_id = $this->request->post['order_status_id'];
					} else {
						$order_status_id = $this->config->get('config_order_status_id');
					}

					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);

					$json['success'] = $this->language->get('text_success');
				}
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->deleteOrder($order_id);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function history() {
		$this->load->language('api/order');

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'append',
				'comment'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('checkout/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);

			if ($order_info) {
				$this->model_checkout_order->addOrderHistory($order_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify']);

				$json['success'] = $this->language->get('text_success');
			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

//cart
	public function addToCart($pid) {
               
$log=new Log("order.log");

$this->request->post['product_id']=$pid;
$log->write($this->request->post);
		$json = array();
                
		$this->load->library('user');
                $this->user = new User($this->registry);

                $this->load->library('customer');
                $this->customer = new Customer($this->registry);
                
                $this->load->library('tax');//
                $this->tax = new Tax($this->registry);
            
                $this->load->library('pos_cart');//
                $this->cart = new Pos_cart($this->registry);
                
		$this->load->model('catalog/product');
                
                if (isset($this->request->post['product_id'])) {
                    $product_id = $this->request->post['product_id'];
                } else {
                    $product_id = 0;
                }
                        
		$product_info = $this->model_catalog_product->getProduct($product_id);
$log->write($product_info);

		if ($product_info) {			
			if (isset($this->request->post['quantity'])) {
				$quantity = $this->request->post['quantity'];
			} else {
				$quantity = 1;
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();	
			}

			$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf('%s field required', $product_option['name']);
				}
			}

			if (!$json) {
				$this->cart->add($this->request->post['product_id'], $quantity, $option);

				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));
$log->write($json);
				// Totals
				$this->adminmodel('pos/extension');
				$total_data = array();					
				$total = 0;
$log->write($total);
				$log->write($this->session->data);
				$taxes = $this->cart->getTaxes();

$log->write($taxes);
				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$sort_order = array(); 
					$results = $this->model_pos_extension->getExtensions('total');
					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
					}
					array_multisort($sort_order, SORT_ASC, $results);
					foreach ($results as $result) {
						if ($this->config->get($result['code'] . '_status')) {
							$this->adminmodel('pos/' . $result['code']);

							$this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
						}
						$sort_order = array(); 
						foreach ($total_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}
						array_multisort($sort_order, SORT_ASC, $total_data);			
					}
				}
                                
                                $json['total_data'] = $total_data;
				$json['total'] = $this->currency->format($total);
			} 
		}
$log->write($json);

return 		$json;

}




}