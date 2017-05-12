<?php
	/**
		-
	*/

	class UsePayment extends dbConnect {

		private $sd = null;

		function __construct( $method = 1 ) {
			$this->md = (isset($_POST['md']))? 2 : $method ;
			// Make sure we actually have a text to reply to
			if (!$this->inboundText()) {
				return false;
			}
			
			return $this->updatePayment();
		}               

		public function inboundText(){
			if(!$this->sd){ $this->sd = ($this->md == 1)? $_GET : $_POST ; }
			if(!isset($this->sd["INVOICE_NO"], $this->sd["RECEIPT"])){ return false ; }
			
			return true;
		}

		function checkPayment( $result = null ){

			$sql_array  = array( $this->sd["RECEIPT"] );
			$sql_texts  = "SELECT * FROM `tbl_payment` WHERE tbl_payment.RECEIPT = ? LIMIT 1;";

			$db_sql = $this->pdoExecsql(PR_DATABASE, PR_USERNAME, $sql_texts, $sql_array);

			$sqlrow = ($db_sql != NULL)? $db_sql->rowCount() : 0 ;
			if($sqlrow > 0){
				$this->dt = $db_sql->fetch(PDO::FETCH_ASSOC);

				$db_sql = NULL;
				return 0;
			}

			$db_sql = NULL;
			$this->ec = json_encode(array("status" => "failed", "errorno" => 2, "data" => array('msg' => 'does not exist')));
			return $this->ouTput();
		}

		function updatePayment(){

			if($this->checkPayment() == 0){
				$sql_array  = array( 1, $this->sd["INVOICE_NO"], 2, $this->dt["ID"] );
				$sql_texts  = "UPDATE tbl_payment SET tbl_payment.`STATUS` = ?, tbl_payment.INVOICE_NO = ? WHERE tbl_payment.`STATUS` = ? AND tbl_payment.ID = ?;";

				$db_sql = $this->pdoExecsql(PR_DATABASE, PR_USERNAME, $sql_texts, $sql_array);
				$sqlrow = ($db_sql != NULL)? $db_sql->rowCount() : 0 ;
				unset($db_sql);

				if($sqlrow > 0){
					$this->ec = json_encode(array("status" => "success", "errorno" => 0, "data" => $this->dt ));
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
	$payment = new UsePayment();
	#----------------------------------------------------------------------------------
?>