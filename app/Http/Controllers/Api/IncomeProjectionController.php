<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomeProjectionDetail;
use App\Models\IncomeProjectionHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Visus\Cuid2\Cuid2;

class IncomeProjectionController extends Controller
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
        $validator = Validator::make($request->all(),[
            'year_id' => ['required','exists:years,id','unique:income_projection_headers,year_id'],
            'details' => ['required','array'],
            'details.*.votehead_id' => ['required','exists:vote_heads,id'],
            'details.*.amount' => ['required','numeric','gt:0'],
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()],422);
        }

        DB::beginTransaction();

        try {

            $id = new Cuid2();

            IncomeProjectionHeader::create([
                'id' => $id->toString(),
                'year_id' => $request->year_id
            ]);

            foreach ($request->details as $detail) {
                IncomeProjectionDetail::create([
                    'header_id' => $id->toString(),
                    'votehead_id' => $detail['votehead_id'],
                    'amount' => $detail['amount']
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Income projection created'], 201);
            
        } catch (\Exception $e) {
           Log::error($e->getMessage());
           DB::rollBack();
           return response()->json(['message' => 'An error occurred while creating income projection'], 500);
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
