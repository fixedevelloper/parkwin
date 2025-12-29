<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SavingResource;
use App\Http\Resources\TontineResource;
use App\Models\Payment;
use App\Models\Saving;
use App\Models\Tontine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SavingController extends Controller
{
    /** Liste des épargnes */
    public function index()
    {
        $savings = Saving::where('user_id', auth()->id())->latest()->get();
        return Helpers::success(SavingResource::collection($savings));
    }

    /** Créer une épargne */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'amount' => 'nullable|numeric|min:0'
        ]);

        $saving = Saving::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'amount_goal' => $data['amount'] ?? 0
        ]);

        return Helpers::success($saving, 'Épargne créée');
    }
    public function show($id)
    {
        $saving = Saving::findOrFail($id);

        return Helpers::success(new SavingResource($saving));
    }

    /** Dépôt sur épargne
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        try {
            $request->validate([
                'savingId' => 'required|exists:savings,id',
                'amount' => 'required|numeric|min:100',
                //'method' => 'required|string'
            ]);

            $saving = Saving::where('user_id', auth()->id())->findOrFail($request->savingId);

            DB::transaction(function () use ($saving, $request) {
                $saving->increment('balance', $request->amount);

                Payment::create([
                    'user_id' => auth()->id(),
                    'amount' => $request->amount,
                    'saving_id' => $saving->id,
                    'method_pay' => 'WALLET',
                    'type' => 'EPARGNE',
                    'status' => 'SUCCESS'
                ]);
            });

            return Helpers::success(new SavingResource($saving), 'Dépôt effectué');
        }catch (\Exception $exception){
            logger($exception->getMessage());
            return Helpers::error('Depot echoue');
        }

    }
    public function payments($id)
    {
        $saving = Saving::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $payments = Payment::where('saving_id', $saving->id)
            ->where('type', 'EPARGNE')
            ->orderByDesc('created_at')
            ->get();

        return Helpers::success(PaymentResource::collection($payments));
    }
}


