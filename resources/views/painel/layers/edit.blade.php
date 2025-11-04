<x-painel-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('painel.layers.index') }}" 
               class="text-gray-600 hover:text-gray-900">
                ← Voltar
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Camada: {{ $layer->name }}
            </h2>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('painel.layers.update', $layer->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Nome da Camada -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Camada *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $layer->name) }}"
                           required
                           maxlength="100"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Pontos de Interesse, Rotas de Ônibus, etc.">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload GeoJSON (Opcional na edição) -->
                <div class="mb-6">
                    <label for="geojson" class="block text-sm font-medium text-gray-700 mb-2">
                        Atualizar Geometria (Opcional)
                    </label>
                    <input type="file" 
                           name="geojson" 
                           id="geojson" 
                           accept=".json,.geojson"
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
                        Deixe em branco para manter a geometria atual. 
                        Se enviar um novo arquivo, a geometria será substituída.
                    </p>
                </div>

                <!-- Informações da camada atual -->
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <h4 class="font-medium text-gray-900 mb-2">ℹ️ Informações da Camada</h4>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">ID:</dt>
                            <dd class="font-medium text-gray-900">{{ $layer->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Criada em:</dt>
                            <dd class="font-medium text-gray-900">{{ $layer->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Atualizada em:</dt>
                            <dd class="font-medium text-gray-900">{{ $layer->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Botões -->
                <div class="flex gap-4">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                        Salvar Alterações
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
