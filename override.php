<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Override
 *
 * @copyright   Copyright (C) 2013 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Joomla Override plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Override
 * @since       3.1
 */
class PlgSystemOverride extends JPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An array that holds the plugin configuration.
	 *
	 * @access  protected
	 * @since   3.1
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		// Defines.
		define('JPATH_OVERRIDE', JPATH_ROOT . '/overrides');
		define('JPATH_OVERRIDE_SITE', JPATH_OVERRIDE);
		define('JPATH_OVERRIDE_ADMINISTRATOR', JPATH_OVERRIDE . '/administrator');

		jimport('joomla.filesystem.folder');

		$folder = __DIR__ . '/overrides';

		if (!JFolder::exists(JPATH_OVERRIDE) && JFolder::exists($folder))
		{
			JFolder::move($folder, JPATH_SITE . '/overrides');
		}

		$this->loadLanguage();
	}

	/**
	 * After initialise call the classes to override the existing classes.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public function onAfterInitialise()
	{
		// Get the application.
		$app   = JFactory::getApplication();
		$input = $app->input;
		$files = array();

		if ($app->isAdmin())
		{
			if ($input->get('option') == 'com_content')
			{
				$files = array(
					'ContentModelArticles' => JPATH_OVERRIDE_ADMINISTRATOR . '/components/com_content/models/articles.php'
				);
			}
		}
		elseif ($app->isSite())
		{
			if ($input->get('option') == 'com_content')
			{
				$files = array(
					'ContentModelArticles' => JPATH_OVERRIDE_SITE . '/components/com_content/models/articles.php'
				);
			}
		}

		foreach ($files as $class => $path)
		{
			// Register dependent classes.
			JLoader::register($class, $path, true);
		}
	}
}
