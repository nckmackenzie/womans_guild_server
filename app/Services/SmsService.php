<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $sms;

    public function __construct()
    {
        // Set your app credentials
        $username = env('AFRICAS_TALKING_USERNAME');  
        $apiKey = env('AFRICAS_TALKING_API_KEY');

        // Initialize the SDK
        $AT = new AfricasTalking($username, $apiKey);

        // Get the SMS service
        $this->sms = $AT->sms();
    }

    /**
     * Send an SMS to the specified recipients.
     *
     * @param array $recipients An array of phone numbers in international format.
     * @param string $message The message to be sent.
     * @param string|null $from Optional shortCode or senderId.
     * @return array The response from Africa's Talking API.
     */
    public function sendSMS(array $recipients, string $message, string $from = 'SPAULWGUILD')
    {
        try {
            // Format recipients array to a comma-separated string
            $recipientsString = implode(',', $recipients);

            // Send the SMS
            $result = $this->sms->send([
                'to'      => $recipientsString,
                'message' => $message,
                'from'    => $from
            ]);

            return $result;

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getContactById(string $id)
    {
        $member = DB::table('members')->select('contact')->where('id', $id)->first();
        return "+254" . substr($member->contact, 1);
    }
}
