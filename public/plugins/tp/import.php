<pre>
<?php
	require_once("./lib/PHPExcel.php");	
	$inputFileName = "./docs/Staff_Planning_2013_TS.xlsx";
	
// 	Setup the schedule reader	
	$day_array = array();
	$day_array[1] = "D";
	$columns_array = range("E", "Z");	
	$appends_array = range("A", "H");
	foreach($appends_array as $append) {
		$columns_array[] = "A" . $append;
	}
	$i = 1;
	foreach($columns_array as $column) {
		$day_array[$column] = $i;
		$i++;
	}
	
//	print_r($day_array);
	
//	$inputFileType = 'Excel5';
	$inputFileType = 'Excel2007';
//	$inputFileType = 'Excel2003XML';
//	$inputFileType = 'OOCalc';
//	$inputFileType = 'SYLK';
//	$inputFileType = 'Gnumeric';
//	$inputFileType = 'CSV';
	
	class MyReadFilter implements PHPExcel_Reader_IReadFilter 
	{ 
	    private $_startRow = 0; 
	    private $_endRow   = 0; 
	    private $_columns  = array(); 

	    //  Get the list of rows and columns to read 
	    public function __construct($startRow, $endRow, $columns) { 
	        $this->_startRow = $startRow; 
	        $this->_endRow   = $endRow; 
	        $this->_columns  = $columns; 
	    } 

	    public function readCell($column, $row, $worksheetName = '') { 
	        //  Only read the rows and columns that were configured 
	        if (($row >= $this->_startRow) && ($row <= $this->_endRow)) { 
	            if (in_array($column,$this->_columns)) { 
	                return true; 
	            } 
	        } 
	        return false; 
	    }
	}
	
//	Setup the employee list reader	
	$columns_array = array("B");
	$readFilter = new MyReadFilter(4, 41, $columns_array);
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	$objReader->setLoadSheetsOnly("Employee List");
	$objReader->setReadFilter($readFilter);
//	Read the employee list from sheet
	$objPHPExcel = $objReader->load($inputFileName);
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(NULL, FALSE, FALSE, TRUE);

//	Check the database for existing entries
	$mysqli = new mysqli("localhost", "testadmin", "testpw", "test");
	$users_query =  "SELECT `id`, `first_name`, `last_name` ";
	$users_query .= "FROM `users` ";
	$users_query .= "ORDER BY `id` ASC ";
	$result = $mysqli->query($users_query);
	$users_array = array();
	while($names_array = $result->fetchAssoc()) {
		$users_array[$names_array["first_name"] . " " . $names_array["last_name"]] = $names_array["id"];
	}
	
//	print_r($users_array);
	$sheetUsers_array = array();
	
	for($i = 0; $i <= count($sheetData)-1; $i++) {
		if(!empty($sheetData[$i]["B"])) {
			$schedRow = ($i - 3) * 5;
//			echo $i . " - " . $schedRow . ": " . $sheetData[$i]["B"] . "<br />";
			$sheetUsers_array[$i] = $sheetData[$i]["B"];
		}
		
	}
//	print_r($sheetUsers_array);
	foreach($sheetUsers_array as $row => $sheetAgent) {
		if(!array_key_exists($sheetAgent, $users_array)) {
			unset($sheetUsers_array[$row]);
		}
	}
//	print_r($sheetUsers_array);
	function columnsArray($startColumn, $endColumn) {
		$startLength = 	strlen($startColumn);
		$endLength = 	strlen($endColumn);
		if($endLength < $startLength) {
			return FALSE;
		} else {
			if($startLength == 1 && $endLength == 1) {
				$columns_array = range($startColumn, $endColumn);
				return $columns_array;
			}
		}
	}
	
	$columns_array = range("A", "Z");	
	$appends_array = range("A", "H");
	foreach($appends_array as $append) {
		$columns_array[] = "A" . $append;
	}
	
	
	class chunkReadFilter implements PHPExcel_Reader_IReadFilter
	{
		private $_startRow = 0;
		private $_endRow = 0;
		private $_columns  = array(); 

		//  We expect a list of the rows that we want to read to be passed into the constructor
		public function __construct($startRow, $chunkSize, $columns) {
			$this->_startRow	= $startRow;
			$this->_endRow		= $startRow + $chunkSize;
			$this->_columns		= $columns;
		}

		public function readCell($column, $row, $worksheetName = '') {
			//  Only read the heading row, and the rows that were configured in the constructor
			if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
				if (in_array($column,$this->_columns)) { 
	                return true; 
	            }
			}
			return false;
		}
	}
	
	$chunkSize = 5;
	
	for($startRow = 5; $startRow <= 160; $startRow += $chunkSize) {
		$agentRow = ($startRow / 5) + 3;
		if(array_key_exists($agentRow, $sheetUsers_array)) {
			$endRow = $startRow + 4;
			$filterSubset = new chunkReadFilter($startRow, $chunkSize, $columns_array);
			$sheetNames_array = array("July"); 
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
//			$objReader->setReadDataOnly(true);
			$objReader->setLoadSheetsOnly($sheetNames_array); 
			$objReader->setReadFilter($filterSubset);
			$objPHPExcel = $objReader->load($inputFileName);
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(NULL, FALSE, FALSE, TRUE);
//			print_r($sheetData[$startRow]);
			foreach($sheetData[$startRow] as $column => $value) {
				if(array_key_exists($column, $day_array)) {
					if(!(empty($value) && !is_numeric($value))) {
						$day = (strlen($day_array[$column]) == 1) ? ("0" . $day_array[$column]) : ($day_array[$column]);
						echo $sheetUsers_array[$agentRow] . " 2013-07-" . $day . " " . $value . "<br />";
					}
				}
			}
		}
		
	}
	
?>


<?php
	
//	$sheetComments = $objPHPExcel->getActiveSheet()->getComments();
//	print_r($sheetComments);
	
//	print_r($sheetData);
//	print_r($objPHPExcel); 
 ?>
</pre>