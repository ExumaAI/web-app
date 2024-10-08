@props([
    'file' => null,
    'accept' => 'image/jpg,image/jpeg,image/png,image/svg+xml',
    'multiple' => false,
])

<div x-data="{
    isMultiple: Boolean('{{ $multiple }}') || false,
    progress: 0,
    isFocused: false,
    handleFiles() {
        if (this.isMultiple === true) {
            @this.uploadMultiple('{{ $attributes->wire('model')->value }}', this.$refs.input.files, () => {
            }, () => {
            }, (event) => {
                this.progress = event.detail.progress || 0
            })
        } else {
            @this.upload('{{ $attributes->wire('model')->value }}',  this.$refs.input.files[0], () => {
            }, () => {
            }, (event) => {
                this.progress = event.detail.progress || 0
            });
        }
    }
}"
     x-cloak>
    @if(! $file)
        @php $randomId = Str::random(6); @endphp
        <label for="file-{{ $randomId }}" class="relative block leading-tight bg-white hover:bg-gray-100 cursor-pointer inline-flex items-center transition duration-500 ease-in-out group overflow-hidden
            border-2 w-full pl-3 pr-4 py-2 rounded-lg border-dashed"
               wire:loading.class="pointer-events-none"
               :class="{ 'border-indigo-300': isFocused === true }"
        >
            {{-- hack to get the progress of upload file --}}
            <input
                    type="hidden"
                    name="{{ $attributes->wire('model')->value }}"
                    {{ $attributes->wire('model') }}
            />

            <input
                    type="file"
                    id="file-{{ $randomId }}"
                    class="absolute inset-0 cursor-pointer opacity-0 text-transparent sr-only"
                    accept="{{ $accept }}"
                    :multiple="isMultiple"
                    x-ref="input"
                    x-on:change.prevent="handleFiles"
                    x-on:focus="isFocused = true"
                    x-on:blur="isFocused = false"
            />

            {{-- Upload Progress --}}
            <div wire:loading.flex wire:target="{{ $attributes->wire('model')->value }}" wire:loading.class="w-full">
                <div class="text-center flex-1 p-4">
                    <div class="mb-2">{{ __('Uploading...') }}</div>
                    <div>
                        <div class="h-3 relative max-w-lg mx-auto rounded-full overflow-hidden">
                            <div class="w-full h-full bg-gray-200 absolute"></div>
                            <div class="h-full bg-green-500 absolute" x-bind:style="'width:' + progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Placeholder text for mode 'attachment' --}}
            <div class="flex items-center justify-center flex-1 px-4 py-2" wire:loading.class="hidden" wire:target="{{ $attributes->wire('model')->value }}">
                @if($slot->isEmpty())
                    @if($multiple)
                        <svg class="-mr-5 -mt-2 transform -rotate-6 h-8 w-8 text-gray-300 group-hover:text-indigo-300 transition duration-500 ease-in-out" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                            <rect width="256" height="256" fill="none"></rect>
                            <g>
                                <circle cx="99.99951" cy="92" r="12"></circle>
                                <path d="M208.00049,31.99963h-160a16.01833,16.01833,0,0,0-16,16V175.97369l-.001.0307.001,31.99524a16.01833,16.01833,0,0,0,16,16h160a16.01834,16.01834,0,0,0,16-16v-160A16.01834,16.01834,0,0,0,208.00049,31.99963Zm-28.68653,80a16.019,16.019,0,0,0-22.62792,0l-44.68555,44.68653L91.314,135.99963a16.02161,16.02161,0,0,0-22.62792,0L48.00049,156.68457V47.99963h160l.00586,92.6922Z"></path>
                            </g>
                        </svg>
                        <svg class="transition duration-500 ease-in-out relative h-8 w-8 transform rotate-3 text-gray-400 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                            <rect width="256" height="256" fill="none"></rect>
                            <path d="M168.001,100.00017v.00341a12.00175,12.00175,0,1,1,0-.00341ZM232,56V200a16.01835,16.01835,0,0,1-16,16H40a16.01835,16.01835,0,0,1-16-16V56A16.01835,16.01835,0,0,1,40,40H216A16.01835,16.01835,0,0,1,232,56Zm-15.9917,108.6936L216,56H40v92.68575L76.68652,112.0002a16.01892,16.01892,0,0,1,22.62793,0L144.001,156.68685l20.68554-20.68658a16.01891,16.01891,0,0,1,22.62793,0Z"></path>
                        </svg>
                    @else
                        <svg class="h-8 w-8 text-gray-300 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                            <rect width="256" height="256" fill="none"></rect>
                            <path d="M168.001,100.00017v.00341a12.00175,12.00175,0,1,1,0-.00341ZM232,56V200a16.01835,16.01835,0,0,1-16,16H40a16.01835,16.01835,0,0,1-16-16V56A16.01835,16.01835,0,0,1,40,40H216A16.01835,16.01835,0,0,1,232,56Zm-15.9917,108.6936L216,56H40v92.68575L76.68652,112.0002a16.01892,16.01892,0,0,1,22.62793,0L144.001,156.68685l20.68554-20.68658a16.01891,16.01891,0,0,1,22.62793,0Z"></path>
                        </svg>
                    @endif

                    <span class="ml-2 text-gray-600">{{ is_array($file) ? __('Browse files') : __('Browse file') }} |
                        <span class="text-sm">PNG, JPEG, SVG</span></span>
                @else
                    {{ $slot }}
                @endif
            </div>
        </label>
    @endif

    {{-- Loading indicator for file remove --}}
    <div wire:loading.delay wire:loading.flex wire:target="removeUpload" wire:loading.class="w-full">
        <div class="text-sm text-red-500 bg-red-100 flex-1 p-1 text-center rounded">
            {{ __('Removing file...') }}
        </div>
    </div>

    {{-- Preview for mode 'attachment' --}}
    <div>
        @if(is_array($file) && count($file) > 0)
            @foreach($file as $key => $f)
                <div class="py-3 flex {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                    <div class="w-16 mr-4 flex-shrink-0 shadow-xs rounded-lg">
                        @if(collect(['jpg', 'png', 'jpeg', 'webp'])->contains($f->getClientOriginalExtension()))
                            <div class="relative pb-16 overflow-hidden rounded-lg border border-gray-100">
                                <img src="{{ $f->temporaryUrl() }}" alt="" class="w-full h-full absolute object-cover rounded-lg">
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gray-100 text-blue-500 flex items-center justify-center rounded-lg border border-gray-100">
                                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if ($multiple)
                            {{-- prints attachment.* --}}
                            @error($attributes->wire('model')->value . '.'. $key)
                            <p class="text-sm text-red-600" class="mb-2">{{ $message }}</p>
                            @enderror
                        @endif

                        <div class="text-sm font-medium truncate w-40 md:w-auto">{{ $f->getClientOriginalName() }}</div>
                        <div class="flex items-center space-x-1">
                            <div class="text-xs text-gray-500">{{ Str::formatBytes($f->getSize()) }}</div>
                            <div class="text-gray-400 text-xs">&bull;</div>
                            <div class="text-xs text-gray-500 uppercase">{{ $f->getClientOriginalExtension() }}</div>
                        </div>

                        <button
                                wire:key="remove-attachment-{{ $f->getFilename() }}"
                                wire:loading.attr="disabled"
                                type="button"
                                x-on:click.prevent="$wire.removeUpload('{{ $attributes->wire('model')->value }}', '{{ $f->getFilename() }}')" class="text-xs text-red-500 appearance-none hover:underline">
                            Remove
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            @if($file)
                <div class="mt-3 flex">
                    <div class="w-16 mr-4 flex-shrink-0 shadow-xs rounded-lg">
                        @if(collect(['jpg', 'png', 'jpeg', 'webp', 'svg'])->contains($file->getClientOriginalExtension()))
                            <div class="relative pb-16 w-full overflow-hidden rounded-lg border border-gray-100">
                                <img src="{{ $file->temporaryUrl() }}" class="w-full h-full absolute object-cover rounded-lg" alt="">
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gray-100 text-blue-500 flex items-center justify-center rounded-lg border border-gray-100">
                                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>

                        @error($attributes->wire('model')->value)
                        <p class="text-sm text-red-600" class="mb-2">{{ $message }}</p>
                        @enderror

                        <div class="text-sm font-medium truncate w-40 md:w-auto">{{ $file->getClientOriginalName() }}</div>
                        <div class="flex items-center space-x-1">
                            <div class="text-xs text-gray-500">{{ Str::formatBytes($file->getSize()) }}</div>
                            <div class="text-gray-400 text-xs">&bull;</div>
                            <div class="text-xs text-gray-500 uppercase">{{ $file->getClientOriginalExtension() }}</div>
                        </div>
                        <button wire:loading.attr="disabled"
                                type="button"
                                x-on:click.prevent="$wire.removeUpload('{{ $attributes->wire('model')->value }}', '{{ $file->getFilename() }}')" class="text-xs text-red-500 appearance-none hover:underline">
                            {{ __('Remove') }}
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
