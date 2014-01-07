<?PHP

#base85::run_tests();
#print base85::encode("Hello World");    // Should be: 87cURD]i,"Ebo7
#print base85::decode('87cURD]i,"Ebo7'); // Should be: Hello World

/////////////////////////////////////////////////////////////////

class base85 
{

	static function run_tests() {
		base85::my_test('The quick brown fow jumps over the lazy dog.','<+ohcEHPu*CER),Dg-(AAoDl9C3=B4F!,CEATAo8BOr<&@=!2AA8c*5');
		base85::my_test("Hello world",'87cURD]j7BEbo7');

		base85::my_test('D9uWjh[','6ofBkC1pf');
		base85::my_test('JCINaFj]b','8jc0F@7G!;@K');
		base85::my_test('knQKwu','CMm!BGBE');
		base85::my_test('F9IXlmG','7QF%BCi)Z');

		// Raw non-printable data
		$bytes = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
		base85::my_test($bytes,'*f_`K4Dd$)@O^eMIeR]@:`\'5)');
	}

	public static function decode($str) {
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

	public static function encode($str) {
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

	private static function my_test($str,$result) {
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

}
