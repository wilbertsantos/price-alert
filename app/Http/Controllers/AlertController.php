<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Alert;
use App\Jobs\ProcessAlert;
use App\Services\DataTableService;
use Illuminate\Support\Str;

class AlertController extends Controller
{
    public function index(Request $request)
    {


        $users = DataTableService::of(Alert::where('status', 'active')->orderByDesc('id'))->make($request);

        return response()->json([
            'success' => true,
            'payload' => $users,
        ]);
    }


    public function create(Request $request)
    {
        return view('alerts.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coin' => 'required',
            'price' => 'required|numeric',
            'condition' => 'required|in:lower,higher',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $alert = new Alert();
        $alert->coin = Str::upper($request->input('coin'));
        $alert->price = $request->input('price');
        $alert->condition = $request->input('condition');
        $alert->status = 'active';
        $alert->notes = $request->input('notes');
        $alert->author = $request->input('author');
        //$alert->user_id = Auth::id();
        $alert->save();

        //ProcessAlert::dispatch($alert);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully.');
    }

    public function edit(Request $request, Alert $alert)
    {
        return view('alerts.edit', compact('alert'));
    }

    public function update(Request $request, Alert $alert)
    {
        $validator = Validator::make($request->all(), [
            'coin' => 'required',
            'price' => 'required|numeric',
            'condition' => 'required|in:lower,higher',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $alert->coin = $request->input('coin');
        $alert->price = $request->input('price');
        $alert->condition = $request->input('condition');
        $alert->save();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert updated successfully.');
    }

    public function destroy(Request $request, Alert $alert)
    {


        $id = $request->input('id');
        try {
            $alert = Alert::find($id);
            $alert->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
        ]);

    }

    public function undoDelete(Request $request, $id)
    {
        $alert = Alert::withTrashed()->findOrFail($id);
        $alert->restore();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert restored successfully.');
    }
}
