<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Regex;
use Tea\Regex\Config;
use Tea\Regex\Tests\Mocks\StringObject;

class RegexTest extends TestCase
{

	public function testBuilder()
	{
		$builder = Regex::builder('#', 'xi');

		$this->assertIsBuilder($builder);
		$this->assertEquals('xi', $builder->getModifiers());
		$this->assertEquals('#', $builder->getDelimiter());
	}

	public function createProvider()
	{
		return [
			[
				[
					Config::delimiter().'^\+254\d{9}$'.Config::delimiter().Config::modifiers(),
					Config::modifiers(),
					Config::delimiter()
				],
				'^\+254\d{9}$',
				null,
				null
			],
			[
				[
					'#^\+254\d{9}$#'.Config::modifiers(),
					Config::modifiers(),
					'#'
				],
				'^\+254\d{9}$',
				null,
				'#'
			],
			[
				[
					'#^\+254\d{9}$#usx',
					'usx',
					'#'
				],
				'^\+254\d{9}$',
				'usx',
				'#'
			],
			[
				[
					Config::delimiter().'^\+254\d{9}$'.Config::delimiter().'uim',
					'uim',
					Config::delimiter()
				],
				'^\+254\d{9}$',
				'uim',
				null
			],
			[
				[
					'/^\+254\d{9}$/',
					'',
					'/'
				],
				'^\+254\d{9}$',
				false,
				'/'
			],
		];
	}

	/**
	 * @dataProvider createProvider()
	 */
	public function testCreate(array $expected, $body, $modifiers = null, $delimiter = null)
	{
		$regex = Regex::create($body, $modifiers, $delimiter);
		$this->assertIsRegularExpression($regex);
		list($epattern, $emodifiers, $edelimiter) = $expected;
		$this->assertEquals($emodifiers, $regex->getModifiers());
		$this->assertEquals($edelimiter, $regex->getDelimiter());
		$this->assertEquals($epattern, $regex);
		$this->assertEquals($epattern, (string) $regex);
	}

	public function fromProvider()
	{
		return [
			[
				[
					Config::delimiter().'^\+254\d{9}$'.Config::delimiter().Config::modifiers(),
					Config::modifiers(),
					Config::delimiter()
				],
				'^\+254\d{9}$'
			],
			[
				[
					'#^\+254\d{9}$#'.Config::modifiers(),
					Config::modifiers(),
					'#'
				],
				'#^\+254\d{9}$#',
			],
			[
				[
					'#^\+254\d{9}$#usx',
					'usx',
					'#'
				],
				'#^\+254\d{9}$#usx',
			],
			[
				[
					'/^\+254\d{9}$/',
					'',
					'/'
				],
				'#^\+254\d{9}$#uis',
				false,
				'/'
			],
			[
				[
					'/^\+254\d{9}$/',
					'',
					'/'
				],
				Regex::create('^\+254\d{9}$', false, '/')
			],
			[
				[
					'/^\+254\d{9}$/ix',
					'ix',
					'/'
				],
				Regex::create('^\+254\d{9}$', 'ix', '/')
			],
		];
	}

	/**
	 * @dataProvider fromProvider()
	 */
	public function testFrom(array $expected, $pattern, $modifiers = null, $delimiter = null)
	{
		$regex = Regex::from($pattern, $modifiers, $delimiter);
		$this->assertIsRegularExpression($regex);
		list($epattern, $emodifiers, $edelimiter) = $expected;
		$this->assertEquals($emodifiers, $regex->getModifiers());
		$this->assertEquals($edelimiter, $regex->getDelimiter());
		$this->assertEquals($epattern, $regex);
		$this->assertEquals($epattern, (string) $regex);
	}


	public function fromInstanceProvider()
	{
		return [
			[
				[
					'/(?:(?:[A-Za-z]){0,2})(?:(?:(?:\d)){1,})/u',
					'u',
					'/'
				],
				Regex::builder('/', 'u')
					->max(2)
					->letters()
					->min(1)
					->digits()
			],
			[
				[
					'/^\+254\d{9}$/',
					'',
					'/'
				],
				Regex::create('^\+254\d{9}$', false, '/')
			],
			[
				[
					'/^\+254\d{9}$/ix',
					'ix',
					'/'
				],
				Regex::create('^\+254\d{9}$', 'ix', '/')
			],
		];
	}

	/**
	 * @dataProvider fromInstanceProvider()
	 */
	public function testFromInstance(array $expected, $pattern, $modifiers = null, $delimiter = null)
	{
		$regex = Regex::fromInstance($pattern, $modifiers, $delimiter);
		$this->assertIsRegularExpression($regex);
		list($epattern, $emodifiers, $edelimiter) = $expected;
		$this->assertEquals($emodifiers, $regex->getModifiers());
		$this->assertEquals($edelimiter, $regex->getDelimiter());
		$this->assertEquals($epattern, $regex);
		$this->assertEquals($epattern, (string) $regex);
	}


	public function filterProvider()
	{
		return [
			[
				["+254701888020", "+254711345543"],
				"/^\+254\d{9}$/u",
				explode(" ", "0722555121 +254701888020 0733446643 0700072245 +254711345543"),
			],
			[
				explode(" ", "0722555121 0733446643 0700072245"),
				"/^\+254\d{9}$/u",
				explode(" ", "0722555121 +254701888020 0733446643 0700072245 +254711345543"),
				true,
			],
		];
	}

	/**
	 * @dataProvider filterProvider()
	 */
	public function testFilter($expected, $pattern, $input, $invert = false)
	{
		$result = Regex::filter($pattern, $input, $invert);
		$this->assertEquals($expected, array_values($result));
	}


	public function filterThrowsFilterErrorProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4}/u",
				["Call 555-1212 or 1-800-555-1212"]
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				["foobar foobar foobar"],
			],
		];
	}

	/**
	 * @dataProvider filterThrowsFilterErrorProvider()
	 * @expectedException \Tea\Regex\Exception\FilterError
	 */
	public function testFilterThrowsFilterError($pattern, $input)
	{
		Regex::filter($pattern, $input);
	}


/***Match***/

	public function matchProvider()
	{
		return [
			[
				[
					'555-1212',
					'555-1212',
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					'Call 555-1212',
					'555-1212',
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212"
			],
			[
				[
					'Call 555-1212',
					'Call',
					'555-1212',
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212"
			],
			[
				[
					'555-1212',
					'555-1212',
				],
				StringObject::create("/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"),
				StringObject::create("Call 555-1212 or 1-800-555-1212"),
			],
		];
	}

	/**
	 * @dataProvider matchProvider()
	 */
	public function testMatch($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$matches = Regex::match($pattern, $subject, $flags, $offset);
		$this->assertInstanceOfMatches($matches);
		$this->assertEquals($expected, $matches->result());
	}


	public function matchThrowsMatchErrorProvider()
	{
		return [
			[
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4}/u",
				"Call 555-1212 or 1-800-555-1212"
			],
			[
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				["Call 555-1212 or 1-800-555-1212"]
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider matchThrowsMatchErrorProvider()
	 * @expectedException \Tea\Regex\Exception\MatchError
	 */
	public function testMatchThrowsMatchError($pattern, $subject)
	{
		Regex::match($pattern, $subject);
	}


	public function matchAllProvider()
	{
		return [
			[
				[
					['555-1212', '1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*(?:[a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['Call', 'or'],
					['555-1212', '1-800-555-1212'],
				],
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				[
					['Call 555-1212', ' or 1-800-555-1212'],
					['Call', 'or'],
					['555-1212', '1-800-555-1212'],
				],
				StringObject::create("/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"),
				StringObject::create("Call 555-1212 or 1-800-555-1212"),
			],
		];
	}

	/**
	 * @dataProvider matchAllProvider()
	 */
	public function testMatchAll($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$matches = Regex::matchAll($pattern, $subject, $flags, $offset);
		$this->assertInstanceOfMatches($matches);
		$this->assertEquals($expected, $matches->result());
	}


	public function matchAllThrowsMatchErrorProvider()
	{
		return [
			[
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/qt",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4}/u",
				"Call 555-1212 or 1-800-555-1212"
			],
			[
				"/\s*([a-zA-Z]*)\s*(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				["Call 555-1212 or 1-800-555-1212"]
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider matchAllThrowsMatchErrorProvider()
	 * @expectedException \Tea\Regex\Exception\MatchError
	 */
	public function testMatchAllThrowsMatchError($pattern, $subject)
	{
		Regex::matchAll($pattern, $subject);
	}

	public function matchesProvider()
	{
		return [
			[
				true,
				"/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212",
			],
			[
				false,
				"/\s(?:[a-zA-Z]+)\s{4,}(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u",
				"Call 555-1212 or 1-800-555-1212"
			],
			[
				true,
				StringObject::create("/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{4})/u"),
				StringObject::create("Call 555-1212 or 1-800-555-1212"),
			],
			[
				false,
				StringObject::create("/(\d{0,1}\-{0,1}(?:\d\d\d\-){1,2}\d{6})/u"),
				StringObject::create("Call 555-1212 or 1-800-555-1212"),
			],
		];
	}

	/**
	 * @dataProvider matchesProvider()
	 */
	public function testMatches($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$result = Regex::matches($pattern, $subject, $flags, $offset);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals($expected, $result);
	}


	/**
	 * @dataProvider matchThrowsMatchErrorProvider()
	 * @expectedException \Tea\Regex\Exception\MatchError
	 */
	public function testMatchesThrowsMatchError($pattern, $subject)
	{
		Regex::matches($pattern, $subject);
	}

	/**
	 * @dataProvider matchesProvider()
	 */
	public function testIs($expected, $pattern, $subject, $flags = 0, $offset = 0)
	{
		$result = Regex::is($pattern, $subject, $flags, $offset);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals($expected, $result);
	}


	/**
	 * @dataProvider matchThrowsMatchErrorProvider()
	 * @expectedException \Tea\Regex\Exception\MatchError
	 */
	public function testIsThrowsMatchError($pattern, $subject)
	{
		Regex::is($pattern, $subject);
	}


	public function replaceProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?<=^|\s)0(7\d{2})/ux",
				'+254$1',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
			],

			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"]
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				-1,
				20
			],
			[
				[
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				2,
				10
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				"/ (?: ^| (?P<space> \s+ ) ) (?P<netId> 07\d{2} )/ux",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2
			],
		];
	}

	/**
	 * @dataProvider replaceProvider()
	 */
	public function testReplace($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$revs = 1;
		for ($i=0; $i < $revs; $i++) {
			$result = Regex::replace($pattern, $replace, $subject, $limit);
		}

		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->result());
		if(!is_null($count)){
			$this->assertEquals($count, $result->count());
		}
	}


	public function replaceThrowsReplacementErrorProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/qwerty",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:^|(\s+))0(7\d{2}/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				"",
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replaceThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplaceThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		Regex::replace($pattern, $replace, $subject, $limit);
	}

	public function replaceCallbackProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				"/ (?: ^| (?P<space> \s+ ) ) (?P<netId> 07\d{2} )/ux",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2
			],
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/ (?: ^| (?P<space> \s+ ) ) (?P<prefix> (?:\+{0,1}254 | 07\d{2}) )/ux",
				function($matches){
					if($matches->prefix === '+254')
						return $matches[1] . $matches[2];
					elseif (strpos($matches->prefix, '254') === 0)
						return $matches[1] . '+'. $matches[2];
					elseif (strpos($matches->prefix, '07') === 0)
						return $matches[1].'+254'. substr($matches[2], 1);
					else
						return $matches[1] . $matches[2];
				},
				"0722555121 0701888020 254733446643 254700072245 +254711345543",
				-1,
				5
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245 +254711345543"),
				"/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				-1,
				4
			],

			[
				['home' =>  "+254722555121", 'office' => "+254701888020", 'cell' => "+254733446643"],
				"/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				['home' =>  "0722555121", 'office' => "0701888020", 'cell' => "0733446643"]
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				"/(?:^|(?P<space>\s+))(?P<netId>07\d{2})/u",
				function($matches){
					return $matches->space.'+254'. substr($matches->netId, 1);
				},
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
				],
				-1,
				20
			],
		];
	}

	/**
	 * @dataProvider replaceCallbackProvider()
	 */
	public function testReplaceCallback($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$callback = function($matches) use ($replace){
			$this->assertInstanceOfMatches($matches);
			return $replace($matches);
		};

		$result = Regex::replaceCallback($pattern, $callback, $subject, $limit);
		$this->assertInstanceOfReplacement($result);
		$this->assertEquals($expected, $result->result());
		if(!is_null($count)){
			$this->assertEquals($count, $result->count());
		}
	}

	public function replaceCallbackThrowsReplacementErrorProvider()
	{
		return [
			[
				"/(?:^|(?P<space>\s+))0(?P<provider>7\d{2})/qwerty",
				function($matches){
					return $matches->space.'+254'.$matches->provider;
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:^|(?P<space>\s+))0(?P<provider>7\d{2}/u",
				function($matches){
					return $matches->space.'+254'.$matches->provider;
				},
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				function($matches){
					return $matches[0];
				},
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replaceCallbackThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplaceCallbackThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		Regex::replaceCallback($pattern, $replace, $subject, $limit);
	}


	public function replacedProvider()
	{
		return [
			[
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				-1,
				4
			],
			[
				null,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				-1,
			],
			[
				null,
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
				-1,
			],
			[
				"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
				2,
				2,
			],

			[
				explode(" ", "+254722555121 +254701888020 +254733446643 +254700072245"),
				"/^0(7\d{2})/u",
				'+254$1',
				explode(" ", "0722555121 0701888020 0733446643 0700072245 +254711345543"),
				-1,
				4
			],

			[
				['home' =>  "+254722555121", 'cell' => "+254733446643"],
				"/^0(7\d{2})/u",
				'+254$1',
				['home' =>  "0722555121", 'office' => "+254701888020", 'cell' => "0733446643"],
				-1,
				2
			],
			[
				[
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				-1,
				16
			],
			[
				[
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 0733446643 0700072245 +254711345543",
				],
				"/(?:^|(\s+))0(7\d{2})/u",
				'$1+254$2',
				[
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"0722555121 0701888020 0733446643 0700072245 +254711345543",
					"+254722555121 +254701888020 +254733446643 +254700072245 +254711345543",
				],
				2,
				8
			],
		];
	}

	/**
	 * @dataProvider replacedProvider()
	 */
	public function testReplaced($expected, $pattern, $replace, $subject, $limit = -1, $count = null)
	{
		$result = Regex::replaced($pattern, $replace, $subject, $limit);
		if(!is_null($expected)){
			$this->assertInstanceOfReplacement($result);
			$this->assertEquals($expected, $result->result());
			if(!is_null($count)){
				$this->assertEquals($count, $result->count());
			}
		}
		else{
			$this->assertNull($result);
		}

	}


	public function replacedThrowsReplacementErrorProvider()
	{
		return [
			[
				"/(?:^|(\s+))0(7\d{2})/qwerty",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:^|(\s+))0(7\d{2}/u",
				'$1+254$2',
				"0722555121 0701888020 0733446643 0700072245 +254711345543",
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				'',
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider replacedThrowsReplacementErrorProvider()
	 * @expectedException \Tea\Regex\Exception\ReplacementError
	 */
	public function testReplacedThrowsReplacementError($pattern, $replace, $subject, $limit = -1)
	{
		Regex::replaced($pattern, $replace, $subject, $limit);
	}

	public function splitProvider()
	{
		return [
			[
				["hypertext", "language", "programming"],
				"/[\s,]+/",
				"hypertext language, programming"
			],
			[
				["HypertextLanguageProgramming"],
				"/[\s,]+/",
				"HypertextLanguageProgramming"
			],
			[
				['f', 'o', 'o', 'b', 'a', 'r'],
				'/(?: |)/',
				"foo bar",
				-1,
				PREG_SPLIT_NO_EMPTY
			],
			[
				["hypertext", "language, programming"],
				"/[\s,]+/",
				"hypertext language, programming",
				2
			],
		];
	}

	/**
	 * @dataProvider splitProvider()
	 */
	public function testSplit($expected, $pattern, $subject, $limit = -1, $flags = 0)
	{
		$result = Regex::split($pattern, $subject, $limit, $flags);
		$this->assertEquals($expected, $result);
	}

	public function splitThrowsSplitErrorProvider()
	{
		return [
			[
				"/[\s,]+/qwerty",
				"hypertext language, programming"
			],
			[
				"/(?:\D+|<\d+>)*[!?]/",
				"foobar foobar foobar",
			],
		];
	}

	/**
	 * @dataProvider splitThrowsSplitErrorProvider()
	 * @expectedException \Tea\Regex\Exception\SplitError
	 */
	public function testSplitThrowsSplitError($pattern, $subject, $limit = -1, $flags = 0)
	{
		Regex::split($pattern, $subject, $limit, $flags);
	}
}
