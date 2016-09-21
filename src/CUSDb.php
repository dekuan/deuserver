<?php

namespace dekuan\deuserver;

/**
 *	class of CUSDb
 */
class CUSDb
{
	protected static $m_cServiceIns;

	//	min length of HTMID
	const CONST_HTMID_MIN_LENGTH	= 24;


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

	//
	//	get table info
	//
	public function GetTableInfo( $sTableName, $sKey )
	{
		//
		//	sTableName	- table name
		//	sKey		- username or u_mid
		//	RETURN		- array of data
		//
		$ArrRet	= [];

		if ( ! $this->IsValidTableName( $sTableName ) ||
			! is_string( $sKey ) || 0 == strlen( $sKey ) )
		{
			return [];
		}

		if ( 0 == strcasecmp( CUSConst::DB_USER_TABLE, $sTableName ) )
		{
			//	sKey is the username
			$ArrRet = $this->GetTableInfoByKey( $sTableName, $sKey );
		}
		else if ( 0 == strcasecmp( CUSConst::DB_UEXTEND_TABLE, $sTableName ) ||
			0 == strcasecmp( CUSConst::DB_UBIND_TABLE, $sTableName ) ||
			0 == strcasecmp( CUSConst::DB_UASSOCIATE_TABLE, $sTableName ) )
		{
			//	sKey is the u_mid
			$ArrRet = $this->GetTableInfoWithMid( $sTableName, $sKey );
		}
		else if ( 0 == strcasecmp( CUSConst::DB_VERIFYCODE_TABLE, $sTableName ) )
		{
			$ArrRet =
			[
				'hostid'	=> intval( CUSConst::DB_DEFAULT_HOST_ID ),
				'tableid'	=> intval( 0 ),
				'table'		=> $sTableName
			];
		}

		return $ArrRet;
	}

	//
	//	get table info by key
	//
	public function GetTableInfoByKey( $sTableName, $sKey )
	{
		//
		//	RETURN	- Array( 'hostid', 'tableid', 'table' );
		//
		$ArrRet	= [];

		if ( ! $this->IsValidTableName( $sTableName ) ||
			! is_string( $sKey ) || 0 == strlen( $sKey ) )
		{
			return [];
		}

		$nHostId	= CUSConst::DB_DEFAULT_HOST_ID;
		$nTableId	= $this->GetHashTableId( $sKey, CUSConst::DB_TABLE_AMOUNT );
		if ( $nTableId >= 0 )
		{
			$ArrRet =
			[
				'hostid'	=> intval( $nHostId ),
				'tableid'	=> intval( $nTableId ),
				'table'		=> $this->GetNewTableName( $sTableName, $nTableId )
			];
		}

		return $ArrRet;
	}

	//
	//	get table info with mid
	//
	public function GetTableInfoWithMid( $sTableName, $u_mid )
	{
		//
		//	RETURN	- Array( 'hostid', 'tableid', 'table' );
		//
		$ArrRet = [];

		//	...
		$nHostId	= 0;
		$nTableId	= -1;

		if ( ! $this->IsValidTableName( $sTableName ) ||
			! is_string( $u_mid ) || 0 == strlen( $u_mid ) )
		{
			return [];
		}

		//	...
		$ArrMIdInfo = $this->ParseMId( $u_mid );
		if ( $ArrMIdInfo && is_array( $ArrMIdInfo ) && count( $ArrMIdInfo ) &&
			array_key_exists( 'hostid', $ArrMIdInfo ) &&
			array_key_exists( 'tableid', $ArrMIdInfo ) )
		{
			$nHostId	= $ArrMIdInfo['hostid'];
			$nTableId	= $ArrMIdInfo['tableid'];
			if ( $nTableId >= 0 )
			{
				$ArrRet =
				[
					'hostid'	=> intval( $nHostId ),
					'tableid'	=> intval( $nTableId ),
					'table'		=> $this->GetNewTableName( $sTableName, $nTableId )
				];
			}
		}

		//	...
		return $ArrRet;
	}

	public function GetNewTableName( $sTableName, $nTableId )
	{
		if ( ! $this->IsValidTableName( $sTableName ) || ! $this->IsValidTableId( $nTableId ) )
		{
			return '';
		}
		return ( $sTableName . '_' . strval( $nTableId ) );
	}

	//	get hash table id
	public function GetHashTableId( $sKey, $nMaxCount )
	{
		$nRet	= -1;

		if ( ! is_string( $sKey ) || 0 == strlen( $sKey ) || ! is_numeric( $nMaxCount ) )
		{
			return -1;
		}

		//	...
		$sKey = strtolower( trim( $sKey ) );
		if ( is_string( $sKey ) && strlen( $sKey ) > 0 )
		{
			$nCrc = crc32( $sKey );
			$nRet = ( abs( $nCrc ) % $nMaxCount );
		}

		return $nRet;
	}


	public function IsValidTableInfo( $arrTableInfo )
	{
		return ( is_array( $arrTableInfo ) &&
			array_key_exists( 'hostid', $arrTableInfo ) &&
			array_key_exists( 'tableid', $arrTableInfo ) &&
			array_key_exists( 'table', $arrTableInfo ) &&
			$this->IsValidHostId( $arrTableInfo['hostid'] ) &&
			$this->IsValidTableId( $arrTableInfo['tableid'] ) &&
			$this->IsValidTableName( $arrTableInfo['table'] ) );
	}

	public function IsValidHostId( $nHostId )
	{
		return ( is_numeric( $nHostId ) && $nHostId >= 0 && $nHostId < 1000 );
	}

	public function IsValidTableId( $nTableId )
	{
		return ( is_numeric( $nTableId ) && $nTableId >= 0 && $nTableId < 1000 );
	}
	public function IsValidTableName( $sTableName )
	{
		return ( is_string( $sTableName ) && strlen( $sTableName ) > 0 );
	}

	public function IsValidHTMId( $sStr )
	{
		return $this->IsValidMId( $sStr );
	}
	public function IsValidMId( $sStr )
	{
		//
		//	sStr	- 1011491015223910134582261886
		//
		$bRet	= false;

		if ( is_string( $sStr ) && strlen( $sStr ) > self::CONST_HTMID_MIN_LENGTH )
		{
			$Arr = $this->ParseMId( $sStr );
			if ( is_array( $Arr ) && count( $Arr ) > 0 )
			{
				$bRet = true;
			}
		}

		return $bRet;
	}

	public function GetNewUniqueHTMId( $nHostId, $nTableId, $vSeed = '' )
	{
		if ( ! $this->IsValidHostId( $nHostId ) || ! $this->IsValidTableId( $nTableId ) )
		{
			return '';
		}

		//	..
		$sRet	= '';
		$vSeed	= ( is_string( $vSeed ) ? $vSeed : '' );
		$sData	= $this->GetNewUniqueMId( $vSeed );
		if ( is_string( $sData ) && strlen( $sData ) > 0 )
		{
			$sRet = $this->_CreateHTMId( $nHostId, $nTableId, $sData );
		}

		return $sRet;
	}

	public function GetNewUniqueMId( $vSeed = '' )
	{
		//	18-digits number
		$sRandomMicro	= substr( $this->GetRandomByMicrotime(), 0, 18 );

		//	4-digits number by vSeed
		$sRandomNumber	= substr( $this->GetRandomBySeed( $vSeed, 6 ), -4, 4 );

		//	md5( date("YmdHis") . "-" . rand( 100000, 999999 ) . "-" . uniqid() . "-" . strval( $vSeed ) );
		return sprintf( "%s%s", strval( $sRandomMicro ), strval( $sRandomNumber ) );
	}

	public function GetRandomByMicrotime()
	{
		//
		//	RETURN	- (18位) 201512290629412493 / 101522390629412493
		//		  145137634324938800
		//
		$sRet = '';

		//
		//	微秒数
		//	0.24938800 1451376343	return = 145137634324938800
		//	0.05140000 1456572145	return = 145657214505140000
		//
		$sMicrotime = microtime();
		if ( is_string( $sMicrotime ) && strlen( $sMicrotime ) > 0 )
		{
			$arrTime = explode( ' ', $sMicrotime );
			if ( $arrTime && is_array( $arrTime ) && count( $arrTime ) >= 2 )
			{
				$arrNewTime = explode( '.', $arrTime[ 0 ] );
				if ( $arrNewTime && is_array( $arrNewTime ) && count( $arrNewTime ) >= 2 )
				{
					$nMicroRand	= $arrNewTime[ 1 ];	//	24938800
					$nTimestamp	= $arrTime[ 1 ];	//	1451376343
					if ( ! is_numeric( $nMicroRand ) || $nMicroRand < 10000000 )
					{
						$nMicroRand = rand( 10000000, 99999999 );
					}

					//
					//	...
					//
					$sStr	= ( strval( $nTimestamp ) . '' . strval( $nMicroRand ) );
					if ( strlen( $sStr ) < 18 )
					{
						$sStr = sprintf( "%d", time() );
						while ( strlen( $sStr ) < 18 )
						{
							$sStr .= sprintf( "%d", rand( 1000, 9999 ) );
						}
					}

					//
					//	...
					//
					$sRet	= substr( $sStr, 0, 18 );

					//	...
				//	$nYear	= intval( date( 'Y', $nTimestamp ) ) - 1000;
				//	$nMonth	= intval( date( 'm', $nTimestamp ) ) + 10;
				//	$nDay	= intval( date( 'd', $nTimestamp ) ) + 10;
				//	$sOther	= date( 'His', $nTimestamp );
				//	$sDate	= sprintf( "%d%02d%02d%s", $nYear, $nMonth, $nDay, $sOther );
				//	$sRandS	= substr( strval( $nMicroRand ), -4 );		//	2493
				//
				//	//	20160227142900
				//	$sRet	= sprintf( "%s%s", strval( $sDate ), strval( $sRandS ) );
				}
			}
		}

		return $sRet;
	}

	public function GetRandomBySeed( $vSeed, $nDigitCount = 6 )
	{
		//
		//	crc32
		//	303258319
		//
		if ( ! is_string( $vSeed ) && ! is_numeric( $vSeed ) )
		{
			return '';
		}
		if ( $nDigitCount <= 0 )
		{
			return '';
		}

		//	...
		$sRandom = sprintf( "%s-%d-%s", date("YmdHis"), rand( 100000, 999999 ), uniqid() );
		if ( empty( $vSeed ) )
		{
			$vSeed = ( $sRandom . '-' . $this->GetRandomByMicrotime() );
		}
		else
		{
			$vSeed = ( $vSeed . '-' . $sRandom . '-' . $this->GetRandomByMicrotime() );
		}

		//	...
		$nNumber	= abs( crc32( strval( $vSeed ) ) );
		$nDigitCount	= min( $nDigitCount, 9 );
		if ( $nNumber < 100000000 )
		{
			$nNumber += 100000000;
		}

		//	...
		return strval( substr( strval( $nNumber ), ( -1 * $nDigitCount ) ) );
	}

	public function ParseMId( $sMixId )
	{
		$ArrRet = [];

		//	...
		$sStringId	= "";
		$nHostId	= 0;
		$nTableId	= -1;
		$sData		= "";

		//	...
		$sStringId = trim( strval( $sMixId ) );
		if ( strlen( $sStringId ) < self::CONST_HTMID_MIN_LENGTH )
		{
			return [];
		}

		//	101109cccff98
		//	----------------------------------------
		//	101	- host id + 100
		//	109	- table id + 100
		//	cccff98	- data
		$nHostId	= intval( substr( $sStringId, 0, 3 ) ) - 100;
		$nTableId	= intval( substr( $sStringId, 3, 3 ) ) - 100;
		$sData		= trim( substr( $sStringId, 6 ) );

		if ( $this->IsValidHostId( $nHostId ) && $this->IsValidTableId( $nTableId ) )
		{
			$ArrRet =
			[
				'hostid'	=> $nHostId,	//	host id
				'tableid'	=> $nTableId,	//	table id
				'mid'	        => $sData,	//	mid
			];
		}

		//	...
		return $ArrRet;
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _CreateHTMId( $nHostId, $nTableId, $vData )
	{
		if ( ! $this->IsValidHostId( $nHostId ) || ! $this->IsValidTableId( $nTableId ) )
		{
			return '';
		}

		//	...
		$sData = strval( strtolower( trim( strval( $vData ) ) ) );
		return sprintf( "%d%d%s", ( 100 + $nHostId ), ( 100 + $nTableId ), $sData );
	}
}


?>