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

	public function por_tipo_get($tipo=0, $pagina=0){
		if ($tipo==0){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Falta el parámetro de tipo'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}
		
		$pagina = $pagina * 10;
		$query = $this->db->query("SELECT * FROM productos WHERE linea_id = " . $this->db->escape($tipo) . "  LIMIT " . $this->db->escape($pagina) . ", 10");
		$response = array(
			'error' => FALSE,
			'productos' => $query->result()
		);
		return $this->response($response);
	}

	public function buscar_get($termino = 'no especif'){
		$query = $this->db->query("SELECT * FROM productos WHERE producto LIKE " . $this->db->escape("%" . $termino . "%"));
		$response = array(
			'error' => FALSE,
			'termino' => $termino,
			'productos' => $query->result()
		);
		return $this->response($response);
	}
}
