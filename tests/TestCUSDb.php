<?php

/************************************************************
 *
 * Command line:
 * 	phpunit tests/TestCUSDb.php
 *
 * */

//	for dev
@ ini_set( 'display_errors',		'On' );
@ ini_set( 'max_execution_time',	'600' );
@ ini_set( 'max_input_time',		'0' );
@ ini_set( 'memory_limit',		'512M' );
@ error_reporting( E_ALL );

@ ini_set( 'date.timezone', 'Etc/GMT+0' );
@ date_default_timezone_set( 'Etc/GMT+0' );


require __DIR__ . '/../src/CUSConst.php';
require __DIR__ . '/../src/CUSDb.php';
//require __DIR__ . '/../vendor/autoload.php';


use dekuan\deuserver;


class TestCUSDb extends PHPUnit_Framework_TestCase
{
	private $m_cUSDb;

	public function __construct()
	{
		$this->m_cUSDb	= deuserver\CUSDb::GetInstance();
	}


	public function testGetTableInfoByKey()
	{
		$bSuccess	= false;
		$arrTable	= $this->m_cUSDb->GetTableInfoByKey( 'user_table', 'unm:xingxing@gmail.com' );
		if ( is_array( $arrTable ) &&
			array_key_exists( 'hostid', $arrTable ) &&
			array_key_exists( 'tableid', $arrTable ) &&
			array_key_exists( 'table', $arrTable ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetTableInfoByKey', -1, $bSuccess, $arrTable );
	}
	public function testGetTableInfoWithMid()
	{
		$bSuccess	= false;
		$arrTable	= $this->m_cUSDb->GetTableInfoWithMid( 'uextend_table', '1011491015223910134582261886' );
		if ( is_array( $arrTable ) &&
			array_key_exists( 'hostid', $arrTable ) &&
			array_key_exists( 'tableid', $arrTable ) &&
			array_key_exists( 'table', $arrTable ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetTableInfoWithMid', -1, $bSuccess, $arrTable );
	}
	public function testGetTableInfo_Others()
	{
		$bSuccess	= false;
		$arrTable	= $this->m_cUSDb->GetTableInfo( 'verifycode_table', '1011491015223910134582261886' );
		if ( is_array( $arrTable ) &&
			array_key_exists( 'hostid', $arrTable ) &&
			array_key_exists( 'tableid', $arrTable ) &&
			array_key_exists( 'table', $arrTable ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetTableInfo', -1, $bSuccess, $arrTable );
	}



	public function testGetNewTableName()
	{
		$bSuccess	= false;
		$sTableName	= 'user_table';
		$sNewTableName	= $this->m_cUSDb->GetNewTableName( $sTableName, 10 );
		if ( is_string( $sNewTableName ) &&
			strlen( $sNewTableName ) > 0 &&
			strstr( $sNewTableName, $sTableName ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetNewTableName', -1, $bSuccess );
	}
	public function testGetHashTableId()
	{
		$bSuccess	= false;
		$nTableId	= $this->m_cUSDb->GetHashTableId( 'unm:xingxing@gmail.com', 100 );
		if ( is_numeric( $nTableId ) && -1 != $nTableId )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetNewTableName', -1, $bSuccess );
	}

	public function testIsValidHostId()
	{
		$bSuccess	= false;
		if ( false == $this->m_cUSDb->IsValidHostId( 'abc' ) &&
			false == $this->m_cUSDb->IsValidHostId( -1 ) &&
			false == $this->m_cUSDb->IsValidHostId( 1002 ) &&
			true == $this->m_cUSDb->IsValidHostId( 99 ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'IsValidHostId', -1, $bSuccess );
	}
	public function testIsValidTableId()
	{
		$bSuccess	= false;
		if ( false == $this->m_cUSDb->IsValidTableId( 'abc' ) &&
			false == $this->m_cUSDb->IsValidTableId( -1 ) &&
			false == $this->m_cUSDb->IsValidTableId( 1002 ) &&
			true == $this->m_cUSDb->IsValidTableId( 99 ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'IsValidTableId', -1, $bSuccess );
	}


	public function testIsValidMId()
	{
		$bSuccess	= false;
		if ( false == $this->m_cUSDb->IsValidMId( -1 ) &&
			false == $this->m_cUSDb->IsValidMId( '101149101' ) &&
			false == $this->m_cUSDb->IsValidMId( '0010491015223910134582261886' ) &&
			true == $this->m_cUSDb->IsValidMId( '1011491015223910134582261886' ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'IsValidMId', -1, $bSuccess );
	}

	public function testGetNewUniqueHTMId()
	{
		$bSuccess	= false;
		$sUniqueHTMId	= $this->m_cUSDb->GetNewUniqueHTMId( 100, 20, 'sdfdfdffdf' );
		if ( is_string( $sUniqueHTMId ) &&
			strlen( $sUniqueHTMId ) > 0 &&
			$this->m_cUSDb->IsValidMId( $sUniqueHTMId ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetNewUniqueHTMId', -1, $bSuccess );
	}

	public function testGetNewUniqueMId()
	{
		$bSuccess	= false;
		$sUniqueMId	= $this->m_cUSDb->GetNewUniqueMId( 'sdfdfdffdf' );
		if ( is_string( $sUniqueMId ) &&
			strlen( $sUniqueMId ) > 0 )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetNewUniqueMId', -1, $bSuccess );
	}

	public function testGetRandomByMicrotime()
	{
		$bSuccess	= false;
		$sRandom	= $this->m_cUSDb->GetRandomByMicrotime();
		if ( is_string( $sRandom ) &&
			strlen( $sRandom ) > 0 )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetRandomByMicrotime', -1, $bSuccess );
	}

	public function testGetRandomBySeed()
	{
		$bSuccess	= false;
		$nDigitCount	= 7;
		$sRandom	= $this->m_cUSDb->GetRandomBySeed( 'unm:xingxing@gmail.com', $nDigitCount );
		if ( is_string( $sRandom ) &&
			$nDigitCount == strlen( $sRandom ) )
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetRandomBySeed', -1, $bSuccess );
	}

	public function testParseMId()
	{
		$bSuccess	= false;
		$nDigitCount	= 7;
		$arrMIdRight	= $this->m_cUSDb->ParseMId( '1011491015223910134582261886' );
		$arrMIdWrong1	= $this->m_cUSDb->ParseMId( '0010491015223910134582261886' );
		$arrMIdWrong2	= $this->m_cUSDb->ParseMId( '10114910152239101345' );
		$arrMIdWrong3	= $this->m_cUSDb->ParseMId( 'abc' );
		if ( is_array( $arrMIdRight ) &&
			array_key_exists( 'hostid', $arrMIdRight ) &&
			array_key_exists( 'tableid', $arrMIdRight ) &&
			array_key_exists( 'mid', $arrMIdRight ) &&
			is_array( $arrMIdWrong1 ) && 0 == count( $arrMIdWrong1 ) &&
			is_array( $arrMIdWrong2 ) && 0 == count( $arrMIdWrong2 ) &&
			is_array( $arrMIdWrong3 ) && 0 == count( $arrMIdWrong3 )
		)
		{
			$bSuccess = true;
		}

		$this->_OutputResult( __FUNCTION__, 'GetRandomBySeed', -1, $bSuccess );
	}



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	protected function _OutputResult( $sFuncName, $sCallMethod, $nErrorId, $bAssert, $vData = null )
	{
		printf( "\r\n# %s->%s\r\n", $sFuncName, $sCallMethod );
		printf( "# ErrorId : %6d, result : [%s]", $nErrorId, ( $bAssert ? "OK" : "ERROR" ) );
		printf( "\r\n" );
		if ( is_array( $vData ) )
		{
			print_r( $vData );
		}
		else
		{
			var_dump( $vData );
		}

		$this->assertTrue( $bAssert );
	}
}