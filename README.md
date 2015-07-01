# BackEnd Compras

[![Build Status](https://github.com/DwylaN/compras)](https://github.com/DwylaN/compras)
[![Dependency Setatus](https://github.com/DwylaN/compras)](https://github.com/DwylaN/compras)
[![Coverage Status](https://github.com/DwylaN/compras)](https://github.com/DwylaN/compras)

[Bakcend](https://github.com/DwylaN/compras) para modulo de Simulación de sistemas materia de  [Upiicsa](http://www.upiicsa.ipn.mx/Paginas/inicio.aspx)
hecho en PHP SQL relacional mysql/psql


## GET / Muestra todos los proveedores registrados

    http://localhost/compras/api/proveedores

    Método: GET
    Respuesta : JSON


## GET /:rfc Muestra información de un proveedor en específico

    http://localhost/compras/api/proveedores/:HFDG910565O8I

    Método: GET
    Respuesta : JSON

     Ejemplo respuesta


	
		"succes":true,
		"message":{
			"id":"1",
			"rfc":"HFDG910565O8I",
			"razon_social":"razón",
			"direccion":"su dirección",
			"telefono":"56465464",
			"calificacion":"9",
			"vendedor":"Adrian"
		}
	

## POST / Crea un vendedor

    http://localhost/compras/api/proveedores/

    Método: POST
    Respuesta : JSON

     Ejemplo request


	
		{
			"id":"1",
			"rfc":"HFDG910565O8I",
			"razon_social":"razón",
			"direccion":"su dirección",
			"telefono":"56465464",
			"calificacion":"9",
			"vendedor":"Adrian"
		}
	

     Ejemplo respuesta


	
		"succes":true,
		"message":{
			"id":"1",
			"rfc":"HFDG910565O8I",
			"razon_social":"razón",
			"direccion":"su dirección",
			"telefono":"56465464",
			"calificacion":"9",
			"vendedor":"Adrian"
		}
	


## PUT / Actualiza un vendedor

http://localhost/compras/api/proveedores/

Método: PUT
Respuesta : JSON

 Ejemplo request



	{
		"id":"1",
		"rfc":"HFDG910565O8I",
		"razon_social":"razón",
		"direccion":"su dirección",
		"telefono":"56465464",
		"calificacion":"9",
		"vendedor":"Adrian 2"
	}


 Ejemplo respuesta



	"succes":true,
	"message":{
		"id":"1",
		"rfc":"HFDG910565O8I",
		"razon_social":"razón",
		"direccion":"su dirección",
		"telefono":"56465464",
		"calificacion":"9",
		"vendedor":"Adrian 2"
	}


## Credits

  - [Jesús Hernández](http://github.com/JesusHV)

## License

The MIT License (MIT)

Copyright (c) 2015 Nicholas Penree

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE