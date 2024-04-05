<?php

$file = __DIR__ . "/../base85.class.php";
require($file);

class Core extends PHPUnit_Framework_TestCase
{
    public function test_base85() {
		$this->assertEquals(base85::encode("\0"),'!!');
		$this->assertEquals(base85::encode("food\0bat"),'AoDTu!+KAY');
		$this->assertEquals(base85::encode('    '),'y');
		$this->assertEquals(base85::encode(' '),'+9');
		$this->assertEquals(base85::encode(str_repeat("\0",4)),'z');
		$this->assertEquals(base85::encode("bird\0\0\0\0bath"),'@VKjnz@UX@l');

		$this->assertEquals(base85::encode('The quick brown fox jumps over the lazy dog.'),'<+ohcEHPu*CER),Dg-(AAoDo:C3=B4F!,CEATAo8BOr<&@=!2AA8c*5');
		$this->assertEquals(base85::encode("Hello world"),'87cURD]j7BEbo7');

		$this->assertEquals(base85::encode('D9uWjh['),'6ofBkC1pf');
		$this->assertEquals(base85::encode('JCINaFj]b'),'8jc0F@7G!;@K');
		$this->assertEquals(base85::encode('knQKwu'),'CMm!BGBE');
		$this->assertEquals(base85::encode('F9IXlmG'),'7QF%BCi)Z');

		// Raw non-printable data
		$bytes = base64_decode("Ho4q/TxtN3xiKfBafvdtkFAYAZ4=");
		$this->assertEquals(base85::encode($bytes),'*f_`K4Dd$)@O^eMIeR]@:`\'5)');
    }

} // End of class
?>
