<?PHP

my_test('ecH`','ARe8=');
my_test('Xrs25u5I<','=E8I<20CcJ49');
my_test('iu_Hr2H}','Bm!cRE\\^16');
my_test('ecH`','ARe8=');
my_test("Hello world",'87cURD]j7BEbo7');
my_test('Fw1','7X$Q');
my_test('g3^','B/<i');
my_test('SC',';aU');
my_test('D9uWjh[','6ofBkC1pf');
my_test('JCINaFj]b','8jc0F@7G!;@K');
my_test('6;r`bV','2EPqe@TE');
my_test(':?r7','3^7XD');
my_test(']','>l');
my_test('^','?2');
my_test('`X','?s!');
my_test('Z8vg','=u^\\<');
my_test('knQKwu','CMm!BGBE');
my_test('G\\;=ohOwH','7pJ,=DeMpj8,');
my_test('F9IXlmG','7QF%BCi)Z');

/////////////////////////////////////////////////////////////////

function base85_decode($str) {
	$str = preg_replace("/ \t\r\n\f/","",$str);
	$str = preg_replace("/z/","!!!!!",$str);
	$str = preg_replace("/y/","+<VdL/",$str);

	// Pad the end of the string so it's a multiple of 5
	$padding = 5 - (strlen($str) % 5);
	if (strlen($str) % 5 === 0) {
		$padding = 0;
	}
	$str .= str_repeat('u',$padding);

	$num = 0;
	$ret = '';

	// Foreach 5 chars, convert it to an integer
	while ($chunk = substr($str, $num * 5, 5)) {
		$tmp = 0;

		foreach (unpack('C*',$chunk) as $item) {
			$tmp *= 85;
			$tmp += $item - 33;
		}

		// Conver the integer in to a string
		$ret .= pack('N', $tmp);

		$num++;
	}

	// Remove any padding we had to add
	$ret = substr($ret,0,strlen($ret) - $padding);

	return $ret;
}

function base85_encode($str) {
	$ret   = '';
	$debug = 0;

	$padding = 4 - (strlen($str) % 4);
	if (strlen($str) % 4 === 0) {
		$padding = 0;
	}

	if ($debug) {
		printf("Length: %d = Padding: %s<br /><br />\n",strlen($str),$padding);
	}

	// If we don't have a four byte chunk, append \0s
	$str .= str_repeat("\0", $padding);

	foreach (unpack('N*',$str) as $chunk) {
		// If there is an all zero chunk, it has a shortcut of 'z'
		if ($chunk == "\0") {
			$ret .= "z";
			continue;
		}

		// Four spaces has a shortcut of 'y'
		if ($chunk == unpack('N', '    ')) {
			$ret .= "y";
			continue;
		}

		if ($debug) {
			var_dump($chunk); print "<br />\n";
		}

		// Convert the integer into 5 "quintet" chunks
		for ($a = 0; $a < 5; $a++) {
			$b	= intval($chunk / (pow(85,4 - $a)));
			$ret .= chr($b + 33);

			if ($debug) {
				printf("%03d = %s <br />\n",$b,chr($b+33));
			}

			$chunk -= $b * pow(85,4 - $a);
		}
	}

	// If we added some null bytes, we remove them from the final string
	if ($padding) {
		$ret = substr($ret,0,strlen($ret) - $padding);
	}

	return $ret;
}

function my_test($str,$result) {
	$enc     = base85_encode($str);
	$correct = intval($result == $enc);
	$raw_str = $str;

	$enc = htmlentities($enc);
	$str = htmlentities($str);

	if ($correct) {
		$color = "lightgreen";
	} else {
		$color = "red";
	}

	print "<div>Testing: \"$str\"</div>\n";
	print "<span style=\"background-color: $color\">Encode: \"$str\" => \"$enc\"</span>\n";
	print "<br />";

	$decoded = base85_decode($result);
	$correct = intval($decoded == $raw_str);

	if ($correct) {
		$color = "lightgreen";
	} else {
		$color = "red";
	}

	$result  = htmlentities($result);
	$decoded = htmlentities($decoded);
	print "<span style=\"background-color: $color\">Decode: \"$result\" => \"$decoded\"</span>\n";
	print "<br />";
	print "<br />";
}
