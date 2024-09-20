<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MailchimpMarketing\ApiClient;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class MailchimpController extends Controller
{
    public function __construct()
    {
        // $this->mailchimp = new \MailchimpMarketing\ApiClient();
        $this->mailchimp = new ApiClient();
        $this->mailchimp->setConfig([
            'apiKey' => env('MAILCHIMP_API_KEY'),
            'server' => env('MAILCHIMP_SERVER_PREFIX'),
        ]);
    }

    public function createMailchimpAudience(Request $request)
    {
        try {
            $response = $this->mailchimp->lists->createList([
                "name" => "PHP Developers Meetup",
                "permission_reminder" => "permission_reminder",
                "email_type_option" => false,
                "contact" => [
                    "company" => "Mailchimp",
                    "address1" => "405 N Angier Ave NE",
                    "city" => "Atlanta",
                    "state" => "GA",
                    "zip" => "30308",
                    "country" => "NG",
                ],
                "campaign_defaults" => [
                    "from_name" => "Gettin' Together",
                    "from_email" => "gibsonroq@gmail.com",
                    "subject" => "PHP Developer's Meetup",
                    "language" => "EN_US",
                ],
            ]);

            $response = $this->mailchimp->ping->get();

            return response()->json([
                'success' => true,
                'message' => 'Audience created successfully',
                'data' => $response,
            ]);
        } catch (\MailchimpMarketing\ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function addMemberToMailchimpAudience(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()->first(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $list_id = env('MAILCHIMP_AUDIENCE_ID');

            $response = $this->mailchimp->lists->setListMember($list_id, md5(strtolower($request->email)), [
                'email_address' => $request->email,
                'status' => 'subscribed',
                'status_if_new' => 'subscribed',
                // 'merge_fields' => [
                //     'FNAME' => $request->first_name,
                //     'LNAME' => $request->last_name,
                // ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User added to audience successfully',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function listMailchimpAudienceMembers()
    {
        try {
            $list_id = env('MAILCHIMP_AUDIENCE_ID');

            $response = $this->mailchimp->lists->getListMembersInfo($list_id);

            return response()->json([
                'success' => true,
                'message' => 'Audience members listed successfully',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
