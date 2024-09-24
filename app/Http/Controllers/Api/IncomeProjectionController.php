<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomeProjectionDetail;
use App\Models\IncomeProjectionHeader;
use Illuminate\Database\QueryException;
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
        $query = DB::table('income_projection_headers')
                    ->join('years', 'income_projection_headers.year_id', '=', 'years.id')
                    ->join('income_projection_details', 'income_projection_headers.id', '=', 'income_projection_details.header_id')
                    ->select('years.name as year_name', 
                              DB::raw('COALESCE(SUM(income_projection_details.amount), 0) AS total_amount'), 
                              'income_projection_headers.id')
                    ->groupBy('income_projection_headers.id','years.name');
        $projections = $query->orderBy('years.created_at', 'desc');
        return response()->json(['data' => $projections->get()], 200);
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
        try {
            $projection = IncomeProjectionHeader::findOrFail($id);

            $details = DB::table('income_projection_details')
                        ->join('vote_heads', 'income_projection_details.votehead_id', '=', 'vote_heads.id')
                        ->select('vote_heads.name as votehead', 
                                          'income_projection_details.id',
                                          'income_projection_details.votehead_id',
                                          'income_projection_details.amount')
                        ->where('income_projection_details.header_id', $id)
                        ->get();
            
            $result = [
                'year_id' => $projection->year_id,
                'details' => $details
            ];

            return response()->json(['data' => $result], 200);

        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Income projection selected not found.'], 500);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message' => 'There was a problem fetching the income projection.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'year_id' => ['required','exists:years,id'],
            'details' => ['required','array'],
            'details.*.votehead_id' => ['required','exists:vote_heads,id'],
            'details.*.amount' => ['required','numeric','gt:0'],
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()],422);
        }

        DB::beginTransaction();

        try {
            $projection = IncomeProjectionHeader::findOrFail($id);
            $projection->year_id = $request->year_id;   
            $projection->save();

            DB::table('income_projection_details')->where('header_id', $id)->delete();

            foreach ($request->details as $detail) {
                IncomeProjectionDetail::create([
                    'header_id' => $id,
                    'votehead_id' => $detail['votehead_id'],
                    'amount' => $detail['amount']
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Income projection updated'], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while updating income projection'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $projection = IncomeProjectionHeader::findOrFail($id);
            $projection->delete();

            DB::table('income_projection_details')->where('header_id', $id)->delete();

            DB::commit();

            return response()->json(['message' => 'Income projection deleted'], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while deleting income projection'], 500);
        }
    }
}
