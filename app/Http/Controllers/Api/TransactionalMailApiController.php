<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\MailQueue;
use App\Models\OutboundMailAccount;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionalMailApiController extends Controller
{
    /**
     * POST /api/authenticate
     * Authenticates API client using email & password and returns a Sanctum Bearer token.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'token_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        $tokenName = $request->input('token_name', 'transactional-mail-api-token');
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authenticated successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * GET /api/accounts
     * Lists email accounts configured to send out emails.
     */
    public function accounts(Request $request): JsonResponse
    {
        $statusFilter = $request->query('status');
        $typeFilter = $request->query('type');

        $query = OutboundMailAccount::query();

        if ($statusFilter !== null) {
            $query->where('status', (int) $statusFilter);
        }

        if (!empty($typeFilter)) {
            $query->where('type', strtoupper($typeFilter));
        }

        $accounts = $query->orderBy('status', 'desc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($account) {
                $rawConfig = json_decode($account->config, true) ?: [];

                // Sanitize sensitive credentials in config for security
                $sanitizedConfig = $rawConfig;
                if (isset($sanitizedConfig['password'])) {
                    $sanitizedConfig['password'] = '••••••••';
                }
                if (is_array($sanitizedConfig) && !isset($sanitizedConfig['ip_address'])) {
                    foreach ($sanitizedConfig as $k => $item) {
                        if (is_array($item) && isset($item['value'])) {
                            $sanitizedConfig[$k]['value'] = '••••••••';
                        }
                    }
                }

                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => strtoupper($account->type),
                    'status' => (int) ($account->status ?? 1),
                    'is_active' => (int) ($account->status ?? 1) === 1,
                    'active_after' => $account->active_after,
                    'from_name' => $rawConfig['from_username'] ?? null,
                    'from_email' => $rawConfig['from_address'] ?? null,
                    'host' => $rawConfig['ip_address'] ?? $rawConfig['host'] ?? null,
                    'port' => $rawConfig['port'] ?? null,
                    'encryption' => $rawConfig['encryption'] ?? null,
                    'config' => $sanitizedConfig,
                    'created_at' => $account->created_at,
                    'updated_at' => $account->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $accounts->count(),
            'accounts' => $accounts,
        ], 200);
    }

    /**
     * POST /api/create_account
     * Creates a new account for sending out emails with configuration parameters from the request body.
     */
    public function createAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:outbound_mail_accounts,name',
            'type' => 'nullable|string|in:SMTP,API,smtp,api',
            'status' => 'nullable|in:0,1,true,false',
            'active_after' => 'nullable|date',
            'config' => 'nullable',
            // Allow individual parameters in request body as well:
            'host' => 'nullable|string',
            'ip_address' => 'nullable|string',
            'port' => 'nullable|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'from_username' => 'nullable|string',
            'from_address' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = strtoupper($request->input('type', 'SMTP'));

        // Resolve status
        $statusInput = $request->input('status', 1);
        $status = ($statusInput === 0 || $statusInput === '0' || $statusInput === false || $statusInput === 'false') ? 0 : 1;

        // Build configuration array
        $config = [];
        if ($request->has('config') && is_array($request->input('config'))) {
            $config = $request->input('config');
        } elseif ($request->has('config') && is_string($request->input('config'))) {
            $config = json_decode($request->input('config'), true) ?: [];
        }

        if ($type === 'SMTP') {
            // Merge top-level request parameters if not already in config
            $host = $request->input('host', $request->input('ip_address', $config['ip_address'] ?? $config['host'] ?? '127.0.0.1'));
            $port = (int) $request->input('port', $config['port'] ?? 587);
            $username = $request->input('username', $config['username'] ?? null);
            $password = $request->input('password', $config['password'] ?? null);
            $encryption = $request->input('encryption', $config['encryption'] ?? 'tls');
            $fromUsername = $request->input('from_username', $config['from_username'] ?? 'Campaign Stack');
            $fromAddress = $request->input('from_address', $config['from_address'] ?? null);

            $config = [
                'ip_address' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'encryption' => $encryption,
                'from_username' => $fromUsername,
                'from_address' => $fromAddress,
            ];
        }

        $account = new OutboundMailAccount();
        $account->name = $request->name;
        $account->type = $type;
        $account->status = $status;
        $account->active_after = $request->filled('active_after') ? $request->active_after : now()->subMinute();
        $account->config = json_encode($config);
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Outbound mail account created successfully.',
            'account' => [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type,
                'status' => (int) $account->status,
                'active_after' => $account->active_after,
                'config' => $config,
                'created_at' => $account->created_at,
            ],
        ], 201);
    }

    /**
     * POST /api/queue_email
     * Adds an email to the queue. Uses null newsletter for transactional emails.
     * No template will be involved here.
     */
    public function queueEmail(Request $request): JsonResponse
    {
        // Support common naming variations for recipient email
        $recipientEmail = $request->input('email', $request->input('recipient_email', $request->input('to')));

        // Prepare request data with normalized email for validation
        $request->merge(['email' => $recipientEmail]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'name' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'outbound_mail_account_id' => 'nullable|integer|exists:outbound_mail_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // 1. Resolve contact
        if ($request->filled('contact_id')) {
            $contact = Contact::find($request->contact_id);
        } else {
            $cleanEmail = strtolower(trim($recipientEmail));
            $nameInput = $request->input('firstname', $request->input('name', ''));
            $lastNameInput = $request->input('lastname', '');

            $contact = Contact::firstOrCreate(
                ['email' => $cleanEmail],
                [
                    'firstname' => $nameInput ?: null,
                    'lastname' => $lastNameInput ?: null,
                ]
            );
        }

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to determine or create recipient contact record.',
            ], 500);
        }

        // 2. Validate outbound mail account if explicitly specified
        if ($request->filled('outbound_mail_account_id')) {
            $account = OutboundMailAccount::find($request->outbound_mail_account_id);
            if (!$account || (int) $account->status === 0) {
                return response()->json([
                    'success' => false,
                    'message' => "The specified outbound mail account (ID: {$request->outbound_mail_account_id}) is inactive or not found.",
                ], 422);
            }
        }

        // 3. Insert directly into mail_queues with newsletter_id = null (Transactional)
        $mailQueue = new MailQueue();
        $mailQueue->id = (string) Str::uuid();
        $mailQueue->newsletter_id = null; // Transactional emails have null newsletter
        $mailQueue->contact_id = $contact->id;
        $mailQueue->outbound_mail_account_id = $request->outbound_mail_account_id;
        $mailQueue->subject = $request->subject;
        $mailQueue->body = $request->body;
        $mailQueue->status = 'Q';
        $mailQueue->attempt = 0;
        $mailQueue->error = null;
        $mailQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Transactional email queued successfully.',
            'data' => [
                'queue_id' => $mailQueue->id,
                'newsletter_id' => null,
                'recipient_email' => $contact->email,
                'recipient_name' => trim(($contact->firstname ?? '') . ' ' . ($contact->lastname ?? '')),
                'contact_id' => $contact->id,
                'subject' => $mailQueue->subject,
                'status' => $mailQueue->status,
                'outbound_mail_account_id' => $mailQueue->outbound_mail_account_id,
                'created_at' => $mailQueue->created_at,
            ],
        ], 201);
    }
}
