<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH. '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Productos extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();

		header("Access-Control-Allow-Methods: GET");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
	}

	public function todos_get($pagina=0)
	{
		$pagina = $pagina * 10;
		$query = $this->db->query("SELECT * FROM productos LIMIT " . $this->db->escape($pagina) . ", 10");
		$response = array(
			'error' => FALSE,
			'productos' => $query->result()
		);
		return $this->response($response);
	}
}
