<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClosingBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'year_id' => ['required','exists:years,id'],
            'details' => ['required','array']
        ]);

        if($validated->fails()){
            return response()->json(['message' => $validated->errors()->first()],422);
        }

        DB::beginTransaction();

        try{
            
            foreach($request->details as $detail){
                DB::table('closing_balances')->insert([
                    'year_id' => $request->year_id,
                    'member_id' => $detail['member_id'],
                    'amount' => $detail['balance'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Closing balance created'], 200);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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

    public function checkYear(string $id)
    {
        $yearCreated = DB::table('closing_balances')->where('year_id', $id)->count();
        
        return response()->json(['data' => $yearCreated == 0 ? false : true],200);
    }

    public function getClosingBalance(string $id)
    {
        $results = DB::select("CALL sp_closing_balances(?)",[$id]);
        return response()->json(['data' => $results],200);
    }
}
