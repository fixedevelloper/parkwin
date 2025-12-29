<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helpers;
use App\Http\Resources\TontineResource;
use App\Http\Resources\TourResource;
use App\Models\Tontine;
use App\Models\TontineMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class TontineController extends Controller
{
    // Liste toutes les tontines où l'utilisateur est membre
    public function index()
    {
        try {
            $tontines = auth()->user()->tontines;// via relation belongsToMany
            return Helpers::success(TontineResource::collection($tontines));
        }catch (\Exception $exception){
            logger($exception);
            return Helpers::error($exception->getMessage());
        }

    }

    // Créer une nouvelle tontine
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:1',
                'frequency' => 'required|in:daily,weekly,monthly'
            ]);

            $tontine = Tontine::create([
                'owner_id' => Auth::id(),
                'reference' => Str::random(12),
                'name' => $data['name'],
                'amount' => $data['amount'],
                'frequency' => $data['frequency'],
                'participants_count' => 1
            ]);

            // Ajouter automatiquement le créateur comme membre
            TontineMember::create([
                'user_id' => Auth::id(),
                'tontine_id' => $tontine->id,
                'position' => 1
            ]);

            return Helpers::success($tontine);
        }catch (\Exception $exception){
            logger($exception);
            return Helpers::error($exception->getMessage());
        }

    }
    public function show($id)
    {
        $tontine = Tontine::with(['members.user', 'owner'])->findOrFail($id);

        $isMember = $tontine->members()
            ->where('user_id', Auth::id())
            ->exists();

        return Helpers::success([
            'tontine' => new TontineResource($tontine),
            'is_member' => $isMember
        ]);
    }
    public function findByReference($reference)
    {
        try {
            $tontine = Tontine::query()->where('reference',$reference)->first();
            return Helpers::success($tontine);
        }catch (\Exception $exception){
            return Helpers::error('Reference non trouve');
        }

    }

    // Rejoindre une tontine existante
    public function join($id)
    {
        $tontine = Tontine::with(['members.user', 'owner'])->findOrFail($id);

        // Vérifier si l'utilisateur est déjà membre
        if ($tontine->members()->where('user_id', Auth::id())->exists()) {
            return Helpers::error('Vous êtes déjà membre de cette tontine.');
        }

        // Ajouter le membre
        $member = TontineMember::create([
            'user_id' => Auth::id(),
            'tontine_id' => $tontine->id,
            'position' => $tontine->members()->count() + 1
        ]);

        // Mettre à jour le nombre de participants
        $tontine->increment('participants_count');

        return Helpers::success(new TontineResource($tontine));
    }
    // Créer un nouveau tour pour une tontine

    public function createTour($tontineId)
    {
        $tontine = Tontine::findOrFail($tontineId);

        // Vérifier s'il reste des participants
        $members = $tontine->members()->orderBy('position')->get();
        if ($members->isEmpty()) {
            return Helpers::error("Aucun membre pour créer un tour.");
        }

        // Déterminer le cycle_number automatiquement
        $lastCycle = $tontine->tours()->max('cycle_number') ?? 0;
        $nextCycle = $lastCycle + 1;

        // Déterminer le bénéficiaire du cycle (round-robin)
        $beneficiary = $members[($nextCycle - 1) % $members->count()]->user;

        $tour = $tontine->tours()->create([
            'cycle_number' => $nextCycle,
            'beneficiary_id' => $beneficiary->id,
            'amount' => $tontine->amount,
            'status' => 'PENDING'
        ]);

        return Helpers::success($tour);
    }

// Lister tous les tours d'une tontine
    public function listTours($id)
    {
        $userId = Auth::id();

        $tontine = Tontine::with([
            'tours' => function ($query) use ($userId) {
                $query->with([
                    'payments' => function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                            ->where('status', 'SUCCESS')
                            ->where('type', 'TONTINE');
                    }
                ]);
            }
        ])->findOrFail($id);

        return Helpers::success(
            TourResource::collection($tontine->tours)
        );
    }



}
