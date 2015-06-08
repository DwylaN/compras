<?php 
	date_default_timezone_set('UTC');
	$app->group('/ventas', function () use ($app)	{

		// =============================================================
		//
		//  GET /
		//   
		// =============================================================
		$app->get('/', function () use ($app) {

			$ventas = ORM::for_table('ventas')->find_many();
			
			$response = array();
			foreach ($ventas as $key => $value) {
				$response[] = array(
								'id' => $value->id,
								'id_cliente' => $value->id_cliente,
								'id_producto' => $value->id_producto,
								'fecha' => $value->fecha
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
		// $app->get('/:no_orden', function ($no_orden) use ($app) {

		// 	$compra = ORM::for_table('compras')
		// 		->select('compras.*')
		// 		->where('no_orden', $no_orden)
		// 		->find_one();

		// 	$response = array(
		// 			'id' => $compra->id,
		// 			'no_orden' => $compra->no_orden,
		// 			'no_empleado' => $compra->no_empleado,
		// 			'nombre_articulo' => $compra->nombre_articulo,
		// 			'status' => $compra->status,
		// 			'VoBo' => $compra->VoBo
		// 		);
		// 	$app->response->setStatus(200);
		// 	$app->response->setBody(json_encode($response));
				
		// });
		
		// $app->options('/', function () use ($app) {
		// 	$app->response->setStatus(204);
		// 	$app->response->setBody(json_encode(array('message' => 'ok')));
		// });
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
				'id_cliente' => array(true, "string",1, 9),
				'id_producto' => array(true, "string",1 , 5)				
			);
			
			$v = new Validator($app->request->getBody(), $rules);
			$params = $v->validate();	

			if(count($v->getErrors()) > 0){
				$app->response->setStatus($v->getCode());
				$app->response->setBody(json_encode($v->getErrors()));
				$app->stop();
			}

			$venta = ORM::for_table('ventas')->create();
		
			$venta->id_cliente = $params['id_cliente'];
			$venta->id_producto = $params['id_producto'];			
			$venta->fecha = date("D M j G:i:s T Y");

			$venta->save();

			$response = array(
				'succes' => true,
				'message' => $venta
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

