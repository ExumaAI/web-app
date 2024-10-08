<?php

namespace App\Providers;

use App\Helpers\Classes\Helper;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Repositories\ExtensionRepository;
use App\Services\MemoryLimit;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Facades\Health;
use Theme;
use URL;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ExtensionRepositoryInterface::class => ExtensionRepository::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $dbConnectionStatus = Helper::dbConnectionStatus();

        $this->forceSchemeHttps();

        app()->useLangPath(
            base_path('lang')
        );

        $locale = 'en';

        if ($dbConnectionStatus) {
            Schema::defaultStringLength(191);
            // frontend setting shared
            if (Schema::hasTable('settings_two')) {
                $settings_two = SettingTwo::first();
                $locale = Helper::settingTwo('languages_default', $locale);
            }
            if (Schema::hasTable('app_settings')) {
                if (setting('front_theme') == null) {
                    setting(['front_theme' => 'default'])->save();
                }
                if (setting('dash_theme') == null) {
                    setting(['dash_theme' => 'default'])->save();
                }

                // set app theme
                $activated_front_theme = setting('front_theme');
                $activated_dash_theme = setting('dash_theme');

                if ($activated_front_theme == $activated_dash_theme) {
                    Theme::set($activated_front_theme);
                } else {
                    if (request()->is('dashboard*') || request()->is('*/dashboard*')) {
                        Theme::set($activated_dash_theme);
                    } else {
                        Theme::set($activated_front_theme);
                    }
                }
            }
            $this->configSet();
            $this->jobRuns();
        } else {
            Theme::set('default');
        }

        app()->setLocale($locale);

        Health::checks([
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            DatabaseCheck::new(),
            // UsedDiskSpaceCheck::new(),
            MemoryLimit::new(),
        ]);

        $this->bootBladeDirectives();

    }

    public function jobRuns(): void
    {
        if (Schema::hasTable('jobs')) {
            $wordlist = DB::table('jobs')->where('id', '>', 0)->get();

            if (count($wordlist) > 0) {
                // change each job not default to default
                DB::table('jobs')
                    ->where('queue', '<>', 'default')
                    ->update(['queue' => 'default']);

                Artisan::call('queue:work --once');
            }
        }
    }

    public function configSet(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::first();
            $siteConfig = $this->app['config'];
            $siteConfig->set('broadcasting.connections.pusher.key', setting('pusher_app_key', ''));
            $siteConfig->set('broadcasting.connections.pusher.secret', setting('pusher_app_secret', ''));
            $siteConfig->set('broadcasting.connections.pusher.app_id', setting('pusher_app_id', ''));
            $siteConfig->set('broadcasting.connections.pusher.cluster', setting('pusher_app_cluster', 'mt1'));
            $siteConfig->set('broadcasting.connections.pusher.options.host', 'api-' . setting('pusher_app_cluster',
                'mt1') . '.pusher.com');

            $siteConfig = $this->app['config'];
            $siteConfig->set('services.recaptcha.key', $settings->recaptcha_sitekey);
            $siteConfig->set('services.recaptcha.secret', $settings->recaptcha_secretkey);

            $siteConfig->set('newsletter.lists.subscribers.id', setting('mailchimp_list_id', ''));
            $siteConfig->set('newsletter.driver_arguments.api_key', setting('mailchimp_api_key', ''));

            Config::set([
                'mail.mailers' => [
                    (env('MAIL_SMTP') ?? 'smtp') => [
                        'transport'  => env('MAIL_DRIVER') ?? 'smtp',
                        'host'       => $settings->smtp_host ?? env('MAIL_HOST'),
                        'port'       => (int) $settings->smtp_port ?? (int) env('MAIL_PORT'),
                        'encryption' => $settings->smtp_encryption ?? env('MAIL_ENCRYPTION'),
                        'username'   => $settings->smtp_username ?? env('MAIL_USERNAME'),
                        'password'   => $settings->smtp_password ?? env('MAIL_PASSWORD'),
                    ],
                    'timeout'          => null,
                    'local_domain'     => env('MAIL_EHLO_DOMAIN'),
                    'auth_mode'        => null,
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ]);

            Config::set(
                [
                    'mail.from' => [
                        'address' => $settings->smtp_email ?? env('MAIL_FROM_ADDRESS'), 'name' => $settings->smtp_sender_name ??
                        env('MAIL_FROM_NAME'),
                    ],
                ]
            );
        }
    }

    public function forceSchemeHttps(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function bootBladeDirectives(): void
    {
        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo rtrim(rtrim(number_format((float) $expression, 2), '0'), '.'); ?>";
        });

        Blade::directive('pushOnceFor', static function ($expression) {
            [$name, $suffix] = str($expression)->substr(1, -1)
                ->trim()
                ->replace('-', '_')
                ->explode(':');

            $key = '__pushonce_' . $name . '_' . $suffix;

            return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$name}'); ?>";
        });

        Blade::directive('endPushOnceFor', static function () {
            return '<?php $__env->stopPush(); endif; ?>';
        });
    }
}
