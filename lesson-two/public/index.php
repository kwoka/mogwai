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
use App\DB;
use App\AuthToken;

// load app settings
if (file_exists(__DIR__ . '/../config/settings.php')) {
	$settings = require __DIR__ . '/../config/settings.php';
} else {
	$settings = require __DIR__ . '/../config/settings.php.dist';
}

$app = new App($settings);

$container = $app->getContainer();

// attach database model class
$container['db'] = new DB($container['settings']['db']);

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
		$statement = $container['db']->query('SELECT id, password FROM user WHERE email = ?', [$postBody['email']]);
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
		return $response->withJson(['returnCode' => 0, 'userId' => $user['id'], 'token' => AuthToken::encode(['userId' => $user['id'], 'expires' => strtotime('+1 day')], $container['authToken']['secret'])])->output();
	} catch(\PDOException $e) {
		return $response->withStatus(500)->withJson(['returnCode' => 4, 'error' => 'Database Error Occurred.'])->output();
	}
});

$app->route(['POST', 'OPTIONS'], '/users/create', function(Request $request, Response $response) use ($container) {
	// check for the auth header
	$authToken = $request->getRequestHeader('Authorization');
	if (!$authToken) {
		return $response->withStatus(401)->withJson(['returnCode' => 11, 'error' => 'You are not authorized to make this request.'])->output();
	}
	// decode the token
	$decodedToken = AuthToken::decode($authToken, $container['settings']['authToken']['secret']);
	if (!$decodedToken) {
		return $response->withStatus(401)->withJson(['returnCode' => 12, 'error' => 'Your token is invalid, unauthorized.'])->output();
	}
	$postBody = $request->getParsedBody();
	if (!Validator::validate($postBody, [
		'email' => 'required',
		'email' => 'email',
		'firstName' => 'required',
		'lastName' => 'required',
		'password' => 'required',
		'confirmPassword' => 'required'
	])) {
		return $response->withStatus(400)->withJson(['returnCode' => 13, 'error' => 'Invalid Parameters.'])->output();
	}
	// check if the password and confirm password match
	if ($postBody['password'] != $postBody['confirmPassword']) {
		return $response->withStatus(400)->withJson(['returnCode' => 14, 'error' => 'Password and password confirmation did not match.')->output();
	}
	try {
		// check if a user by that username already exists in the database.
		$statement = $container['db']->select('SELECT id FROM user WHERE email = ?'[$postBody['email']]);
		$user = $statement->fetch(\PDO::FETCH_ASSOC);
		if ($user) {
			return $response->withStatus(400)->withJson(['returnCode' => 14, 'error' => 'That email address is already in use.'])->output();
		}
		// insert the new user account
		$statement = $container['db']->query('INSERT INTO user (email, first_name, last_name, password) VALUES (?, ?, ?, ?)', [
			$postBody['email'], $postBody['firstName'], $postBody['lastName'], password_hash($postBody['password'], PASSWORD_DEFAULT)
		]);
		$userId = $container['db']->lastInsertId();
		if (!$userId) {
			return $response->withStatus(500)->withJson(['returnCode' => 15, 'error' => 'Failed to add the new user to the database'])->output();
		}
		return $response->withJson(['returnCode' => 0, 'userId' => $userId])->output();
	} catch(\PDOException $e) {
		return $response->withStatus(500)->withJson(['returnCode' => 16, 'error' => 'A database error occurred.'])->output();
	}
});

$app->run();
