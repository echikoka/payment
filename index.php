<?php
	require_once './Connect/index.php' ;

	class Payment extends dbConnect {

		private $sd = null;

		function __construct( $method = 1 ) {
			$this->md = (isset($_POST['md']))? 2 : $method ;
			// Make sure we actually have a text to reply to
			if (!$this->inboundText()) {
				return false;
			}
			
			return $this->iPay();
		}               

		public function inboundText(){
			if(!$this->sd){ $this->sd = ($this->md == 1)? $_GET : $_POST ; }
			if(!isset($this->sd["RECEIPT"], $this->sd["task"])){ return false ; }
			if(strlen($this->sd["RECEIPT"]) < 5){
				return false;
			}

			return true;
		}

		function iPay(){

			switch (trim($this->sd["task"])) {
				case 'get':
					$get_money = new GetPayment;
				break;
				case 'use':
					$use_money = new UsePayment;
				break;
			}

		}
	}

	#----------------------------------------------------------------------------------
	$payment = new Payment();
	#----------------------------------------------------------------------------------
?>