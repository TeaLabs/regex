<?php
namespace Tea\Regex;

class Expression
{
	/**
	 * @var String
	 */
	protected $pattern;

	/**
	 * @var String
	 */
	protected $modifiers;

	/**
	 * @var String
	 */
	protected $delimiter;

	/**
	 * @var String
	 */
	protected $_pregMatchFlags;

	/**
	 * @var String
	 */
	protected $_method = "preg_match";

	public function __construct($pattern, $modifiers = null, $delimiter = null, $pregMatchFlags = null)
	{
		$this->pattern  = $pattern;
		$this->modifiers = $modifiers ?: Config::modifiers();
		$this->delimiter = $delimiter ?: Config::delimiter();
		$this->modifiers = $flags;
		$this->_pregMatchFlags = $pregMatchFlags;

		if (strpos($this->_flags, "g") !== false) {
			$this->_flags  = str_replace("g", "", $this->_flags);
			$this->_method = "preg_match_all";
		}
	}

	/**
	 * @return String
	 */
	public function __toString()
	{
		return $this->getExpression();
	}

	/**
	 * @return String
	 */
	public function getExpression()
	{
		return $this->_expr;
	}

	/**
	 * @return String
	 */
	public function getFlags()
	{
		return $this->_flags;
	}

	/**
	 * alias for matches
	 *
	 * @deprecated
	 * @param $string
	 * @return bool
	 */
	public function test($string)
	{
		return $this->matches($string);
	}

	/**
	 * check string w/ preg_match
	 *
	 * @param $string
	 * @return bool
	 */
	public function matches($string)
	{
		$matches = array();

		return (bool)call_user_func_array(
			$this->_method,
			array(
				sprintf("/%s/%s", $this->_expr, $this->_flags),
				$string,
				&$matches,
				$this->_pregMatchFlags ?: null,
			)
		);
	}

	public function exec($haystack)
	{
		return $this->findIn($haystack);
	}

	/**
	 * execute preg_match, return matches
	 *
	 * @param $haystack
	 * @return array
	 */
	public function findIn($haystack)
	{
		$matches = array();
		call_user_func_array(
			$this->_method,
			array(
				sprintf("/%s/%s", $this->_expr, $this->_flags),
				$haystack,
				&$matches,
				$this->_pregMatchFlags ?: null,
			)
		);

		if (!isset($matches[1]) && isset($matches[0]) && is_array($matches[0])) {
			return $matches[0];
		}

		return $matches;
	}


	public function replace($string, $callback)
	{
		return preg_replace_callback(
			sprintf("/%s/%s", $this->_expr, $this->_flags),
			function ($hit) use ($callback) {
				return call_user_func($callback, $hit[0]);
			},
			$string
		);
	}

}