<?php

namespace lnpay\components;


use lnpay\node\models\NodeListener;
use Indigo\Ini\Renderer;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Supervisor\Configuration\Configuration;
use Supervisor\Configuration\Loader\IniFileLoader;
use Supervisor\Configuration\Section\Program;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class SupervisorComponent extends \yii\base\Component
{
    public static function init_api()
    {
        $api = new \Supervisor\Api(getenv('SUPERVISOR_RPC_HOST'), getenv('SUPERVISOR_RPC_PORT') /* username, password */);

        //Make sure supervisor is running!
        try {
            $api->getPid();
        } catch (\Throwable $t) {
            \LNPay::error($t->getMessage(),__METHOD__);
            //return false;
        }

        return $api;
    }

    /**
     * @param $config_filename
     * @param $listeners
     * @return bool
     * @throws \Exception
     */
    public static function writeLndRpcConfigFile($config_filename,$listeners)
    {
        $config = new Configuration;
        $renderer = new Renderer;

        // usually /etc/supervisor/conf.d
        $supervisorConfPath = getenv('SUPERVISOR_CONF_PATH');

        $filesystem = new Filesystem(new Local($supervisorConfPath));


        foreach ($listeners as $program) {
            $program = NodeListener::findOne($program);

            $section = new Program($program->id, $program->supervisor_parameters);
            $config->addSection($section);
        }

        $c = $renderer->render($config->toArray());
        if ($filesystem->put($config_filename,$c)) {
            return TRUE;
        } else {
            throw new \Exception('Unable to write supervisor file!');
        }
    }

    /**
     * @param $listener
     * @return bool
     * @throws \Exception
     */
    public static function updateLndRpcConfigFile($listener_id,$parameters)
    {
        $listener = NodeListener::findOne($listener_id);
        $listener->supervisor_parameters = $parameters;

        $config = new Configuration;
        $renderer = new Renderer;

        // usually /etc/supervisor/conf.d
        $supervisorConfPath = getenv('SUPERVISOR_CONF_PATH');

        $filesystem = new Filesystem(new Local($supervisorConfPath));

        $loader = new IniFileLoader($filesystem, $listener->config_filename);

        $config = $loader->load($config);

        $section = new Program($listener->id, $listener->supervisor_parameters);
        $config->addSection($section);

        $c = $renderer->render($config->toArray());
        if ($filesystem->put($listener->config_filename,$c)) {
            return $listener->save();
        } else {
            throw new \Exception('Unable to write supervisor file!');
        }
    }


    /**
     * @param $file_name
     * @return bool
     * @throws \Exception
     */
    public static function removeLndRpcConfigFile($file_name)
    {
        try {
            unlink(getenv('SUPERVISOR_CONF_PATH').$file_name);
            unlink(getenv('SUPERVISOR_SERVER_APP_PATH').'runtime/supervisor/'.str_replace('.conf','',$file_name).'.err.log');
            unlink(getenv('SUPERVISOR_SERVER_APP_PATH').'runtime/supervisor/'.str_replace('.conf','',$file_name).'.out.log');
            $api = static::init_api();
            $api->__call('reloadConfig');
        } catch (\Throwable $f) {
            \LNPay::error($f,__METHOD__);
        }

        return true;
    }

    /**
     * @param $program_name
     * @return array|bool
     */
    public static function getProcessInfo($program_name)
    {
        $api = static::init_api();
        try {
            return $api->getProcessInfo($program_name);
        } catch (\Exception $e) {
            //\LNPay::error($e,__METHOD__);
            return false;
        }
    }

    /**
     * @param $program_name
     * @return bool
     */
    public static function stopProcess($program_name)
    {
        $api = static::init_api();
        try {
            return $api->stopProcess($program_name);
        } catch (\Exception $e) {
            \LNPay::error($e,__METHOD__);
            return false;
        }

    }

    /**
     * @param $program_name
     * @return bool|string
     */
    public static function startProcess($program_name)
    {
        $api = static::init_api();

        //Ghetto workaround for lack of `update` command via supervisor RPC
        try { $api->__call('reloadConfig'); } catch (\Exception $e) { }
        try { $api->removeProcessGroup($program_name); } catch (\Exception $e) { }
        try { $api->addProcessGroup($program_name); } catch (\Exception $e) { }

        try {
            return $api->startProcess($program_name);
        } catch (\Exception $e) {
            //\LNPay::error($e,__METHOD__);
            return $e->getMessage();
        }
    }

    /**
     * @param $program_name e.g. `lnod_xyzxyz--SubscribeInvoices
     */
    public static function removeProcess($program_name)
    {
        $api = static::init_api();

        try { $api->stopProcess($program_name); } catch (\Exception $e) {}
        try { $api->removeProcessGroup($program_name); } catch (\Exception $e) {}
        try { $api->__call('reloadConfig'); } catch (\Exception $e) { }
    }

    /**
     * @param $program_name  e.g. `lnod_xyzxyz--SubscribeInvoices
     */
    public static function restartProcess($program_name)
    {
        $api = static::init_api();
        $api->stopProcess($program_name);
        $api->startProcess($program_name);
    }

    /**
     * @return bool
     */
    public static function restartSupervisor()
    {
        $api = static::init_api();
        return $api->restart();
    }

    /**
     * @return array
     */
    public static function getAllProcessInfo()
    {
        $api = static::init_api();
        $info = $api->getAllProcessInfo();

        \LNPay::info(VarDumper::export($info),__METHOD__);

        return $info;
    }
}
?>