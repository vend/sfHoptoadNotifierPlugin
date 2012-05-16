<?php

/**
 * sfHoptoadNotifierPlugin configuration.
 *
 * @package     sfHoptoadNotifierPlugin
 */
class sfHoptoadNotifierPluginConfiguration extends sfPluginConfiguration
{
  /* instance of rich's client */
  private $hoptoadClient;
  
  /**
   * @return a configured hoptoad client.
   */
  public function getHoptoadClient() {
    return $this->hoptoadClient;
  }
  
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // load the lib
    require_once($this->getRootDir() . '/lib/rich-php-hoptoad-notifier/Hoptoad.php');
    
    // key
    $apiKey = sfConfig::get('app_sf_hoptoad_notifier_plugin_api_key', false);
    // if no key, then we do not connect to hoptoad
    if ($apiKey == false) {
      return;
    }
    // pear, curl, or zend
    $client = sfConfig::get('app_sf_hoptoad_notifier_plugin_client', 'pear');
    // Environment (prod, test, ...)
    try {
      $env = $this->configuration->getEnvironment();
    } catch (Exception $e) {
      // if called from CLI or other ...
      $env = 'unknown';
    }

    // Instanciate the service
    $this->hoptoadClient = new Services_Hoptoad($apiKey, $env, $client);
    
    // handle general php errors
    // (commented, because we will do it through sf handlers)
    // $hoptoad->installNotifierHandlers();

    // handle sf exceptions
    $this->dispatcher->connect(
      'application.throw_exception',
      array('sfHoptoadNotifier', 'handleExceptionEvent')
    );

    $this->dispatcher->connect(
      'hoptoad.notify_exception',
      array('sfHoptoadNotifier', 'handleExceptionEvent')
    );

    // handle log errors, ...
    $this->dispatcher->connect(
      'application.log',
      array('sfHoptoadNotifier', 'handleLogEvent')
    );
  }

  public function configure()
  {
    // in this method, config (app.yml) is not already loaded, so we must not initialize the hoptoad object.
  }
}
