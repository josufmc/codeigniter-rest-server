<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH. '/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Login extends REST_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->database();

		header("Access-Control-Allow-Methods: GET");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
	}

	public function index_post(){
		$data = $this->post();
		if (!isset($data['correo']) || !isset($data['contrasena'])){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'La información enviada no es válida'
			);
			return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
		}

		$condiciones = array(
			'correo' => $data['correo'],
			'contrasena' => $data['contrasena']
		);
		$query = $this->db->get_where('login', $condiciones);
		$usuario = $query->row();

		if (!isset($usuario)){
			$response = array(
				'error' => TRUE,
				'mensaje' => 'Usuario/contraseña no son válidos'
			);
			return $this->response($response, REST_Controller::HTTP_UNAUTHORIZED);
		}

		//$token = bin2hex(openssl_random_pseudo_bytes(20));
		$token = hash('ripemd160', $data['correo']);
		$actualizar_token = array(
			'token' => $token
		);
		$this->db->reset_query();
		$this->db->where('id', $usuario->id);
		$hecho = $this->db->update('login', $actualizar_token);
		
		
		$response = array(
			'error' => FALSE,
			'token' => $token,
			'id_usuario' => $usuario->id
		);
		$this->response($response);
	}
}
