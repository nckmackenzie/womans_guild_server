<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class YearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Year::query();
        if($request->has('search')){
            $query->where('name','like',"%{$request->search}%");
        }

        $years = $query->orderBy('name','asc')->get();
        return response()->json(['data' => $years],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(),[
            'name' => ['required','unique:years'],
            'start_date' => ['required','date','before_or_equal:end_date'],
            'end_date' => ['required']
        ]);

        if($validator->fails()){
            return response()->json(['status' => 422,
                                     'message' => 'validation error.Check your input and try again.',
                                    'errors' => $validator->errors()],422);
        }

        $year = Year::create([
            'name'=> $request->name,
            'start_date' => date('Y-m-d',strtotime($request->start_date)),
            'end_date' => date('Y-m-d',strtotime($request->end_date)),
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Year created successfully.','data' => $year],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $year = Year::findOrFail($id);
        return response()->json(['data' => $year],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $year = Year::findOrFail($id);
        $year->update([
            'name'=> $request->name,
            'start_date' => date('Y-m-d',strtotime($request->start_date)),
            'end_date' => date('Y-m-d',strtotime($request->end_date)),
            'updated_by' => $request->user()->id,
        ]);
        return response()->json(['message' => 'Year updated successfully.','data' => $year],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function activeYears()
    {
        $years = Year::where('is_closed',0)->select('id','name')->get();
        return response()->json(['data' => $years]);
    }
}
