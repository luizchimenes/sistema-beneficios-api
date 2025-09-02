<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitarBeneficioRequest;
use App\Http\Requests\AprovarSolicitacaoRequest;
use App\Services\SolicitacaoBeneficioService;
use App\Http\Resources\SolicitacaoBeneficioResource;
use App\Models\SolicitacaoBeneficio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitacaoBeneficioController extends Controller
{
    public function __construct(
        private SolicitacaoBeneficioService $solicitacaoService
    ) {}

    public function index(Request $request)
    {
        $usuario = Auth::user();
        
        $solicitacoes = SolicitacaoBeneficio::porUsuario($usuario->id)
            ->with(['beneficio', 'aprovador', 'segundoAprovador'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return SolicitacaoBeneficioResource::collection($solicitacoes);
    }

    public function store(SolicitarBeneficioRequest $request): JsonResponse
    {
        try {
            $solicitacao = $this->solicitacaoService->criarSolicitacao(
                Auth::user(),
                $request->validated()
            );

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Solicitação criada com sucesso',
                'dados' => new SolicitacaoBeneficioResource($solicitacao)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ], 422);
        }
    }

    public function show(SolicitacaoBeneficio $solicitacao): SolicitacaoBeneficioResource
    {
        $this->authorize('view', $solicitacao);
        
        return new SolicitacaoBeneficioResource(
            $solicitacao->load(['beneficio', 'aprovador', 'segundoAprovador'])
        );
    }

    public function aprovar(SolicitacaoBeneficio $solicitacao, AprovarSolicitacaoRequest $request): JsonResponse
    {
        $this->authorize('approve', $solicitacao);

        try {
            $solicitacaoAtualizada = $this->solicitacaoService->aprovarSolicitacao(
                $solicitacao,
                Auth::user(),
                $request->observacoes
            );

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Solicitação aprovada com sucesso',
                'dados' => new SolicitacaoBeneficioResource($solicitacaoAtualizada)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ], 422);
        }
    }

    public function rejeitar(SolicitacaoBeneficio $solicitacao, AprovarSolicitacaoRequest $request): JsonResponse
    {
        $this->authorize('reject', $solicitacao);

        try {
            $solicitacaoAtualizada = $this->solicitacaoService->rejeitarSolicitacao(
                $solicitacao,
                Auth::user(),
                $request->motivo_rejeicao
            );

            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Solicitação rejeitada',
                'dados' => new SolicitacaoBeneficioResource($solicitacaoAtualizada)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ], 422);
        }
    }

    public function pendentesAprovacao(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario->podeAprovar()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Acesso negado'
            ], 403);
        }

        $solicitacoes = SolicitacaoBeneficio::pendentes()
            ->with(['usuario', 'beneficio'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return SolicitacaoBeneficioResource::collection($solicitacoes);
    }
}