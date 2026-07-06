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
    <div class="px-4 py-5 bg-white sm:p-6 text-gray-900">
       <div class="grid grid-cols-6 gap-6">
        <!-- Account Name -->
        <div class="col-span-4" md:col-span-4">
             <label class="block font-medium text-sm" for="book_number">Account Name</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_name" name="account_name" type="text" value="{{ $account->account_name }}" >
        </div>
        <br />
        <!-- Account Type -->
        <div class="col-span-4 md:col-span-4">
             <label class="block font-medium text-sm" for="book_name">Account Type</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_type" name="account_type" type="text" value="{{ $account->account_type }}" required>
        </div>
        <br />
        <!-- Account Config -->
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="book_author">Account IP Address</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_ip_address" name="account_ip_address" type="text" value="{{ $account->account_ip_address }}" >
        </div>
        <br />
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="book_author">Account Username</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_username" name="account_username" type="text" value="{{ $account->account_username }}" >
        </div>
        <br />
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="book_author">Account Password</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_password" name="account_password" type="text" value="{{ $account->account_password }}" >
        </div>
        <br />
        <div class="col-span-4 md:col-span-2">
             <label class="block font-medium text-sm" for="book_author">Account Port</label>
             <input class="form-input rounded-md shadow-sm mt-1 block" id="account_port" name="account_port" type="text" value="{{ $account->account_port }}" >
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
