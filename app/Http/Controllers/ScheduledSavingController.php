<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Models\ScheduledSaving;
use Illuminate\Http\Request;

class ScheduledSavingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'saving_id' => 'required|exists:savings,id',
            'amount' => 'required|numeric|min:100',
            'frequency' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date'
        ]);

        $scheduled = ScheduledSaving::create([
            'saving_id' => $data['saving_id'],
            'user_id' => auth()->id(),
            'amount' => $data['amount'],
            'frequency' => $data['frequency'],
            'start_date' => $data['start_date'],
            'next_run_date' => $data['start_date'],
            'active' => true
        ]);

        return Helpers::success($scheduled);
    }
    public function toggle($id)
    {
        $scheduled = ScheduledSaving::where('id',$id)
            ->where('user_id',auth()->id())
            ->firstOrFail();

        $scheduled->update(['active' => !$scheduled->active]);

        return Helpers::success($scheduled);
    }

}
