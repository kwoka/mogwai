<?php
namespace App;

/**
 * Class for generating and authorizing tokens.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class AuthToken
{
	/**
	 * Generates a new token.
	 * @param array $payload token payload assoc array
	 * @param string $key hash key
	 * @param string $hashAlgo hashing algorithm
	 * @return string $token encoded token
	 */
	public static function encode(array $payload, $key, $hashAlgo = "AES-128-CBC")
	{
		$ivlen = openssl_cipher_iv_length($hashAlgo);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$encodedText = openssl_encrypt(json_encode($payload), $hashAlgo, $key, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $encodedText, $key, true);
		return base64_encode($iv.$hmac.$encodedText);
	}

	/**
	 * Decodes a token into payload assoc array.
	 * @param string $token
	 * @param string $key
	 * @return array|false when not a valid token
	 */
	public static function decode($token, $key, $hashAlgo = "AES-128-CBC")
	{
		$c = base64_decode($token);
		$ivlen = openssl_cipher_iv_length($hashAlgo);
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, 32);
		$encodedText = substr($c, $ivlen + 32);
		$decodedToken = openssl_decrypt($encodedText, $hashAlgo, $key, OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $endodedText, $key, true);
		if (!hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
		{
			return false;
		}
		$payload = json_decode($decodedToken, true);
		if ($payload['expires'] < time()) {
			return false;
		}
		return $payload;
	}
}
