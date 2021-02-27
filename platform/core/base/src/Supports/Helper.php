<?php

namespace Botble\Base\Supports;

use Artisan;
use Cache;
use Eloquent;
use Event;
use Exception;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Request;
use Schema;

class Helper
{
    /**
     * Load helpers from a directory
     * @param string $directory
     * @since 2.0
     */
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

    /**
     * @param Eloquent | Model $object
     * @param string $sessionName
     * @return bool
     */
    public static function handleViewCount(Eloquent $object, $sessionName): bool
    {
        if (!array_key_exists($object->id, session()->get($sessionName, []))) {
            try {
                $object->increment('views');
                session()->put($sessionName . '.' . $object->id, time());
                return true;
            } catch (Exception $exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * Format Log data
     *
     * @param array $input
     * @param string $line
     * @param string $function
     * @param string $class
     * @return array
     */
    public static function formatLog($input, $line = '', $function = '', $class = ''): array
    {
        return array_merge($input, [
            'user_id'   => Auth::check() ? Auth::user()->getKey() : 'System',
            'ip'        => Request::ip(),
            'line'      => $line,
            'function'  => $function,
            'class'     => $class,
            'userAgent' => Request::header('User-Agent'),
        ]);
    }

    /**
     * @param string $module
     * @param string $type
     * @return boolean
     */
    public static function removeModuleFiles(string $module, $type = 'packages'): bool
    {
        $folders = [
            public_path('vendor/core/' . $type . '/' . $module),
            resource_path('assets/' . $type . '/' . $module),
            resource_path('views/vendor/' . $type . '/' . $module),
            resource_path('lang/vendor/' . $type . '/' . $module),
            config_path($type . '/' . $module),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }

        return true;
    }

    /**
     * @param string $command
     * @param array $parameters
     * @param null $outputBuffer
     * @return bool|int
     * @throws Exception
     * @deprecated since v5.5, will be removed in v5.7
     */
    public static function executeCommand(string $command, array $parameters = [], $outputBuffer = null): bool
    {
        if (!function_exists('proc_open')) {
            if (config('app.debug') && config('core.base.general.can_execute_command')) {
                throw new Exception(trans('core/base::base.proc_close_disabled_error'));
            }
            return false;
        }

        if (config('core.base.general.can_execute_command')) {
            return Artisan::call($command, $parameters, $outputBuffer);
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function isConnectedDatabase(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public static function clearCache(): bool
    {
        Event::dispatch('cache:clearing');

        try {
            Cache::flush();
            if (!File::exists($storagePath = storage_path('framework/cache'))) {
                return true;
            }

            foreach (File::files($storagePath) as $file) {
                if (preg_match('/facade-.*\.php$/', $file)) {
                    File::delete($file);
                }
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        Event::dispatch('cache:cleared');

        return true;
    }

    /**
     * @return bool
     */
    public static function isActivatedLicense(): bool
    {
        if (!File::exists(storage_path('.license'))) {
            return false;
        }

        $coreApi = new Core;

        $result = $coreApi->verifyLicense(true);

        if (!$result['status']) {
            return false;
        }

        return true;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    public static function getCountryNameByCode(?string $countryCode): ?string
    {
        if (empty($countryCode)) {
            return null;
        }

        return Arr::get(self::countries(), $countryCode, $countryCode);
    }

    /**
     * @return string[]
     */
    public static function countries(): array
    {
        return trans('core/base::base.countries', []);
    }

    /**
     * @return bool|string
     */
    public static function getIpFromThirdParty()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://ipecho.net/plain');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
