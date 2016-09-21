<?php

namespace dekuan\deuserver;

/**
 *	class of CUSConst
 */
class CUSConst
{
	//	common status
	const STATUS_UNVERIFIED		= 0;	//	unverified
	const STATUS_OKAY		= 1;	//	okay
	const STATUS_DELETED		= 2;	//	deleted
	const STATUS_EXPIRED		= 3;	//	expired
	const STATUS_DENIED		= 4;	//	denied
	const STATUS_COMPLETE		= 5;	//	complete
	const STATUS_ABORT		= 6;	//	abort
	const STATUS_PENDING		= 7;	//	pending
	const STATUS_ACCEPTED		= 8;	//	accepted
	const STATUS_REJECTED		= 9;	//	rejected
	const STATUS_ARCHIVED		= 10;	//	archived


	//	There's one database merely.
	const DB_DEFAULT_HOST_ID	= 1;

	//	We split a big table into 100 pieces.
	const DB_TABLE_AMOUNT		= 100;

	const DB_USER_TABLE		= "user_table";
	const DB_UEXTEND_TABLE		= "uextend_table";
	const DB_UBIND_TABLE		= "ubind_table";
	const DB_UASSOCIATE_TABLE	= "uassociate_table";
	const DB_VERIFYCODE_TABLE	= "verifycode_table";

}


?>