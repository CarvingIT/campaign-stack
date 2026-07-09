@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
	@if(empty($book->id))
            {{ __('New Mail Account') }}
	@else
            {{ __('Edit Mail Account') }}
	@endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
	        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    			<div class="mt-6 text-gray-500">
				<form name="save-book" action="/saveaccount" method="post">
				<input type="hidden" name="account_id" value="{{ $account->id }}" />	
				@csrf	
<div class="overflow-hidden sm:rounded-md">
        <h3>Account Details</h3>
    <div class="px-4 py-5 bg-white sm:p-6 text-gray-900">
       <div class="grid grid-cols-6 gap-6">
        <!-- Account Name -->
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_name">Name</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_name" name="account_name" type="text" value="{{ $account->name }}" placeholder="Smart Repo">
        </div>
        <!-- Account Type -->
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_type">Type</label>
             <select class="form-input rounded-md shadow-sm mt-1 block" id="account_type" name="account_type">
                <option value="">Select Type</option>
                <option value="587" @if(@$account->type == 'SMTP') selected @endif>SMTP</option>
             </select>
        </div>
        @php
            $config_array = json_decode($account->config);
        @endphp
        <!-- Account Config -->
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_ip_address">Server IP Address / Host</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_ip_address" name="account_ip_address" type="text" value="{{ @$config_array->ip_address }}" placeholder="192.123.43.23">
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_username">Username</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_username" name="account_username" type="text" value="{{ @$config_array->username }}" placeholder="bob">
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_password">Password</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_password" name="account_password" type="text" value="{{ @$config_array->password }}" placeholder="bob123">
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_port">Port</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_port" name="account_port" type="text" value="{{ @$config_array->port }}" required placeholder="8080">
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_encryption">Encryption</label>
             <select class="form-input rounded-md shadow-sm mt-1 block" id="account_encryption" name="account_encryption">
                <option value="">Select Encryption</option>
                <option value="SSL" @if(@$config_array->encryption == 'SSL') selected @endif>SSL</option>
                <option value="TLS" @if(@$config_array->encryption == 'TLS') selected @endif>TLS</option>
             </select>
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_from_username">From username</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_from_username" name="account_from_username" type="text" value="{{ @$config_array->from_username }}" placeholder="Bob T."">
        </div>
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="account_username">From Address</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_from_address" name="account_from_address" type="text" value="{{ @$config_array->from_address }}" placeholder="bob@email.com">
        </div>
        <div>&nbsp;</div>
       </div>
    </div>

    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
     <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 m-1" wire:loading.attr="disabled">
    Save
     </button>
     <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 m-1" wire:loading.attr="disabled" onclick="window.history.back();">
    Cancel
     </button>
   </div>
          </div>
				</form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
