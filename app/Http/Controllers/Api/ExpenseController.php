<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['votehead:id,name', 'member:id,name']);

        
        $filters = [];

       
        if ($request->has('votehead')) {
            $filters[] = ['votehead_id', '=', $request->query('votehead')];
        }

        if ($request->has('search')) {
           $filters[] = ['amount', '=', $request->query('search')];
        }

        if (!empty($filters)) {
            $query->where($filters);
        }

        $expenses = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $expenses], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required','date','before_or_equal:today'],
            'votehead_id' => ['required','string','exists:vote_heads,id'],
            'amount' => ['required','min:0'],
            'payment_method' => ['required','in:cash,cheque,mpesa,bank'],
            'payment_reference' =>['required'],
            'member_id' => ['nullable','exists:members,id']
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense = Expense::create(array_merge($request->all(), ['date' => date('Y-m-d',strtotime($request->date)), 'user_id' => $request->user()->id]));
        if(!$expense) return response()->json(['message' => 'Failed to create expense'], 500);
        return response()->json(['data' => $expense], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            
            $expense = Expense::with(['votehead:id,name', 'member:id,name'])->findOrFail($id);
            return response()->json(['data' => $expense], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'date' => ['required','date','before_or_equal:today'],
            'votehead_id' => ['required','string','exists:vote_heads,id'],
            'amount' => ['required','min:0'],
            'payment_method' => ['required','in:cash,cheque,mpesa,bank'],
            'payment_reference' =>['required'],
            'member_id' => ['nullable','exists:members,id']
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update(array_merge($request->all(), ['date' => date('Y-m-d',strtotime($request->date))]));
        return response()->json(['data' => $expense], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            return response()->json(['message' => 'Expense deleted successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }
    }
}
