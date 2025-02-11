<?php

class Crud_model extends CI_Model
{
	/* View */

	function insert($data)
	{
		$this->db->insert('account', $data);
		$accountid = $this->db->insert_id();
		$query = 'INSERT INTO `naaman`.`user`(`account_id`,`pfp`)VALUES (' . $accountid . ', "default_pp.png");';
		$this->db->query($query);
		return $accountid;
	}

	function insertdetail($data)
	{
		$id = $this->session->userdata('tempid');
		$this->db->where('account_id', $id);
		$this->db->update('account', $data);
		return $id;
	}

	function can_login($username, $password)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('account');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$store_password = $this->encrypt->decode($row->password);
				if ($password = $store_password) {
					$this->session->set_userdata('id', $row->account_id);
					$this->session->set_userdata('username', $row->username);
				} else {
					return 'Wrong Password';
				}
			}
		} else {
			return 'username do not exist';
		}
	}

	function login_session()
	{

		$this->db->where('account_id', $this->session->userdata('id'));
		$query = $this->db->get('user');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$this->session->set_userdata('uid', $row->user_id);
				$this->session->set_userdata('pfp', $row->pfp);
				if (!$this->session->set_userdata('pfp', $row->pfp)) {
					$this->session->set_userdata('pfp', 'assets/Images/default_pp.png');
				}

				$this->session->set_userdata('nick', $row->nickname);
				if (!$this->session->set_userdata('nick', $row->nickname)) {
					$this->session->set_userdata('nick', $this->session->userdata('username'));
				}

				$this->session->set_userdata('status', $row->user_description);
			}
		}
	}

	function update_profile($data)
	{
		$id = $this->session->userdata('id');
		$where = "account_id = '$id'";
		$str = $this->db->update_string('account', $data, $where);
		$this->db->query($str);
	}

	//^not finished

	function display_records()
	{
		$query = $this->db->get("account");
		return $query->result();
	}

	function displayuserpass($username, $password)
	{
		$query = $this->db->get("`naaman`.`account` where username='$username' and password='$password'");
		return $query->result();
	}

	function displaymatch($username, $password)
	{
		$query = $this->db->get("`naaman`.`account` where username='$username' and password='$password'");
		$row = $query->num_rows();
		return $row;
	}

	public function registerUser($data)
	{
		return $this->db->insert('account', $data);
	}

	public function login($email)
	{
		return $this->db->get_where('account', ['email' => $email])->row();
	}

	public function setSession($user)
	{
		$data = array(
			'user_id' => $user->account_id,
			'first_name' => $user->f_name,
			'last_name' => $user->l_name,
			'email' => $user->email,
			'user_role' => 3
		);
		$this->session->set_userdata($data);
	}

}
