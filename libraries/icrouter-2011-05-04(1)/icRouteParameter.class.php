<?php 
/**
 * Describes parameter in url
 *
 * @author     Igor Crevar <crewce@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php
 * @version    0.8
 */
class icRouteParameter
{
	const ANY = 0;
	const INT = 1;
	const FLOAT = 2; //value will be float
	const REG_REPLACE = 4; //
	const REG_MATCH = 5;
	const IN = 6;
	const NOT_IN = 7;
	/**
	 * @brief: Type of parameter
	 *
	 * @var pick one of above const values
	 */
	protected $type;
	/**
	 * @brief: Additional parameter
	 *
	 * @var mixed. Additonal parameters, depends on type
	 */
	protected $additional;
	
	/*
	 * @param int $type is type of parser
	 * @param mixed $additional is additional parameters
	 */
	public function __construct($type = INT, $additional = array() )
	{
		$this->type = $type;
		//default for reg replace
		if ( $type == icRouteParameter::REG_REPLACE  && is_string($additional) )
		{
			$this->additional = array($additional, '-');
			return;
		}
		$this->additional = $additional;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	/*
	 * Parse value depending on type of parameter
	 * @params mixed $value value to parse
	 * @throws icException for IN_ARRAY and REG_MATCH
	 * @return parsed value
	 */
	public function parseValue( $value )
	{
		switch ( $this->type )
		{
			case icRouteParameter::INT:
				return intval( $value ); 
			case icRouteParameter::FLOAT:
				return doubleval( $value );
			case icRouteParameter::REG_REPLACE:	
				//third parameter can be set to avoid strtolower
				if ( !isset($this->additional[2]) || !$this->additional[2])
				{
					$value = strtolower($value);
				}			
				$value = preg_replace( '/[^'.$this->additional[0].']/', $this->additional[1], $value );
				$value = preg_replace( '/'.$this->additional[1].'+/', $this->additional[1], $value );
				return trim( $value, $this->additional[1] );
			case icRouteParameter::REG_MATCH:
				if ( preg_match( '/^'.$this->additional.'$/' , $value) )
				{
					//if matches returns value otherwise throw exception
					return $value;
				}
				throw new icException( "Parameter $value is not match $this->additional" );				
			case icRouteParameter::IN:	
				if ( !in_array($value, $this->additional) )
				{
					$valuesOk = join(', ',$this->additional);
					throw new icException( "Parameter $value is not match one of [$valuesOk]" );
				}	
				return $value;	
			case icRouteParameter::NOT_IN:	
				if ( in_array($value, $this->additional) )
				{
					$valuesOk = join(', ',$this->additional);
					throw new icException( "Parameter $value must not be in [$valuesOk]" );
				}	
				return $value;		
			case icRouteParameter::ANY:
				return $value;
		}
		
	}
	
	/*
	 * Retrieves pattern for regular expressions. Depends on type
	 * @return: string value (pattern part)
	 */
	public function getPattern()
	{
		switch ( $this->type )
		{
			case icRouteParameter::INT:
				return '[0-9]+'; 
			case icRouteParameter::FLOAT:
				return '-?[0-9]+|[0-9]+\.[0-9]+';
			case icRouteParameter::REG_REPLACE:
				return '['.$this->additional[0].']+';
			case icRouteParameter::REG_MATCH:
				return $this->additional;
			case icRouteParameter::IN:
				return join('|', $this->additional);
			case icRouteParameter::NOT_IN:
				return '(?!'.join('|', $this->additional).')[^\/]+';	
			case icRouteParameter::ANY:
				return '[^\/]+';
		}
	}

	/*
	 *TODO: is this code really neccessary?
	protected static $defaultIntParameter = null;
	protected static $defaultFloatParameter = null;
	protected static $defaultReplaceParameter = null;

	public static function getInt()
	{
		if ( self::$defaultIntParameter == null ) self::$defaultIntParameter = new icRouteParameter(icRouteParameter::INT);
		return self::$defaultIntParameter;
	}
	public static function getFloat()
	{
		if ( self::$defaultFloatParameter == null ) self::$defaultFloatParameter = new icRouteParameter(icRouteParameter::FLOAT);
		return self::$defaultFloatParameter;
	}
	public static function getReplace()
	{
		if ( self::$defaultReplaceParameter == null ) self::$defaultReplaceParameter = new icRouteParameter(icRouteParameter::REG_REPLACE, array('a-zA-Z0-9_\-', '-') );
		return self::$defaultReplaceParameter;
	}
	*/
}