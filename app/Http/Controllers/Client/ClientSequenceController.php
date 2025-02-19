<?php

namespace App\Http\Controllers\Client;

use App\Models\Client\Client;
use App\Models\Client\ClientSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientSequenceController
{
    /**
     * @OA\Get(path="/client_sequences",
     *   tags={"Clients"},
     *   summary="Numéro de séquence suivant des clients",
     *   description="Numéro de séquence suivant des clients",
     *   operationId="clientSequence",
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
     *                          description="Numéro de séquence suivant pour la création d'un client",
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
     * @return \Illuminate\Http\Response
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
        $lastSequence = ClientSequence::latest()->first();
        if($lastSequence) {
            $lastNumero = $lastSequence->numero;
        } else {
            $lastNumero = '010000';
        }
        $res = Client::where('numero_saisi', $lastNumero)->first();
        if($res) {
            $newLastNumero = Str::padLeft($lastNumero + 1, 6, $pad = '0');
            ClientSequence::create([
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

    public function show(ClientSequence $clientSequence)
    {
    }

    public function update(Request $request, ClientSequence $clientSequence)
    {
    }

    public function destroy(ClientSequence $clientSequence)
    {
    }
}
