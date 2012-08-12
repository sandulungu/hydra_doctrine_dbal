<?php
/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @link        https://github.com/z7/hydra
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra;

use Doctrine\DBAL\DriverManager;

// Default database config.
$hooks['app.config'][-1000][] = function(App $app, &$config) {
    $config['doctrine.dbal']['default'] = array(
        'driver' => 'pdo_mysql',
    );
};

// Init PDO and MongoDB services.
$hooks['app.init'][0][] = function (App $app, &$services) {
    foreach ($app->config->doctrine__dbal as $name => &$params) {
        $services['app.doctrine.dbal' . ($name == 'default' ? '' : ".$name")][0] = function() use (&$params) {
            if ($params['driver'] == 'pdo_mysql') {
                $params += array(
                    'username' => 'root',
                    'dbname' => 'hydra',
                    'charset' => 'utf8',
                );

                // This is only required for PHP < 5.3.6
                if (isset($params['charset'])) {
                    $params += array(
                        'mysqlSessionInit' => $params['charset'],
                    );
                    if ($params['mysqlSessionInit']) {
                        $evm = new \Doctrine\Common\EventManager();
                        $evm->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit(
                            $params['mysqlSessionInit']
                        ));
                        return DriverManager::getConnection($params, null, $evm);
                    }
                }
            }
            
            return DriverManager::getConnection($params);
        };
    }
    
};
