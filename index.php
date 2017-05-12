<?php
	require_once '../Connect/index.php' ;

	class Payment extends dbConnect {

		private $sd = null;

		function __construct( $method = 1 ) {
			$this->md = (isset($_POST['md']))? 2 : $method ;
			// Make sure we actually have a text to reply to
			if (!$this->inboundText()) {
				return false;
			}
			
			return $this->savePayment();
		}               

		public function inboundText(){
			if(!$this->sd){ $this->sd = ($this->md == 1)? $_GET : $_POST ; }
			if(!isset($this->sd["SUPER_TYPE"], $this->sd["TYPE"], $this->sd["RECEIPT"], $this->sd["TIME"], $this->sd["PHONE"], $this->sd["NAME"], $this->sd["AMOUNT"])){ return false ; }
			
			return true;
		}

		function checkPayment( $result = null ){

			$sql_array  = array( $this->sd["RECEIPT"] );
			$sql_texts  = "SELECT * FROM `tbl_payment` WHERE tbl_payment.RECEIPT = ? LIMIT 1;";

			$db_sql = $this->pdoExecsql(PR_DATABASE, PR_USERNAME, $sql_texts, $sql_array);

			$sqlrow = ($db_sql != NULL)? $db_sql->rowCount() : 0 ;
			if($sqlrow > 0){

				$db_sql = NULL;
				$this->ec = json_encode(array("status" => "failed", "errorno" => 2, "data" => array('msg' => 'exist')));
				return $this->ouTput();
			}

			$db_sql = NULL;
			return 0;
		}

		function savePayment(){

			$sql_array  = array( $this->sd["SUPER_TYPE"], $this->sd["TYPE"], $this->sd["RECEIPT"], $this->sd["TIME"], $this->sd["PHONE"], $this->sd["NAME"], $this->sd["AMOUNT"] );
			$sql_texts  = "INSERT INTO tbl_payment( SUPER_TYPE, TYPE, RECEIPT, `TIME`, PHONE, `NAME`, AMOUNT ) VALUES( ?, ?, ?, ?, ?, ?, ? );";

			if($this->checkPayment() == 0){
				$db_sql = $this->pdoExecsql(PR_DATABASE, PR_USERNAME, $sql_texts, $sql_array);
				$sqlrow = ($db_sql != NULL)? $db_sql->rowCount() : 0 ;
				unset($db_sql);

				if($sqlrow > 0){

					$this->ec = json_encode(array("status" => "success", "errorno" => 0, "data" => array('msg' => 'success')));
					return $this->ouTput();
				} else {
					$this->ec = json_encode(array("status" => "failed", "errorno" => 1, "data" => array('msg' => 'failed')));
				}

			}

			return $this->ouTput();
		}

		function ouTput(){

			//	Avoid caching
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			header("Content-type: application/json; charset=utf-8");

			if (isset($this->ec)) {
				echo($this->ec);
			}
		}
	}

	#----------------------------------------------------------------------------------
	$payment = new Payment();
	#----------------------------------------------------------------------------------
?>