<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MailchimpMarketing\ApiClient;

class MailchimpAudience extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailchimp:audience';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe users to Mailchimp audience';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mailchimp = new \MailchimpMarketing\ApiClient();
        $mailchimp->setConfig([
            'apiKey' => env('MAILCHIMP_API_KEY'),
            'server' => env('MAILCHIMP_SERVER_PREFIX'),
        ]);

        $list_id = env('MAILCHIMP_AUDIENCE_ID');
        // $response = $mailchimp->lists->getListMembersInfo($list_id);

        $users = \App\Models\User::all()->where(['is_active' => 1]);

        foreach ($users as $user) {
            try {
                $user_hash = md5(strtolower($user->email));

                $response = $mailchimp->lists->addListMember($list_id, $user_hash, [
                    'email_address' => $user->email,
                    'status_if_new' => 'subscribed',
                    'status' => 'subscribed',
                ]);

                $this->info('User ' . $user->email . ' subscribed to Mailchimp audience');
                $this->info('Mailchimp : ' . $response);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
