<?php

namespace Gzhegow\Di\Tests\Services\Closure;

/**
 * Class MyClosureAService
 */
class MyClosureAService implements MyClosureServiceAInterface
{
	/**
	 * @var mixed
	 */
	protected $dynamicOption;


	/**
	 * @return mixed
	 */
	public function getDynamicOption()
	{
		return $this->dynamicOption;
	}

	/**
	 * @param mixed $value
	 *
	 * @return MyClosureAService
	 */
	public function setDynamicOption($value)
	{
		$this->dynamicOption = $value;

		return $this;
	}


	/**
	 * @return void
	 */
	public static function getStaticOption()
	{
		return static::$staticOption;
	}

	/**
	 * @param $value
	 *
	 * @return void
	 */
	public static function setStaticOption($value)
	{
		static::$staticOption = $value;
	}


	/**
	 * @var mixed
	 */
	protected static $staticOption;
}