<?php

namespace App\Http\Controllers;

use App\Models\SimpinRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimpinRuleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $this->authorize('view simpin rule', $user);
        try
        {
            $rules = SimpinRule::all();
            $data['title'] = 'Simpin Rule List';
            $data['rules'] = $rules;
            return view('simpin-rule.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function create()
    {
        try
        {
            $user = Auth::user();
            $this->authorize('create simpin rule', $user);
            $data['title'] = 'Simpin Rule Create';
            return view('simpin-rule.create', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function store(Request $request)
    {
        try
        {
            $user = Auth::user();
            $this->authorize('create simpin rule', $user);
            DB::transaction(function () use($request)
            {
                $rule = new SimpinRule();
                $rule->code = $request->code;
                $rule->description = $request->description;
                $rule->value = $request->value;
                $rule->amount = $request->amount;
                $rule->save();
            });

            return redirect()->route('simpin-rule-list')->withSuccess('Success');
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function edit($id)
    {
        try
        {
            $user = Auth::user();
            $this->authorize('edit simpin rule', $user);
            
            $rule = SimpinRule::findOrFail($id);
            $data['title'] = 'Simpin Rule Create';
            $data['rule'] = $rule;
            return view('simpin-rule.edit', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $user = Auth::user();
            $this->authorize('create simpin rule', $user);

            $rule = SimpinRule::findOrFail($id);
            DB::transaction(function () use($request, $rule)
            {
                $rule->code = $request->code;
                $rule->description = $request->description;
                $rule->value = $request->value;
                $rule->amount = $request->amount;
                $rule->save();
            });

            return redirect()->route('simpin-rule-list')->withSuccess('Success');
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Terjadi Kesalahan');
        }
    }
}
