<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH. '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Prueba extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();

		header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
	}

	
	public function index()
	{
		echo('Hola mundo');
	}

	public function obtener_arreglo_get($index = 0)
	{
		$arreglo = array('manzana', 'pera', 'piña');
		if (
			$index > count($arreglo)-1 ||
			$index < 0
		){
			$respuesta = array(
				'error' => TRUE,
				'mensaje' => 'No existe elemento en esa posición'
			);
			return $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
		}
		//echo(json_encode($arreglo[$index]));
		return $this->response(array(
			'error' => FALSE,
			'fruta' => $arreglo[$index]
		));
	}

	public function obtener_producto_get($codigo){
		
		$query = $this->db->query("SELECT * FROM productos WHERE codigo=" . $this->db->escape($codigo));
		return $this->response($query->result());
	}
}
