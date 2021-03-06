<?php
namespace Tea\Regex\Tests;

use Tea\Regex\RegexStatic as Regex;
use Tea\Regex\Modifiers;
use Tea\Regex\Builder;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
	protected $a = 'aaa';
	protected $A = 'AAA';

	public function allProvider()
	{
		return [
			[[1.4, '3.8'], '/^(\d+)?\.\d+$/u', [1, 'sds' ,1.4, 3, '3.8', 'a122.4', 'i5']],
			[['a122', 'i5'], '/^\w+\d+$/u', [1, 'sds' ,1.4, 3, '3.8', 'ab122.4', 'a122', 'i5']],
			[['sds', 'ab122.4', 'a122', 'i5'], '/^[a-zA-Z]+.*$/u', [1, 'sds' ,1.4, 3, '3.8', 'ab122.4', 'a122', 'i5']],
		];
	}

	/**
	 * @dataProvider allProvider()
	 */
	public function testAll($expected, $pattern, $input, $flags = 0, $offset = 0, $length = null)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			$actual = Regex::all($pattern, $input, $flags, $length);
		}

		$this->assertEquals($expected, array_values($actual));

	}

	public function delimiterProvider()
	{
		return [
			[Regex::DEFAULT_DELIMITER, null],
			[Regex::DEFAULT_DELIMITER, ''],
			['/', '/'],
			['+', '+'],
			['#', '#'],
		];
	}

	/**
	 * @dataProvider delimiterProvider()
	 */
	public function testDelimiter($expected, $new= null)
	{
		$old = Regex::delimiter();
		Regex::delimiter($new);
		$actual = Regex::delimiter();
		$reset = Regex::delimiter($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}

	public function matchProvider()
	{
		return [
			// [['defऑ'], 'defऑ$', 'abcdefऑ'],
			[
				[
					"<b>example: </b><div align=\"left\">this is a test</div><p>\nparagraph\n",
					'example: </b><div align="left">this is a test',
					'paragraph'
				],
				'<[^>]+>(.*)<\/[^>]+>(?:.*)(?:\R*(.*)\R*)',
				'<b>example: </b><div align="left">this is a test</div>'
				."<p>\nparagraph\n</p>"
			],
			// [[], 'defऑ$', 'abcdef']
		];
	}

	/**
	 * @dataProvider matchProvider()
	 */
	public function testMatch($expected, $pattern, $subject, $flags =0, $offset = 0)
	{
		$revs = 10000;
		for ($i=0; $i < $revs; $i++) {
			$actual = Regex::match('/'.$pattern.'/u', $subject, $flags, $offset);
		}
		// $ms = print_r($actual, true);
		// $ms = str_replace(["\n"], ["\n  "], $ms);
		// echo "\n*****\nPattern: [{$pattern}], Subject: [{$subject}], Matches : {$ms}";

		$this->assertEquals($expected, $actual);
	}

	public function _mbEregProvider()
	{
		return [
			// [['defऑ'], 'defऑ$', 'abcdefऑ'],
			[
				[
					'<b>example: </b><div align="left">this is a test</div>',
					'example: </b><div align="left">this is a test'
				],
				'<[^>]+>(.*)<\/[^>]+>',
				'<b>example: </b><div align="left">this is a test</div>'
			],
			// [null, 'defऑ$', 'abcdef']
		];
	}

	/**
	 * @dataProvider mbEregProvider()
	 */
	public function _testMbEreg($expected, $pattern, $subject, $flags =0, $offset = 0)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			mb_ereg($pattern, $subject, $actual);
		}
		$ms = print_r($actual, true);
		$ms = str_replace(["\n"], ["\n  "], $ms);
		echo "\n*****\nPattern: [{$pattern}], Subject: [{$subject}], Matches : {$ms}";

		$this->assertEquals($expected, $actual);
	}

	/**
	 * @dataProvider mbEregProvider()
	 */
	public function _testSearch($expected, $pattern, $subject, $flags =0, $offset = 0)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			mb_ereg_search_init($subject, $pattern);
			$actual = mb_ereg_search_regs();
		}
		$ms = print_r($actual, true);
		$ms = str_replace(["\n"], ["\n  "], $ms);
		echo "\n*****\nPattern: [{$pattern}], Subject: [{$subject}], Matches : {$ms}";

		$this->assertEquals($expected, $actual);
	}

	public function modifiersProvider()
	{
		return [
			[Regex::DEFAULT_MODIFIERS, null],
			['ui', 'ui'],
			['uix', 'uix'],
			['', ''],
		];
	}

	/**
	 * @dataProvider modifiersProvider()
	 */
	public function testModifiers($expected, $new= null)
	{
		$old = Regex::modifiers();
		Regex::modifiers($new);
		$actual = Regex::modifiers();
		$reset = Regex::modifiers($old);

		$this->assertEquals($expected, $actual);
		$this->assertEquals($old, $reset);
	}

	public function quoteProvider()
	{
		return [
			[null, null],
			[null, null, '/'],
			['', '', '/'],
			['\[x\\'.Regex::delimiter().'z\]', '[x'.Regex::delimiter().'z]'],
			['\[x\/z\]', '[x/z]', '/'],
			['\[x\/z\]', '[x/z]', null, '/'],
			['\[x/z\]', '[x/z]', false, '/'],
			[
				['\[x\/z\]', '\[x\/z\]', '\[x\/z\]'],
				['[x/z]', '[x/z]', '[x/z]'],
				'/'
			]
		];
	}

	/**
	 * @dataProvider quoteProvider()
	 */
	public function testQuote($expected, $string = null, $delimiter = null, $globalDelimiter = null)
	{
		$origDelimiter = Regex::delimiter();
		Regex::delimiter($globalDelimiter);
		$actual = Regex::quote($string, $delimiter);
		Regex::delimiter($origDelimiter);
		$this->assertEquals($expected, $actual);
		$this->assertEquals($origDelimiter, Regex::delimiter());
	}

	public function safeWrapProvider()
	{
		$re = '([a-zA-Z_][a-zA-Z0-9_-]*|)';
		$reb = "\\{$re}\\";
		$dlm = Regex::delimiter();
		return [
			[ "{$dlm}{$re}{$dlm}", "{$re}"],
			[ "/{$re}/", "{$re}", '/'],
			[ "/{$re}/", "/{$re}/"],
			[ "+{$re}+", "+{$re}+"],
			[ "/{$re}/im", "/{$re}/im"],
			[ "#{$re}#", "{$re}", '#'],
			[ "#{$re}#im", "#{$re}#im", '#'],
			[ "~{$re}~iADJ", "~{$re}~iADJ"],
			[ "+{$re}+iADJ", "+{$re}+iADJ"],
			[ "%{$re}%iADJ", "%{$re}%iADJ"]
		];
	}

	/**
	 * @dataProvider safeWrapProvider()
	 */
	public function testSafeWrap($expected, $regex, $delimiter = null, $bracketStyle = false)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			$actual = Regex::safeWrap($regex, $delimiter, $bracketStyle);
		}
		$this->assertEquals($expected, $actual);
	}


	public function safeWrapBracketStyleProvider()
	{
		$re = '([a-zA-Z_][a-zA-Z0-9_-]*|)';
		$reb = "\\{$re}\\";
		$dlm = Regex::delimiter();
		return [
			[ "[{$re}]iADJ", "[{$re}]iADJ"],
			[ "({$re})iADJ", "({$re})iADJ"],
			[ "<{$re}>iADJ", "<{$re}>iADJ"],
			[ "{{$re}}iADJ", "{{$re}}iADJ"],
			[ "{{$reb}}", "{$reb}", '{}'],
			[ "{{$re}}", "{$re}", '{}', ['<{[','>}]']],
			[ "<{$reb}>", "$reb", '<>'],
			[ "<{$re}>", "$re", '<>', ['<{[','>}]']],
		];
	}

	/**
	 * @dataProvider safeWrapBracketStyleProvider()
	 */
	public function testSafeWrapBracketStyle($expected, $regex, $delimiter = null, $bracketStyle = true)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			$actual = Regex::safeWrap($regex, $delimiter, $bracketStyle);
		}
		$this->assertEquals($expected, $actual);
	}

	public function wrapProvider()
	{
		$dlmt = Regex::delimiter();
		return [
			[ $dlmt.'(.*)'.$dlmt, '(.*)'],
			[ $dlmt.'(.*)'.$dlmt, '(.*)', ''],
			[ '/(.*)/', '(.*)', '/'],
			[ '~(.*)~', '(.*)', '~'],
			[
				[$dlmt.'(1*)'.$dlmt, $dlmt.'(2*)'.$dlmt, $dlmt.'(3*)'.$dlmt],
				['(1*)', '(2*)', '(3*)']
			],
		];
	}

	/**
	 * @dataProvider wrapProvider()
	 */
	public function testWarp($expected, $regex, $delimiter = null)
	{
		$actual = Regex::wrap($regex, $delimiter);
		$this->assertEquals($expected, $actual);
	}

	public function _testCasing()
	{
		$ms = print_r(array_filter(array_values(['a' => null, 'B' => null, 'A' => 'AAA', 'b' => 'bbb'])), true);
		$ms = str_replace(["\n"], ["\n  "], $ms);
		echo "\n*****\n Array Values : {$ms}";
	}

}