<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \App\Page;
use \App\PageAdmin;
use \App\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

$app->get('/admin', function(){
	User::verifyLogin();
	$page = new PageAdmin();

	$page->setTpl("index");
});

$app->get('/admin/login', function(){
	$page = new PageAdmin([
		"header" => false,
		"footer" => false,
	]);

	$page->setTpl("login");
});

$app->post('/admin/login', function(){
	User::login($_POST['login'], $_POST['password']);

	header("Location: /admin");
	exit;
});

$app->get('/admin/logout', function(){
	User::logout();

	header("Location: /admin/login");
	exit;
});

$app->get('/admin/users', function(){
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl('users', [
		'users' => $users
	]);


});

$app->get('/admin/users/create', function(){
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl('users-create');


});

$app->get('/admin/users/:iduser/delete', function($idUser){
	User::verifyLogin();

	$user = new User();

	$user->get((int)$idUser);

	$user->delete();

	header('Location: /admin/users');
	exit;
	
});

$app->get('/admin/users/:iduser', function($idUser){
	User::verifyLogin();

	$user = new User();

	$user->get((int)$idUser);

	$page = new PageAdmin();

	$page->setTpl('users-update', [
		'user' => $user->getValues()
	]);

});

$app->post('/admin/users/create', function(){
	User::verifyLogin();

	$user = new User();

	$_POST['indadmin'] = (isset($_POST['inadmin']))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

$app->post('/admin/users/:iduser', function($idUser){
	User::verifyLogin();
	
	$user = new User();

	$user->get((int)$idUser);

	$_POST['indadmin'] = (isset($_POST['inadmin']))?1:0;

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});

$app->run();
