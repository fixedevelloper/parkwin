<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helpers;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return Helpers::success(PaymentResource::collection($payments));
    }
    public function historyByTontine(Request $request, $id)
    {
        $payments = Payment::query()
            ->join('tours', 'payments.tour_id', '=', 'tours.id')
            ->where('payments.type', 'TONTINE')
            ->where('tours.tontine_id', $id)
            ->select('payments.*')
            ->latest('payments.created_at')
            ->paginate(20);

        return Helpers::success(
            PaymentResource::collection($payments)
        );
    }



    public function pay(Request $request) {
        try {
            $data = $request->validate([
                'sessionId'=>'required|exists:tours,id',
                'amount'=>'required|numeric'
            ]);

            $tour = Tour::findOrFail($data['sessionId']);

            Payment::create([
                'tour_id'=>$tour->id,
                'user_id'=>auth()->id(),
                'amount'=>$data['amount'],
                'method_pay'=>'MOMO',
                'status'=>'SUCCESS'
            ]);

            $tour->update(['status'=>'PAID']);
            return Helpers::success(['message'=>'Paiement réussi']);
        }catch (\Exception $exception){
            return Helpers::error(['message'=>'Paiement echoue']);
        }



    }
    // Enregistrer un paiement d'un membre pour un tour
    public function payTour(Request $request, $tourId)
    {
        $tour = Tour::findOrFail($tourId);

        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string', // OM, MOMO, CARD
            'reference' => 'nullable|string'
        ]);

        // Vérifier si l'utilisateur est membre de la tontine
        if (!$tour->tontine->members()->where('user_id', Auth::id())->exists()) {
            return Helpers::error("Vous n'êtes pas membre de cette tontine.");
        }

        $payment = $tour->payments()->create([
            'user_id' => Auth::id(),
            'amount' => $data['amount'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'status' => 'PENDING' // on peut mettre SUCCESS si payé directement
        ]);

        // Vérifier si tous les membres ont payé
        $totalPaid = $tour->payments()->sum('amount');
        if ($totalPaid >= $tour->amount * $tour->tontine->participants_count) {
            $tour->update(['status' => 'PAID']);
            $tour->payments()->update(['status' => 'SUCCESS']);
        }

        return Helpers::success($payment);
    }

// Lister les paiements d'un tour
    public function listPayments($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        return Helpers::success($tour->payments()->with('user')->get());
    }

}
