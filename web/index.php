<?php

require('../vendor/autoload.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/cowsay', function() use($app) {
  $app['monolog']->addDebug('cowsay');
  return "<pre>".\Cowsayphp\Cow::say("Cool beans")."</pre>";
});

$app->get('/webhook', function() use($app) {
  $app['monolog']->addDebug('webhook!');

  $challenge = $_REQUEST['hub_challenge'];
  $verify_token = $_REQUEST['hub_verify_token'];

  if ($verify_token === 'castlevania') {
    return $challenge;
  }

  return "FAILURE :'(";
});

$app->post('/webhook', function (?Request $request) {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($data, true));

    return new Response('', 200);
});

$app->run();
