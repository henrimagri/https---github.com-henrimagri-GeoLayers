<?php

namespace App\Http\Controllers;

use App\Services\LayerService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

/**
 * Controller do Painel Administrativo de Layers
 * 
 * Gerencia o CRUD de layers através de uma interface web protegida.
 * Requer autenticação (middleware 'auth').
 * 
 * Rotas (prefixo /painel):
 * - GET /painel/layers - Lista todas as layers
 * - GET /painel/layers/create - Formulário de criação
 * - POST /painel/layers - Criar nova layer
 * - GET /painel/layers/{id}/edit - Formulário de edição
 * - PUT /painel/layers/{id} - Atualizar layer
 * - DELETE /painel/layers/{id} - Remover layer
 */
class PainelController extends Controller
{
    /**
     * @param LayerService $service
     */
    public function __construct(
        protected LayerService $service
    ) {}

    /**
     * Lista todas as layers com paginação e busca
     * 
     * Query params opcionais:
     * - search: termo de busca por nome
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $layers = $this->service->search($search, perPage: 10);

        return view('painel.layers.index', [
            'layers' => $layers,
            'search' => $search,
        ]);
    }

    /**
     * Exibe o formulário de criação de layer
     * 
     * @return View
     */
    public function create(): View
    {
        return view('painel.layers.create');
    }

    /**
     * Cria uma nova layer a partir do upload de GeoJSON
     * 
     * Campos do formulário:
     * - name: nome da camada (obrigatório, max 100 caracteres)
     * - geojson: arquivo .geojson (obrigatório, max 10MB)
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados do formulário
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'geojson' => ['required', 'file', 'mimes:json,geojson', 'max:10240'], // max 10MB
        ], [
            'name.required' => 'O nome da camada é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            'geojson.required' => 'O arquivo GeoJSON é obrigatório.',
            'geojson.mimes' => 'O arquivo deve ser do tipo .json ou .geojson.',
            'geojson.max' => 'O arquivo não pode ter mais que 10MB.',
        ]);

        try {
            // Cria a layer usando o service
            $layer = $this->service->createFromGeoJson(
                name: $validated['name'],
                geojsonFile: $request->file('geojson')
            );

            // Redireciona com mensagem de sucesso
            return redirect()
                ->route('painel.layers.index')
                ->with('success', "Camada '{$layer->name}' criada com sucesso!");

        } catch (ValidationException $e) {
            // Se houver erro de validação do GeoJSON, volta com os erros
            return back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            // Qualquer outro erro
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar camada: ' . $e->getMessage()]);
        }
    }

    /**
     * Exibe o formulário de edição de layer
     * 
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $layer = $this->service->search()->getCollection()->find($id);
        
        if (!$layer) {
            abort(404, 'Layer não encontrada');
        }

        return view('painel.layers.edit', [
            'layer' => $layer,
        ]);
    }

    /**
     * Atualiza uma layer existente
     * 
     * Campos do formulário:
     * - name: nome da camada (obrigatório)
     * - geojson: arquivo .geojson (opcional - se não enviado, mantém geometria atual)
     * 
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Validação
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'geojson' => ['nullable', 'file', 'mimes:json,geojson', 'max:10240'],
        ], [
            'name.required' => 'O nome da camada é obrigatório.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            'geojson.mimes' => 'O arquivo deve ser do tipo .json ou .geojson.',
            'geojson.max' => 'O arquivo não pode ter mais que 10MB.',
        ]);

        try {
            // Atualiza a layer
            $layer = $this->service->update(
                id: $id,
                name: $validated['name'],
                geojsonFile: $request->file('geojson')
            );

            return redirect()
                ->route('painel.layers.index')
                ->with('success', "Camada '{$layer->name}' atualizada com sucesso!");

        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar camada: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove uma layer
     * 
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->service->delete($id);

            return redirect()
                ->route('painel.layers.index')
                ->with('success', 'Camada removida com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Erro ao remover camada: ' . $e->getMessage()]);
        }
    }
}
