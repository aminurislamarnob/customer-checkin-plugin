<?php

namespace IncPath;

final class PluginInit
{
	/**
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_services() 
	{
		return [
			Pages\Admin\Admin::class,
			Base\AddCustomFieldToCheckout::class,
			Base\ViewCustomerInfo::class,
			Base\AdminEnqueue::class,
			Base\PublicEnqueue::class,
			Base\SettingsLinks::class,
			// Base\DraftsListTable::class,
		];
	}

    /**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 * @return
	 */
	public static function register_services() 
	{
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

    /**
     * Initialize the class
     * @param  class $class    class from the services array
     * @return class instance  new instance of the class
     */
	private static function instantiate( $class )
	{
		$service = new $class();
		return $service;
	}

}