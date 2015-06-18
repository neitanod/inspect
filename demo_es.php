<?php
require_once("Inspect.php");




echo "<h2>INSPECCIONANDO TIPOS ESCALARES DE PHP</h2>";


$long_text = '

Descripción

  Inspect::view() - Permite explorar el contenido de una variable de PHP usando
                    el browser (mediante una interfaz javascript).
  Inspect::dump() - Vuelca información detallada sobre el contenido de una
                    variable de PHP (imprime la descripción como un texto).

Ambas funciones son intercambiables.  La única diferencia entre ellas es el
formato de la información de salida.

Sintaxis

  variant Inspect::view(string $name, variant $element)
  variant Inspect::view(variant $element)

  variant Inspect::dump(string $name, variant $element)
  variant Inspect::dump(variant $element)

Ambas funciones devuelven el valor $element recibido para poder ser llamadas
inline.  Por ejemplo, si tenemos:

 $myvar = $myObject->someMethod();

Podemos inspeccionar $myObject de la siguiente manera:

  $myvar = Inspect::view($myObject)->someMethod();


Inspect::view() muestra el contenido del elemento $element,
que puede ser una variable de PHP de cualquier tipo.

El parámetro $name debe informarle a Inspect::view() el nombre de
la variable que se le está pasando en el segundo parámetro.

Ej:  Inspect::view("text", $text);

Si se omite el primer parámetro -es decir, cuando se usa la sintaxis alternativa-
la función intentará capturar la expresión que se usó en la llamada:

  Inspect::view(strtoupper($myVar));

En este ejemplo, el valor a inspeccionar será una cadena en mayúsculas, mientras
que el nombre descriptivo de la expresión recibida será la cadena
"strtoupper($myVar)".

El parámetro $name también puede usarse como etiqueta para el output (una frase
identificatoria), pero si Inspect::view() no conoce el nombre de la variable que 
está inspeccionando no ofrecerá correctamente la funcionalidad de ipath.

Ej:  Inspect::view("Datos del usuario:", $this->user);

La funcionalidad de ipath consiste en que el usuario pueda
hacer click en el símbolo > que precede a cada línea de la
inspección y obtener el nombre de la variable que se está
viendo en esa línea (para poder manipularla fácilmente con
PHP).  Esto es útil cuando se exploran ramas profundas dentro
de un objeto o array.


';

echo "<h3>Ejemplo usando Inspect::view(\$long_text):</h3>\n";
Inspect::view($long_text);
echo "<h3>Ejemplo usando Inspect::dump(\$long_text):</h3>\n<textarea cols=\"120\" rows=\"70\">\n";
Inspect::dump($long_text);
echo "\n</textarea>";

$short_text = "Este es un texto corto con acentos.  ÁáÉé.  Está codificado en ISO-8859-1.";
echo "<h3>Ejemplo usando Inspect::view(\$short_text):</h3>\n";
Inspect::view($short_text);
echo "<h3>Ejemplo usando Inspect::dump(\$short_text):</h3>\n<textarea cols=\"120\" rows=\"6\">\n";
Inspect::dump($short_text);
echo "\n</textarea>";

$integer = 1000;
Inspect::view($integer);

$decimal = 10.50;
Inspect::view($decimal);

echo "<h3>Ejemplo usando Inspect::dump(\$decimal):</h3>\n<textarea cols=\"120\" rows=\"5\">\n";
Inspect::dump($decimal);
echo "\n</textarea>";

$text = 'This is a string';
Inspect::view($text);

$array = array('a'=>'A','b'=>'B',0,1,2,3);
Inspect::view($array);

echo "<h2>INSPECCIONANDO OBJETOS NATIVOS DE PHP</h2>";

$directoryIterator = new DirectoryIterator(".");
Inspect::view("directoryIterator", $directoryIterator);

$reflectionParameter = new ReflectionParameter('substr',1);
Inspect::view("reflectionParameter",$reflectionParameter);
echo "<h3>Ejemplo usando Inspect::dump():</h3>\n<textarea cols=\"120\" rows=\"25\">\n";
Inspect::dump("reflectionParameter",$reflectionParameter);
echo "\n</textarea>";




echo "<h2>INSPECCIONANDO OBJETOS DEFINIDOS POR EL USUARIO:</h2>";
class DemoClass extends DirectoryIterator{
	private $private_property_cannot_show_value = 5;
	public $public_property_bool_is_dir = 6;
	public $public_property_string = "This is a public string";
	public function __construct($path = "."){
	  parent::__construct($path);
	  $this->public_property_bool_is_dir = $this->isDir();
	}
	private static function privateFunction(array $param){
		return false;
	}
}
$demoObject = new DemoClass();

Inspect::view('demoObject', $demoObject);

echo "<h3>Ejemplo usando Inspect::dump():</h3>\n<textarea cols=\"120\" rows=\"25\">\n";
Inspect::dump('demoObject', $demoObject);
echo "\n</textarea>";



echo "<h2>REFERENCIAS CIRCULARES EN ARRAYS U OBJETOS:</h2>";

$array1 = array(
'Vamos a generar una referencia circular dentro de un array',
'y ver si el inspector se da cuenta...',
'El elemento que le sigue a este contendrá un puntero a $array1',
'puntero_a_array1' => 1);
$array1['puntero_a_array1'] = &$array1;
Inspect::view('array1', $array1);

$array2 = array(
'Ahora hagamos una referencia circular usando ',
'dos arrays que se referencien entre si...',
'puntero_a_array3' => 1,
'mas contenido después de la referencia circular.'
);
$array3 = array(
'Este es el $array3, que contiene una referencia a $array2.',
'puntero_a_array2' => &$array2
);
$array2['puntero_a_array3'] = &$array3;
Inspect::view('array2', $array2);
Inspect::view('array3', $array3);




echo "<h2>EXPLORANDO UN ARRAY CASTEADO A OBJETO:</h2>";

$obj = (object) array('foo' => 'bar', 'property' => 'value',
'explanation' => "Este objeto fué creado con la línea:
\$obj = (object) array('foo' => 'bar', 'property' => 'value'...");

Inspect::view("obj", $obj);


echo "<h2>INSPECCIONANDO UN OBJETO CREADO ON THE FLY:</h2>";

$e->f->g->h->i = 'Este objeto fué creado con la línea:
$e->f->g->h->i = "Este objeto...';

Inspect::view('e', $e);


class A {
	private $ccpriv = "CCPRIV";
	public $ccpub = "CCPUB";
}
class B extends A {
}

class C extends B {
	public $var;
	public $xxx = array("Hola","Mundo",array("hola"=>"mundo","Foobar"=>array(5=>"Foo",6=>"Bar",
	"Este objeto es de la clase A, que hereda de la clase B, que a su vez hereda de la clase C.
Todo esto debería verse en el nodo Ancestors.
Además, por tratarse de una clase del usuario el nodo Definition
nos dice en qué archivo está el código fuente.",true,false,123,12.5)));
	private $bar;
	protected $foo;
	public function test(array $arg1, $arg2 = "Saraza") {
		return true;
	}
	private function test2(array $arg1, $arg2 = null) {
		return false;
	}
}



function a($param1 = "nada"){

	$bt = debug_backtrace();
  Inspect::view("bt",$bt);

	$a = new A; $a->var = "Foo Bar";
  Inspect::view('a', $a);
  Inspect::view("A Object Vars", get_object_vars($a));
}

function b($a = "optativo"){ a("algo");}

Inspect::view("Clases declaradas: ",get_declared_classes());
Inspect::view("Funciones definidas: ",get_defined_functions());
