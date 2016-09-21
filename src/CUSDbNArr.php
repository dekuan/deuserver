<?php

namespace dekuan\deuserver;

/**
 *	class of CUSDbNArr
 */
class CUSDbNArr
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
	public function GetTableInfo( $sTableName, $sKey, & $nRefHostId, & $nRefTableId, & $sRefTable )
	{
		//
		//	sTableName	- table name
		//	sKey		- username or u_mid
		//	RETURN		- array of data
		//
		$bRet	= false;

		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName )
			|| ! is_string( $sKey ) || 0 == strlen( $sKey ) )
		{
			return false;
		}

		if ( 0 == strcasecmp( CUSConst::DB_USER_TABLE, $sTableName ) )
		{
			//	sKey is the username
                        $bRet = $this->GetTableInfoByKey( $sTableName, $sKey, $nRefHostId, $nRefTableId, $sRefTable );
		}
		else if ( 0 == strcasecmp( CUSConst::DB_UEXTEND_TABLE, $sTableName ) ||
			0 == strcasecmp( CUSConst::DB_UBIND_TABLE, $sTableName ) ||
			0 == strcasecmp( CUSConst::DB_UASSOCIATE_TABLE, $sTableName ) )
		{
			//	sKey is the u_mid
                        $bRet = $this->GetTableInfoWithMid( $sTableName, $sKey, $nRefHostId, $nRefTableId, $sRefTable );
		}
		else if ( 0 == strcasecmp( CUSConst::DB_VERIFYCODE_TABLE, $sTableName ) )
		{
                        $bRet           = true;
                        $nRefHostId     = intval( CUSConst::DB_DEFAULT_HOST_ID );
                        $nRefTableId    = 0;
                        $sRefTable      = $sTableName;
		}

		return $bRet;
	}

	//
	//	get table info by key
	//
	public function GetTableInfoByKey( $sTableName, $sKey, & $nRefHostId, & $nRefTableId, & $sRefTable )
	{
		//
		//	RETURN	- Array( 'hostid', 'tableid', 'table' );
		//
		$bRet = false;

		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName ) ||
			! is_string( $sKey ) || 0 == strlen( $sKey ) )
		{
			return false;
		}

		$nHostId	= CUSConst::DB_DEFAULT_HOST_ID;
		$nTableId	= $this->GetHashTableId( $sKey, CUSConst::DB_TABLE_AMOUNT );
		if ( $nTableId >= 0 )
		{
                        $bRet           = true;
                        $nRefHostId     = intval( $nHostId );
                        $nRefTableId    = intval( $nTableId );
                        $sRefTable      = $this->GetNewTableName( $sTableName, $nTableId );
		}

		return $bRet;
	}

	//
	//	get table info with mid
	//
	public function GetTableInfoWithMid( $sTableName, $u_mid, & $nRefHostId, & $nRefTableId, & $sRefTable )
	{
		//
		//	RETURN	- Array( 'hostid', 'tableid', 'table' );
		//
		$bRet = false;

		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName ) )
		{
			return false;
		}
		if ( ! is_string( $u_mid ) || 0 == strlen( $u_mid ) )
		{
			return false;
		}

		//	...
                $nHostId	= 0;
                $nTableId	= -1;
                $sMId        = '';

		if ( $this->ParseMId( $u_mid, $nHostId, $nTableId, $sMId ) )
		{
			if ( $nTableId >= 0 )
			{
                                $bRet           = true;
                                $nRefHostId     = intval( $nHostId );
                                $nRefTableId    = intval( $nTableId );
                                $sRefTable      = $this->GetNewTableName( $sTableName, $nTableId );
			}
		}

		//	...
		return $bRet;
	}

	public function GetNewTableName( $sTableName, $nTableId )
	{
		if ( ! is_string( $sTableName ) || 0 == strlen( $sTableName )
			|| ! is_numeric( $nTableId ) || $nTableId < 0 )
		{
			return '';
		}
		return ( $sTableName . '_' . strval( $nTableId ) );
	}

	//	get hash table id
	public function GetHashTableId( $sKey, $nMaxCount )
	{
		$nRet	= -1;

		if ( ! is_string( $sKey ) || 0 == strlen( $sKey )
			|| ! is_numeric( $nMaxCount ) )
		{
			return -1;
		}

		//	...
		$sKey = strtolower( trim( $sKey ) );
		if ( is_string( $sKey ) && strlen( $sKey ) )
		{
			$nCrc = crc32( $sKey );
			$nRet = ( abs( $nCrc ) % $nMaxCount );
		}

		return $nRet;
	}


	public function IsValidHostId( $nHostId )
	{
		return ( is_numeric( $nHostId ) && $nHostId >= 0 && $nHostId < 1000 );
	}

	public function IsValidTableId( $nTableId )
	{
		return ( is_numeric( $nTableId ) && $nTableId >= 0 && $nTableId < 1000 );
	}

	public function IsValidHTMId( $sStr )
	{
		return $this->IsValidMId( $sStr );
	}
	public function IsValidMId( $sStr )
	{
		if ( ! is_string( $sStr ) || 0 == strlen( $sStr ) ||
			strlen( $sStr ) <= self::CONST_HTMID_MIN_LENGTH )
		{
			return false;
		}

                $bRet           = false;
                $nHostId        = 0;
                $nTableId       = -1;
                $sMId           = '';

		if ( $this->ParseMId( $sStr, $nHostId, $nTableId, $sMId ) )
                {
                        if ( $nHostId > 0 && $nTableId > 0 && ! empty( $sMId ) )
                        {
                                $bRet = true;
                        }
                }

                //      ...
		return $bRet;
	}

	public function GetNewUniqueHTMId( $nHostId, $nTableId, $vSeed = '' )
	{
		if ( ! $this->IsValidHostId( $nHostId ) || ! $this->IsValidTableId( $nTableId ) )
		{
			return '';
		}

		$vData = md5( $nHostId . "-" . $nTableId . "-" . $this->GetNewUniqueMId( strval( $vSeed ) ) );
		return $this->CreateMId( $nHostId, $nTableId, $vData );
	}

	public function GetNewUniqueMId( $vSeed = '' )
	{
		return md5( date("YmdHis") . "-" . rand( 100000, 999999 ) . "-" . uniqid() . "-" . strval( $vSeed ) );
	}

	public function CreateMId( $nHostId, $nTableId, $vData )
	{
		if ( ! $this->IsValidHostId( $nHostId ) || ! $this->IsValidTableId( $nTableId ) )
		{
			return "";
		}
		return ( strval( 100 + $nHostId ). "" . strval( 100 + $nTableId ) . "" . strtolower( trim( $vData ) ) );
	}

	public function ParseMId( $sMixId, & $nRefHostId, & $nRefTableId, & $sRefMId  )
	{
		$bRet		= false;
		$sStringId	= "";
		$nHostId	= 0;
		$nTableId	= -1;
		$sData		= "";

		//	...
		$sStringId = trim( strval( $sMixId ) );
		if ( strlen( $sStringId ) < self::CONST_HTMID_MIN_LENGTH )
		{
			return false;
		}

		//	101109cccff98
		//	----------------------------------------
		//	101	- host id + 100
		//	109	- table id + 100
		//	cccff98	- data
		$nHostId	= intval( substr( $sStringId, 0, 3 ) ) - 100;
		$nTableId	= intval( substr( $sStringId, 3, 3 ) ) - 100;
		$sData		= trim( substr( $sStringId, 6 ) );

		if ( $nHostId > 0 && $nTableId >= 0 )
		{
                        $bRet           = true;
                        $nRefHostId     = $nHostId;	//	host id
                        $nRefTableId    = $nTableId;	//	table id
                        $sRefMId        = $sData;	//	mid
		}

		//	...
		return $bRet;
	}
}


?>