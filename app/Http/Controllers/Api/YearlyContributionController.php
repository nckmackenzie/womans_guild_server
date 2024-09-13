<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YealyContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class YearlyContributionController extends Controller
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
            'amount' => ['required','numeric','gt:0'], 
            'year_id' => ['required','exists:years,id','unique:yealy_contributions,year_id']
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first()],422);
        }

        try {            
            YealyContribution::create($request->all());
            // DB::table('yealy_contributions')->insert($request->all());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong'],500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $year = YealyContribution::where('year_id',$id)->first();
        if($year){
            return response()->json(['success' => false],200);
        }
        return response()->json(['success' => true],200);
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
