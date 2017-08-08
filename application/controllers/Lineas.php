<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH. '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Lineas extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();

		header("Access-Control-Allow-Methods: GET");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
	}

	public function index_get()
	{
		$query = $this->db->query("SELECT * FROM lineas");
		$response = array(
			'error' => FALSE,
			'lineas' => $query->result()
		);
		return $this->response($response);
	}
}
