<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Mensajes enviados') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900">

                @can('access-user-metrics')
                    <div class="mb-6 flex items-center space-x-4">
                        <label for="user_selector" class="text-gray-700 font-semibold">{{ __('Ver métricas para:') }}</label>
                        <select id="user_selector" class="block w-auto rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @if(Auth::id() == $user->id) selected @endif>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <button id="loadUserMetrics" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-300">
                            {{ __('Cargar Métricas') }}
                        </button>
                    </div>
                @endcan

                  <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden shadow-md">
                      <thead class="bg-gray-50">
                          <tr>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plataforma</th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receptores</th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adjunto</th>
                              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enviado</th>
                          </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                          @foreach ($messages as $msg)
                              <tr>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $msg->platform }}</td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ implode(', ', json_decode($msg->recipients)) }}</td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ Str::limit($msg->message, 50) }}</td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                      @if ($msg->attachment)
                                          <a class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out hover:underline rounded-md" href="{{ asset('storage/' . $msg->attachment) }}" target="_blank">View</a>
                                      @else
                                          N/A
                                      @endif
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $msg->created_at->format('j F, Y') }}</td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>

                  <div class="mt-4">
                      {{ $messages->links() }}
                  </div>
              </div>
          </div>
      </div>
  </div>

  <div id="metricsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
      <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto relative">
          <button id="closeMetricsModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">
              &times;
          </button>

          <h3 class="text-2xl font-bold mb-6 text-gray-800">{{ __('Métricas de Mensajes') }}</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                  <h4 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Total de Mensajes Enviados:') }}</h4>
                  <p id="totalMessages" class="text-3xl font-extrabold text-blue-600"></p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                  <h4 class="text-lg font-semibold text-gray-700 mb-4">{{ __('Distribución por Plataforma') }}</h4>
                  <div class="w-full max-w-sm">
                        <canvas id="platformChart"></canvas>
                  </div>
                  <div id="chartLegend" class="mt-4 text-sm text-gray-600">
                      </div>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                  <h4 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Destinatarios Repetidos:') }}</h4>
                  <ul id="repeatedRecipients" class="list-disc list-inside text-gray-700">
                      </ul>
                  <p id="noRepeatedRecipients" class="text-gray-500 italic hidden">{{ __('No hay destinatarios repetidos.') }}</p>
              </div>

              <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                  <h4 class="text-lg font-semibold text-gray-700 mb-2">{{ __('Destinatarios Más Frecuentes (Top 5):') }}</h4>
                  <ul id="mostFrequentRecipients" class="list-decimal list-inside text-gray-700">
                      </ul>
                  <p id="noFrequentRecipients" class="text-gray-500 italic hidden">{{ __('No hay destinatarios frecuentes.') }}</p>
              </div>
          </div>
      </div>
  </div>
  @push('scripts')
    <div id="app-data" data-messages-user-route="{{ route('messages.user', ['userId' => 'PLACEHOLDER']) }}"></div>
    @vite('resources/js/metrics.js')
  @endpush
</x-app-layout>
