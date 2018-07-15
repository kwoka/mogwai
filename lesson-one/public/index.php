<?php
/**
 * Entry point for the application.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
require __DIR__ . '/../vendor/autoload.php';

use App\Request;
use App\Response;
use App\Validator;
use App\App;

// load app settings
if (file_exists(__DIR__ . '/../config/settings.php')) {
	$settings = require __DIR__ . '/../config/settings.php';
} else {
	$settings = require __DIR__ . '/../config/settings.php.dist';
}

$app = new App($settings);

$container = $app->getContainer();

/**
 * Define app routes.
 */
$app->route(['POST', 'OPTIONS'], '/login', function(Request $request, Response $response) use ($container) {
	// get and validate parameters
	$postBody = $request->getParsedBody();
	if (!Validator::validate($postBody, [
		'email' => 'required',
		'email' => 'email',
		'password' => 'required'
	])) {
		return $response->withStatus(400)->withJson(['returnCode' => 1, 'error' => 'Invalid Parameters'])->output();
	}

	try {
		// query the database for the user info to confirm login
		$conn = new \PDO("mysql:host={$container['settings']['db']['host']};dbname={$container['settings']['db']['database']}", $container['settings']['db']['user'], $container['settings']['db']['pass']);
		$stmt = $conn->prepare('SELECT id, password FROM user WHERE email = ?');
		$stmt->execute([$postBody['email']]);
		$user = $stmt->fetch(\PDO::FETCH_ASSOC);
		// not found by that email address
		if (!$user) {
			return $response->withStatus(404)->withJson(['returnCode' => 2, 'error' => 'User Not Found.'])->output();
		}
		// verify the password match the database hash
		if (!password_verify($postBody['password'], $user['password'])) {
			return $response->withStatus(401)->withJson(['returnCode' => 3, 'error' => 'Invalid Credentials.'])->output();
		}
		// success!
		return $response->withJson(['returnCode' => 0, 'userId' => $user['id']])->output();
	} catch(\PDOException $e) {
		return $response->withStatus(500)->withJson(['returnCode' => 4, 'error' => 'Database Error Occurred.'])->output();
	}
});

$app->run();
