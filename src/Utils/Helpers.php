<?php
namespace Tea\Regex\Utils;

use Closure;
use ArrayAccess;
use Traversable;
use InvalidArgumentException;

class Helpers
{

	/**
	 * Check if the given flag is set in flags.
	 *
	 * @param int $flag
	 * @param int $flags
	 * @return bool
	 */
	public static function hasFlag($flag, $flags)
	{
		return (((int) $flags) & ((int) $flag)) === ((int) $flag);
	}

	/**
	 * Check if the given flag is set in flags.
	 *
	 * @param int $flag
	 * @param int $flags
	 * @return bool
	 */
	public static function removeFlag($flag, $flags)
	{
		return $flags & ~((int) $flag);
	}

	/**
	 * Determine whether a value can be casted to string. Returns true if value is a
	 * scalar (String, Integer, Float, Boolean etc.), null or if it's an object that
	 * implements the __toString() method. Otherwise, returns false.
	 *
	 * @param  mixed   $value
	 * @return bool
	 */
	public static function isStringable($value)
	{
		return is_string($value)
				|| is_null($value)
				|| (is_object($value) && method_exists($value, '__toString'))
				|| is_scalar($value);
	}

	/**
	 * Determine whether a value is iterable and not a string.
	 *
	 * @param  mixed   $value
	 * @return bool
	 */
	public static function isNoneStringIterable($value)
	{
		return is_array($value) || (is_iterable($value) && !static::isStringable($value));
	}

	public static function implodeIterable($iterable, $withKeys = false, $glue = null, $prefix = '[', $suffix = ']')
	{
		$results = [];
		foreach ($iterable as $key => $value) {
			$value = static::isNoneStringIterable($value)
					? static::implodeIterable($value, $withKeys, $glue, $prefix, $suffix)
					: (string) $value;
			$results[] = $withKeys ? "{$key} => {$value}" : $value;
		}

		if(is_null($glue)) $glue = ', ';
		return $prefix.join($glue, $results).$suffix;
	}

	public static function iterableToArray($iterable)
	{
		return is_array($iterable) ? $iterable : iterator_to_array($iterable);
	}

	public static function toArray($object)
	{
		if (!is_object($object))
			return (array) $object;
		elseif(method_exists($object, '__toString'))
			return [$object];
		elseif ($object instanceof Traversable)
			return iterator_to_array($object);
		else
			return (array) $object;
	}

	public static function isArrayAccessible($object)
	{
		return is_array($object) || $object instanceof ArrayAccess;
	}

	public static function value($object)
	{
		return $object instanceof Closure ? $object() : $object;
	}

	public static function iterFirst($array, callable $callback = null, $default = null)
	{
		if (is_null($callback)) {
			if (empty($array)) {
				return static::value($default);
			}

			foreach ($array as $item) {
				return $item;
			}
		}

		foreach ($array as $key => $value) {
			if (call_user_func($callback, $value, $key)) {
				return $value;
			}
		}

		return static::value($default);
	}

	public static function type($value)
	{
		return is_object($object) ? get_class($value) : gettype($value);
	}
}

