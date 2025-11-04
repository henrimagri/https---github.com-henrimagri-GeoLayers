<x-painel-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('painel.layers.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                ← Voltar
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nova Camada
            </h2>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('painel.layers.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Nome da Camada -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Camada *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           required
                           maxlength="100"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Pontos de Interesse, Rotas de Ônibus, etc.">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Nome descritivo para identificar esta camada no mapa.
                    </p>
                </div>

                <!-- Upload GeoJSON -->
                <div class="mb-6">
                    <label for="geojson" class="block text-sm font-medium text-gray-700 mb-2">
                        Arquivo GeoJSON *
                    </label>
                    <input type="file" 
                           name="geojson" 
                           id="geojson" 
                           accept=".json,.geojson"
                           required
                           class="w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                  @error('geojson') border-red-500 @enderror">
                    @error('geojson')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Arquivo .geojson ou .json contendo geometrias válidas (pontos, linhas, polígonos). Tamanho máximo: 10MB.
                    </p>
                </div>

                <!-- Informações sobre GeoJSON -->
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700">
                    <h4 class="font-bold mb-2">📄 Sobre o formato GeoJSON</h4>
                    <p class="text-sm mb-2">
                        O GeoJSON é um formato para codificar dados geográficos usando JSON. 
                        Exemplos de estruturas válidas:
                    </p>
                    <ul class="text-sm list-disc list-inside space-y-1">
                        <li><strong>Point:</strong> Um ponto único (ex: localização de um lugar)</li>
                        <li><strong>LineString:</strong> Uma linha (ex: rota, estrada)</li>
                        <li><strong>Polygon:</strong> Um polígono (ex: bairro, área)</li>
                        <li><strong>Feature:</strong> Uma geometria com propriedades</li>
                        <li><strong>FeatureCollection:</strong> Coleção de várias features</li>
                    </ul>
                    <p class="text-sm mt-2">
                        💡 Você pode criar GeoJSON em sites como 
                        <a href="https://geojson.io" target="_blank" class="underline font-medium">geojson.io</a>
                    </p>
                </div>

                <!-- Exemplo de GeoJSON -->
                <details class="mb-6">
                    <summary class="cursor-pointer text-sm font-medium text-gray-700 hover:text-gray-900 mb-2">
                        📋 Ver exemplo de GeoJSON
                    </summary>
                    <pre class="mt-2 p-4 bg-gray-100 rounded-md text-xs overflow-x-auto"><code>{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "name": "São Paulo"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [-46.6333, -23.5505]
      }
    },
    {
      "type": "Feature",
      "properties": {
        "name": "Rio de Janeiro"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [-43.1729, -22.9068]
      }
    }
  ]
}</code></pre>
                </details>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                        Criar Camada
                    </button>
                    <a href="{{ route('painel.layers.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-painel-layout>
