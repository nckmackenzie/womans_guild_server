<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('incomes')
                ->select(['incomes.id', 
                          'date', 
                          'votehead_id', 
                          'amount', 
                          'member_id',
                          'vote_heads.name as votehead_name',
                          'members.name as member_name'
                         ])
                ->leftJoin('members', 'members.id', '=', 'incomes.member_id')
                ->join('vote_heads', 'vote_heads.id', '=', 'incomes.votehead_id');
        if($request->has('search')){
            $query->where('vote_heads.name', 'like', "%{$request->search}%")
                  ->orWhere('members.name', 'like', "%{$request->search}%")
                  ->orWhere('incomes.amount', 'like', "%{$request->search}%");
        }
        
        $incomes = $query->orderBy('date', 'desc')->get();
        
        return response()->json(['data' => $incomes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required','before_or_equal:today'],
            'votehead_id' => ['required','exists:vote_heads,id'],
            'amount' => ['required','numeric','gt:0'],
            'member_id' => ['nullable','exists:members,id'],
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Income::create(array_merge($request->all(), 
                                ['date' => date('Y-m-d',strtotime($request->date)), 
                                'user_id' => $request->user()->id]));
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while creating income'], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while creating income'], 500);
        }

        return response()->json(['message' => 'Income created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $income = Income::findOrFail($id);

            return response()->json(['data' => $income], 200);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Income selected not found.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required','before_or_equal:today'],
            'votehead_id' => ['required','exists:vote_heads,id'],
            'amount' => ['required','numeric','gt:0'],
            'member_id' => ['nullable','exists:members,id'],
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $income = Income::findOrFail($id);
            $income->update(array_merge($request->all(), 
                                ['date' => date('Y-m-d',strtotime($request->date)), 
                                'user_id' => $request->user()->id]));
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while updating income'], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while updating income'], 500);
        }

        return response()->json(['message' => 'Income updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $income = Income::findOrFail($id);
            $income->delete();
            
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting income'], 500);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting income'], 500);
        }

        return response()->json(['message' => 'Income deleted'], 204);
    }
}
