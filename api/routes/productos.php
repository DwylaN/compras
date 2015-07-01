<?php 
	date_default_timezone_set('UTC');
	$app->group('/productos', function () use ($app)	{

		// =============================================================
		//
		//  GET /
		//   
		// =============================================================
		$app->get('/', function () use ($app) {

			$productos = ORM::for_table('productos')->find_many();
			
			$response = array();
			foreach ($productos as $key => $value) {
				$response[] = array(
								'id' => $value->id,
								'nombre' => $value->nombre,
								'marca' => $value->marca
							);
				
			}		

			$app->response->setStatus(200);
			$app->response->setBody(json_encode($response));

				
		});
		
		$app->options('/', function () use ($app) {
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});
		// =============================================================
		//
		//  GET /:id
		//   
		// =============================================================
		$app->get('/:nombre', function ($nombre) use ($app) {

			$producto = ORM::for_table('productos')
				->select('productos.*')
				->where('nombre', $nombre)
				->find_one();

			$response = array(
					'id' => $producto->id,
					'nombre' => $producto->nombre,
					'marca' => $producto->marca
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
		//  PUT /:id
		//	
		// 
		// =============================================================
			
		// $app->put('/:no_orden', function ($no_orden) use ($app) {

		// 	// validate params
		// 	$params = array();
		// 	$pars = json_decode($app->request->getBody());
		// 	foreach ($pars as $key => $value) {
		// 	 	$params[$key] = $value;
		// 		$params['no_orden'] = $no_orden;
		// 	}
			
		// 	$rules = array(
		// 		'VoBo' => array(true, "string",0 , 19),
		// 		'no_orden' => array(true, "string",0 , 9)
		// 	);
			
		// 	$v = new Validator(json_encode($params), $rules);
		// 	$params = $v->validate();	

		// 	if(count($v->getErrors()) > 0){
		// 		$app->response->setStatus($v->getCode());
		// 		$app->response->setBody(json_encode($v->getErrors()));
		// 		$app->stop();
		// 	}

		// 	// Verificar si existe el número de orden.
		// 	$orden = ORM::for_table('compras')
		// 		->select('compras.*')
		// 		->where('no_orden', $params['no_orden'])
		// 		->find_one();

		// 	if (!$orden) {
		// 		$error = array(
		// 			'succes' => false,
		// 			'message' => 'No existe el no_orden:'. $params['no_orden']
		// 		);
		// 		$app->response->setStatus(400);
		// 		$app->response->setBody(json_encode($error));
		// 		$app->stop();
		// 	}

		// 	$orden->set('status', true);
		// 	$orden->set('VoBo', $params['VoBo']);

		// 	if($orden->save()){
		// 		$response = array(
		// 			'succes' => true,
		// 			'message' => 'Status actualizado'
		// 		);
		// 		$app->response->setStatus(200);
		// 		$app->response->setBody(json_encode($response));
		// 	}
								
		// });

		// $app->options('/:nombre', function () use ($app){
		// 	$app->response->setStatus(204);
		// 	$app->response->setBody(json_encode($response));
		// });

		// =============================================================
		//
		//  POST / crear una nueva venta 
		//  
		// =============================================================
		$app->post('/', function () use($app){

			// validate params
			$rules = array(
				'nombre' => array(true, "string",1, 99),
				'marca' => array(true, "string",1 , 99)				
			);
			
			$v = new Validator($app->request->getBody(), $rules);
			$params = $v->validate();	

			if(count($v->getErrors()) > 0){
				$app->response->setStatus($v->getCode());
				$app->response->setBody(json_encode($v->getErrors()));
				$app->stop();
			}

			$producto = ORM::for_table('productos')
				->select('productos.*')
				->where('nombre', $params['nombre'])
				->find_one();

			if ($producto) {
				$error = array(
					'succes' => false,
					'message' => 'Ya existe el producto '.$params['nombre']
				);
				
				$app->response->setStatus(400);
				$app->response->setBody(json_encode($error));
				$app->stop();
			}

			$producto = ORM::for_table('productos')->create();
		
			$producto->nombre = $params['nombre'];
			$producto->marca = $params['marca'];				

			$producto->save();

			$response = array(
				'succes' => true,
				'message' => $producto
			);

			$app->response->setStatus(201);
			$response = $params;
			$app->response->setBody(json_encode($response));
			$app->stop();
						
		});
			
		$app->options('/', function () use ($app){
			$app->response->setStatus(204);
			$app->response->setBody(json_encode(array('message' => 'ok')));
		});	
	 
	});
 ?>

