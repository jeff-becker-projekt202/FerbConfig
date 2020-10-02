<?php
declare(strict_types=1);
namespace Ferb\Conf;
use Ferb\Conf\Abstractions\ConfigInterface;
use Ferb\Conf\Util\ConfigPath;
use Ferb\Conf\Providers\JsonFileProvider;
use Ferb\Conf\Providers\EnvProvider;
use Ferb\Conf\Providers\PhpIniProvider;
use Ferb\Conf\Providers\IncludeProvider;
use Ferb\Conf\Providers\WordpressOptionsProvider;
use Ferb\Conf\Providers\ArrayProvider;
class ConfigBuilder {
    private $providers =[];
    private $do_add = true;
    public function add($provider){
        if($this->do_add){
            $this->providers[] = $provider;
        }
        $this->do_add = true;
        return $this;
    }
    public function create(){
        return new ConfigRoot($this->providers);
    }
    public function when($condition){
        $this->do_add = $condition;
        return $this;
    }
    public function add_json($file, $prefix = '', $delimiter = ''){
        return $this->add(new JsonFileProvider($file, $prefix, $delimiter));
    }
    public function add_env_vars($prefix = '', $delimiter = ''){
        return $this->add(new EnvProvider($prefix, $delimiter));
    }
    public function add_include_file($file){
       return $this->add(new IncludeProvider($file));
    }
    public function add_wordpress_options(){
        return $this->add(new WordpressOptionsProvider());
    }
    public function add_array($array){
        return $this->add(new ArrayProvider($array));
    }
}
// // this config allows us to build a complex
// // configuration graph and then interogate that
// // 
// $config = (new ConfigBuilder())
//     ->add_json($base_json) // file of non-secure config settings like ORM config
//     ->when($env == 'local-dev')->add_include_file($local_config) // overrides from the local dev env
//     ->when($env == 'prod')->add_include_file($prod_config) // overrides from prod
//     ->add_env_vars() // overrides in $_ENV
//     ->add_wordpress_options() // overrides in the wordpress options table
//     ->create(); 

// //logger factory is a wrapper around Monolog that allows us to:
// // - configure different handlers, processors, formaters and level per-channel
// // - treat channels as hierarchical so that configuration cascades
// // - do it all from config so that different environments can have overrides
// $logger_factory = $config->section('Logging')->as_object(LoggerFactory::class);
// $real_config['Logging'] = [
//     'channels' => [
//         '*' => [
//             'level' => Logger::EMERGENCY,
//             'handlers' => ['file'],
//         ],
//         'Wordpress'=>[ // wordpress is noisy, make it shut-up
//             'level' => Logger::FATAL,
//             'handlers'=>['file']
//         ],
//         'Wordpress\\Queries'=>[ // but log all the sql exceptions
//             'level' => Logger::INFO,
//             'handlers'=>['sql-file'],
//         ],
//         'MyAddon'=>[
//             'level' => Logger::INFO,
//             'handlers'=>['file']
//         ],
//         'MyAddon\\TwitchyFeature'=>[
//             'level' => Logger::Debug,
//             'handlers'=>['file']
//         ]
//     ],
//     'handlers' => [
//         'newrelic' => [
//             'class' => 'Monolog\\Handler\\NewRelicHandler',
//             'condition' => [
//                 'callable' => 'extension_loaded',
//                 'args' => ['newrelic'],
//             ],
//         ],
//         'error_log' => [
//             'class' => 'Monolog\\Handler\\ErrorLogHandler',
//         ],
//         'file' => [
//             'class' => 'Monolog\\Handler\\RotatingFileHandler',
//             'args' => [
//                 'level' => Logger::DEBUG,
//                 'filename' => [
//                     'callable' => 'MyNs\\LoggerFactory::get_log_file',
//                     'args' => ['my-addon-name'],
//                 ],
//             ],
//         ],
//         'sql-file' => [
//             'class' => 'Monolog\\Handler\\RotatingFileHandler',
//             'args' => [
//                 'level' => Logger::DEBUG,
//                 'filename' => [
//                     'callable' => 'MyNs\\LoggerFactory::get_log_file',
//                     'args' => ['my-addon-name-sql'],
//                 ],
//             ],
//         ],
//     ],
// ];