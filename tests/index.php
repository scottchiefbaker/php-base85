<?PHP

require("../base85.class.php");

run_tests();

/////////////////////////////////////////////////////////////////

function run_tests() {
	run_test("\0",'!!');
	run_test("food\0bat",'AoDTu!+KAY');
	run_test('    ','y');
	run_test(' ','+9');
	run_test(str_repeat("\0",4),'z');
	run_test("bird\0\0\0\0bath",'@VKjnz@UX@l');

	run_test('The quick brown fox jumps over the lazy dog.','<+ohcEHPu*CER),Dg-(AAoDo:C3=B4F!,CEATAo8BOr<&@=!2AA8c*5');
	run_test("Hello world",'87cURD]j7BEbo7');

	run_test('D9uWjh[','6ofBkC1pf');
	run_test('JCINaFj]b','8jc0F@7G!;@K');
	run_test('knQKwu','CMm!BGBE');
	run_test('F9IXlmG','7QF%BCi)Z');

	// Raw non-printable data
	$bytes = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
	run_test($bytes,'*f_`K4Dd$)@O^eMIeR]@:`\'5)');
}

function run_test($str,$result) {
	$enc     = base85::encode($str);
	$correct = intval($result == $enc);
	$raw_str = $str;

	$enc = htmlentities($enc);
	$str = htmlentities($str);

	if ($correct) {
		$color = "lightgreen";
	} else {
		$color = "red";
	}

	if (!ctype_print($str)) {
		$str = "<code><b>" . md5($str) . "</b></code>";
	} else {
		$str = "\"$str\"";
	}

	print "<div>Testing: $str</div>\n";
	print "<span style=\"background-color: $color\">Encode: $str => \"$enc\"</span>\n";
	print "<br />";

	$decoded = base85::decode($result);
	$correct = intval($decoded == $raw_str);

	if ($correct) {
		$color = "lightgreen";
	} else {
		$color = "red";
	}

	$result  = htmlentities($result);
	$decoded = htmlentities($decoded);

	if (!ctype_print($decoded)) {
		$decoded = "<code><b>" . md5($decoded) . "</b></code>";
	} else {
		$decoded = "\"$decoded\"";
	}

	print "<span style=\"background-color: $color\">Decode: \"$result\" => $decoded</span>\n";
	print "<br />";
	print "<br />";
}

function raw_dump($raw,$format = "dec",$printable = 0) {
	$str   = unpack("H*hex",$raw);
	$bytes = str_split($str['hex'],2);

	foreach ($bytes as &$i) {
		$i = "0x$i"; // Hex representation

		// Convert the hex to decimal
		if ($format != 'hex') {
			$i = hexdec($i);
		}

		$char = chr($i);

		if ($printable) {
			if (!ctype_print($char)) {
				$char = "";
			}

			$i .= " [<b>$char</b>]";
		}
	}

	$ret = join(" ",$bytes);

	return $ret;
}
