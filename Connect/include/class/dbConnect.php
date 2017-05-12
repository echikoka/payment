<?php
	class dbConnect {
		
		//	PDO APPLICATION - DB CONNECT
		function db_Connect($db_name, $db_user){
			//	---
			try{
					$db = new PDO("mysql:host=". DB_LOCATION .";dbname=". $db_name, $db_user, DB_PASSWORD);
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					
					return $db;
				}catch(PDOException $e){
					#	echo "Sorry, Connection Failed";
					file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
			}

			return NULL;
		}
		
		//	PDO APPLICATION - SQL QUERY EXCUTE
		function pdoExecsql($db_name, $db_user, $sqltext, $arra_value){
			try{
					$connects = $this->db_Connect($db_name, $db_user);
					$pre_sqll = $connects->prepare($sqltext);
					$pre_sqll->execute($arra_value);
					$connects = NULL;
					
					return $pre_sqll;
				}catch(PDOException $e){
					#	echo "Sorry, Query Error";
					file_put_contents('PDOErrorexecute.txt', $e->getMessage(), FILE_APPEND);
			}
			
			return NULL;
		}
		
	}
?>