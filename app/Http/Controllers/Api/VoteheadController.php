<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VoteHead;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class VoteheadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VoteHead::query();
        if($request->has('search')){
            $query->where('name','like',"%{$request->search}%");
        }

        $voteheads = $query->orderBy('name', 'asc')->get();
        return response()->json(['status' => 200, 'data' => $voteheads],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','unique:vote_heads'],
            'voteheadType' => ['required',Rule::in(['INCOME', 'EXPENSE']),]
        ]);

        if($validator->fails()){
            return response()->json(['message' => 'Validation error. Invalid figures entered.', 
                                     'errors' => $validator->messages(),
                                     'status' => 422], 422);
        }

        VoteHead::create($request->all());

        return response()->json(['message' => 'Votehead created successfully.', 'status' => 201], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $votehead = VoteHead::find($id);
        if(!$votehead){
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        }
        return response()->json(['status' => 200, 'data' => $votehead],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','unique:vote_heads,name,'.$id],
            'voteheadType' => ['required',Rule::in(['INCOME', 'EXPENSE']),]
        ]);

        if($validator->fails()){
            return response()->json(['status' => 422, 
                                     'message' => 'Validation error. Invalid figures entered.', 
                                     'errors' => $validator->messages()],422);
        }

        try {
            $votehead = VoteHead::findOrFail($id);
            $votehead->update($request->all());
            return response()->json(['status' => 200, 'message' => 'Votehead updated successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $votehead = VoteHead::findOrFail($id);
            $votehead->delete();
            return response()->json(['status' => 204, 'message' => 'Votehead deleted successfully.'], 204);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }
    }

    public function voteheadByType(Request $request)
    {
        $query = VoteHead::query()->where('isActive',1);
        if($request->has('type')){
            $query->where('voteheadType', $request->type);
        }
        $voteheads = $query->select('id','name')->get();
        return response()->json(['status' => 200, 'data' => $voteheads], 200);
    }
}
