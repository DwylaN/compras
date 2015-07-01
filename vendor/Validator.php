<?php 

	/**
	*  Validator
	*  
	*  Valida los parámentros enviados según las reglas definidas para los valores esperados
	*
	*  @author Jesús Hernandez
	*  @version 2.1
	*/
	class Validator {

		private $errors = array();
		private $params = array();
		private $rules = array();
		private $required = 0;
		private $code = 200;
		
		/**
		*
		* Constructor
		*
		* @author Jesús Hernandez
		* @param String - Lista de parámetros que serán convertidos a objeto JSON para ser validados.
		* @param Array - Lista de reglas para validar los parámetros
		*
		*/
		function __construct($pars, $rules = array()) {

			// si los parámetros están vacíos o no son envíados
			if (strlen($pars) <= 0 || $pars == "{}") {
				$this->code = 400;
				$this->errors[] = "EMPTY_PARAMS_ERROR";
				return 0;
			} 

			$params = json_decode($pars);

			// si no es un objeto válido
			if(!$params){
				$this->code = 400;
				$this->errors[] = "NO_JSON_PARAMS_ERROR";
				return 0;
			}

			// Establecer parámetros que´podrian ser una inyección.
			$found = array("/</", "/>/");
			$replace = "";
			// Verificar cada parametro y escapar caracteres de una inyección.
			$t = array();
			foreach ($params as $key => $value) {
				$a = preg_replace($found, $replace, $value);
				$t [$key] = preg_replace('/\s\s+/', ' ', trim($a));
			}
			$params = $t;
			
			$this->formatParams($params);
			$this->rules = $rules;

			// se obtiene el número de parámetros que son requeridos
			foreach ( new ArrayIterator($this->rules) as $key => $value) {
				if( $this->rules[$key][0] )
					$this->required++;
			}

		}

		/**
		*
		* validate
		*
		* Valida los datos enviados según las reglas establecidas
		*
		* @author Jesús Hernandez
		* @return Array - Arreglo de datos después de la validación.
		*
		*/
		public function validate() {
			
			// se inicia el contador de parámetros requeridos enviados
			$required_count = 0;
			
			foreach (new ArrayIterator($this->params) as $key => $value) {
				
				// si el parámetro está en la lista de reglas se procede a la validación,
				// si no, se envía un error
				if (array_key_exists($key, $this->rules)) {
					
					// este parámetro que se envío es requerido y se cuenta
					if ($this->rules[$key][0])
						$required_count++;

					$rules = $this->rules[$key];

					if (count($rules) < 2) {
						$this->code = 500;
						$this->errors["INTERNAL_VALIDATION_ERROR"] = "Validation rules are not defined for '". $key . "'";
						return 0;
					}

					switch ($rules[1]) {
						case 'email':
							$this->validateEmail($key, $value, $rules);
							break;

						case 'string':
							$this->validateString($key, $value, $rules);
							break;

						case 'cp':
							$this->validateCp($key, $value, $rules);
							break;

						case 'integer':
							$this->validateInteger($key, $value, $rules);
							break;

						case 'double':
							$this->validateDouble($key, $value, $rules);
							break;

						case 'bool':
							$this->validateBool($key, $value, $rules);
							break;
						
						default:
							$this->code = 500;
							$this->errors["INTERNAL_VALIDATION_ERROR"] = "Data type `".$rules[1]."` in rules is not supported";
							break;
					}

				} else {
					$this->code = 400;
					$this->errors[$key] = "INVALID_PARAM_ERROR";
					return 0;
				}

			} // end switch

			return $this->params;
		}

		/**
		*
		* validateBool
		*
		* Valida que el valor sea de tipo "bool".
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		*
		*/
		public function validateBool($key, $value, $rules) {
			
			if (!$this->isBool($value)) {
				$this->code = 400;
				$this->errors[$key] = "Invalid value for `" . $key . "` field";
				return 0;
			}

			// si hay que cumplir con un valor específico.
			if (isset($rules[2])) {
				if ($rules[2] != $value) {
					$this->code = 400;
					$this->errors[$key] = "Invalid value for `" . $key . "` field";
					return 0;
				}
			}
		}

		/**
		*
		* validateDouble
		*
		* Valida que el valor sea de tipo "double"
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		* @param Array El conjunto de reglas para validar.
		*
		*/
		public function validateDouble($key, $value, $rules) {
			// validación por regexp. acepta que sea negativo.
			if (!preg_match('/^-?(?:\d+|\d*\.\d+)$/', $value)) {
				$this->code = 400;
				$this->errors[$key] = "Invalid value for `" . $key . "` field";
				return 0;
			}

			// si hay valor mínimo
			if ( isset($rules[2]) ) {
				// si hay un valor máximo
				if (isset($rules[3])) {
					// el valor mínimo debe ser mayor a cero y no mayor al valor máximo
					if($rules[2] > $rules[3]) {
						$this->code = 500;
						$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'min' value for '". $key . "'";
						return 0;
					}
					// el valor máximo debe ser mayor a cero y no mayor al valor mínimo
					if ($rules[3] < $rules[2]) {
						$this->code = 500;
						$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'max' value for '". $key . "'";
						return 0;
					}

					// validación por valor máximo
					if ( $rules[3] < $value ) {
						$this->code = 400;
						$this->errors[$key] = "Invalid 'max' value for `" . $key . "` field";
						return 0;
					}
				}

				// validación por valor mínimo
				if ( $rules[2] > $value ) {
					$this->code = 400;
					$this->errors[$key] = "Invalid 'min' value for `" . $key . "` field";
					return 0;
				}

			}

		}

		/**
		*
		* validateInteger
		*
		* Valida que el valor sea de tipo "integer"
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		* @param Array El conjunto de reglas para validar.
		*
		*/
		public function validateInteger($key, $value, $rules) {
			// validación por regexp. acepta que sea negativo.
			if (!preg_match('/^([1-9]|\-)[0-9]*$/', $value)) {
				$this->code = 400;
				$this->errors[$key] = "Invalid value for `" . $key . "` field";
				return 0;

			}

			// si hay valor mínimo
			if ( isset($rules[2]) ) {
				// si hay un valor máximo
				if (isset($rules[3])) {
					// el valor mínimo debe ser mayor a cero y no mayor al valor máximo
					if($rules[2] > $rules[3]) {
						$this->code = 500;
						$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'min' value for '". $key . "'";
						return 0;
					}
					// el valor máximo debe ser mayor a cero y no mayor al valor mínimo
					if ($rules[3] < $rules[2]) {
						$this->code = 500;
						$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'max' value for '". $key . "'";
						return 0;
					}

					// validación por valor máximo
					if ( $rules[3] < $value ) {
						$this->code = 400;
						$this->errors[$key] = "Invalid 'max' value for `" . $key . "` field";
						return 0;
					}
				}

				// validación por valor mínimo
				if ( $rules[2] > $value ) {
					$this->code = 400;
					$this->errors[$key] = "Invalid 'min' value for `" . $key . "` field";
					return 0;
				}


			}
		}

		/**
		*
		* validateCp
		*
		* Valida que el valor sea de tipo "cp" (5 dígitos)
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		*
		*/
		public function validateCp($key, $value) {
			// validación por regexp
			if (!preg_match('/[0-9]{5,}/', $value)) {
				$this->code = 400;
				$this->errors[$key] = "Invalid value for `" . $key . "` field";
				return 0;
			}
		}

		/**
		*
		* validateEmail
		*
		* Valida que el valor sea de tipo "email"
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		* @param Array El conjunto de reglas para validar.
		*
		*/
		private function validateEmail($key, $value, $rules) { 	
			// el valor mínimo debe ser mayor a cero y no mayor al valor máximo
			if($rules[2] < 0 || $rules[2] > $rules[3]) {
				$this->code = 500;
				$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'min' value for '". $key . "'";
				return 0;
			}
			// el valor máximo debe ser mayor a cero y no mayor al valor mínimo
			if ($rules[3] < 0 || $rules[3] < $rules[2]) {
				$this->code = 500;
				$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'max' value for '". $key . "'";
				return 0;
			}

			$min = $rules[2];
			$max = $rules[3];

			// validación de longitud mínima
			if ( strlen($value) < $min ) {
				$this->code = 400;
				$this->errors[$key] = "Invalid 'min' value for `" . $key . "` field";
				return 0;
			}
			// validación de longitud máxima
			if ( strlen($value) > $max) {
				$this->code = 400;
				$this->errors[$key] = "Invalid 'max' value for `" . $key . "` field";
				return 0;
			}
			// validación por filtro
			if ( filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE ) {
				$this->code = 400;
				$this->errors[$key] = "Invalid 'email' value for `" . $key . "` field";
				return 0;
			}

		}

		/**
		*
		* validateString
		*
		* Valida que el valor sea de tipo "String"
		*
		* @author Jesús Hernandez
		* @param String El nombre del índice en el arreglo de parámetros.
		* @param String El valor del dato a validar.
		* @param Array El conjunto de reglas para validar.
		*
		*/
		private function validateString($key, $value, $rules) {
			
			// si se establece un valor exacto como regla no se verifica por longitud
			if(!is_string($rules[2])){
				// Las longitudes deben de ser enteras
				if(!is_int($rules[2]) || !is_int($rules[3])){
					$this->code = 500;
					$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid rules format for '". $key . "'";
					return 0;
				}
				// el valor mínimo debe ser mayor a cero y no mayor al valor máximo
				if($rules[2] < 0 || $rules[2] > $rules[3]) {
					$this->code = 500;
					$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'min' value for '". $key . "'";
					return 0;
				}
				// el valor máximo debe ser mayor a cero y no mayor al valor mínimo
				if ($rules[3] < 0 || $rules[3] < $rules[2]) {
					$this->code = 500;
					$this->errors["INTERNAL_VALIDATION_ERROR"] = "Invalid 'max' value for '". $key . "'";
					return 0;
				} 

				$min = $rules[2];
				$max = $rules[3];

				// validación de longitud mínima
				if ( strlen($value) < $min ) {
					$this->code = 400;
					$this->errors[$key] = "Invalid 'min' value for `" . $key . "` field";
					return 0;
				}
				// validación de longitud máxima
				if ( strlen($value) > $max) {
					$this->code = 400;
					$this->errors[$key] = "Invalid 'max' value for `" . $key . "` field";
					return 0;
				}

				// validación por regexp
				if (!preg_match('/^[A-Za-z0-9áéíóúñÁÉÍÓÚ_#\.\-+[:space:]]*$/', $value)) {
					$this->code = 400;
					$this->errors[$key] = "Invalid value for `" . $key . "` field";
					return 0;
				}


			} else {

				if ($value != $rules[2]) {
					$this->code = 400;
					$this->errors[$key] = "Wrong value for " . $key . " field";
					return 0;
				}
				
			}	

		}

		/**
		*
		* formatParams
		*
		* Descompone el objeto de parámetros en un solo arreglo unidimensional para ser validado.
		* y lo asigna al atributo 'params'.
		* 
		* @author Jesús Hernandez
		* @param Array Lista de parámetros a ser descompuestos en un array si es que son compuestos.
		*              o de tipo objeto JSON.
		*/
		private function formatParams($params) {
			
			foreach ($params as $key => $value) {
				if(is_object($value)) {
					$this->formatParams($value);
				} else {
					$this->params[$key] = $value;
				}
			}
		}

		/**
		*
		* isBool Verifica si un valor es de tipo booleano.
		*
		* Verifica si un valor es booleano. Valores aceptados: 1, 0, '1', '0', true, false
		* Cadenas aceptadas como booleanos: 'true', 'false', 'yes', 'no'
		*
		* @author Jesús Hernandez
		* @param String  Valor a validar
		* @return Boolean
		*/
		private function isBool($val)
		{
			$booleans = array(1, 0, '1', '0', true, false);
			$literals = array('true', 'false', 'yes', 'no');
			foreach ($booleans as $bool) {
				if ($val === $bool) {
					return true;
				}
			}

			return in_array(strtolower($val), $literals);
		}

		/**
		*
		* getErrors
		*
		* Devuelve los errores almacenados
		* 
		* @author Jesús Hernandez
		* @return Array
		*
		*/
		public function getErrors()
		{
			return $this->errors;
		}

		/**
		*
		* getCode
		*
		* Devuelve el código de la respuesta generada durante el proceso de validación.
		*
		* @author Jesús Hernandez
		* @return Integer
		*/
		public function getCode()
		{
			return $this->code;
		}
	}

 ?>