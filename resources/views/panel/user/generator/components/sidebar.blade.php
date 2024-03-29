@php
    $plan = Auth::user()->activePlan();
    $plan_type = 'regular';

    if ($plan != null) {
        $plan_type = strtolower($plan->plan_type);
    }

    $auth = Auth::user();
@endphp

@push('style')
    <style>
        @keyframes sidebar-step-slide-in {
            from {
                transform: translateX(25px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes sidebar-step-slide-out {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(-25px);
                opacity: 0;
            }
        }

        ::view-transition-old(sidebar-step-container) {
            animation: sidebar-step-slide-out 0.3s ease both;
        }

        ::view-transition-new(sidebar-step-container) {
            animation: sidebar-step-slide-in 0.3s ease both;
        }

        /* Make the magic happen */
        .lqd-generator-sidebar-step-container {
            view-transition-name: sidebar-step-container;
        }
    </style>
@endpush

<div
    class="lqd-generator-sidebar-backdrop visible fixed inset-0 z-30 flex items-center justify-center bg-white/90 ps-[--sidebar-w] opacity-100 transition-opacity duration-300 dark:bg-black/80"
    x-init
    :class="{ 'opacity-100': !sideNavCollapsed, 'opacity-0': sideNavCollapsed, 'invisible': sideNavCollapsed }"
    @click="toggleSideNavCollapse('collapse')"
>
    <div class="flex max-w-sm cursor-pointer flex-col items-center justify-center !gap-5 px-8 text-center max-md:hidden">
        <img class="mx-auto h-auto w-full max-w-[40px] group-[.navbar-shrinked]/body:block dark:!hidden"
             src="/{{ $setting->logo_collapsed_path }}"
             @if (isset($setting->logo_collapsed_2x_path)) srcset="/{{ $setting->logo_collapsed_2x_path }} 2x" @endif
             alt="{{ $setting->site_name }}"
        >
        <h4 class="m-0 text-[19px]">@lang('Your content will appear here.')</h4>
        <p class="m-0 text-[15px] font-medium text-black/50 dark:text-white/50">
            @lang('Simply select a pre-built template or create your own')
            <span class="text-black underline dark:!text-white">
                @lang('custom content here.')
            </span>
        </p>
    </div>
</div>

<div
    class="lqd-generator-sidebar group/sidebar fixed bottom-0 start-0 top-[--editor-tb-h] z-40 w-[--sidebar-w] translate-x-0 bg-[--tblr-body-bg] shadow-[2px_4px_26px_rgba(0,0,0,0.05)] transition-all duration-500 ease-[cubic-bezier(0.25,0.8,0.49,1.0)] group-[&.lqd-generator-sidebar-collapsed]/generator:-translate-x-[calc(100%-35px)]">
    <button
        class="lqd-generator-sidebar-toggle bg-black/15 absolute -end-2.5 top-16 z-10 flex !h-5 !w-5 origin-center translate-x-0 items-center justify-center rounded-full border-none p-0 text-black/90 transition-colors hover:bg-black hover:text-white dark:bg-white dark:text-black dark:hover:bg-white dark:hover:text-black max-lg:top-1/2 max-lg:-translate-y-1/2 max-md:!h-7 max-md:!w-7"
        @click.prevent="toggleSideNavCollapse()"
        :class="{ 'rotate-180': sideNavCollapsed }"
    >
        <span class="absolute start-1/2 top-1/2 inline-block h-10 w-10 -translate-x-1/2 -translate-y-1/2"></span>
        <svg
            class="h-3.5 w-3.5"
            xmlns="http://www.w3.org/2000/svg"
            width="44"
            height="44"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path
                stroke="none"
                d="M0 0h24v24H0z"
                fill="none"
            />
            <path d="M15 6l-6 6l6 6" />
        </svg>
    </button>

    <div
        class="lqd-generator-sidebar-inner h-full w-full translate-x-0 overflow-y-auto !pt-5 transition-all delay-100 duration-300 ease-out group-[&.lqd-generator-sidebar-collapsed]/generator:-translate-x-4 group-[&.lqd-generator-sidebar-collapsed]/generator:opacity-0">
        <div class="lqd-steps !mb-5 !px-5">
            <p class="lqd-step flex items-center justify-between !gap-4 text-sm font-semibold">
                <span class="flex items-center justify-between !gap-3">
                    <span
                        class="bg-primary inline-flex !h-5 !w-5 items-center justify-center rounded-md text-[12px] font-normal text-white"
                        x-text="generatorStep + 1"
                    >
                        1
                    </span>
                    <span
                        class="active hidden [&.active]:inline-block"
                        :class="{ 'active': generatorStep === 0 }"
                    >
                        {{ __('Choose a Template') }}
                    </span>
                    <span
                        class="hidden [&.active]:inline-block"
                        :class="{ 'active': generatorStep === 1 }"
                    >
                        {{ __('Add Information') }}
                    </span>
                </span>

                <button
                    class="hidden !h-6 !w-6 cursor-default items-center justify-center border-none bg-transparent p-0 text-inherit [&.active]:flex"
                    {{-- :class="{ 'active': generatorStep === 0 }" --}}
                >
                    <svg
                        class="!h-5 !w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        />
                        <path d="M4 6l16 0" />
                        <path d="M8 12l8 0" />
                        <path d="M6 18l12 0" />
                    </svg>
                </button>

                <button
                    class="hidden !h-6 !w-6 items-center justify-center rounded-md border-none bg-transparent p-0 text-inherit transition-all hover:!bg-black/5 dark:hover:!bg-white/5 [&.active]:flex"
                    :class="{ 'active': generatorStep === 1 }"
                    @click.prevent="setGeneratorStep(0)"
                >
                    <svg
                        class="!h-5 !w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        width="44"
                        height="44"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path
                            stroke="none"
                            d="M0 0h24v24H0z"
                            fill="none"
                        />
                        <path d="M5 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M19 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M5 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                        <path d="M19 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                    </svg>
                </button>
            </p>
        </div> <!-- .lqd-steps -->

        <div
            class="lqd-generator-sidebar-step-container"
            data-step="0"
            :class="{ 'hidden': generatorStep !== 0 }"
        >
            <form
                class="lqd-generator-search-form group !mb-6 !px-5"
                @submit.prevent
            >
                <div class="input-icon w-full max-lg:bg-[#fff] max-lg:dark:bg-zinc-800">
                    <span class="input-icon-addon">
                        <svg
                            class="icon"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                stroke="none"
                                d="M0 0h24v24H0z"
                                fill="none"
                            />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </span>
                    <input
                        class="peer"
                        type="search"
                        @keyup="setItemsSearchStr($el.value)"
                        placeholder="{{ __('Search for templates and documents...') }}"
                        @keyup.slash.prevent.window="$el.focus()"
                        aria-label="Search in website"
                    >
                    <kbd
                        class="peer-focus:scale-70 pointer-events-none absolute !end-[12px] top-1/2 inline-block -translate-y-1/2 bg-[var(--tblr-bg-surface)] text-[17px] font-bold transition-all group-[.is-searching]:invisible group-[.is-searching]:opacity-0 peer-focus:invisible peer-focus:opacity-0 max-lg:hidden">
                        /
                    </kbd>
                    <span class="absolute !end-[20px] top-1/2 -translate-y-1/2">
                        <span
                            class="spinner-border spinner-border-sm text-muted hidden group-[.is-searching]:block"
                            role="status"
                        ></span>
                    </span>
                    <span
                        class="pointer-events-none absolute !end-3 top-1/2 -translate-x-2 -translate-y-1/2 opacity-0 transition-all group-[.done-searching]:hidden group-[.is-searching]:hidden peer-focus:translate-x-0 peer-focus:!opacity-100 rtl:-scale-x-100"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="25"
                            viewBox="0 96 960 960"
                            width="25"
                            fill="currentColor"
                        >
                            <path d="m375 816-43-43 198-198-198-198 43-43 241 241-241 241Z" />
                        </svg>
                    </span>
                </div>
            </form>

            <div
                class="lqd-generator-categories"
                x-data="{ activeFilter: '{{ $filters->first()->name }}' }"
            >
                @foreach ($filters as $filter)
                    @php
                        $cat_items = $list->filter(function ($list_item) use ($filter) {
                            return str()->contains($list_item->filters, $filter->name) && $list_item->active == 1 && !Str::startsWith($list_item->slug, 'ai_');
                        });
                    @endphp
                    @if (!$cat_items->isEmpty())
                        <div class="lqd-generator-category group/cat">
                            <button
                                class="lqd-generator-filter-trigger text-heading flex w-full items-center justify-between gap-2 border-b-0 border-e-0 border-s-0 border-t border-solid border-[--tblr-border-color] bg-transparent !px-5 !py-6 font-semibold leading-tight group-first/cat:border-t-0"
                                @click.prevent="activeFilter === '{{ $filter->name }}' ? activeFilter = '' : activeFilter = '{{ $filter->name }}'"
                            >
                                {{ str()->ucfirst($filter->name) }}
                                <svg
                                    class="!h-4 !w-4 transition-transform group-[&.lqd-showing-search-results]/sidebar:!rotate-0"
                                    :class="{ 'rotate-180': activeFilter !== '{{ $filter->name }}' }"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="44"
                                    height="44"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    fill="none"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        stroke="none"
                                        d="M0 0h24v24H0z"
                                        fill="none"
                                    />
                                    <path d="M6 9l6 6l6 -6" />
                                </svg>
                            </button>

                            <div
                                @class([
                                    'lqd-generator-category-list flex flex-col !gap-4 !pt-3 !pb-8 !px-5 group-[&.lqd-showing-search-results]/sidebar:!flex',
                                    'hidden' => !$loop->first,
                                ])
                                :class="{ 'hidden': activeFilter !== '{{ $filter->name }}' }"
                            >
                                @foreach ($cat_items as $item)
                                    @if ($item->active != 1 || Str::startsWith($item->slug, 'ai_'))
                                        @continue
                                    @endif
                                    @php
                                        $upgrade = false;
                                        if ($app_is_demo) {
                                            if ($item->premium == 1 && $plan_type === 'regular') {
                                                $upgrade = true;
                                            }
                                        } else {
                                            if ($auth->type != 'admin' && $item->premium == 1 && $plan_type === 'regular') {
                                                $upgrade = true;
                                            }
                                        }
                                    @endphp

                                    <div
                                        class="lqd-generator-item group relative flex w-full items-center !gap-2 rounded-full !px-2.5 !py-2 transition-all hover:scale-105 hover:shadow-xl hover:shadow-black/5"
                                        data-title="{{ str()->lower(__($item->title)) }}"
                                        data-description="{{ str()->lower(__($item->description)) }}"
                                        data-filter="{{ $item->filters }}"
                                        :class="{
                                            'hidden': itemsSearchStr !== '' && (!$el.getAttribute('data-title').includes(itemsSearchStr) && !$el.getAttribute(
                                                'data-description').includes(itemsSearchStr) && !$el.getAttribute('data-filter').includes(itemsSearchStr)),
                                        }"
                                        style="background: {{ $item->color }}"
                                    >
                                        <span
                                            class="relative flex h-9 w-9 items-center justify-center rounded-full bg-white shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg [&_svg]:h-[20px] [&_svg]:w-[20px]"
                                        >
                                            @if ($item->image !== 'none')
                                                <span class="inline-block transition-all duration-300 group-hover:scale-110">
                                                    {!! html_entity_decode($item->image) !!}
                                                </span>
                                            @endif
                                            <span @class([
                                                'absolute bottom-0 end-0 !h-3 !w-3 rounded border-2 border-solid border-white',
                                                'bg-green-500' => $item->active == 1,
                                                'bg-red-500' => $item->active != 1,
                                            ])></span>
                                        </span>

                                        <div>
                                            <h4 class="relative m-0 inline-block text-sm font-medium dark:text-black">
                                                {{ __($item->title) }}
                                                <span
                                                    class="absolute start-[calc(100%+0.35rem)] top-1/2 inline-block -translate-x-1 -translate-y-1/2 align-bottom opacity-0 transition-all group-hover:translate-x-0 group-hover:!opacity-100 rtl:-scale-x-100"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="18"
                                                        height="18"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="2"
                                                        stroke="currentColor"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    >
                                                        <path d="M9 6l6 6l-6 6"></path>
                                                    </svg>
                                                </span>
                                            </h4>
                                        </div>

                                        @if ($item->active == 1)
                                            <div
                                                class="z-2 @if ($upgrade) bg-[--tblr-body-bg] opacity-75 @endif absolute left-0 top-0 h-full w-full transition-all">
                                                @if ($upgrade)
                                                    <div class="absolute right-2 top-2 z-10 rounded-md bg-[#E2FFFC] px-2 py-0.5 font-medium text-black">
                                                        {{ __('Upgrade') }}
                                                    </div>
                                                    <a
                                                        class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.payment.subscription')) }}"
                                                    >
                                                        {{ __('Upgrade') }}
                                                    </a>
                                                @elseif($item->type == 'text' or $item->type == 'code')
                                                    @if ($item->slug == 'ai_article_wizard_generator')
                                                        @if (Auth::user()->remaining_words > 0 or Auth::user()->remaining_words == -1)
                                                            <a
                                                                class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.articlewizard.new')) }}"
                                                            >
                                                                {{ __('Create Workbook') }}
                                                            </a>
                                                        @endif
                                                    @else
                                                        @if (Auth::user()->remaining_words > 0 or Auth::user()->remaining_words == -1)
                                                            <a
                                                                class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator.workbook', $item->slug)) }}"
                                                            >
                                                                {{ __('Create Workbook') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                @elseif($item->type == 'voiceover')
                                                    @if (Auth::user()->remaining_words > 0 or Auth::user()->remaining_words == -1)
                                                        <a
                                                            class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator', $item->slug)) }}"
                                                        >
                                                            {{ __('Create Workbook') }}
                                                        </a>
                                                    @endif
                                                @elseif($item->type == 'image')
                                                    @if (Auth::user()->remaining_images > 0 or Auth::user()->remaining_images == -1)
                                                        <a
                                                            class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator', $item->slug)) }}"
                                                        >
                                                            {{ __('Create') }}
                                                        </a>
                                                    @endif
                                                @elseif($item->type == 'audio')
                                                    @if (Auth::user()->remaining_words > 0 or Auth::user()->remaining_words == -1)
                                                        <a
                                                            class="absolute left-0 top-0 inline-block h-full w-full overflow-hidden -indent-[99999px]"
                                                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.generator', $item->slug)) }}"
                                                        >
                                                            {{ __('Create') }}
                                                        </a>
                                                    @endif
                                                @else
                                                    <div class="absolute inset-0 flex items-center justify-center bg-zinc-900 bg-opacity-5 backdrop-blur-[1px]">
                                                        <a
                                                            class="btn text-dark pointer-events-none cursor-default bg-white"
                                                            href="#"
                                                            disabled=""
                                                        >
                                                            {{ __('No Tokens Left') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Getting generator options via ajax call --}}
                                            <a
                                                class="absolute inset-0 z-20 inline-block [&.loading]:animate-pulse [&.loading]:bg-white/50"
                                                href="/dashboard/user/generator/{{ $item->slug }}"
                                                x-init
                                                x-target="lqd-generator-options"
                                                @ajax:before="document.querySelector('#document_title').value = ''; $el.classList.add('loading')"
                                                @ajax:success="setGeneratorStep(1)"
                                                @ajax:after="$el.classList.remove('loading')"
                                            ></a>
                                        @endif
                                    </div> <!-- .lqd-generator-item -->
                                @endforeach
                            </div> <!-- .lqd-generator-category-list -->
                        </div> <!-- .lqd-generator-category -->
                    @endif
                @endforeach
            </div> <!-- .lqd-generator-categories -->
        </div> <!-- .lqd-generator-sidebar-step-container -->

        <div
            class="lqd-generator-sidebar-step-container hidden"
            data-step="1"
            :class="{ 'hidden': generatorStep !== 1 }"
        >
            <div
                class="lqd-generator-options !px-5 !pb-8"
                id="lqd-generator-options"
                x-merge.transition
            >

            </div> <!-- .lqd-generator-options -->

        </div> <!-- .lqd-generator-sidebar-step-container -->
    </div> <!-- .lqd-generator-sidebar-inner -->
</div> <!-- .lqd-generator-sidebar -->

@if ($setting->hosting_type != 'high')
    <input
        id="guest_id"
        type="hidden"
        value="{{ $apiUrl }}"
    >
    <input
        id="guest_event_id"
        type="hidden"
        value="{{ $apikeyPart1 }}"
    >
    <input
        id="guest_look_id"
        type="hidden"
        value="{{ $apikeyPart2 }}"
    >
    <input
        id="guest_product_id"
        type="hidden"
        value="{{ $apikeyPart3 }}"
    >
@endif
