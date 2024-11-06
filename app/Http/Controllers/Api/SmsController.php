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
    
    public function resetPasswordLink(String $token, String $contact)
    {
        $message = 'Your Password reset link: '.env('FRONTEND_URL').'/reset-password?token='.$token;
        $contact = "+254" . substr($contact, 1);
        $result = $this->smsService->sendSMS([$contact], $message);
        return response()->json(['status' => $result['status']]);
    }

    public function sendBalanceSms(Request $request)
    {
        $recipients = $request->recipients;
        $failureCount = 0;
        $successCount = 0;

        if (!is_array($recipients)) {
            return response()->json(['message' => 'Recipients must be an array'], 422);
        }

        if(!count($recipients)) {
            return response()->json(['message' => 'At least one recipient is required'], 422);
        }

        foreach ($recipients as $recipient) {
            $contact = $this->smsService->getContactById($recipient['id']);
            $message = "Dear, ".ucfirst($recipient['name']).", your current balance is Ksh.".number_format($recipient['balance']) . 
                       " Make a deposit towards clearing this balance.";
            $result = $this->smsService->sendSMS([$contact], $message);
            $response = $result['status'];
            if($response === 'fail') {
                $failureCount++;
            }elseif ($response === 'success') {
                $successCount++;
            }
        }

        if($successCount === 0) return response()->json(['status' => 'fail', 'message' => 'Failed to send sms']);
        if($successCount > 0 && $failureCount > 0) return response()->json(['status' => 'success', 'message' => "$successCount messages sent, $failureCount messages failed."]);
        if($failureCount === 0) return response()->json(['status' => 'success', 'message' => 'Successfully sent sms']);

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
