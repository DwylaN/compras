<?php
require_once "PHPMailer/PHPMailerAutoload.php";
require_once "pop3/class.pop3.php";
require_once "Validator_mailer.php";

/**
* PHPMailer multiple email Class Mailer.
* PHP Version 1.0
* @package PHPMailer
* @author Jesús Hernández Villalobos.
* @copyright 2014 Jesús Hernández Villalobos.
* @license D. R. ©; Centro de Tecnologías Unificadas 2013-2014.
* Prohibida su reproducción total o parcial, incluyendo
* cualquier medio electrónico o magnético.
* Lago Zurich No. 245, Torre Presa Falcón, Piso 19, Plaza Carso,
* Ampliación Granada, Miguel Hidalgo, México, Distrito Federal.
* 
*/

/**
* The class Mailer.
* 
* Esta clase recibe todos los destinatarios
* copias, copias ocultas(maximo 5 destinatarios respectivamente),
* arrays que contienen subject, from,
* from_name y adicional y opcional reply, reply_name.
* 
* Ademas la clase recibe rutas de archivos
* y html. Configura tipo de autenticacion, puerto, tipo de seguridad,
* dominio, usuario y contraseña de el servidor de correo asociado a la
* cuenta de correo utilizada para finalmente poder enviar correos.
* 
*
* @access public
*/
class Mailer{

	private $to = array();
	private $cc = array();
	private $cco = array();
	private $subject = "";
	private $from = "";
	private $from_name = "";
	private $body = "";
	private $attachment = array();
	private $embeddedimg = array();
	private $errors = ""; 
	private $warnings = "";
	private $port = "";
	private $smtp_secure = "";
	private $auth = "";
	private $host = "";
	private $username = "";
	private $password = "";
	private $t_validator = "";
	private $replyto = array();
	private $rp = "";

	function __construct(){

	}
	/**
    * Funcion que envia el correo
    * @param $to
    * @param $cc
    * @param $cco
    * @param $subject
    * @param $from
    * @param $body
    * Valida que los destinatarios con sus
    * respectivos nombres y asunto tengan
    * un formato válido.
    * 	
    * Se envia correo a los destinatarios recibidos 
    * Retorna boolean true al enviar correo exitoso.
    * @return boolean
    */
	public function Send($value = ''){
		$to = $this->to;	
		// Cuenta que los destinatarios to no sean mas de 5.
		$contador = count($to);
		if ($contador > 5) {
			$this->errors = "MAX_FIVE_RECIPIENTS";
			return 0;
		}
		// Se evoca la funcion validadora para comprobar
		// los formatos de correo y respectivos nombres.
		$t = $this->Valida_dest($to);

		if ($t == 0) {
			return 0;
		}
		
		$cc = $this->cc;
		// Cuenta que los destinatarios cc(si hubiese) no sean mas de 5.
		$contador = count($cc);
		if ($contador > 5) {
			$this->errors = "MAX_FIVE_COPYS";
			return 0;
		
		}
		// Se evoca la funcion validadora para comprobar
		// los formatos de correo y respectivos nombres(si hubiese).
		$c = $this->Valida_dest($cc);

		if ($c == 0) {
			return 0;
		}

		$cco = $this->cco;
		// Cuenta que los destinatarios cco(si hubiese) no sean mas de 5.
		$contador = count($cco);
		
		if ($contador > 20) {
			$this->errors = "MAX_TWENTY_BCC";
			return 0;
		
		}
		// Se evoca la funcion validadora para comprobar
		// los formatos de correo y respectivos nombres(si hubiese).
		$co = $this->Valida_dest($cco);

		if ($co == 0) {
			return 0;
		}

		$subject = $this->subject;
		// Se evoca la funcion validadora para comprobar 
		// el formato del asunto.
		if (!empty($subject)) {
			$s = $this->Valida_headers($subject);
			if ($s == 0){
				return 0;
			}
			foreach ($subject as $key => $value) {
				$subject = $value;
			}
		}

		

		$from = $this->from;
		if (!empty($from)) {
			// Se evoca la funcion validadora para comprobar 
			// el formato del correo y nombre del remitente.
			$def_from = 1;
			$f = $this->Valida_headers($from);

			if ($f == 0){
				return 0;
			}
			$from = array();
			foreach ($f as $key => $value) {
				$from[] = $value;
			}
		}else{
			$def_from = 0;
		}
		
		$replyto = $this->replyto;
		if (!empty($replyto)) {
			$rp = 1;
			// Se evoca la funcion validadora para comprobar 
			// el formato del correo y nombre para informar
			// sobre correos fallidos(si hubiese).
			$rt = $this->Valida_headers($replyto);

			if ($rt == 0){
				return 0;
			}

			$replyto = array();
			foreach ($rt as $key => $value) {
				$replyto[] = $value;
			}
		}else{
			$rp = 0;
		}
				
		$body = $this->body;
		// Recibimos las rutas y alias de los archivos adjuntos
		// necesarios a enviar en el correo (si hubiese).		
		$attachment = $this->attachment;
		if (!empty($attachment)) {
			$z = 2;
			$attachment_r = array();
			$attachment_t = array();
			foreach (new ArrayIterator($this->attachment) as $route => $type) {
    			$attachment_r[] = $route; 
    			$attachment_t[] = $type; 
    		}
    		
		}else{
    		$z = 1; 
    	}
    	// Recibimos las rutas y alias de las imagenes embebidas en un html 
		// necesarios a enviar en el correo (si hubiese).	
    	$embeddedimg = $this->embeddedimg;
		if (!empty($embeddedimg)) {
			$y = 2;
			$embeddedimg_r = array();
			$embeddedimg_t = array();
			foreach (new ArrayIterator($this->embeddedimg) as $route => $type) {
    			$embeddedimg_r[] = $route; 
    			$embeddedimg_t[] = $type; 
    		}
    		
		}else{
    		$y = 1; 
    	}
    	// si se deseára configurar los parametros de el dominio
    	// y cuenta de correo default, aquí se recibirian los parámetros.
		$port = $this->port;
    	$smtp_secure = $this->smtp_secure;
    	$auth = $this->auth;
    	$host = $this->host;
    	$username = $this->username;
    	$password = $this->password;

		$mail = new phpmailer();
		
		$mail->CharSet = 'UTF-8';
		
		$mail->Mailer = "smtp";

		$mail->IsSMTP();
 			
 		ini_set('max_execution_time', 600);	
  
		$mail->SMTPAuth = true;
		// si se indica un puerto este se utilizara,
		// de lo contrario su usará el default.	
		if (empty($port)) {
			$mail->Port = 465;
		}else{
			$mail->Port = $port;
		}
		// si se indica un tipo de seguridad este se utilizara,
		// de lo contrario su usará el default.	
		if(empty($smtp_secure)){
			$mail->SMTPSecure = 'ssl';
		}else{
			$mail->SMTPSecure = $smtp_secure;
		}
		// si se indica un cambio en la autenticación este se utilizara,
		// de lo contrario su usará el default.
		if (empty($auth)) {
			$mail->SMTPAuth = true;
		}else{
			$mail->SMTPAuth = $auth;
		}
		// si se indica un host este se utilizara,
		// de lo contrario su usará el default.
		if (empty($host)) {
		    $mail->Host = "securemail.aplus.net";
		}else{
			$mail->Host = $host;
		}
		// si se indica un usuario este se utilizara,
		// de lo contrario su usará el default.
		if (empty($username)) {
			$mail->Username = "basededatos@globalcorporation.cc";
		}else{
			$mail->Username = $username;
		}
		// si se indica un password este se utilizara,
		// de lo contrario su usará el default.  
		if (empty($password)) {
		  	$mail->Password = "kIU8a#4i";
		}else{
			$mail->Password = $password;
		}

		$mail->Subject = $subject;

		if ($def_from == 1) {
			$mail->SetFrom($from[1], $from[0]);
		}else{
			$mail->SetFrom('basededatos@globalcorporation.cc', 'Global Corporation');
		}
		
		if ($rp == 1) {
			$mail->AddReplyTo($replyto[1], $replyto[0]);
		}
		
		
		$mail->Body = " ";
		$mail->MsgHTML($body);

		if ($z == 2) {
    		for ($a=0; $a < count($attachment_r); $a++) { 
    			$mail->AddAttachment($attachment_r[$a], $attachment_t[$a]);
    		}
    	}

    		
		if ($y == 2) {
    		for ($a=0; $a < count($embeddedimg_r); $a++) { 
    			$mail->AddEmbeddedImage($embeddedimg_r[$a], $embeddedimg_t[$a]);
    		}
    	}

		for ($i=0; $i < count($to); $i++) { 
			$a = $to[$i];
			$mail->AddAddress($a['direccion'], $a['nombre']);
		}

		for ($j=0; $j < count($cc); $j++) { 
			$a = $cc[$j];
			$mail->AddCC($a['direccion'], $a['nombre']);  
		}

		for ($k=0; $k < count($cco); $k++) { 
			$a = $cco[$k];
			$mail->AddBCC($a['direccion'], $a['nombre']);  
		}

		$mail->IsHTML(true);

    	if($mail->Send()){
    		return true;
    	}else{
    		$this->errors = "SEND_MAIL_ERROR ".$mail->ErrorInfo;
			return 0;
    	}

	}
	
	/**
	*
	* Valida dest
	*
	* Obtiene parametros de los destinatarios
	* y los valída.
	* 
	* Valida los datos enviados según las reglas establecidas.
	*
	* @author Hiram E. Pérez
	* @param arrays de strings e emails
	* @return arrays
	*
	*/
	private function Valida_dest($valor){
		$this->val = $valor;
		$vald = $this->val;
		$rules = array(
			'nombre' => array(false, "string",0, 99),
			'direccion' => array(true, "email",1, 99)

		);		

		

		$nombres = array();
		$dirs = array();

		for ($i=0; $i < count($vald); $i++) { 
			
			$a = $vald[$i];
            $nombres[] = $a['nombre'];
            $dirs[] = $a['direccion'];
        	
			if (strlen($a['nombre']) == 0) {
			 	$this->warnings = " WARNING_'".$a['direccion']."'_WHITOUT_NAME";
			}

			$js = json_encode($a);

			$v = new Validator_Mailer($js, $rules);
			$params = $v->validate();
		
			if(count($v->getErrors()) > 0){
				
				$this->errors= $v->getErrors();
				return 0;				
			}

		}
		

		return $vald;

	} 

	/**
	*
	* Valida_headers
	*
	* Obtiene parametros de los headers
	* y los valída.
	* 
	* Valida los datos enviados según las reglas establecidas.
	*
	* @author Hiram E. Pérez
	* @param arrays de strings e emails
	* @return arrays
	*
	*/
	private function Valida_headers($valor){

		$this->headers = $valor;
		$head = $this->headers;

		$h = json_encode($head);

		$rules = array(
			'subject' => array(false, "string",0, 99),
			'direccion' => array(true, "email",1, 99),
			'nombre' => array(false, "string",0, 99)
			

		);

		$v = new Validator_Mailer($h, $rules);

		$params = $v->validate();
		
		if(count($v->getErrors()) > 0){
			
			$this->errors= $v->getErrors();
			return 0;				
		}

		return $head;

	}


		/**
	*
	* getParams
	*
	* Retorna parametros requeridos almacenados
	* 
	* @author Jesús
	* @param String y arrays
	* @return String y arrays
	*
	*/
	public function getTo()
	{
		return $this->to;
	}
	public function getCc()
	{
		return $this->cc;
	}
	public function getCco()
	{
		return $this->cco;
	}
	public function getSubject()
	{
		return $this->subject;
	}
	public function getFrom()
	{
		return $this->from;
	}
	public function getFrom_name()
	{
		return $this->from_name;
	}	
	public function getBody()
	{
		return $this->body;
	}
	public function getAttachment()
	{
		return $this->attachment;
	}
	public function getEmbeddedImg()
	{
		return $this->embeddedImg;
	}
	public function getPort()
	{
		return $this->port;
	}
	public function getSMTP_secure()
	{
		return $this->smtp_secure;
	}
	public function getAuth()
	{
		return $this->auth;
	}
	public function getHost()
	{
		return $this->host;
	}
	public function getUserName()
	{
		return $this->username;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function getTo_validator()
	{
		return $this->password;
	}
	public function getReplyTo()
	{
		return $this->replyto;
	}





	/**
	*
	* setParams
	*
	* Obtiene parametros de los objetos
	* 
	* @author Jesús
	* @param String y arrays
	* @return String y arrays
	*
	*/
	public function setto($valor)
	{
		$this->to = $valor;

		$contador = count($valor);	
		if ($contador == 0) {
			//$this->code = 400;
			$this->errors = "EMPTY_TO_ERROR";
			return 0;
		} 
			
	}
	public function setcc($valor)
	{
		$this->cc = $valor;
	}
	public function setcco($valor)
	{
		$this->cco = $valor;
	}	
	public function setSubject($valor)
	{
		$this->subject = $valor;
	}
	public function setFrom($valor)
	{
		$this->from = $valor;
		if (empty($valor)) {
			//$this->code = 400;
			$this->errors = "EMPTY_FROM_ERROR";
			return 0;
		}
	}
	public function setFrom_name($valor)
	{
		$this->from_name = $valor;		
	}	
	public function setBody($valor)
	{
		$this->body = $valor;
	}
	public function setAttachment($valor)
	{
		$this->attachment = $valor;
	}
	public function setEmbeddedImg($valor)
	{
		$this->embeddedimg = $valor;
	}
	public function setPort($valor)
	{
		$this->port = $valor;
	}
	public function setSMTP_secure($valor)
	{
		$this->smtp_secure = $valor;
	}
	public function setAuth($valor)
	{
		$this->auth = $valor;
	}
	public function setHost($valor)
	{
		$this->host = $valor;
	}
	public function setUserName($valor)
	{
		$this->username = $valor;
	}
	public function setPassword($valor)
	{
		$this->password = $valor;
	}
	public function setTo_validator($valor)
	{
		$this->password = $valor;
	}
	public function setReplyTo($valor)
	{
		$this->replyto = $valor;
	}


	/**
	*
	* getError
	*
	* Retorna errores almacenados
	* 
	* @author Jesús
	* @return String
	*
	*/
	public function getError()
	{
		return $this->errors;
	}


	/**
	*
	* getWarnings
	*
	* Retorna advertencias almacenadas
	* 
	* @author Jesús
	* @return String
	*
	*/
	public function getWarnings()
	{
		return $this->warnings;
	}
	

}

?>