<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Envio de mensajes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form class="flex flex-col gap-4" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                      @csrf

                      <div class="form-group flex flex-col w-1/2">
                        <label for="platform">{{ __('Plataforma:') }}</label>
                        <select name="platform" required>
                            <option value="">{{ __('-- Seleccionar plataforma --') }}</option>
                            <option value="Telegram">{{ __('Telegram') }}</option>
                            <option value="Whatsapp">{{ __('Whatsapp') }}</option>
                            <option value="Discord">{{ __('Discord') }}</option>
                            <option value="Slack">{{ __('Slack') }}</option>
                        </select>
                      </div>

                      <div class="form-group flex flex-col w-full">
                        <label for="recipients">{{ __('Receptores (separados por coma):') }}</label>
                        <input type="text" name="recipients" required>
                      </div>

                      <div class="form-group flex flex-col w-full">
                        <label for="message">{{ __('Mensaje:') }}</label>
                        <textarea name="message" required></textarea>
                      </div>

                      <div class="form-group flex flex-col w-1/2">
                        <label for="attachment">{{ __('Adjunto:') }}</label>
                        <input type="file" name="attachment">
                      </div>

                      <button class="w-24 bg-blue-500 transition delay-150 duration-300 transition-discrete hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ __('Enviar') }}</button>
                  </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
