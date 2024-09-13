<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Visus\Cuid2\Cuid2;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('budget_headers')
                        ->leftJoin('budget_details', 'budget_headers.id', '=', 'budget_details.header_id') 
                        ->join('years', 'budget_headers.year_id', '=', 'years.id')
                        ->select(
                            'budget_headers.id','budget_headers.name','years.name as year_name', 
                            DB::raw('COALESCE(SUM(budget_details.amount), 0) AS total_amount') 
                        );
        if($request->has('search')){
            $query->where('budget_headers.name', 'like', '%' . $request->search . '%');
        }
        
        $budgets = $query->groupBy('budget_headers.id','budget_headers.name','years.name') 
                         ->orderBy('budget_headers.created_at', 'desc') // Order by creation date
                         ->get();

        return response()->json(['data' => $budgets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'name'  => ['required','string','unique:budget_headers'],
           'year_id'  => ['required','unique:budget_headers,year_id'],
            'details' => 'required|array',  // Ensure details are provided as an array
            'details.*.votehead_id' => ['required','string','exists:vote_heads,id'],
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cuid = new Cuid2();
        $generatedCuid = $cuid->toString();

        DB::beginTransaction();

        try {

             DB::table('budget_headers')->insert([
                'id' => $generatedCuid,
                'name' => $request->name,
                'year_id' => $request->year_id,
            ]);

            foreach ($request->details as $detail) {
                DB::table('budget_details')->insert([
                    'header_id' => $generatedCuid,
                    'votehead_id' => $detail['votehead_id'],
                    'amount' => $detail['amount'],
                    'description' => $detail['description'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Budget created successfully.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['message' => 'Could not create budget.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
