<?

class PrependClassException extends Exception
{
	public function __construct( $message = "", $code = 0, $previous = NULL )
	{
		parent::__construct( get_class($this) . " : $message", $code, $previous );
	}
}

class InvalidParameterException extends PrependClassException {}
class RequireParameterNullException extends PrependClassException {}

abstract class Responsor
{
	protected $params;

	abstract public function Response();
	abstract public function VerifyParam();
	public function ReadParam()
	{
		$szParams	= ReqParam( $_GET, 'param_list', FPregReplace("/[^a-z,_]/") );
		if( empty($szParams) )
		{
			throw new InvalidParameterException('param_list is null');
		}

		$aParams	= explode( ",", $szParams );
		foreach( $aParams as $szParam )
		{
			$this -> params[$szParam]	= ReqParam( $_GET, $szParam );
		}
	}
}

class ListResponsor extends Responsor
{
	public function Response()
	{
		require_once "Store.php";
		$oDb	=& StoreSelector::Select('sqlite');
		$oResult = $oDb -> FindData( array('context' => $this -> params['context']) );
		echo json_encode($oResult);
	}

	public function VerifyParam()
	{
		$aRequireParam	= array('context');
		foreach( $aRequireParam as $szRequireParam )
		{
			if( empty( $this -> params[$szRequireParam] ) )
			{
				$this -> params[$szRequireParam] = '';
				//throw new RequireParameterNullException($szRequireParam);
			}
		}
	}
}

class ViewResponsor extends Responsor
{
	public function Response()
	{
		//require_once "Store.php";
		//$oDb	=& StoreSelector::Select('sqlite');
		//$oResult = $oDb -> FindData( array('context' => $this -> params['context'], 'data' => $this -> params['data']) );
		header("Location: " ."/music/" . $this -> params['context'] . "/" . $this -> params['data']);
	}

	public function VerifyParam()
	{
		$aRequireParam	= array('context', 'data');
		foreach( $aRequireParam as $szRequireParam )
		{
			if( empty( $this -> params[$szRequireParam] ) )
			{
				throw new RequireParameterNullException($szRequireParam);
			}
		}
	}
}

class ResponsorSelector
{
	static private $hDict	= array(
		'list' => ListResponsor,
		'view' => ViewResponsor
		);

	static public function Select()
	{
		$szAction	= ReqParam( $_GET, 'action' );
		if( self::$hDict[$szAction] == NULL )
		{
			throw new InvalidParameterException('action');
		}
		return new self::$hDict[$szAction];
	}
}

