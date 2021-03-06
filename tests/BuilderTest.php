<?php
namespace Tea\Regex\Tests;

use Tea\Regex\Regex;
use Tea\Regex\Config;
use Tea\Regex\Builder;
use Tea\Regex\Modifiers;

class BuilderTest extends TestCase
{

	/**
	 * @var \Tea\Regex\Builder
	 */
	public $r;

	public function setUp()
	{
		$this->r = new Builder();
	}

	protected function create($delimiter = null, $modifiers = null)
	{
		return new Builder($delimiter, $modifiers);
	}

	public function testCreate()
	{
		$builder = $this->create('#', 'xi')
				->max(2)->letters()
				->min(1)->digits();

		$this->assertIsBuilder($builder);
		$this->assertEquals('(?:(?:[A-Za-z]){0,2})(?:(?:(?:\d)){1,})', $builder->getBody());
		$this->assertEquals('xi', $builder->getModifiers());
		$this->assertEquals('#', $builder->getDelimiter());
	}

	public function modifiersProvider()
	{
		return [
			[
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS
			],
			[
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::ANCHORED,
				[Modifiers::UTF8, Modifiers::CASELESS, Modifiers::DOTALL, Modifiers::ANCHORED],
				Modifiers::UTF8.Modifiers::CASELESS
			],
		];
	}

	/**
	 * @dataProvider modifiersProvider()
	 */
	public function testModifiers($expected, $modifiers, $inital = null)
	{
		$builder = $this->create(null, $inital)->modifiers($modifiers);
		$this->assertEquals($expected, $builder->getModifiers());
	}


	public function modifierProvider()
	{
		return [
			[
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS
			],
			[
				Modifiers::UTF8.Modifiers::DOTALL.Modifiers::CASELESS,
				Modifiers::CASELESS,
				Modifiers::UTF8.Modifiers::DOTALL
			],
		];
	}

	/**
	 * @dataProvider modifierProvider()
	 */
	public function testModifier($expected, $modifiers, $inital = null)
	{
		$builder = $this->create(null, $inital)->modifier($modifiers);
		$this->assertEquals($expected, $builder->getModifiers());
	}


	public function removeModifiersProvider()
	{
		return [
			[
				Modifiers::CASELESS.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				Modifiers::UTF8.Modifiers::DOTALL.Modifiers::MULTILINE,
				[Modifiers::CASELESS, Modifiers::DOLLAR_ENDONLY, Modifiers::ANCHORED],
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
		];
	}

	/**
	 * @dataProvider removeModifiersProvider()
	 */
	public function testRemoveModifiers($expected, $remove, $inital)
	{
		$builder = $this->create(null, $inital)->removeModifiers($remove);
		$this->assertEquals($expected, $builder->getModifiers());
	}

	public function removeModifierProvider()
	{
		return [
			[
				Modifiers::CASELESS.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				Modifiers::UTF8.Modifiers::DOTALL.Modifiers::MULTILINE,
				Modifiers::CASELESS,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::MULTILINE,
			],
		];
	}

	/**
	 * @dataProvider removeModifierProvider()
	 */
	public function testRemoveModifier($expected, $remove, $inital)
	{
		$builder = $this->create(null, $inital)->removeModifier($remove);
		$this->assertEquals($expected, $builder->getModifiers());
	}

	public function hasModifierProvider()
	{
		return [
			[
				true,
				Modifiers::UTF8.Modifiers::DOTALL,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				true,
				Modifiers::CASELESS,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				false,
				Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
			],
			[
				false,
				Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
			],
		];
	}

	/**
	 * @dataProvider hasModifierProvider()
	 */
	public function testHasModifier($expected, $value, $inital)
	{
		$result = $this->create(null, $inital)->hasModifier($value);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals($expected, $result);
	}

	public function hasModifiersProvider()
	{
		return [
			[
				true,
				Modifiers::UTF8.Modifiers::DOTALL,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				true,
				[Modifiers::UTF8, Modifiers::DOTALL],
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				true,
				Modifiers::CASELESS,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL.Modifiers::DOLLAR_ENDONLY
				.Modifiers::ANCHORED.Modifiers::MULTILINE,
			],
			[
				true,
				Modifiers::ANCHORED.Modifiers::DOTALL,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
				true
			],
			[
				false,
				Modifiers::DOTALL.Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
			],
			[
				false,
				Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
			],
			[
				false,
				Modifiers::ANCHORED,
				Modifiers::UTF8.Modifiers::CASELESS.Modifiers::DOTALL
				.Modifiers::DOLLAR_ENDONLY.Modifiers::MULTILINE,
				true
			],
		];
	}

	/**
	 * @dataProvider hasModifiersProvider()
	 */
	public function testHasModifiers($expected, $value, $inital, $any = false)
	{
		$result = $this->create(null, $inital)->hasModifiers($value, $any);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals($expected, $result);
	}


	public function testIgnoreCase()
	{
		$builder = $this->create()->ignoreCase();
		$this->assertTrue($builder->hasModifier(Modifiers::CASELESS));
		$builder->ignoreCase(false);
		$this->assertFalse($builder->hasModifier(Modifiers::CASELESS));
	}

	public function testMultiLine()
	{
		$builder = $this->create()->multiLine();
		$this->assertTrue($builder->hasModifier(Modifiers::MULTILINE));
		$builder->multiLine(false);
		$this->assertFalse($builder->hasModifier(Modifiers::MULTILINE));
	}

	public function testCompile()
	{
		$builder = $this->create('/', 'u')
			->min(1)->max(2)->letters()
			->max(4)->digits();

		$regex = $builder->compile();
		$this->assertIsRegularExpression($regex);
		$this->assertEquals($builder->getBody(), $regex->getBody());
		$this->assertEquals($builder->getModifiers(), $regex->getModifiers());
		$this->assertEquals($builder->getDelimiter(), $regex->getDelimiter());
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testGlobalMatch()
	{
		$this->create()->globalMatch();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testPregMatchFlags()
	{
		$this->create()->pregMatchFlags();
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testGetRegExp()
	{
		$this->create()->getRegExp();
	}

	public function testMoney()
	{
		$regEx = $this->r
			->find("€")
			->min(1)->digits()
			->then(",")
			->digit()
			->digit()
			->compile();

		$this->assertTrue($regEx->matches("€128,99"));
		$this->assertTrue($regEx->matches("€81,99"));

		$this->assertFalse($regEx->matches("€8,9"));
		$this->assertFalse($regEx->matches("12.123.8,99 €"));

	}

	public function testMoney2()
	{

		$regEx = $this->r
			->find("€")
			->exactly(1)->whitespace()
			->min(1)->digits()
			->then(".")
			->exactly(3)->digits()
			->then(",")
			->digit()
			->digit()
			->compile();

		$this->assertTrue($regEx->matches("€ 1.228,99"));
		$this->assertTrue($regEx->matches("€ 452.000,99"));

		$this->assertFalse($regEx->matches("€8,9"));
		$this->assertFalse($regEx->matches("12.123.8,99 €"));

	}

	public function testAllMoney()
	{
		$builder1 = $this->r
			->find("€")
			->min(1)->digits()
			->then(",")
			->digit()
			->digit();

		$this->assertTrue($builder1->compile()->matches("€128,99"));
		$this->assertTrue($builder1->compile()->matches("€81,99"));

		$builder2 = $this->r->getNew()
			->find("€")
			->min(1)->digits()
			->then(".")
			->exactly(3)->digits()
			->then(",")
			->digit()
			->digit();

		$this->assertTrue($builder2->compile()->matches("€1.228,99"));
		$this->assertTrue($builder2->compile()->matches("€452.000,99"));

		$combined = $this->r->getNew()
			->eitherFind($builder1)
			->orFind($builder2);

		$this->assertTrue($combined->compile()->matches("€128,99"));
		$this->assertTrue($combined->compile()->matches("€81,99"));
		$this->assertTrue($combined->compile()->matches("€1.228,99"));
		$this->assertTrue($combined->compile()->matches("€452.000,99"));


	}

	public function testMaybe()
	{
		$regEx = $this->r
			->startOfLine()
			->notDigit()
			->maybe("a")
			->compile();

		$this->assertTrue($regEx->matches("aabba1"));

		$this->assertFalse($regEx->matches("12aabba1"));

	}

	public function testMaybeSome()
	{
		$regEx = $this->r
			->startOfLine()
			->notDigit()
			->maybeSome(array("a", "b", "c"))
			->compile();

		$this->assertTrue($regEx->matches("aabba1"));

		$this->assertFalse($regEx->matches("12aabba1"));
	}

	public function testSome()
	{
		$regEx = $this->r
			->startOfLine()
			->notDigit()
			->some(array("a", "b", "c"))
			->compile();

		$this->assertTrue($regEx->matches("aabba1"));

		$this->assertFalse($regEx->matches("12aabba1"));
	}

	public function testLettersDigits()
	{
		$regEx = $this->r
			->startOfLine()
			->min(3)
			->letters()
			->append($this->r->getNew()->min(2)->digits())
			->compile();

		$this->assertTrue($regEx->matches("asf24"));

		$this->assertFalse($regEx->matches("af24"));
		$this->assertFalse($regEx->matches("afs4"));
		$this->assertFalse($regEx->matches("234asas"));

	}

	public function testNotLetter()
	{
		$regEx = $this->r
			->startOfLine()
			->notLetter()
			->compile();

		$this->assertTrue($regEx->matches("234asd"));
		$this->assertFalse($regEx->matches("asd425"));
	}

	public function testNotLetters()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(1)
			->notLetters()
			->compile();

		$this->assertTrue($regEx->matches("234asd"));
		$this->assertTrue($regEx->matches("@234asd"));

		$this->assertFalse($regEx->matches("asd425"));
	}

	public function testNotDigit()
	{
		$regEx = $this->r
			->startOfLine()
			->notDigit()
			->compile();

		$this->assertTrue($regEx->matches("a234asd"));

		$this->assertFalse($regEx->matches("45asd"));
	}

	public function testNotDigits()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(1)
			->notDigits()
			->compile();

		$this->assertTrue($regEx->matches("a234asd"));
		$this->assertTrue($regEx->matches("@234asd"));

		$this->assertFalse($regEx->matches("425asd"));
	}

	public function testAny()
	{
		$regEx = $this->r
			->startOfLine()
			->any()
			->compile();

		$this->assertTrue($regEx->matches("a.jpg"));
		$this->assertTrue($regEx->matches("a.b_asdasd"));
		$this->assertTrue($regEx->matches("4"));

		$this->assertFalse($regEx->matches(""));
	}

	public function testOfAny()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)
			->ofAny()
			->find("_")
			->compile();

		$this->assertTrue($regEx->matches("12_123123.jpg"));
		$this->assertTrue($regEx->matches("ab_asdasd"));

		$this->assertFalse($regEx->matches("425asd"));
	}

	public function testOfAny2()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(3)->ofAny()
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("pqr"));
	}

	public function testAnything()
	{
		$regEx = $this->r
			->startOfLine()
			->anything()
			->compile();

		$this->assertTrue($regEx->matches("a.jpg"));
		$this->assertTrue($regEx->matches("a.b_asdasd"));
		$this->assertTrue($regEx->matches("4"));
	}

	public function testAnythingBut()
	{
		$regEx = $this->r
			->startOfInput()
			->anythingBut("admin")
			->compile();

		$this->assertTrue($regEx->matches("a.jpg"));
		$this->assertTrue($regEx->matches("a.b_asdasd"));
		$this->assertTrue($regEx->matches("4"));

		$this->assertFalse($regEx->matches("admin"));

	}

	public function testAnythingBut2()
	{
		$regEx = $this->r
			->startOfLine()
			->anythingBut("Y")
			->compile();

		$this->assertTrue($regEx->matches("a.jpg"));
		$this->assertTrue($regEx->matches("a.b_asdasd"));
		$this->assertTrue($regEx->matches("4"));

		$this->assertFalse($regEx->matches("YY"));
		$this->assertFalse($regEx->matches("Y"));

	}

	public function testNeitherNor()
	{

		$regEx = $this->r
			->startOfLine()
			->neither($this->r->getNew()->exactly(1)->of("milk"))
			->nor($this->r->getNew()->exactly(1)->of("juice"))
			->nor($this->r->getNew()->exactly(1)->of(Config::delimiter()))
			->compile();

		$this->assertTrue($regEx->matches("beer"));

		$this->assertFalse($regEx->matches("milk"));
		$this->assertFalse($regEx->matches("juice"));
		$this->assertFalse($regEx->matches(Config::delimiter()));

	}

	public function testNeitherNor2()
	{

		$regEx = $this->r
			->startOfLine()
			->neither("milk")
			->min(0)
			->ofAny()
			->nor($this->r->getNew()->exactly(1)->of("juice"))
			->compile();

		$this->assertTrue($regEx->matches("beer"));

		$this->assertFalse($regEx->matches("milk"));
		$this->assertFalse($regEx->matches("juice"));

	}

	public function testLowerCasew()
	{
		$regEx = $this->r
			->startOfLine()
			->lowerCaseLetter()
			->compile();

		$this->assertTrue($regEx->matches("a24"));

		$this->assertFalse($regEx->matches("234a"));
		$this->assertFalse($regEx->matches("A34"));
	}

	public function testLowerCaseLetters()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)
			->lowerCaseLetters()
			->compile();

		$this->assertTrue($regEx->matches("aa24"));

		$this->assertFalse($regEx->matches("aAa234a"));
		$this->assertFalse($regEx->matches("234a"));
		$this->assertFalse($regEx->matches("A34"));
	}

	public function testUpperCaseLetter()
	{
		$regEx = $this->r
			->startOfLine()
			->upperCaseLetter()
			->compile();

		$this->assertTrue($regEx->matches("A24"));

		$this->assertFalse($regEx->matches("aa234a"));
		$this->assertFalse($regEx->matches("34aa"));
	}

	public function testUpperCaseLetters()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)
			->upperCaseLetters()
			->compile();

		$this->assertTrue($regEx->matches("AA24"));

		$this->assertFalse($regEx->matches("aAa234a"));
		$this->assertFalse($regEx->matches("234a"));
		$this->assertFalse($regEx->matches("a34"));
	}

	public function testLetterDigit()
	{
		$regEx = $this->r
			->ignoreCase()
			->startOfLine()
			->letter()
			->append($this->r->getNew()->digit())
			->compile();

		$this->assertTrue($regEx->matches("a5"));

		$this->assertFalse($regEx->matches("5a"));

	}

	public function testTab()
	{
		$regEx = $this->r
			->startOfLine()
			->tab()
			->compile();

		$this->assertTrue($regEx->matches("\tp"));
		$this->assertFalse($regEx->matches("q\tp\t"));
		$this->assertFalse($regEx->matches("p\t"));

	}

	public function testTab2()
	{
		$regEx = $this->create('%')
			->startOfLine()
			->exactly(1)->of("/p%")
			->tab()
			->exactly(1)->of("/q%")
			->compile();

		$this->assertTrue($regEx->matches("/p%\t/q%"));
	}

	public function testTabs()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)
			->tabs()
			->compile();

		$this->assertTrue($regEx->matches("\t\tp"));

		$this->assertFalse($regEx->matches("\tp"));
		$this->assertFalse($regEx->matches("q\tp\t"));
		$this->assertFalse($regEx->matches("p\t"));

	}


	public function testWhiteSpace()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)->whitespace()
			->then("p")
			->then("d")
			->then("r")
			->exactly(1)->whitespace()
			->compile();

		$this->assertTrue($regEx->matches("  pdr "));

		$this->assertFalse($regEx->matches(" pdr "));
		$this->assertFalse($regEx->matches("  pd r "));
		$this->assertFalse($regEx->matches(" p dr "));

	}

	public function testMoreWhiteSpace()
	{
		$regEx = $this->r
			->startOfLine()
			->whitespace()
			->then("p")
			->then("d")
			->then("r")
			->exactly(1)->whitespace()
			->compile();

		$this->assertTrue($regEx->matches("\tpdr\t"));
	}

	public function testNotWhitespace()
	{
		$regEx = $this->r
			->startOfLine()
			->notWhitespace()
			->compile();

		$this->assertTrue($regEx->matches("a234asd"));

		$this->assertFalse($regEx->matches(" 45asd"));
		$this->assertFalse($regEx->matches("\t45asd"));
	}

	public function testNotWhitespace2()
	{
		$regEx = $this->r
			->startOfLine()
			->min(1)
			->notWhitespace()
			->compile();

		$this->assertTrue($regEx->matches("a234asd"));

		$this->assertFalse($regEx->matches(" 45asd"));
		$this->assertFalse($regEx->matches("\t45asd"));
	}

	public function testLineBreak()
	{
		$regEx = $this->r
			->startOfLine()
			->lineBreak()
			->compile();

		$this->assertTrue($regEx->matches("\n\ra234asd"));
		$this->assertTrue($regEx->matches("\na234asd"));
		$this->assertTrue($regEx->matches("\ra234asd"));

		$this->assertFalse($regEx->matches(" 45asd"));
		$this->assertFalse($regEx->matches("\t45asd"));
	}

	public function testLineBreaks()
	{
		$regEx = $this->r
			->startOfLine()
			->min(2)
			->lineBreaks()
			->compile();

		$this->assertTrue($regEx->matches("\n\ra234asd"));
		$this->assertTrue($regEx->matches("\n\na234asd"));
		$this->assertTrue($regEx->matches("\r\ra234asd"));

		$this->assertFalse($regEx->matches(" 45asd"));
		$this->assertFalse($regEx->matches("\t45asd"));
	}


	public function testStartOfLine()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(1)
			->of("p")
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertFalse($regEx->matches("qp"));
	}

	public function testEndOfLine()
	{
		$regEx = $this->r
			->exactly(1)
			->of("p")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertFalse($regEx->matches("pq"));
	}

	public function testEitherLikeOrLike()
	{
		$regEx = $this->r
			->startOfLine()
			->eitherFind($this->r->getNew()->exactly(1)->of("p"))
			->orFind($this->r->getNew()->exactly(2)->of("q"))
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertTrue($regEx->matches("qq"));

		$this->assertFalse($regEx->matches("pqq"));
		$this->assertFalse($regEx->matches("qqp"));
	}


	public function testOrLikeChain()
	{

		$regEx = $this->r
			->eitherFind($this->r->getNew()->exactly(1)->of("p"))
			->orFind($this->r->getNew()->exactly(1)->of("q"))
			->orFind($this->r->getNew()->exactly(1)->of("r"))
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertTrue($regEx->matches("q"));
		$this->assertTrue($regEx->matches("r"));

		$this->assertFalse($regEx->matches("s"));
	}

	public function testEitherOr()
	{
		$regEx = $this->r
			->eitherFind("p")
			->orFind("q")
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertTrue($regEx->matches("q"));

		$this->assertFalse($regEx->matches("r"));
	}

	public function testAnyOf()
	{
		$regEx = $this->r
			->anyOf(
				array(
					"abc",
					"def",
					"q",
					$this->r->getNew()->exactly(2)->digits()
				)
			)
			->compile();

		$this->assertTrue($regEx->matches("abc"));
		$this->assertTrue($regEx->matches("def"));
		$this->assertTrue($regEx->matches("22"));

		$this->assertFalse($regEx->matches("r"));
		$this->assertFalse($regEx->matches("1"));

		$regEx = $this->r
			->getNew()
			->anyOf(array())
			->compile();

		$this->assertTrue($regEx->matches("p"));
	}

	public function testExactly()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(3)->of("p")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("ppp"));

		$this->assertFalse($regEx->matches("pp"));
		$this->assertFalse($regEx->matches("pppp"));
	}

	public function testMin()
	{
		$regEx = $this->r
			->startOfLine()
			->min(2)->of("p")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("pp"));
		$this->assertTrue($regEx->matches("ppp"));
		$this->assertTrue($regEx->matches("ppppppp"));

		$this->assertFalse($regEx->matches("p"));
	}

	public function testMax()
	{
		$regEx = $this->r
			->startOfLine()
			->max(3)->of("p")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("p"));
		$this->assertTrue($regEx->matches("pp"));
		$this->assertTrue($regEx->matches("ppp"));

		$this->assertFalse($regEx->matches("pppp"));
		$this->assertFalse($regEx->matches("pppppppp"));
	}

	public function testMinMax()
	{
		$regEx = $this->r
			->startOfLine()
			->min(3)->max(7)->of("p")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("ppp"));
		$this->assertTrue($regEx->matches("ppppp"));
		$this->assertTrue($regEx->matches("ppppppp"));

		$this->assertFalse($regEx->matches("pp"));
		$this->assertFalse($regEx->matches("p"));
		$this->assertFalse($regEx->matches("pppppppp"));
		$this->assertFalse($regEx->matches("pppppppppppp"));
	}

	public function testOf()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)->of("p p p ")
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("p p p p p p "));

		$this->assertFalse($regEx->matches("p p p p pp"));
	}


	public function testOfGroup()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(3)->of("p")->asGroup()
			->exactly(1)->of("q")->asGroup()
			->exactly(1)->ofGroup(1)
			->exactly(1)->ofGroup(2)
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("pppqpppq"));
	}

	public function testGroupIncrement()
	{
		//aa--aa--
		$builder1 = $this->r
			->exactly(2)->of("a")->asGroup()
			->exactly(2)->of("-")->asGroup()
			->exactly(1)->ofGroup(1)
			->exactly(1)->ofGroup(2);

		//bb--bb--
		$builder2 = $this->r
			->getNew()
			->exactly(2)->of("b")->asGroup()
			->exactly(2)->of("-")->asGroup()
			->exactly(1)->ofGroup(1)
			->exactly(1)->ofGroup(2);

		$builder3 = $this->r
			->getNew()
			->find("123");

		$regExp = $this->r
			->getNew()
			->startOfInput()
			->append($builder1)
			->append($builder2)
			->append($builder3)
			->endOfInput()
			->compile();

		$this->assertTrue($regExp->matches("aa--aa--bb--bb--123"));

		$this->assertFalse($regExp->matches("def123abc"));
		$this->assertFalse($regExp->matches("abcabc"));
		$this->assertFalse($regExp->matches("abcdef312"));

	}

	public function testNamedGroup()
	{
		$regEx = $this->r
			->exactly(3)->digits()->asGroup("numbers")
			->compile();

		$res = $regEx->match("hello-123-abc");

		$this->assertTrue($res->has('numbers'));

	}

	public function testFrom()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(3)->from(array("p", "q", "r"))
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("ppp"));
		$this->assertTrue($regEx->matches("qqq"));
		$this->assertTrue($regEx->matches("ppq"));
		$this->assertTrue($regEx->matches("rqp"));

		$this->assertFalse($regEx->matches("pyy"));
	}


	public function testNotFrom()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(3)->notFrom(array("p", "q", "r"))
			->endOfLine()
			->compile();

		$this->assertTrue($regEx->matches("lmn"));

		$this->assertFalse($regEx->matches("mnq"));
	}

	public function testLike()
	{
		$regEx = $this->r
			->startOfLine()
			->exactly(2)->like(
				$this->r->getNew()
					->min(1)->of("p")
					->min(2)->of("q")
			)
			->endOfLine()
			->compile();


		$this->assertTrue($regEx->matches("pqqpqq"));

		$this->assertFalse($regEx->matches("qppqpp"));
	}


	public function testReluctantly()
	{

		$regEx = $this->r
			->exactly(2)->of("p")
			->min(2)->ofAny()->reluctantly()
			->exactly(2)->of("p")
			->compile();

		$matches = $regEx->match("pprrrrpprrpp");
		$this->assertTrue($matches[0] == "pprrrrpp");
	}

	public function testAhead()
	{
		$regEx = $this->r
			->exactly(1)->of("dart")
			->ahead($this->r->getNew()->exactly(1)->of("lang"))
			->compile();

		$this->assertTrue($regEx->matches("dartlang"));
		$this->assertTrue($regEx->matches("dartlanglang"));
		$this->assertTrue($regEx->matches("langdartlang"));

		$this->assertFalse($regEx->matches("dartpqr"));
		$this->assertFalse($regEx->matches("langdart"));

	}

	public function testNotAhead()
	{
		$regEx = $this->r
			->exactly(1)->of("dart")
			->notAhead($this->r->getNew()->exactly(1)->of("pqr"))
			->compile();

		$this->assertTrue($regEx->matches("dartlang"));

		$this->assertFalse($regEx->matches("dartpqr"));
	}

	public function testAsGroup()
	{
		$regEx = $this->r
			->min(1)->max(3)->of("p")
			->exactly(1)->of("dart")->asGroup()
			->exactly(1)->from(array("p", "q", "r"))
			->compile();

		$matches = $regEx->match("pdartq");
		$this->assertTrue($matches[1] == "dart");
	}

	public function testOptional()
	{
		$regEx = $this->r
			->min(1)->max(3)->of("p")
			->exactly(1)->of("dart")
			->optional($this->r->getNew()->exactly(1)->from(array("p", "q", "r")))
			->compile();

		$this->assertTrue($regEx->matches("pdartq"));
	}

	public function testDelimiter()
	{
		$regEx = $this->r
			->startOfInput()
			->exactly(3)->digits()
			->exactly(1)->of("/")
			->exactly(2)->letters()
			->endOfInput()
			->compile();

		$this->assertTrue($regEx->matches("123/ab"));
	}

	public function testSomething()
	{
		$regEx = $this->r
			->min(1)->max(3)->of("p")
			->something()
			->compile();

		$this->assertTrue($regEx->matches("pphelloq"));
		$this->assertFalse($regEx->matches("p"));
	}

	// public function testAlias()
	// {
	// 	$regEx = $this->r
	// 		->startOfLine()
	// 		->upperCaseLetter()
	// 		->compile();

	// 	//check deprecated alias methods
	// 	$this->assertTrue($regEx->test("A24"));
	// 	$this->assertTrue($regEx->matches("A24"));

	// 	$this->assertArrayHasKey(0, $regEx->exec("A45"));
	// 	$this->assertArrayHasKey(0, $regEx->findIn("A45"));

	// }

}