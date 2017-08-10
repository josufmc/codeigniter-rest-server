<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH. '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Pedidos extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();

		header("Access-Control-Allow-Methods: GET, POST, DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
	}

	public function realizar_orden_post($token = "0", $id_usuario = "0"){
		$data = $this->post();

		if ($token == "0" || $id_usuario == "0"){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Token inválido'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		if(!isset($data['items']) || strlen($data['items']) == 0){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Faltan los items'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		$condiciones = array(
			'id' => $id_usuario,
			'token' => $token
		);

		$this->db->where($condiciones);
		$query = $this->db->get('login');
		$existe = $query->row();

		if(!$existe){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Usuario y token incorrectos'
			);
			return $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
		}
		$this->db->reset_query();

		// Inserción de orden de pedido
		$insertar = array(
			'usuario_id' => $id_usuario
		);
		$this->db->insert('ordenes', $insertar);
		$orden_id = $this->db->insert_id();


		// Inserción de líneas de pedido
		$this->db->reset_query();
		$items = explode(',', $data['items']);
		foreach($items as &$producto_id){
			$data_insertar = array(
				'producto_id' => $producto_id,
				'orden_id' => $orden_id
			);
			$this->db->insert('ordenes_detalle', $data_insertar);
		}

		$response = array(
			'error' => FALSE,
			'orden_id' => $orden_id
		);
		return $this->response($response);
	}

	public function obtener_pedidos_get($token = "0", $id_usuario = "0"){
		if ($token == "0" || $id_usuario == "0"){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Token inválido'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		$condiciones = array(
			'id' => $id_usuario,
			'token' => $token
		);

		$this->db->where($condiciones);
		$query = $this->db->get('login');
		$existe = $query->row();

		if(!$existe){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Usuario y token incorrectos'
			);
			return $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
		}
		$this->db->reset_query();

		$query = $this->db->query('SELECT * FROM ordenes WHERE usuario_id = ' . $this->db->escape($id_usuario));
		$ordenes = array();
		foreach($query->result() as $row){
			$query_detalle = $this->db->query(
				"SELECT OD.orden_id, P.* FROM ordenes_detalle OD INNER JOIN productos P ON P.codigo = OD.producto_id WHERE OD.orden_id = " . 
				$this->db->escape($row->id)
			);
			$orden = array(
				'id' => $row->id,
				'creado_en' => $row->creado_en,
				'detalle' => $query_detalle->result()
			);
			//array_push($ordenes, $orden);
			$ordenes[] = $orden;
		}

		$response = array(
			'error' => FALSE,
			'ordenes' => $ordenes
		);
		return $this->response($response);
	}

	public function borrar_pedido_delete($token = "0", $id_usuario = "0", $orden_id = "0"){
		if ($token == "0" || $id_usuario == "0"){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Token inválido'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		$condiciones = array(
			'id' => $id_usuario,
			'token' => $token
		);

		$this->db->where($condiciones);
		$query = $this->db->get('login');
		$existe = $query->row();

		if(!$existe){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Usuario y token incorrectos'
			);
			return $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
		}
		

		// Verificar si el pedido es del usuario
		$this->db->reset_query();
		$condiciones = array(
			'usuario_id' => $id_usuario,
			'id' => $orden_id
		);
		$this->db->where($condiciones);
		$query = $this->db->get('ordenes');

		$existe = $query->row();
		if (!$existe){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'La orden no puede ser borrada'
			);
			return $this->response($response);
		}

		// Borramos pedido
		$condiciones = array(
			'usuario_id' => $id_usuario,
			'id' => $orden_id
		);
		$this->db->delete('ordenes', $condiciones);

		$condiciones = array(
			'orden_id' => $orden_id
		);
		$this->db->delete('ordenes_detalle', $condiciones);
		$response = array(
			'error' => FALSE,
			'mensaje' => 'Orden borrada'
		);
		return $this->response($response);
	}
}
