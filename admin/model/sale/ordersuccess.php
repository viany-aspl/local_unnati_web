<?php
class ModelSaleOrdersuccess extends Model 
{

	public function completeOrder($order_id,$logged_user) 
	{
		$log=new Log("order-success-".date('Y-m-d').".log");
		$log->write('order_id - '.$order_id);
		$log->write('logged_user - '.$logged_user);
		
		$order_info=$this->getOrder($order_id);
		$product_info=$this->getOrderProducts($order_id);
		$storeINChargeName=$this->getNamebyID($order_info['user_id']);
		$LoggedUserName=$this->getNamebyID($logged_user);
		$store_id=$order_info['store_id'];
		
		if(!empty($order_info['comment']))
		{
			$new_comment=$order_info['comment'].'-0000';
		}
		else
		{
			$new_comment='';
		}
		
		$log->write('order_status_id - '.$order_info['order_status_id']);
		if($order_info['order_status_id']==1)
		{
		$this->load->library('trans');
        $trans=new trans($this->registry);
		
		$log->write('payment_method - '.$order_info['payment_method']);
		
		if(($order_info['payment_method']=='Cash') || ($order_info['payment_method']=='Tagged Cash') || ($order_info['payment_method']=='Subsidy Cash'))
		{
			
			$log->write('payment_method is Cash orTagged Cash or Subsidy Cash ');
			
			$sql1="update oc_user set `cash`=cash+".$order_info['cash']." where `user_id`='".$order_info['user_id']."' ";
			$log->write($sql1);
			$userquery = $this->db->query($sql1);
			try
			{ 		
				$trans->addstoretrans($order_info['cash'],$order_info['store_id'],$order_info['user_id'],'CR',$order_id,$order_info['payment_method'],$order_info['total'],'ORDER SUCCESS BY WEB FROM USER -'.$LoggedUserName);  
                                 
            } 
			catch (Exception $e)
			{
                $log->write($e->getMessage());
            }
			
		}
		$sql2="update oc_order set `order_status_id`=5 where `order_id`='".$order_id."' and order_status_id=1 ";  
		$query = $this->db->query($sql2);
		$log->write($sql2);
		
		
		
		/////product/////////
		foreach($product_info as $product)
		{
			$product_id=$product['product_id'];
			$quantity=$product['quantity'];
			
			$sql3="select * from oc_product_trans where `order_id`='".$order_id."' and `product_id`='".$product_id."' ";
			$query3 = $this->db->query($sql3);
			$log->write($sql3); 
			$log->write($query3->num_rows); 			
			if ($query3->num_rows==0) //////if there is no entry in table means invenotry not decrease till now so we need to deduct the inventory
			{
				$sql4="update oc_product_to_store set `quantity`=quantity-".$quantity." where `product_id`='".$product_id."' and `store_id`='".$store_id."' ";
				$query = $this->db->query($sql4);
				$log->write($sql4);
			
				$trans->addproducttrans($store_id,$product_id,$quantity,$order_id,'DB','SALE','web');   
			}
			
		}
		///////check entry in oc_requisition_to_bill ////////
		if(($order_info['payment_method']=='Tagged') || ($order_info['payment_method']=='Tagged Cash') || ($order_info['payment_method']=='Tagged Subsidy'))
		{
			if(!empty($order_id))
			{
				$sql5="select * from oc_requisition_to_bill where `bill_id`='".$order_id."' ";
				$query5 = $this->db->query($sql5);
				$log->write($sql5);  
				$log->write($query5->num_rows); 
				if ($query5->num_rows==0) //////if there is no entry in table then add an entry to table
				{	
					try
					{ 		  
						$sql6="insert into  oc_requisition_to_bill set `bill_id`='".$order_id."',requisition_id='".$order_info['comment']."' ";
						$query = $this->db->query($sql6);
						$log->write($sql6);                     
					} 
					catch (Exception $e)
					{
						$log->write($e->getMessage());
					}
				}
				
                        }
                }
		$cancel_ip=$this->get_client_ip();
		
		$sql7="insert into oc_order_success_trans set `order_id`='".$order_id."',`success_by`='".$logged_user."',`success_ip`='".$cancel_ip."',`date_time`='".date('Y-m-d h:i:s')."',`order_info`='".serialize($order_info)."',`product_info`='".serialize($product_info)."',`remarks`='".$remarks."',`store_id`='".$store_id."' ";
		
		$query = $this->db->query($sql7); 
		$log->write($sql7);
		return $order_id;
		}
		else
		{
			$log->write('order_status_id - '.$order_info['order_status_id']." so we don't complete this order");
			return '0';
		}
		
	}
	public function getOrder($order_id) 
	{
		
		
		$order_query = $this->db->query("SELECT o.*, 
		(SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer,
		concat(oc_user.firstname,' ',oc_user.lastname) as op_name
		FROM `" . DB_PREFIX . "order` o left join oc_user on oc_user.user_id=o.user_id WHERE o.order_id = '" . (int)$order_id . "'");
		$company_status=5;
		
		if ($order_query->num_rows) 
		{ 
			if((($order_query->row['payment_method']=='Tagged') || ($order_query->row['payment_method']=='Tagged Cash')) && ($order_query->row['order_status_id']==1))
			{ 
				$company_status=0;
				////////check from factory server//////////
				$store_id=$order_query->row['store_id'];
				$indent_number=$order_query->row['comment'];
				$this->load->model('pos/pos');
				$companydata=$this->model_pos_pos->getunitidandcompanyid(array('storeid'=>$store_id)); 
				
				$company_id=$companydata[0]['company_id'];
				$unit_id=$companydata[0]['unit_id'];
				
				/////////////////////////////////////
				if($companydata[0]['company_id']==1)////////check at DSCL
				{ 
					$this->load->model('pos/dscl');
					$send_data['sql']=' select order_status_id from oc_order_req_delivery where order_id='.$order_id;
					$return_data=$this->model_pos_dscl->GetCardDataSql('GetQueryColumnName', $send_data,true); 
					if($return_data[0]['ORDER_STATUS_ID']=='1')
					{
						$company_status=$return_data[0]['ORDER_STATUS_ID'];
						return '1';
					}
					if($return_data[0]['ORDER_STATUS_ID']=='5')
					{
						$company_status=$return_data[0]['ORDER_STATUS_ID'];
					}
					
				}
			
				///////////////////////////////////////////////////////
				if($companydata[0]['company_id']==2)////////check at BCML
				{ 
					$this->load->model('pos/bcml');
					$datatosend=array('unitid'=>$unit_id,'invoiceno'=>$order_id,'storeid'=>$store_id);	
					$databybcml=$this->model_pos_bcml->GetIndentByInvoiceNo('GetIndentByInvoiceNo', $datatosend);
					
					
					if(!empty($databybcml))
					{
						if(!empty($databybcml[0]['IndentNo']))
						{
							$orderdata['OrderID']=$databybcml[0]['InvoiceNo'];
							$orderdata['IndentNo']=$databybcml[0]['IndentNo'];
							if(($orderdata['OrderID']==$order_id) && ($orderdata['IndentNo']==$indent_number))
							{
								$company_status=5;
							}
							else
							{
								$company_status=0;
								return '1';
							}
						}
						else
						{
							$company_status=0;
							return '1';
						}
					}
					else
					{
						$company_status=0;
						return '1';
					}
				}
				////////////////////////////
					//print_r($company_status); 
				//exit;
			
			}
			
			//return '1';
			////////////////////
			if($company_status=='5')
			{
			$reward = 0;

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			if ($order_query->row['affiliate_id']) {
				$affiliate_id = $order_query->row['affiliate_id'];
			} else {
				$affiliate_id = 0;
			}

			$this->load->model('marketing/affiliate');

			$affiliate_info = $this->model_marketing_affiliate->getAffiliate($affiliate_id);

			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
				$language_filename = $language_info['filename'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_filename = '';
				$language_directory = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'user_id'                => $order_query->row['user_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
                                'card_no' => $order_query->row['card_no'],
				'custom_field'            => unserialize($order_query->row['custom_field']),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query->row['order_status_id'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_filename'       => $language_filename,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified'],
				'tagged'		=>   $order_query->row['tagged'],
				'cash'		=>   $order_query->row['cash'],
				'subsidy'		=>   $order_query->row['subsidy'],
				'op_name'=>   $order_query->row['op_name']

			);
			}
			else
			{
				return '1';
			}
		} else {
			return;
		}
	}
	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT oc_order_product.*,oc_product.HSTN as hsn_code FROM " . DB_PREFIX . "order_product left join oc_product on oc_order_product.product_id=oc_product.product_id WHERE oc_order_product.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}
	public function getNamebyID($user_id) {
		$query = $this->db->query("SELECT concat(firstname,' ',lastname) as name from oc_user WHERE user_id = '" . $user_id . "'");

		return $query->row['name'];
	}
	// Function to get the client IP address
	public function get_client_ip() 
	{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
public function decryptRJ256($encrypted)
{
			
     $iv = '!QAZ2WSX#EDC4RFV'; #Same as in C#.NET
     $key = '5TGB&YHN7UJM(IK<'; #Same as in C#.NET	
    //PHP strips "+" and replaces with " ", but we need "+" so add it back in...
    $encrypted = str_replace(' ', '+', $encrypted);
    //get all the bits
    $encrypted = base64_decode($encrypted);
    $rtn = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, MCRYPT_MODE_CBC, $iv);
    $rtn = $this->unpad($rtn);
    return($rtn);
}	

public function encryptRJ256($encrypted)
{
			
     $iv = '!QAZ2WSX#EDC4RFV'; #Same as in C#.NET
     $key = '5TGB&YHN7UJM(IK<'; #Same as in C#.NET	
    //PHP strips "+" and replaces with " ", but we need "+" so add it back in...
    //$encrypted = str_replace(' ', '+', $encrypted);
    //get all the bits
$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
$pad = $blockSize - (strlen($encrypted) % $blockSize);
    $rtn = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted.str_repeat(chr($pad), $pad), MCRYPT_MODE_CBC, $iv);
$rtn = base64_encode($rtn);
    return($rtn);
}
public function pkcs7pad($plaintext, $blocksize)
{
$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC);
    $padsize = $blocksize - (strlen($plaintext) % $blocksize);
    return $plaintext . str_repeat(chr($padsize), $padsize);
}

//removes PKCS7 padding
public function unpad($value)
{
    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $packing = ord($value[strlen($value) - 1]);
    if($packing && $packing < $blockSize)
    {
        for($P = strlen($value) - 1; $P >= strlen($value) - $packing; $P--)
        {
            if(ord($value{$P}) != $packing)
            {
                $packing = 0;
            }
        }
    }

    return substr($value, 0, strlen($value) - $packing); 
}
}