@extends('panel.layout.app', ['disable_tblr' => true])

@section('content')
    <header class="fixed inset-x-0 top-0 flex justify-center px-4 py-6 border-b bg-background/10 backdrop-blur-md">
        <figure>
            @if(isset($setting))
                @if (isset($setting->logo_dashboard))
                    <img
                        class="w-auto max-h-14 dark:hidden"
                        src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}"
                        @if (isset($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden w-auto max-h-14 dark:block"
                        src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}"
                        @if (isset($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @else
                    <img
                        class="w-auto max-h-14 dark:hidden"
                        src="{{ custom_theme_url($setting->logo_path, true) }}"
                        @if (isset($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                    <img
                        class="hidden w-auto max-h-14 dark:block"
                        src="{{ custom_theme_url($setting->logo_dark_path, true) }}"
                        @if (isset($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                        alt="{{ $setting->site_name }}"
                    >
                @endif
            @endif
        </figure>
    </header>
    <div class="flex flex-col items-center justify-center min-h-screen py-12">
        <div class="w-full pt-20 mx-auto text-center lg:w-5/12">
            <h1 class="text-[40vw] leading-none opacity-10 sm:text-[212px]">
                @yield('error_code', '404')
            </h1>

            <h3 class="text-4xl font-bold">
                @yield('error_title', __('Looks like you’re lost.'))
            </h3>

            <p class="text-sm font-medium mb-14 opacity-90">
                @yield('error_subtitle', __('We can’t seem to find the page you’re looking for.'))
            </p>

            <div class="mx-auto sm:w-8/12">
                <x-button
                    class="w-full"
                    size="lg"
                    href="{{ route('index') }}"
                >
                    {{ __('Take me back to the homepage') }}
                </x-button>
            </div>
        </div>
    </div>
@endsection
