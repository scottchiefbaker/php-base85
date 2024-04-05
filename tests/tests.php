<?php

$file = __DIR__ . "/../base85.class.php";
require($file);

$pass_count = 0;
$fail_count = 0;

is_equal(base85::encode("\0"),'!!', "Null");
is_equal(base85::encode('    '),'y', "Four spaces = 'y'");
is_equal(base85::encode(' '),'+9', "Single space");
is_equal(base85::encode(str_repeat("\0",4)),'z', 'Four nulls');

is_equal(base85::encode("food\0bat"),'AoDTu!+KAY', "Null in middle");
is_equal(base85::encode("bird\0\0\0\0bath"),'@VKjnz@UX@l', 'Four null in middle');
is_equal(base85::encode('The quick brown fox jumps over the lazy dog.'),'<+ohcEHPu*CER),Dg-(AAoDo:C3=B4F!,CEATAo8BOr<&@=!2AA8c*5', "Quick brown fox");
is_equal(base85::encode("Hello world"),'87cURD]j7BEbo7', "Hello world");

is_equal(base85::encode('D9uWjh['),'6ofBkC1pf', "String #1");
is_equal(base85::encode('JCINaFj]b'),'8jc0F@7G!;@K', "String #2");
is_equal(base85::encode('knQKwu'),'CMm!BGBE', "String #3");
is_equal(base85::encode('F9IXlmG'),'7QF%BCi)Z', "String #4");

// Raw non-printable data
$bytes  = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
is_equal(base85::encode($bytes),'*f_`K4Dd$)@O^eMIeR]@:`\'5)', "Unprintable chars");

// Github issues
is_equal(base85::encode(md5("test", true)),'$\'/lH7Np6%b2,mG-7?1o', "Github issue #2");

print "\n";

$green = "\033[38;5;10m";
$red   = "\033[38;5;9m";
$reset = "\033[0m";

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

	if ($name) {
		$test_name = $name;
	} else {
		$x         = debug_backtrace();
		$file      = $x[0]['file'];
		$line      = $x[0]['line'];
		$test_name = basename($file) . "#$line";
	}

	$lead = "Test: $test_name ";
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

		$fail_count++;
	}
}
