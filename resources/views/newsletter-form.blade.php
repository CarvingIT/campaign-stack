@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<!--script src="https://tiny.cloud" referrerpolicy="origin"></script-->
<!--script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/8/tinymce.min.js" referrerpolicy="origin"></script-->
<script src="https://cdn.tiny.cloud/1/lgwysaytr4tfzhs9q2uspwfz9zqj4qfhlrclnl0pfhughvrg/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
    tinymce.init({
     selector: 'textarea#body_template', // Replace this CSS selector to match the placeholder element for TinyMCE
     plugins: 'table lists link image code',
     toolbar: 'undo redo | blocks| bullist numlist checklist | code | table | fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | removeformat',

    file_picker_callback (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth
        let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight

        tinymce.activeEditor.windowManager.openUrl({
          url : '/file-manager/tinymce5',
          title : 'Laravel File manager',
          width : x * 0.8,
          height : y * 0.8,
          onMessage: (api, message) => {
            callback(message.content, { text: message.text })
          }
        })
      }

   });
</script>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
	@if(empty($book->id))
            {{ __('New Newsletter') }}
	@else
            {{ __('Edit Newsletter') }}
	@endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
	        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
    			<div class="mt-6 text-gray-500">
				<form name="save-newsletter" action="/savenewsletter" method="post">
				<input type="hidden" name="newsletter_id" value="{{ $newsletter->id }}" />	
				@csrf	
<div class="overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 bg-white sm:p-6 text-gray-900">
       <div class="grid grid-cols-6 gap-6">
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="title">Title</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="title" name="title" type="text" value="{{ $newsletter->title }}" placeholder="Some announcement">
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="tag_id">Tags</label>
                @php
                    $tags_array = explode(",",$newsletter->tag_ids);
                @endphp
             <select class="form-input rounded-md shadow-sm mt-1 block w-full" id="tag_ids" name="tag_ids[]" multiple>
                <option value="">Select Tag</option>
                @foreach($tags as $tag)
                <option value="{{ $tag->id }}" @if(in_array($tag->id,$newsletter->newsletter_tags->pluck('tag_id')->toArray())) selected @endif>{{ $tag->label }}</option>
                @endforeach
             </select>
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="campaign_id">Campaings</label>
             <select class="form-input rounded-md shadow-sm mt-1 block w-full" id="campaign_id" name="campaign_id">
                <option value="">Select Campaign</option>
                @foreach($campaigns as $camp)
                <option value="{{ $camp->id }}" @if($newsletter->campaign_id == $camp->id) selected @endif>{{ $camp->name }}</option>
                @endforeach
             </select>
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="status">Status</label>
             <select class="form-input rounded-md shadow-sm mt-1 block w-full" id="status" name="status">
                <option value="">Select Status</option>
                <option value="D" @if($newsletter->status == 'D') selected @endif>Draft</option>
                <option value="N" @if($newsletter->status == 'N') selected @endif>New</option>
                <option value="Q" @if($newsletter->status == 'Q') selected @endif>Queing</option>
                <option value="S" @if($newsletter->status == 'S') selected @endif>Sent</option>
             </select>
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="subject_template">Subject Template</label>
             <input class="form-input rounded-md shadow-sm mt-1 block w-full" id="subject_template" name="subject_template" type="text" value="{{ $newsletter->subject_template }}" placeholder="New feature [[feature_name]]">
        </div>
        <div class="col-span-8 md:col-span-4">
             <label class="block font-medium text-sm" for="body_template">Body Template</label>
             <textarea class="form-input rounded-md shadow-sm mt-1 block w-full" id="body_template" name="body_template" type="text">{{ $newsletter->body_template }}</textarea>
        </div>
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
