<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Sampah') }}: {{ Str::title(str_replace('_', ' ', $model)) }}
            </h2>
        </x-slot>

        <div class="py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Lengkap</h3>
                </div>

                <div class="mt-4 bg-white shadow-sm rounded-lg p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                        @foreach ($record->getAttributes() as $field => $value)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    {{ Str::title(str_replace('_', ' ', $field)) }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if (Str::endsWith($field, ['foto', 'gambar', 'image']) &&
                                            is_string($value) &&
                                            $value &&
                                            file_exists(public_path('storage/' . $value)))
                                        <img src="{{ asset('storage/' . $value) }}" alt="{{ $field }}"
                                            class="h-32 object-contain rounded border">
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="px-6 py-4 bg-white text-right">
                    <form method="POST"
                        action="{{ route('trash.restore', ['model' => $model, 'id' => $record->id]) }}">
                        @csrf
                        <div class="inline-flex gap-2">
                            <a href="{{ url()->previous() }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Kembali
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Pulihkan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
