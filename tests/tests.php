<?php

$file = __DIR__ . "/../base85.class.php";
require($file);

$pass_count = 0;
$fail_count = 0;

$long_opts = ['filter:', 'encode:', 'decode:'];
$params    = getopt("", $long_opts);

$filter = $params['filter'] ?? "";
$encode = $params['encode'] ?? "";
$decode = $params['decode'] ?? "";

// Check if we're doing an --encode test
if ($encode) {
	$enc = base85::encode($encode, 2) . "\n";
	print "Encoded: $enc\n";
	die;
}

// Check if we're doing a --decode test
if ($decode) {
	$dec = base85::decode($decode, 2) . "\n";
	print "Decoded: $dec\n";
	die;
}

$one  = chr(0);
$four = chr(0) . chr(0) . chr(0) . chr(0);

////////////////////////////////////////////
// Encode
////////////////////////////////////////////

is_equal(base85::encode($one)          , '!!'          , "Encode: Null");
is_equal(base85::encode($four)         , 'z'           , 'Encode: Four nulls');
is_equal(base85::encode(' ')           , '+9'          , "Encode: Single space");
is_equal(base85::encode('    ')        , 'y'           , "Encode: Four spaces = 'y'");
is_equal(base85::encode('1111    2222'), '0ekC;y1,:U?' , "Encode: Four spaces in middle of string");

is_equal(base85::encode("food\0bat")       , 'AoDTu!+KAY'    , "Encode: Null in middle");
is_equal(base85::encode("bird\0\0\0\0bath"), '@VKjnz@UX@l'   , 'Encode: Four null in middle');
is_equal(base85::encode("Hello world")     , '87cURD]j7BEbo7', "Encode: Hello world");
is_equal(base85::encode("©Ożar")           , '_Pp>M]O>g'     , "Encode: Two unicode bytes");

is_equal(base85::encode('The quick brown fox jumps over the lazy dog.'),'<+ohcEHPu*CER),Dg-(AAoDo:C3=B4F!,CEATAo8BOr<&@=!2AA8c*5', "Encode: Quick brown fox");

is_equal(base85::encode('D9uWjh[')  , '6ofBkC1pf'   , "Encode: String #1");
is_equal(base85::encode('JCINaFj]b'), '8jc0F@7G!;@K', "Encode: String #2");
is_equal(base85::encode('knQKwu')   , 'CMm!BGBE'    , "Encode: String #3");
is_equal(base85::encode('F9IXlmG')  , '7QF%BCi)Z'   , "Encode: String #4");

// Raw non-printable data
$bytes  = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
is_equal(base85::encode($bytes),'*f_`K4Dd$)@O^eMIeR]@:`\'5)', "Encode: Unprintable chars");

// Github issues
is_equal(base85::encode(md5("test", true)),'$\'/lH7Np6%b2,mG-7?1o', "Encode: Github issue #2");

////////////////////////////////////////////
// Decode
////////////////////////////////////////////

is_equal(base85::decode('!!'), $one  , "Decode: Null");
is_equal(base85::decode('z') , $four , 'Decode: Four nulls');
is_equal(base85::decode('+9'), ' '   , "Decode: Single space");
is_equal(base85::decode('y') , '    ', "Decode: Four spaces = 'y'");

is_equal(base85::decode("AoDTu!+KAY") , "food\0bat"       , "Decode: Null in middle");
is_equal(base85::decode('@VKjnz@UX@l'), "bird\0\0\0\0bath", 'Decode: Four null in middle');
is_equal(base85::decode("_Pp>M]O>g")  , "©Ożar"           , "Decode: Two unicode bytes");

is_equal(base85::decode('6ofBkC1pf')   , 'D9uWjh['     , "Decode: String #1");
is_equal(base85::decode('8jc0F@7G!;@K'), 'JCINaFj]b'   , "Decode: String #2");
is_equal(base85::decode('CMm!BGBE')    , 'knQKwu'      , "Decode: String #3");
is_equal(base85::decode('7QF%BCi)Z')   , 'F9IXlmG'     , "Decode: String #4");
is_equal(base85::decode('0ekC;y1,:U?') , '1111    2222', "Decode: Four spaces in middle of string");

$bytes  = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
is_equal(base85::decode('*f_`K4Dd$)@O^eMIeR]@:`\'5)'), $bytes, "Decode: Unprintable chars");

///////////////////////////////////////////////////////////////////////////////////////////

$green = "\033[38;5;10m";
$red   = "\033[38;5;9m";
$reset = "\033[0m";

// Make sure we ran SOME tests
if ($pass_count === 0 && $fail_count === 0) {
	print $red . "No tests were run?\n" . $reset;
	exit(9);
}

print "\n";

if ($fail_count) {
	print $red . "$fail_count tests failed\n" . $reset;
	exit(7);
} else {
	print $green . "All $pass_count tests passed\n" . $reset;
}

////////////////////////////////////////////////////////////////////

function is_equal($input, $expected, $name = "") {
	global $pass_count;
	global $fail_count;
	global $filter;

	$x    = debug_backtrace();
	$file = $x[0]['file'];
	$line = $x[0]['line'];

	if ($name) {
		$test_name = $name;
	} else {
		$test_name = basename($file) . "#$line";
	}

	if ($filter && !preg_match("/$filter/i", $test_name)) {
		return;
	}

	$lead = "$test_name ";
	$pad  = str_repeat(" ", 80 - (strlen($lead)));

	$green    = "\033[38;5;10m";
	$red      = "\033[38;5;9m";
	$reset    = "\033[0m";
	$white    = "\033[38;5;15m";
	$ok_str   = $white . "[" . $green . "  OK  " . $reset . $white . "]" . $reset;
	$fail_str = $white . "[" . $red   . " FAIL " . $reset . $white . "]" . $reset;

	if ($input === $expected) {
		print $lead . $pad . $ok_str . "\n";

		$pass_count++;
	} else {
		print $lead . $pad . $fail_str . "\n";
		print "  * Expected '$expected' got '$input'\n";
		print "  * $file on line #$line\n";

		$fail_count++;
	}
}
