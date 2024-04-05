<?PHP

#error_reporting(E_ALL);

// Examples:
// $str = base85::encode("Hello world!");
// $str = base85::decode(":e4D*;K$&\Er");

class base85
{

	public static function decode($str, $debug = 0) {
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

			// Convert the integer in to a string
			$ret .= pack('N', $tmp);

			$num++;
		}

		// Remove any padding we had to add
		$ret = substr($ret,0,strlen($ret) - $padding);

		return $ret;
	}

	public static function encode32($str, $debug = 0) {
		$ret   = "";
		$count = 1;

		// We loop through $str pulling out four bytes at a time and building an integer
		while ($str) {
			$chunk = (ord($str[0]) << 24) + (ord($str[1]) << 16) + (ord($str[2]) << 8) + (ord($str[3]));
			$chunk = sprintf("%u", $chunk);
			$chunk = gmp_init($chunk);

			// We've processed these four bytes so we remove them
			$str = substr($str, 4);

			if ($debug) {
				printf("byte #%d = %s<br />\n", $count, gmp_strval($chunk));
				$count++;
			}

			// If there is an all zero chunk, it has a shortcut of 'z'
			if (gmp_cmp($chunk, 0) == 0) {
				$ret .= "z";
				continue;
			}

			// Four spaces has a shortcut of 'y'
			if (gmp_cmp($chunk, 538976288) == 0) {
				$ret .= "y";
				continue;
			}

			// Convert the integer into 5 "quintet" chunks
			for ($a = 0; $a < 5; $a++) {
				$part = gmp_pow(85, 4 - $a);
				$b    = gmp_intval(gmp_div_q($chunk, $part));
				$ret .= chr($b + 33);

				if ($debug > 1) {
					printf("%03d = %s <br />\n",$b,chr($b+33));
				}

				$chunk -= gmp_mul($b, $part);
			}
		}

		return $ret;
	}

	public static function encode64($str, $debug = 0) {
		$ret = "";

		foreach (unpack('N*',$str) as $chunk) {
			// If there is an all zero chunk, it has a shortcut of 'z'
			if ($chunk == 0) {
				$ret .= "z";
				continue;
			}

			// Four spaces has a shortcut of 'y'
			if ($chunk == 538976288) {
				$ret .= "y";
				continue;
			}

			if ($debug) {
				var_dump($chunk);
				//print "<br />\n";
			}

			// Convert the integer into 5 "quintet" chunks
			for ($a = 0; $a < 5; $a++) {
				$b	= intval($chunk / (pow(85,4 - $a)));
				$ret .= chr($b + 33);

				if ($debug) {
					//printf("%03d = %s <br />\n",$b,chr($b+33));
				}

				$chunk -= $b * pow(85,4 - $a);
			}
		}

		return $ret;
	}

	public static function encode($str, $debug = 0) {
		$ret   = '';

		$padding = 4 - (strlen($str) % 4);
		if (strlen($str) % 4 === 0) {
			$padding = 0;
		}

		if ($debug) {
			printf("\$input = hex2bin(%s);\n", bin2hex($str));
			printf("Length: %d bytes / Padding: %s\n",strlen($str),$padding);
			print "\n";
		}

		// If we don't have a four byte chunk, append \0s
		$str .= str_repeat("\0", $padding);

		///////////////////////////////////////////////////////////////////////////

		$is_32bit = PHP_INT_SIZE == 4;

		if ($is_32bit) {
			$ret = base85::encode32($str, $debug);
		} else {
			$ret = base85::encode64($str, $debug);
		}

		///////////////////////////////////////////////////////////////////////////

		// If we added some null bytes, we remove them from the final string
		if ($padding) {
			$ret = preg_replace("/z$/",'!!!!!',$ret);
			$ret = substr($ret,0,strlen($ret) - $padding);
		}

		return $ret;
	}

} // End of base85 class
