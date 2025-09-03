<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitarBeneficioRequest;
use App\Http\Requests\AprovarSolicitacaoRequest;
use App\Services\SolicitacaoBeneficioService;
use App\Http\Resources\SolicitacaoBeneficioResource;
use App\Models\SolicitacaoBeneficio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SolicitacaoBeneficioController extends Controller
{
    public function __construct(
        private SolicitacaoBeneficioService $solicitacaoService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/solicitacoes",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Lista solicitações do usuário logado",
     *     @OA\Response(
     *         response=200,
     *         description="Solicitações retornadas com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SolicitacaoBeneficio")
     *         )
     *     )
     * )
     */
    public function index()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $solicitacoes = SolicitacaoBeneficio::porUsuario($usuario->id)
            ->with(['beneficio', 'aprovador', 'segundoAprovador'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return SolicitacaoBeneficioResource::collection($solicitacoes);
    }

    /**
     * @OA\Post(
     *     path="/api/solicitacoes",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Cria uma nova solicitação de benefício",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SolicitarBeneficioRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Solicitação criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoBeneficio")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação ou regra de negócio"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/solicitacoes/{id}",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Exibe uma solicitação específica",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da solicitação",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação retornada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoBeneficio")
     *     ),
     *     @OA\Response(response=404, description="Solicitação não encontrada")
     * )
     */
    public function show(SolicitacaoBeneficio $solicitacao): SolicitacaoBeneficioResource
    {
        $this->authorize('view', $solicitacao);

        return new SolicitacaoBeneficioResource(
            $solicitacao->load(['beneficio', 'aprovador', 'segundoAprovador'])
        );
    }

    /**
     * @OA\Post(
     *     path="/api/solicitacoes/{id}/aprovar",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Aprova uma solicitação de benefício",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/AprovarSolicitacaoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação aprovada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoBeneficio")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação ou regra de negócio"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/solicitacoes/{id}/rejeitar",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Rejeita uma solicitação de benefício",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(ref="#/components/schemas/AprovarSolicitacaoRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação rejeitada",
     *         @OA\JsonContent(ref="#/components/schemas/SolicitacaoBeneficio")
     *     ),
     *     @OA\Response(response=422, description="Erro de validação ou regra de negócio"),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/solicitacoes/pendentes-aprovacao",
     *     tags={"Solicitação de Benefícios"},
     *     summary="Lista solicitações pendentes de aprovação para o usuário",
     *     @OA\Response(
     *         response=200,
     *         description="Solicitações pendentes retornadas com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/SolicitacaoBeneficio")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Acesso negado")
     * )
     */
    public function pendentesAprovacao()
    {
        /** @var Usuario $usuario */
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
