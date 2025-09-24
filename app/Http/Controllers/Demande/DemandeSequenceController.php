<?php

namespace App\Http\Controllers\Demande;

use App\Models\Demande\Demande;
use App\Models\Demande\DemandeSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemandeSequenceController
{
    /**
     * @OA\Get(path="/api/demande_sequences",
     *   tags={"Demandes"},
     *   summary="Numéro de séquence suivant des demandes",
     *   description="Numéro de séquence suivant des demandes",
     *   operationId="demandeSequence",
     *   @OA\Response(
     *      response=200,
     *      description="Liste obtenue",
     *      content={
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="object",
     *                      property="data",
     *                      @OA\Property(
     *                          property="next_sequence",
     *                          type="string",
     *                          description="Numéro de séquence suivant pour la création d'une demande",
     *                          example="010000"
     *                      )
     *                  )
     *              )
     *          )
     *      }
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Utilisateur non connecté",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="L'utilisateur n'est même pas connecté",
     *                         example="Vous ne vous êtes pas encore authentifié."
     *                     )
     *                 )
     *             )
     *         }
     *   )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $lastNumero = $this->getNextNumero();

        return response()->json([
            'data' => [
                'next_sequence' => $lastNumero,
            ]
        ]);
    }

    private function getNextNumero()
    {
        $lastSequence = DemandeSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Demande::where('numero', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            DemandeSequence::create([
                'numero' => $newLastNumero,
            ]);
            return $this->getNextNumero();
        } else {
            return $lastNumero;
        }
    }

    public function store(Request $request)
    {
    }

    public function show(DemandeSequence $demandeSequence)
    {
    }

    public function update(Request $request, DemandeSequence $demandeSequence)
    {
    }

    public function destroy(DemandeSequence $demandeSequence)
    {
    }
}
