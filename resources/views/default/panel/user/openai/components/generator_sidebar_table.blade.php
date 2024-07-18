@if ($openai->type == 'audio')
    <div class="space-y-10">
        @forelse ($userOpenai as $entry)
            <x-card
                class="bg-background text-sm font-normal leading-6"
                class:body="max-md:p-5"
                size="lg"
            >
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-full bg-foreground/5 p-3 text-heading-foreground lg:flex-nowrap lg:px-6 lg:py-4">
                    <p class="relative m-0 hidden w-full max-w-[200px] truncate lg:block">
                        {{ basename($entry->input) }}
                    </p>
                    <div class="flex grow justify-end gap-2">
                        <div
                            class="data-audio flex grow items-center"
                            data-audio="/{{ $entry->input }}"
                        >
                            <button type="button">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="9"
                                    height="9"
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
                                    ></path>
                                    <path
                                        d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                        stroke-width="0"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="10"
                                    height="10"
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
                                    ></path>
                                    <path
                                        d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                        stroke-width="0"
                                        fill="currentColor"
                                    ></path>
                                    <path
                                        d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                        stroke-width="0"
                                        fill="currentColor"
                                    ></path>
                                </svg>
                            </button>
                            <div class="audio-preview grow"></div>
                            <span>0:00</span>
                        </div>
                        <x-button
                            class="size-9 relative z-10 shrink-0 border border-foreground/50 shadow-xs"
                            size="none"
                            variant="outline"
                            hover-variant="success"
                            href="/{{ $entry->input }}"
                            target="_blank"
                            title="{{ __('View and edit') }}"
                            download="{{ $entry->input }}"
                        >
                            <x-tabler-download class="size-4" />
                        </x-button>
                        <x-button
                            class="size-9 relative z-10 shrink-0 border border-foreground/50 shadow-xs"
                            size="none"
                            variant="outline"
                            hover-variant="danger"
                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.image.delete', $entry->slug)) }}"
                            onclick="return confirm('Are you sure?')"
                            title="{{ __('Delete') }}"
                        >
                            <x-tabler-x class="size-4" />
                        </x-button>
                    </div>
                </div>
                <p class="lqd-audio-output mb-5">
                    {!! $entry->output !!}
                </p>
                <x-button
                    class="w-full"
                    size="lg"
                    variant="ghost-shadow"
                    href="{{ route('dashboard.user.generator.index', $entry->slug) }}"
                >
                    @lang('Open in AI Editor')
                </x-button>
                <button
                    class="lqd-clipboard-copy size-9 absolute -bottom-4 -end-4 inline-flex items-center justify-center rounded-full border bg-heading-background p-0 text-heading-foreground shadow-lg transition-all hover:-translate-y-[2px] hover:scale-110"
                    data-copy-options='{ "content": ".lqd-audio-output", "contentIn": "<.lqd-card" }'
                    title="{{ __('Copy to clipboard') }}"
                >
                    <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                    <x-tabler-copy
                        class="size-5"
                        stroke-width="1.75"
                    />
                </button>
            </x-card>
        @empty
            <h4>
                {{ __('No entries created yet.') }}
            </h4>
        @endforelse
    </div>
@elseif ($openai->type == 'voiceover')
    <x-table>
        <x-slot:head>
            <tr>
                <th>
                    {{ __('File') }}
                </th>
                <th>
                    {{ __('Language') }}
                </th>
                <th>
                    {{ __('Voice') }}
                </th>
                <th>
                    {{ __('Date') }}
                </th>
                <th>
                    {{ __('Play') }}
                </th>
                <th class="text-end">
                    {{ __('Action') }}
                </th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($userOpenai as $entry)
                @if (empty(json_decode($entry->response)))
                    @continue
                @endif
                <tr class="text-2xs">
                    <td>{{ $entry->title }}</td>
                    <td class="text-3xs">
                        <span class="inline-block rounded-sm bg-heading-foreground/[0.06] px-1.5 py-0.5">
                            @foreach (array_unique(json_decode($entry->response)->language) as $lang)
                                {{ country2flag(explode('-', $lang)[1]) }}
                            @endforeach
                            {{ $lang }}
                        </span>
                    </td>
                    <td>
                        @foreach (array_unique(json_decode($entry->response)->voices) as $voice)
                            {{ getVoiceNames($voice) }}
                        @endforeach
                    </td>
                    <td>
                        <span>{{ $entry->created_at->format('M d, Y') }},
                            <span class="opacity-60">
                                {{ $entry->created_at->format('H:m') }}
                            </span>
                        </span>
                    </td>
                    <td
                        class="data-audio mt-3 flex items-center"
                        data-audio="/uploads/{{ $entry->output }}"
                    >
                        <button type="button">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="9"
                                height="9"
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
                                ></path>
                                <path
                                    d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z"
                                    stroke-width="0"
                                    fill="currentColor"
                                ></path>
                            </svg>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="10"
                                height="10"
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
                                ></path>
                                <path
                                    d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                    stroke-width="0"
                                    fill="currentColor"
                                ></path>
                                <path
                                    d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z"
                                    stroke-width="0"
                                    fill="currentColor"
                                ></path>
                            </svg>
                        </button>
                        <div class="audio-preview grow"></div>
                        <span>0:00</span>
                    </td>
                    <td class="whitespace-nowrap text-end">
                        <x-button
                            class="size-9 relative z-10"
                            size="none"
                            variant="ghost-shadow"
                            hover-variant="primary"
                            href="/uploads/{{ $entry->output }}"
                            target="_blank"
                            title="{{ __('View and edit') }}"
                        >
                            <x-tabler-download class="size-4" />
                        </x-button>
                        <x-button
                            class="size-9 relative z-10"
                            size="none"
                            variant="danger"
                            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.image.delete', $entry->slug)) }}"
                            onclick="return confirm('Are you sure?')"
                            title="{{ __('Delete') }}"
                        >
                            <x-tabler-x class="size-4" />
                        </x-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">{{ __('No entries created yet.') }}</td>
                </tr>
            @endforelse

        </x-slot:body>

    </x-table>

    <div class="float-right m-4">
        {{ $userOpenai->withPath(route('dashboard.user.openai.generator', 'ai_voiceover'))->links('pagination::bootstrap-5-alt') }}
    </div>
@else
    <x-table>
        <x-slot:head>
            <tr>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Result') }}</th>
            </tr>
        </x-slot:head>
        <x-slot:body>
            @forelse ($userOpenai as $entry)
                <tr>
                    <td>
                        <span
                            class="size-11 inline-flex items-center justify-center rounded-full bg-cover bg-center [&_svg]:h-[20px] [&_svg]:w-[20px]"
                            style="background: {{ $entry->generator->color }}"
                        >
                            @if ($entry->generator->image !== 'none')
                                {!! html_entity_decode($entry->generator->image) !!}
                            @endif
                        </span>
                    </td>
                    @if ($openai->type == 'text')
                        <td>
                            {!! $entry->output !!}
                        </td>
                    @elseif($openai->type == 'code')
                        <td>
                            <div class="mt-4 min-h-full border-t pt-8">
                                <pre
                                    class="line-numbers min-h-full [direction:ltr]"
                                    id="code-pre"
                                ><code id="code-output">{{ $entry->output }}</code></pre>
                            </div>
                        </td>
                    @else
                        <td>
                            {{ $entry->output }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="2">{{ __('No entries created yet.') }}</td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-table>
@endif
