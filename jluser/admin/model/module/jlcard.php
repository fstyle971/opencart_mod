<?php
class ModelModuleJlCard extends Model {
	
	// This function is how my blog module creates it's tables to store blog entries. You would call this function in your controller in a
	// function called install(). The install() function is called automatically by OC versions 1.4.9.x, and maybe 1.4.8.x when a module is
	// installed in admin.
	public function createTable() {
		$query = $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "jl_card (card_id INT(11) NOT NULL, user_id INT(11) NOT NULL, rate FLOAT NOT NULL, PRIMARY KEY (card_id))");
	}

	public function deleteTable() {
		$query = $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "jl_card");
	}

	public function addCard($user_id, $data) {
		foreach ($data as $d) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "jl_card` SET card_id = '" . (int)($d['card_id']) . "', user_id = '" . (int)$user_id . "', rate = '" . (float)$d['card_rate'] . "'");
		}
	}
	
	public function editCard($user_id, $data) {
		foreach ($data as $d) {
			if (intval($d['action']) == 1) {
				$this->addCard($user_id, array(array('card_id' => $d['card_id'], 'card_rate' => $d['card_rate'])));
			} elseif (intval($d['action']) == 0) {
				$this->db->query("UPDATE `" . DB_PREFIX . "jl_card` SET rate = '" . (float)($d['card_rate']) . "' WHERE card_id = '" . (int)$d['card_id']  . "' AND user_id = '" . (int)$user_id . "'");
			} else {
				$this->deleteCard($d['card_id']);
			}
			//$query = $this->db->query("SELECT COUNT(*) AS nb FROM `" . DB_PREFIX . "jl_card` WHERE card_id = '" . (int)$d['card_id']  . "'");
			//$count = $query->row['nb'];
		}
	}
	
	public function deleteCard($card_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "jl_card` WHERE card_id = '" . (int)$card_id . "'");
	}
	
	public function deleteCardByUserId($user_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "jl_card` WHERE user_id = '" . (int)$user_id . "'");
	}

	public function checkCardId($data) {
		foreach ($data as &$d) {
			$d = (int) $d;
		}
		$sql = "SELECT card_id
			FROM `" . DB_PREFIX . "jl_card` AS c
			WHERE c.card_id IN (" . implode(',', $data) . ")";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getCardsByUserId($user_id) {
		$query = $this->db->query("SELECT card_id, rate FROM `" . DB_PREFIX . "jl_card` AS c WHERE c.user_id = '" . (int)$user_id . "'");
		return $query->rows;
	}
}
?>
