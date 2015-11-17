<?php
class ModelModuleJlUser extends Model {
	
	// This function is how my blog module creates it's tables to store blog entries. You would call this function in your controller in a
	// function called install(). The install() function is called automatically by OC versions 1.4.9.x, and maybe 1.4.8.x when a module is
	// installed in admin.
	public function createTable() {
		$query = $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "jl_user (user_id INT(11) NOT NULL, address varchar(128), telephone varchar(32), is_point_sale INT(1) NOT NULL, is_cash_collector INT(1) NOT NULL, PRIMARY KEY (user_id))");
		$query = $this->db->query("ALTER TABLE " . DB_PREFIX . "customer ADD `distributor_id` INT(11)");
	}

	public function deleteTable() {
		$query = $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "jl_user");
		$query = $this->db->query("ALTER TABLE " . DB_PREFIX . "customer DROP COLUMN `distributor_id`");
	}

	public function addUser($user_id, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "jl_user` SET user_id = '" . (int)$user_id . "', address = '" . $this->db->escape($data['address']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', is_point_sale = '" . (int)$data['point_sale'] . "', is_cash_collector = '" . (int)$data['cash_collector'] . "'");
	}
	
	public function editUser($user_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "jl_user` SET address = '" . $this->db->escape($data['address']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', is_point_sale = '" . (int)$data['point_sale'] . "', is_cash_collector = '" . (int)$data['cash_collector'] . "' WHERE user_id = '" . (int)$user_id . "'");
	}
	
	public function deleteUser($user_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "jl_user` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function getTotalUsers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "jl_user`");
		return $query->row['total'];
	}

	public function getUsers($data = array()) {
		$sql = "SELECT u.*, j.address, j.telephone, j.is_point_sale, j.is_cash_collector FROM `" . DB_PREFIX . "user` AS u JOIN `" . DB_PREFIX . "jl_user` AS j ON j.user_id = u.user_id";

		$sort_data = array(
			'username',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY username";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getUser($user_id) {
		$query = $this->db->query("SELECT u.*, j.address, j.telephone, j.is_point_sale AS point_sale, j.is_cash_collector AS cash_collector FROM `" . DB_PREFIX . "user` AS u JOIN `" . DB_PREFIX . "jl_user` AS j ON j.user_id = u.user_id WHERE u.user_id = '" . (int)$user_id . "'");

		return $query->row;
	}
	
	public function getCustomers($user_id = 0) {
		$query = $this->db->query("SELECT customer_id, firstname, lastname, CASE WHEN distributor_id IS NOT NULL THEN TRUE ELSE FALSE END AS selected FROM " . DB_PREFIX . "customer WHERE (distributor_id IS NULL) OR (distributor_id IN ('" . (int)$user_id . "')) ");

		return $query->rows;
	}
	
	public function getCustomersByid($data) {
		if (count($data) > 0) {
			$x = array();
			foreach($data as $d) {
				$x[] = (int) $d;
			}
			$x = implode(',', $x);
			
			$query = $this->db->query("SELECT customer_id, firstname, lastname, TRUE AS selected FROM " . DB_PREFIX . "customer WHERE (customer_id IN (" . $x . ")) ");
			return $query->rows;
		}
		return array();
	}
	
	public function setAssociatedCustomer($user_id, $data) {
		//Réinitialisation
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET distributor_id = NULL WHERE distributor_id = '" . (int)$user_id . "'");
		
		if (count($data) > 0) {
			$x = array();
			foreach($data as $d) {
				$x[] = (int) $d;
			}
			$x = implode(',', $x);
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET distributor_id = '" . (int)$user_id . "' WHERE (customer_id IN (" . $x . ")) ");
		}
	}
}
?>
