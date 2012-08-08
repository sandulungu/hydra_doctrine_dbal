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

// Default database config.
$hooks['app.config'][-1000][] = function(&$config) {
    $config['doctrine.dbal'] = array(
        'driver' => 'pdo_mysql',
    );
};

// Default Doctrine DBAL service.
$services['app.doctrine.dbal'][0] = function(App $app) {
    $evm = new \Doctrine\Common\EventManager();
    $params = $app->config['doctrine'];
    
    if ($params['driver'] == 'pdo_mysql') {
        $params += array(
            'username' => 'root',
            'dbname' => 'hydra',
            'charset' => 'utf8',
        );
        
        // This is only required for PHP < 5.3.6
        $params += array(
            'mysqlSessionInit' => $params['charset'],
        );
        if ($params['mysqlSessionInit']) {
            $evm->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit(
                $params['mysqlSessionInit']
            ));
        }
    }
    
    return \Doctrine\DBAL\DriverManager::getConnection($params, null, $evm);
};
