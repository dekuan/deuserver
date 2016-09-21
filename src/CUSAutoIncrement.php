<?php

namespace dekuan\deuserver;


/**
 *	class of CUSAutoIncrement
 */
class CUSAutoIncrement
{
	protected static $m_cServiceIns;


	public function __construct()
	{
	}
	public function __destruct()
	{
	}
	static function GetInstance()
	{
		if ( is_null( self::$m_cServiceIns ) || ! isset( self::$m_cServiceIns ) )
		{
			self::$m_cServiceIns = new self();
		}
		return self::$m_cServiceIns;
	}

	public function UTest()
	{
		//	...
	}

	public function GetCreateTableSQL( $sTableName )
	{
		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName ) )
		{
			return '';
		}

		return "CREATE TABLE " . addslashes( $sTableName ) . "(" .
		"tc_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT," .
		"tc_stub char(1) NOT NULL DEFAULT ''," .
		"PRIMARY KEY (tc_id)," .
		"UNIQUE KEY tc_stub ( tc_stub )" .
		") Engine=InnoDB DEFAULT CHARSET=utf8;";
	}
	public function GetExecuteSQL( $sTableName )
	{
		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName ) )
		{
			return '';
		}

		return "REPLACE INTO " . addslashes( $sTableName ) . " ( tc_stub ) VALUES ('a');";
	}
	public function GetSelectSQL()
	{
		return "SELECT LAST_INSERT_ID();";
	}
	public function GetSelectSQLWithTableName( $sTableName )
	{
		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName ) )
		{
			return '';
		}

		return "SELECT tc_id FROM " . addslashes( $sTableName ) . " LIMIT 1;";
	}
}
