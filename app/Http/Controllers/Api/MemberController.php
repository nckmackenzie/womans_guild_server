<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->membership_type){
            $members = Member::where('membership_type',$request->membership_type)
                               ->where('is_deleted',0)
                               ->get(['id','name']);
            return response()->json(['data' => $members],200);
        }

        $query = Member::query()->where('is_deleted',0);
        if($request->has('search')){
            $query->where(function ($qry) use ($request) {
                        $qry->where('name', 'like', "%{$request->search}%")
                            ->orWhere('contact', 'like', "%{$request->search}%");
                    });

        }
        $members = $query->orderBy('member_no', 'asc')->get();
        return response()->json(['data' => $members],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string'],
            'contact' => ['required','string','min:10','max:10','unique:members'],
            'joining_date' => ['required','date','before_or_equal:today'],
            'birth_date' => ['nullable','date','before_or_equal:today'],
            'status' => ['required','in:active,inactive,departed,deceased'],
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()],422);
        }

        $member = Member::create([
            'member_no' => $this->memberNo(),
            'name' => $request->name,
            'contact' => $request->contact,
            'id_number' => $request->id_number,
            'birth_date' => $request->birth_date ? date('Y-m-d', strtotime($request->birth_date)) : null,
            'joining_date' => date('Y-m-d', strtotime($request->joining_date)),
            'membership_type' => $request->membership_type,
            'status' => $request->status
        ]);
        if(!$member) return response()->json(['message'=> 'Something went wrong while creating user.'],500);

        return response()->json(['message' => 'Member created successfully.'],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::whereCuid($id)->first();

        if(!$member) return response()->json(['message'=> 'Member not found.'],404);
        return response()->json(['data' => $member],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string'],
            'contact' => ['required','string','min:10','max:10','unique:members,contact,'.$id],
            'joining_date' => ['required','date','before_or_equal:today'],
            'birth_date' => ['nullable','date','before_or_equal:today'],
            'status' => ['required','in:active,inactive,departed,deceased'],
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()],422);
        }

        try {
            
            $member = Member::findOrFail($id);

            $member->update([
                'name' => $request->name,
                'contact' => $request->contact,
                'id_number' => $request->id_number,
                'birth_date' => $request->birth_date ? date('Y-m-d', strtotime($request->birth_date)) : null,
                'joining_date' => date('Y-m-d', strtotime($request->joining_date)),
                'membership_type' => $request->membership_type,
                'status' => $request->status
            ]);

            return response()->json(['message' => 'Member updated successfully.'], 200);

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

            $member = Member::findOrFail($id);
            $member->update(['is_deleted' => 1]);
            return response()->json(['message' => 'Member deleted successfully.'], 204);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Votehead not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }
    }
    /**
     * Retrieves the next available member number.
     *
     * @return \Illuminate\Http\JsonResponse The next available member number.
     */
    public function getNextMemberNo()
    {
        $nextMemberNo = $this->memberNo();

        // // If there are no members yet, start from 1 or any default value
        // $nextMemberNo = $lastMemberNo ? $lastMemberNo + 1 : 1;

        return response()->json(['data' => $nextMemberNo]);
    }

    function memberNo()
    {
        $lastMemberNo = Member::max('member_no');

        // If there are no members yet, start from 1 or any default value
        return $lastMemberNo ? $lastMemberNo + 1 : 1;
    }

    public function activeMembers()
    {
        $members = Member::where('status', 'active')->where('is_deleted',0)
                            ->select('id','name')->orderBy('name','asc')->get();
        return response()->json(['data' => $members],200);
    }

    public function memberPromotion(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'promotion_date' => ['required','date','before_or_equal:today'],
            'member_ids' => ['required','array']
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()],422);
        }

        DB::beginTransaction();

        try {
            
            foreach($request->member_ids as $member_id){
                Member::whereCuid($member_id)->update(['membership_type' => 'full','promotion_date' => date('Y-m-d',strtotime($request->promotion_date))]);
            }

            DB::commit();

        } catch (\Exception $e) {
           DB::rollBack();
           Log::error($e->getMessage());
           return response()->json(['status' => 500, 'message' => 'Internal server error.'], 500);
        }

        
    }
}
