<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\PaiementRequest;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\DetteRessource;
use App\Models\Article;
use App\Models\Dette;
use App\Models\Paiement;
use App\Services\DetteService;
use App\Services\Contracts\IDetteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class DetteController extends Controller
{
    protected $detteService;

    public function __construct(IDetteService  $detteService)
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin,boutiquier');
        $this->detteService = $detteService;
    }

    public function all() {
        $dettes =$this->detteService->all();

        $data = new DetteRessource($dettes);
        return $data;
    }




   public function index(Request $request)
    {
        $solde = $request->query('solde');

        // Utiliser le service pour obtenir les dettes avec le filtrage nécessaire
        $dettes = $this->detteService->all($solde);
        $data =  DetteRessource::collection($dettes);


        return [
            'data' => $data,
            'status' => ResponseStatus::SUCCESS,
            'message' => 'Liste des dettes',
            'code' => Response::HTTP_OK
        ];
    }


    public function store(StoreDetteRequest $request)
{
    try{

        $data = $this->detteService->store($request->validated());
        return [
            'data' => $data,
            'status' => ResponseStatus::SUCCESS,
            'message' => 'Dette enregistrée',
            'code' => Response::HTTP_OK
        ];
}
catch (\Exception $e) {
    return [
        'data' => null,
        'status' => ResponseStatus::ECHEC,
        'message' => $e->getMessage(),
        'code' => Response::HTTP_INTERNAL_SERVER_ERROR
    ];
}

}
public function show($id)
{
    try{
    $dette = $this->detteService->show($id);
    if (!$dette) {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Dette introuvable',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }

    return [
        'data' => $dette,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Dette selectionné',
        'code' => Response::HTTP_OK
    ];
}
catch (\Exception $e) {
    return [
        'data' => null,
        'status' => ResponseStatus::ECHEC,
        'message' => $e->getMessage(),
        'code' => Response::HTTP_INTERNAL_SERVER_ERROR

    ];
}

}

public function destroy($id)
{
    try{
    $dette = $this->detteService->show($id);
    if (!$dette) {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Dette introuvable',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
    $dette->delete();
    return [
        'data' => null,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Dette supprimee',
        'code' => Response::HTTP_OK
    ];
}
catch (\Exception $e) {
    return [
        'data' => null,
        'status' => ResponseStatus::ECHEC,
        'message' => $e->getMessage(),
        'code' => Response::HTTP_INTERNAL_SERVER_ERROR
    ];
}

}

public function listArticleDette(Request $request, $id)
{
    try {
        $data = $this->detteService->show($id);

        if (!$data) {
            return [
                'data' => null,
                'status' => ResponseStatus::ECHEC,
                'message' => 'Dette introuvable',
                'code' => Response::HTTP_NOT_FOUND
            ];
        }

        if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
            return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
        }

        if ($request->user()->role->libelle == 'CLIENT') {
            $clientId = $request->user()->client->id ?? null;
            if (!$clientId || $data->client_id !== $clientId) {
                return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
            }
        }

        $dette = new DetteRessource($data);

        return [
            'data' => $dette,
            'status' => ResponseStatus::SUCCESS,
            'message' => 'Liste des articles',
            'code' => Response::HTTP_OK
        ];

    } catch (\Exception $e) {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => $e->getMessage(),
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ];
    }
}



public function listPaiementDette(Request $request,$id){
    try{
    $dette = $this->detteService->listPaiement($id);
    if (!$dette) {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Dette introuvable',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
    if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Vous n\'avez pas les autorisations requises',
            'code' => Response::HTTP_FORBIDDEN
        ];
        }


    if ($request->user()->role->libelle =='CLIENT') {
        $clientId = $request->user()->client->id ?? null; // Récupère l'ID du client lié à l'utilisateur
        if (!$clientId || $dette->client_id !== $clientId) {
            return [
                'data' => null,
                'status' => ResponseStatus::ECHEC,
                'message' => 'Vous n\'avez pas les autorisations requises',
                'code' => Response::HTTP_FORBIDDEN
            ];
        }
    }
    return [
        'data' => $dette,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Liste des paiements',
        'code' => Response::HTTP_OK
    ];
}
catch (\Exception $e) {
    return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
}

}

public function paiementDette(PaiementRequest $request, $id){
    try{
    $dette = Dette::find($id);

    if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
        return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
    }

    //verifier si le montant restant de la dette = 0
    if ($dette->montantRestant == 0) {
        return $this->sendResponse($dette, 'La dette est deja payée', Response::HTTP_CONFLICT,ResponseStatus::ECHEC);
    }
    if($dette->montantDu >= $request->montant){
    DB::transaction(function () use ($dette, $request) {

        $dette->montantRestant = $dette->montantRestant - $request->montant;
        $dette->montantDu = $dette->montantDu + $request->montant;
        $paiement = new Paiement();
        $paiement->dette_id = $dette->id;
        $paiement->montant = $request->montant;
        $paiement->save();

        $dette->save();
    });
}
if($request->montant > $dette->montantRestant){

    return $this->sendResponse($dette, 'Le montant est superieur au montant restant', Response::HTTP_CONFLICT,ResponseStatus::ECHEC);
}


    return $this->sendResponse($dette->load('paiements'), 'Paiement effectue avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }
    catch (\Exception $e) {
        return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
}


}
