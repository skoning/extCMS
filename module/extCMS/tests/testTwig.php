<?php
require_once __DIR__ . '/../../../vendor/twig/twig/lib/Twig/Autoloader.php';
class Project_Twig_Extension extends Twig_Extension
{
  public function getTokenParsers()
  {
    return array(new TokenParser());
  }
}
class TokenParser extends Twig_TokenParser {
  
}
Twig_Autoloader::register();

$loader = new Twig_Loader_Array(array(
    'index.html' => 'Hello {{ name }}!',
));
$twig = new Twig_Environment($loader);
echo $twig->render('index.html', array('name' => 'Fabien'));