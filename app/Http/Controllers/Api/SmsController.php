<?php

namespace App\Http\Controllers\Api;

use App\Services\SmsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    protected $smsService;
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendSms(Request $request)
    {
        $recipients = $request->recipients;
        $message = $request->message;
        
        if (!is_array($recipients)) {
           return response()->json(['message' => 'Recipients must be an array'], 422);
        }
        
        if(!$message) {
            return response()->json(['message' => 'Message is required'], 422);
        }

        if(!count($recipients)) {
            return response()->json(['message' => 'At least one recipient is required'], 422);
        }

        $contacts = [];

        foreach ($recipients as $recipient) {
            $contact = $this->smsService->getContactById($recipient);
            if($contact) {
                $contacts[] = $contact;
            }
        }

        $result = $this->smsService->sendSMS($contacts, $message);

        return response()->json(['status' => $result['status']]);
      
    }
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
        //
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
