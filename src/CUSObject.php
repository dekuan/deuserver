<?php

namespace dekuan\deuserver;


/**
 *	class of CUSObject
 */
class CUSObject
{
	const TYPE_SERIALIZE	= 1;
	const TYPE_JSON		= 2;


	public function __construct()
	{
	}
	public function __destruct()
	{
	}
	
	public function EncodeObject( $ArrObject, $nEncodeType = self::TYPE_SERIALIZE, $vDefaultReturn = null )
	{
		$sRet = "";

		if ( null !== $vDefaultReturn )
		{
			$sRet = $vDefaultReturn;
		}

		if ( is_array( $ArrObject ) )
		{
			$sTemp = $sRet;
			if ( self::TYPE_SERIALIZE == $nEncodeType )
			{
				$sTemp = @ serialize( $ArrObject );
			}
			else if ( self::TYPE_JSON == $nEncodeType )
			{
				$sTemp = @ json_encode( $ArrObject );
			}

			$sRet = $sTemp;
		}

		return $sRet;
	}

	public function DecodeObject( $sString, $nEncodeType = self::TYPE_SERIALIZE, $vDefaultReturn = null )
	{
		$ArrRet = Array();

		if ( null !== $vDefaultReturn )
		{
			$ArrRet = $vDefaultReturn;
		}

		//	...
		$sString = trim( $sString );
		if ( ! empty( $sString ) )
		{
			$ArrTemp = $ArrRet;
			if ( self::TYPE_SERIALIZE == $nEncodeType )
			{
				$ArrTemp = @ unserialize( $sString );
			}
			else if ( self::TYPE_JSON == $nEncodeType )
			{
				$ArrTemp = @ json_decode( $sString, true );
			}

			if ( ! is_array( $ArrTemp ) )
			{
				$ArrTemp = Array();
			}

			//	...
			$ArrRet = $ArrTemp;
		}

		return $ArrRet;
	}
}

?>