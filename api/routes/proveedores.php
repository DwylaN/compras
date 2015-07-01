<?php 
	date_default_timezone_set('UTC');
	$app->group('/proveedores', function () use ($app)	{

		// =============================================================
		//
		//  GET / Muestra todos los proveedores registrados
		//   
		// =============================================================
		$app->get('/', function () use ($app) {

			$clientes = ORM::for_table('proveedores')->find_many();
			
			$proveedores = array();
			foreach ($clientes as $key => $value) {
				$proveedores[] = array(
								'id' => $value->id,
								'rfc' => $value->rfc,
								'razon_social' => $value->razon_social,
								'direccion' => $value->direccion,
								'telefono' => $value->telefono,
								'calificacion' => $value->calificacion,
								'vendedor' => $value->vendedor
							);
				
			}		

			$response = array(
				'success' => true,
				'message' => $proveedores
			);

			$app->response->setStatus(200);
			$app->response->setBody(json_encode($response));

				
		});
		
		$app->options('/', function () use ($app) {
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});
		// =============================================================
		//
		//  GET / Muestra la información de un proveedor en específico
		//  La búsqueda se hace por medio de su id.
		//   
		// =============================================================
		$app->get('/:rfc', function ($rfc) use ($app) {

			$salesman = ORM::for_table('proveedores')
				->select('proveedores.*')
				->where('rfc', $rfc)
				->find_one();

			if (!$salesman) {

				$error = array(
					'success' => false,
					'message' => 'No existe el RFC'.$rfc
				);
				$app->response->setStatus(400);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$vendedor = array(
				'id' => $salesman->id,
				'rfc' => $salesman->rfc,
				'razon_social|' => $salesman->razon_social,
				'direccion' => $salesman->direccion,
				'telefono,' => $salesman->telefono,
				'calificacion' => $salesman->calificacion,
				'vendedor' => $salesman->vendedor
			);

			$response = array(
				'success' => true,
				'message' => $vendedor
			);

			$app->response->setStatus(200);
			$app->response->setBody(json_encode($response));
				
		});
		
		$app->options('/:rfc', function () use ($app) {
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});
		// =============================================================
		//
		//  PUT / Actualiza la información de un proveedor en específico
		//	La búsqueda se hace por medio de su RFC
		// 
		// =============================================================
			
		$app->put('/:rfc', function ($rfc) use ($app) {

			// validate params
			$rules = array(						
				'razon_social' => array(true, "string",1 ,99),
				'direccion' => array(true, "string",1 ,99),
				'telefono' => array(true, "string",1 ,99),
				'calificacion' => array(true, "string",1 ,99),
				'vendedor' => array(true, "string",1 ,99)
			);
			
			$v = new Validator($app->request->getBody(), $rules);
			$params = $v->validate();	

			if(count($v->getErrors()) > 0){
				$app->response->setStatus($v->getCode());
				$app->response->setBody(json_encode($v->getErrors()));
				$app->stop();
			}

			$proveedor = ORM::for_table('proveedores')
				->select('proveedores.*')
				->where('rfc', $rfc)
				->find_one();

			if(!$proveedor){
				$error = array(
					'success' => false,
					'message' => 'No se encuentra registrado el RFC '.$rfc
				);
				$app->response->setStatus(400);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$proveedor->set('razon_social', $params['razon_social']);
			$proveedor->set('direccion', $params['direccion']);
			$proveedor->set('telefono', $params['telefono']);
			$proveedor->set('calificacion', $params['calificacion']);
			$proveedor->set('vendedor', $params['vendedor']);

			if (!$proveedor->save()) {
				$error = array(
					'success' => false,
					'message' => 'Internal server error.'
				);
				$app->response->setStatus(500);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$params['id'] = $proveedor->id();
			$params['rfc'] = $proveedor->rfc;

			$app->response->setStatus(200);
			$app->response->setBody(json_encode($params));
			$app->stop();

		});

		$app->options('/:rfc', function () use ($app){
			$app->response->setStatus(204);
			$app->response->setBody(json_encode($response));
		});

		// =============================================================
		//
		//  Crea un nuevo vendedor.
		//  
		// =============================================================
		$app->post('/', function () use($app){

			// validate params
			$rules = array(
				'rfc' => array(true, "string",1,99),
				'razon_social' => array(true, "string",1 ,99),
				'razon_social' => array(true, "string",1 ,99),
				'direccion' => array(true, "string",1 ,99),
				'telefono' => array(true, "string",1 ,99),
				'calificacion' => array(true, "string",1 ,99),
				'vendedor' => array(true, "string",1 ,99)
			);
			
			$v = new Validator($app->request->getBody(), $rules);
			$params = $v->validate();	

			if(count($v->getErrors()) > 0){
				$app->response->setStatus($v->getCode());
				$app->response->setBody(json_encode($v->getErrors()));
				$app->stop();
			}

			$proveedor = ORM::for_table('proveedores')
				->select('proveedores.*')
				->where('rfc', $params['rfc'])
				->find_one();

			if ($proveedor) {
				$error = array(
					'success' => false,
					'message' => 'Ya está registrado el proveedor '.$params['rfc']
				);
				
				$app->response->setStatus(400);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$proveedor = ORM::for_table('proveedores')->create();
		
			$proveedor->rfc = $params['rfc'];
			$proveedor->razon_social = $params['razon_social'];
			$proveedor->telefono = $params['telefono'];
			$proveedor->calificacion = $params['calificacion'];
			$proveedor->vendedor = $params['vendedor'];
			
			if (!$proveedor->save()) {
				$error = array(
					'success' => false,
					'message' => 'Internal server error'
				);
				
				$app->response->setStatus(500);
				$app->response->setBody(json_encode($error));
				$app->stop();	
			}
			

			$params['id'] = $proveedor->id();

			$app->response->setStatus(201);
			$response = $params;
			$app->response->setBody(json_encode($params));
			$app->stop();
						
		});
			
		$app->options('/', function () use ($app){
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});	

		// =============================================================
		//
		//  DELETE / Elimina a un proveedor por medio de su id.
		//   
		// =============================================================
		$app->delete('/:rfc', function ($rfc) use ($app) {

			$vendedor = ORM::for_table('proveedores')
				->select('proveedores.*')
				->where('rfc', $rfc)
				->find_one();

			if (!$vendedor) {

				$error = array(
					'success' => false,
					'message' => 'No existe el RFC'.$rfc
				);
				$app->response->setStatus(400);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$vendedor->delete();

			$response = array(
				'success' => true,
				'message' => $rfc.' eliminado'
			);

			$app->response->setStatus(200);
			$app->response->setBody(json_encode($response));
				
		});
		
		$app->options('/:rfc', function () use ($app) {
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});
	 
	});
 ?>

