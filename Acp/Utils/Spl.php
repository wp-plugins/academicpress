<?php

class Acp_Utils_Spl {
	
	/**
	 * Intuitive implementation of array_multisort
	 * 
	 * @author http://php.net/manual/en/function.array-multisort.php#91645 (with some modifications)
	 * 
	 * @param array $array Data to be sorted
	 * @param array $columns Columns to be sorted; expects format as [columnName => SORT_DESC|SORT_ASC, column2 => [SORT_DESC|SORT_ASC, SORT_REGULAR]]
	 */
	public static function array_multisort($array, $columns) {
		$colarr = array();
		foreach ($columns as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row)
				$colarr[$col]['_'.$k] = strtolower($row[$col]);
		}
		$params = array();
		foreach ($columns as $col => $order) {
			$params[] =& $colarr[$col];
			$params = array_merge($params, (array)$order);
		}
		call_user_func_array('array_multisort', $params);
		$ret = array();
		$keys = array();
		$first = true;
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				if ($first)
					$keys[$k] = substr($k,1);
				$k = $keys[$k];
				if (!isset($ret[$k])) 
					$ret[$k] = $array[$k];
				if (isset($array[$k][$col]))
					$ret[$k][$col] = $array[$k][$col];
			}
			$first = false;
		}
		return $ret;
	}
}