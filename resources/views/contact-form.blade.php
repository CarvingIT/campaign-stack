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
            {{ __('New Contact') }}
	@else
            {{ __('Edit Contact') }}
	@endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
	        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    			<div class="mt-6 text-gray-500">
				<form name="save-tag" action="/savecontact" method="post">
				<input type="hidden" name="contact_id" value="{{ $contact->id }}" />	
				@csrf	
<div class="overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 bg-white sm:p-6 text-gray-900">
       <div class="grid grid-cols-6 gap-6">
        <!-- Contact Name -->
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="firstname">Firstname</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="firstname" name="firstname" type="text" value="{{ $contact->firstname }}" >
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="lastname">Lastname</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="lastname" name="lastname" type="text" value="{{ $contact->lastname }}" >
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="email">Email</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="email" name="email" type="email" value="{{ $contact->email }}" >
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="company">Company</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="company" name="company" type="text" value="{{ $contact->company }}" >
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="mobile">Mobile</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="mobile" name="mobile" type="text" value="{{ $contact->mobile }}" >
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
