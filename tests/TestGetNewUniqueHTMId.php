<?php

/************************************************************
 *
 * Command line:
 * 	phpunit tests/TestCUSDb.php
 *
 * */

//	for dev
@ ini_set( 'display_errors',		'On' );
@ ini_set( 'max_execution_time',	'0' );
@ ini_set( 'max_input_time',		'0' );
@ ini_set( 'memory_limit',		'12000M' );
@ error_reporting( E_ALL );

@ ini_set( 'date.timezone', 'Etc/GMT+0' );
@ date_default_timezone_set( 'Etc/GMT+0' );


require __DIR__ . '/../src/CUSConst.php';
require __DIR__ . '/../src/CUSDb.php';
//require __DIR__ . '/../vendor/autoload.php';


use dekuan\deuserver;


class CTestGetNewUniqueHTMId extends PHPUnit_Framework_TestCase
{
	private $m_cUSDb;

	public function __construct()
	{
		$this->m_cUSDb	= deuserver\CUSDb::GetInstance();
	}


	public function testGetNewUniqueHTMId()
	{
		$arrResult	= [];
		$nCount		= 1;
		$sDuplicateSeed	= '';
		$sDuplicateKey	= '';

		//	969-506-8
		//	969-506-5
	//	echo ( $this->m_cUSDb->GetNewUniqueHTMId( 969, 506, '969-506-8' ) );
	//	echo "\r\n";
	//	echo ( $this->m_cUSDb->GetNewUniqueHTMId( 969, 506, '969-506-5' ) );
	//	echo "\r\n";
	//	exit();


		for ( $nHostId = 101; $nHostId < 999; $nHostId ++ )
		{
			for ( $nTableId = 101; $nTableId < 999; $nTableId ++ )
			{
				for ( $i = 0; $i < 1000; $i ++ )
				{
					$sSeed	= sprintf( "%d-%d-%d", $nHostId, $nTableId, $i );
					$sKey	= $this->m_cUSDb->GetNewUniqueHTMId( $nHostId, $nTableId, $sSeed );

					if ( ! array_key_exists( $sKey, $arrResult ) )
					{
						$nCount ++;
						$arrResult[ $sKey ] = 1;
					}
					else
					{
						$sDuplicateKey	= $sKey;
						$sDuplicateSeed	= $sSeed;
						break;
					}
				}
			}
		}

		echo "\r\n";
		if ( empty( $sDuplicateKey ) )
		{
			echo "[OK] All keys are unique, count=" . $nCount . ".\r\n";
		}
		else
		{
			echo "[ERROR] Duplicate seed='" . $sDuplicateSeed . "', key='" . $sDuplicateKey . "'\r\n";
			echo "target seed=" .  $arrResult[ $sDuplicateKey ] . "\r\n";
			echo "\r\n\r\n";
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//


}