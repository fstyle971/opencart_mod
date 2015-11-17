<?php

class ControllerModuleJluser extends Controller {
	
	private $error = array();
	CONST DISTRIBUTOR_GROUP_NAME = 'Distributeur';
	
	public function install() {
		$this->load->model('module/jluser');
		$this->model_module_jluser->createTable();
		$this->load->model('module/jlcard');
		$this->model_module_jlcard->createTable();
	}

	public function uninstall() {
		$this->load->model('module/jluser');
		$this->model_module_jluser->deleteTable();
		$this->load->model('module/jlcard');
		$this->model_module_jlcard->deleteTable();
	}

	public function index() {  
		$this->_init();
		$this->getList();
	}

	private function _init() {
		//Chargement des textes de langues
		$this->load->language('module/jluser');
		$this->document->setTitle($this->language->get('heading_title'));
		//Initialisation des chaines
		$this->_setTextString();
		//Chargement des modèles
		$this->load->model('module/jluser');
		$this->load->model('module/jlcard');
		$this->load->model('user/user');
		$this->load->model('user/user_group');
	}
	
	public function insert() {
		$this->_init();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$user_data = array();
			$field = array('username', 'firstname', 'lastname', 'password', 'confirm', 'email');
			foreach($field as $f) {
				$user_data[$f] = $this->request->post["distributor_$f"];
			}
			if ($this->validateUserForm($user_data)) {
				//Par défaut, on active l'utilisateur
				$user_data['status'] = 1;
				$user_data['user_group_id'] = $this->_getDistributorGroupId();
			
				$this->model_user_user->addUser($user_data);
			
				$user_info = $this->model_user_user->getUserByUsername($user_data['username']);
				//$this->load->jl_debug($user_info);
				if ($user_info) {
					$this->session->data['success'] = $this->language->get('text_success');
			
					$distributor_data = array();
					$distributor_field = array('vip_card_number_list', 'address', 'telephone', 'point_sale', 'cash_collector', 'associated_customer');
					foreach($distributor_field as $df) {
						$distributor_data[$df] = $this->request->post["distributor_$df"];
					}
					$distributor_data['vip_card_number_list'] = $this->_setVipCard($distributor_data['vip_card_number_list']);
					$this->model_module_jluser->addUser($user_info['user_id'], $distributor_data);
					$this->model_module_jlcard->addCard($user_info['user_id'], $distributor_data['vip_card_number_list']);
					if (empty($distributor_data["associated_customer"])) {
						$distributor_data["associated_customer"] = array();
					}
					$this->model_module_jluser->setAssociatedCustomer($user_info['user_id'], $distributor_data["associated_customer"]);
					$this->redirect($this->url->link('module/jluser', 'token=' . $this->session->data['token'], 'SSL'));
				}
			}
		}

		$this->getForm();
	}

	public function update() {
		$this->_init();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$user_data = array();
			$field = array('username', 'firstname', 'lastname', 'password', 'confirm', 'email');
			foreach($field as $f) {
				$user_data[$f] = $this->request->post["distributor_$f"];
			}				
			if ($this->validateUserForm($user_data)) {
				
				//Par défaut, on active l'utilisateur
				$user_data['status'] = 1;
				$user_data['user_group_id'] = $this->_getDistributorGroupId();
				$this->model_user_user->editUser($this->request->get['user_id'], $user_data);
				
				//Champs spécifique
				$distributor_data = array();
				$distributor_field = array('vip_card_number_list', 'address', 'telephone', 'point_sale', 'cash_collector', 'associated_customer');
				foreach($distributor_field as $df) {
					$distributor_data[$df] = $this->request->post["distributor_$df"];
				}
				$distributor_data['vip_card_number_list'] = $this->_setVipCard($distributor_data['vip_card_number_list']);
				//$this->load->jl_debug($distributor_data);
				$this->model_module_jluser->editUser($this->request->get['user_id'], $distributor_data);
				$this->model_module_jlcard->editCard($this->request->get['user_id'], $distributor_data['vip_card_number_list']);
				if (empty($distributor_data["associated_customer"])) {
					$distributor_data["associated_customer"] = array();
				}
				$this->model_module_jluser->setAssociatedCustomer($this->request->get['user_id'], $distributor_data["associated_customer"]);
				
				$this->session->data['success'] = $this->language->get('text_success');

				$url = '';
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				$this->redirect($this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			}
		}
		$this->getForm();
	}

	public function delete() {
		$this->_init();
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $user_id) {
				$this->model_user_user->deleteUser($user_id);
				$this->model_module_jluser->deleteUser($user_id);
				$this->model_module_jlcard->deleteCardByUserId($user_id);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$this->redirect($this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
		$this->getList();
	}

	protected function validateUserForm($data) {
		if (!$this->user->hasPermission('modify', 'module/jluser')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$field_prefix = 'distributor_';
		if ((utf8_strlen($data['username']) < 3) || (utf8_strlen($data['username']) > 20)) {
			$this->error[$field_prefix.'username'] = $this->language->get('error_'.$field_prefix.'username');
		}

		$user_info = $this->model_user_user->getUserByUsername($data['username']);

		if (!isset($this->request->get['user_id'])) {
			if ($user_info) {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		} else {
			if ($user_info && ($this->request->get['user_id'] != $user_info['user_id'])) {
				$this->error['warning'] = $this->language->get('error_'.$field_prefix.'exists');
			}
		}

		if ((utf8_strlen($data['firstname']) < 1) || (utf8_strlen($data['firstname']) > 32)) {
			$this->error[$field_prefix.'firstname'] = $this->language->get('error_'.$field_prefix.'firstname');
		}

		if ((utf8_strlen($data['lastname']) < 1) || (utf8_strlen($data['lastname']) > 32)) {
			$this->error[$field_prefix.'lastname'] = $this->language->get('error_'.$field_prefix.'lastname');
		}

		if ($data['password'] || (!isset($this->request->get['user_id']))) {
			if ((utf8_strlen($data['password']) < 4) || (utf8_strlen($data['password']) > 20)) {
				$this->error[$field_prefix.'password'] = $this->language->get('error_'.$field_prefix.'password');
			}

			if ($data['password'] != $data['confirm']) {
				$this->error[$field_prefix.'confirm'] = $this->language->get('error_'.$field_prefix.'confirm');
			}
		}
		
		//Confirmation Email
		if ((utf8_strlen($data['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $data['email'])) {
			$this->error[$field_prefix.'email'] = $this->language->get('error_'.$field_prefix.'email');
		}

		/*//Confirmation vip card number
		$data['vip_card_number_list'] = trim($data['vip_card_number_list']);
		if (!empty($data['vip_card_number_list'])) {
			$card_list = explode('#', $data['vip_card_number_list']);
			$card_list_error = false;
			$i = 0;
			while (($card_list_error === false) && ($i<count($card_list))) {
				$x = explode(';', $card_list[$i]);
				if (count($x) != 2) {
					$card_list_error = true;
				} else {
					$card_number = (int) $x[0];
					if (strpos($x[1], '.') === false) {
						$card_list_error = true;
					} else {
						$card_rate = (float) $x[1];
						if ((is_int($card_number) === false)
							|| (is_float($card_rate) === false)
							|| ($card_rate < 0)
							|| ($card_rate > 1)
							|| (is_float($card_rate) === false))
							 {
							$card_list_error = true;
						}
					}
				}
				$i++;
			}
			if ($card_list_error) {
				$this->error[$field_prefix.'vip_card_number_list'] = $this->language->get('error_'.$field_prefix.'vip_card_number_list');
			}
		}*/

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function getList() {
		$this->_setBreadcrumbs();
		$url = '';
		$url_params = array(
			array('key' => 'sort', 'default' => 'username'),
			array('key' => 'order', 'default' => 'ASC'),
			array('key' => 'page', 'default' => 1),
		);
		$data = array();
		foreach ($url_params as $up) {
			$x = (isset($this->request->get[$up['key']])) ? $this->request->get[$up['key']] : $up['default'];
			$url .= "&{$up['key']}={$x}";
			//Pour la requête qui va récupérer les utilisateurs
			$data[$up['key']] = $x;
		}
		$data['start'] = ($data['page'] - 1) * $this->config->get('config_admin_limit');
		$data['limit'] = $this->config->get('config_admin_limit');

		$this->data['insert'] = $this->url->link('module/jluser/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('module/jluser/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->data['users'] = array();

		$user_total = $this->model_module_jluser->getTotalUsers();

		$results = $this->model_module_jluser->getUsers($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('module/jluser/update', 'token=' . $this->session->data['token'] . '&user_id=' . $result['user_id'] . $url, 'SSL')
			);

			$this->data['users'][] = array(
				'user_id'    => $result['user_id'],
				'username'   => $result['username'],
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'selected'   => isset($this->request->post['selected']) && in_array($result['user_id'], $this->request->post['selected']),
				'action'     => $action
			);
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$sort = array('username', 'status', 'date_added');
		foreach ($sort as $s) {
			$url .= "&sort={$s}&order={$data['order']}";
			$this->data["sort_$s"] = $this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url, 'SSL');
		}

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $user_total;
		$pagination->page = $data['page'];
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $data['sort'];
		$this->data['order'] = $data['order'];

		$this->template = 'module/jluser_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$FIELD_PREFIX = 'distributor';

		/*$entry = array('username', 'firstname', 'lastname', 'password', 'confirm'
			, 'address', 'telephone', 'vip_card_number_list', 'point_sale', 'cash_collector'
		);*/

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$error = array('username', 'firstname', 'lastname', 'password', 'confirm', 'email');
		$this->data['error'] = false;
		foreach($error as $e) {
			if (isset($this->error[$FIELD_PREFIX.'_'.$e])) {
				$this->data['error_'.$FIELD_PREFIX.'_'.$e] = $this->error[$FIELD_PREFIX.'_'.$e];
				$this->data['error'] = true;
			} else {
				$this->data['error_'.$FIELD_PREFIX.'_'.$e] = '';
			}
		}

		$this->_setBreadcrumbs();

		if (isset($this->request->get['user_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$user_info = $this->model_module_jluser->getUser($this->request->get['user_id']);
			$user_info['associated_customer'] = $this->model_module_jluser->getCustomers($this->request->get['user_id']);
			$card_list = $this->model_module_jlcard->getCardsByUserId($this->request->get['user_id']);
			$this->data[$FIELD_PREFIX.'_vip_card_number_list'] = array();
			foreach ($card_list as $cl) {
				$this->data[$FIELD_PREFIX.'_vip_card_number_list'][] = $cl['card_id'] . ';' . $cl['rate'] . ';0';//0 => Action Update
			}
			$this->data[$FIELD_PREFIX.'_vip_card_number_list'] = implode('#', $this->data[$FIELD_PREFIX.'_vip_card_number_list']);
		}
		
		//$this->load->jl_debug($this->data['ac_list'] );
		
		foreach($this->text_string as $ts) {
			if (strpos($ts, $FIELD_PREFIX) !== FALSE) {
				$f = str_replace('entry_', '', $ts);
				if (strpos($f, 'confirm') !== FALSE) {
					//Exception sur la confirmation du mot de passe
					$this->data[$f] = '';
				} else {
					if (isset($this->request->post[$f])) {
						$this->data[$f] = $this->request->post[$f];
					} else {
						if (strpos($f, 'vip_card_number_list') === FALSE) {
							//Il ne faut pas redéfinir la vip_card_number_list
							$x = str_replace($FIELD_PREFIX.'_', '', $f);
							$this->data[$f] = (isset($user_info[$x])) ? $user_info[$x] : '';
						}
					}
				}
			}
		}
		
		if ($this->data[$FIELD_PREFIX.'_associated_customer'] == '') {
			$this->data[$FIELD_PREFIX.'_associated_customer'] = $this->model_module_jluser->getCustomers();
		} elseif(isset($this->request->post[$FIELD_PREFIX.'_associated_customer'])) {
			$this->data[$FIELD_PREFIX.'_associated_customer'] = $this->model_module_jluser->getCustomersById($this->request->post[$FIELD_PREFIX.'_associated_customer']);
		}
		$this->data[$FIELD_PREFIX.'_vip_card_number_list'] = $this->_setVipCard($this->data[$FIELD_PREFIX.'_vip_card_number_list']);
		
		//Récupère le groupe de l'utilisateur
		if (isset($this->request->post['user_group_id'])) {
			$this->data['user_group_id'] = $this->request->post['user_group_id'];
		} elseif (!empty($user_info)) {
			$this->data['user_group_id'] = $user_info['user_group_id'];
		} else {
			$this->data['user_group_id'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($user_info)) {
			$this->data['status'] = $user_info['status'];
		} else {
			$this->data['status'] = 0;
		}
		
		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['user_id'])) {
			$this->data['action'] = $this->url->link('module/jluser/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('module/jluser/update', 'token=' . $this->session->data['token'] . '&user_id=' . $this->request->get['user_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->template = 'module/jluser_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	public function check_card_id() {
		$response = array('success' => true, 'card_id_already_present' => array());
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$value = isset($this->request->post['value']) ? $this->request->post['value'] : '';
			if ($value != '') {
				$data = explode(';', $value);
				foreach ($data as &$d) {
					$d = (int) $d;
				}
				$this->load->model('module/jlcard');
				$card_id_already_present = $this->model_module_jlcard->checkCardId($data);
				foreach ($card_id_already_present as $c) {
					$response['card_id_already_present'][] = $c['card_id'];
				}
			}
			if (count($response['card_id_already_present']) > 0) {
				$response['success'] = false;
			}
		}
		$this->response->setOutput(json_encode($response));
	}
	
	private function _getDistributorGroupId() {
		$user_group = $this->model_user_user_group->getUserGroups();
		$distributor_group_id = 0;
		foreach($user_group as $ug) {
			if ($ug['name'] == self::DISTRIBUTOR_GROUP_NAME) {
				$distributor_group_id = $ug['user_group_id'];
			}
		}
		return $distributor_group_id;
	}

	private function _setVipCard($data) {
		$x = array();
		if (!empty($data)) {
			$card_list = explode('#', $data);
			foreach ($card_list as $cl) {
				if (!empty($cl)) {
					$v = explode(';', $cl);
					$x[] = array('card_id' => $v[0], 'card_rate' => $v[1], 'action' => $v[2]);
				}
			}
		}
		return $x;
	}

	private function _setBreadcrumbs($url = '') {
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/jluser', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);
	}
	
	protected function validateDelete() {
		$this->_init();
		if (!$this->user->hasPermission('modify', 'user/user')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['selected'] as $user_id) {
			if ($this->user->getId() == $user_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}
		}

		if (!$this->error) {
			return true;
		} else { 
			return false;
		}
	}

	private function _setTextString() {
		$this->text_string = array(
			'heading_title',
			'text_enabled',
			'text_disabled',
			'text_content_top',
			'text_content_bottom',
			'text_column_left',
			'text_column_right',
			'text_column_username',
			'text_column_firstname',
			'text_column_lastname',
			'text_column_status',
			'text_column_date_added',
			'text_column_action',
			'text_yes',
			'text_no',
			'text_no_results',
			'text_add',
			'text_delete',
			'text_number',
			'text_rate',
			'button_save',
			'button_cancel',
			'button_add_module',
			'button_remove',
			'button_insert',
			'button_delete',
			'entry_layout',
			'entry_limit',
			'entry_image',
			'entry_position',
			'entry_status',
			'entry_sort_order',
			'entry_distributor_username',
			'entry_distributor_firstname',
			'entry_distributor_lastname',
			'entry_distributor_password',
			'entry_distributor_confirm',
			'entry_distributor_email',
			'entry_distributor_address',
			'entry_distributor_telephone',
			'entry_distributor_vip_card_number_list',
			'entry_distributor_point_sale',
			'entry_distributor_cash_collector',
			'entry_distributor_associated_customer',
			'tab_profile',
			'tab_associated_customer',
		);
		foreach($this->text_string as $ts) {
			$this->data[$ts] = $this->language->get($ts);
		}
	}
}
?>
