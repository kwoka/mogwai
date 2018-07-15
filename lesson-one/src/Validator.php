<?php
namespace App;

/**
 * Validates and sanitizes input.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Validator
{
	/**
	 * Contains validation errors.
	 * @var array
	 */
	private static $errors = [];

	/**
	 * Performs validation and returns whether or not all rules matched.
	 * @param array $data data to be validated
	 * @param array $rules validation rules
	 * @return bool whether or not validation passed
	 * @throws \Exception invalid rule supplied
	 */
	public static function validate(array $data, array $rules)
	{
		self::$errors = [];
		// execute all rules
		foreach ($rules as $field => $rule) {
			if (!method_exists(get_called_class(), $rule)) {
				throw new \Exception('Invalid rule: ' . $rule);
			}
			call_user_func([get_called_class(), $rule], $field, $data);
		}
		return count(self::$errors) == 0;
	}

	/**
	 * Adds an error to the list of errors.
	 * @param string $field name of the field that failed validation
	 * @param string $error error description
	 */
	public static function addError($field, $error)
	{
		if (!isset(self::$errors[$field])) {
			self::$errors[$field] = [$error];
		} else {
			self::$errors[$field][] = $error;
		}
	}

	/**
	 * Ensures the parameter is required.
	 * @param string $field name of the field
	 * @param array $data
	 * @return bool
	 */
	public static function required($field, $data)
	{
		if (empty($data[$field])) {
			self::addError($field, 'This field is required.');
			return false;
		}
		return true;
	}

	/**
	 * Ensures the parameters is an email address.
	 * @param string $field name of the field
	 * @param array $data
	 * @return bool
	 */
	public static function email($field, $data)
	{
		if (isset($data[$field])) {
			if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
				self::addError($field, 'Not an email address.');
				return false;
			}
		}
		return true;
	}
}
