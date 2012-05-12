<?

abstract class Store
{
	abstract public function Get( $szKey );
	abstract public function Set( $szKey, $szContext, $szData );
}

class StoreException extends Exception
{
	public function __construct( $message = "", $code = 0, $previous = NULL )
	{
		parent::__construct( "StoreException : $message", $code, $previous );
	}
}

class SqliteStore extends Store
{
	static private $instance;
	static public function Instance()
	{
		if( self::$instance == NULL )
		{
			$oSqlite = new SqliteStore();
			$oSqlite -> Connect();
			self::$instance = $oSqlite;
		}
		return self::$instance;
	}

	private $conn;

	public function Connect()
	{
		$this -> conn = sqlite_open('shuffle_db', 0666, $error);
		if( $this -> conn == NULL )
		{
			throw new StoreException("sqlite open : $error");
		}
	}

	public function Close()
	{
		if( $this -> conn == NULL )
		{
			sqlite_close( $this -> conn );
			$this -> conn = NULL;
		}
	}
	public function __destruct() { $this -> Close(); }

	private function Query( $szQuery )
	{
		if( $this -> conn == NULL )
		{
			$this -> Connect();
		}

		$oRes	= sqlite_query( $this -> conn, $szQuery, SQLITE_ASSOC, $error );
		if( $oRes == FALSE )
		{
			throw new StoreException("sqlite query : $error");
		}
		return $oRes;
	}

	private function Fetch( $oResources )
	{
		return sqlite_fetch_array( $oResources, SQLITE_ASSOC );
	}
	
	public function Create()
	{
		$this -> Query( "CREATE TABLE shuffle_tbl (id INTEGER PRIMARY KEY, context TEXT, data TEXT)" );
	}

	public function Drop()
	{
		$this -> Query( "DROP TABLE shuffle_tbl" );
	}

	public function Get( $szKey )
	{
		$this -> Escape( $szKey );
		$oResult	= $this -> Query( "SELECT * FROM shuffle_tbl WHERE id = $szKey" );

		if( $oRow = $this -> Fetch( $oResult ) )
		{
			return $oRow;
		}

		return NULL;
	}

	public function GetAll()
	{
		$oResult	= $this -> Query( "SELECT * FROM shuffle_tbl" );

		$aRet	= array();
		while( $oRow = $this -> Fetch( $oResult ) )
		{
			$aRet[]	= $oRow;
		}

		return $aRet;
	}

	public function Count()
	{
		$oResult	= $this -> Query( "SELECT COUNT(*) AS count FROM shuffle_tbl" );

		if( $oRow = $this -> Fetch( $oResult ) )
		{
			return (int)$oRow['count'];
		}

		return NULL;
	}

	public function GetAllContext()
	{
		$oResult	= $this -> Query( "SELECT DISTINCT context FROM shuffle_tbl" );

		$aRet	= array();
		while( $oRow = $this -> Fetch( $oResult ) )
		{
			$aRet[]	= $oRow['context'];
		}

		return $aRet;
	}

	public function Delete( $szKey )
	{
		$this -> Escape( $szKey );
		$this -> Query( "DELETE FROM shuffle_tbl WHERE id = $szKey" );
	}

	public function Truncate()
	{
		$this -> Query( "DELETE FROM shuffle_tbl" );
	}

	public function FindData( $aConditions )
	{
		if( empty($aConditions['context']) )
		{
			throw new StoreException("sqlitestore: no context");
		}

		$aWhere	= array();
		if( $aConditions['context'] != 'ALL' )
		{
			$this -> Escape( $aConditions['context'] );
			if( isset($aConditions['notexact']) and $aConditions['notexact'] )
			{
				$aWhere[]	= "context LIKE \"%{$aConditions['context']}%\"";
			}
			else
			{
				$aWhere[]	= "context = \"{$aConditions['context']}\"";
			}
		}

		if( !empty($aConditions['trail']) )
		{
			$this -> Escape( $aConditions['trail'] );
			$aWhere[]	= "data LIKE \"%{$aConditions['trail']}\"";
		}

		$szWhere = implode( " AND ", $aWhere );
		if( !empty( $szWhere ) )
		{
			$szWhere = "WHERE $szWhere";
		}

		$oResult	= $this -> Query( "SELECT * FROM shuffle_tbl $szWhere" );

		$aRet	= array();
		while( $oRow = $this -> Fetch( $oResult ) )
		{
			$aRet[]	= $oRow;
		}

		return $aRet;
	}

	public function Set( $szKey, $szContext, $szData )
	{
		$this -> Escape( $szContext );
		$this -> Escape( $szData );

		$aResult = NULL;
		if( $szKey )
		{
			$aResult	 = $this -> Get( $szKey );
		}

		if( is_array($aResult) and count( $aResult ) > 0 )
		{
			$this -> Query( "UPDATE shuffle_tbl SET context = \"$szContext\", data = \"$szData\" WHERE id = $szKey" );
		}
		else
		{
			$this -> Query( "INSERT INTO shuffle_tbl(context, data) VALUES (\"$szContext\", \"$szData\")" );
		}
	}

	private function Escape( &$szString )
	{
//		$szString = preg_replace( "/'/", "\\'", $szString );
	}
}

class StoreSelector
{
	static private $hDict	= array(
			'sqlite' => SqliteStore
			);

	static public function Select( $szSelect )
	{
		if( self::$hDict[$szSelect] == NULL )
		{
			throw new StoreException('StoreSelector: no such store');
		}
		$szClass	= self::$hDict[$szSelect];
		return $szClass::Instance();
	}
}

