<?

// COMMON FUNCTION
function ReqParam( &$aMethod, $szParam, $fFilter = NULL )
{
	if( $fFilter == NULL )
	{
		return $aMethod[$szParam];
	}
	else
	{
		return $fFilter( $aMethod[$szParam] );
	}
}

function FPregReplace( $szPattern )
{
	return function( $szStr ) use ( $szPattern ) { return preg_replace( $szPattern, "", $szStr ); };
}

