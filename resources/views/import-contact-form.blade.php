@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('contacts');
        const fileNameDisplay = document.getElementById('selectedFileName');
        const dropzone = document.getElementById('uploadDropzone');

        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    fileNameDisplay.textContent = this.files[0].name + ' (' + Math.round(this.files[0].size / 1024) + ' KB)';
                    fileNameDisplay.parentElement.classList.remove('hidden');
                } else {
                    fileNameDisplay.parentElement.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-3xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1 space-x-2 uppercase tracking-wider">
                    <a href="/contacts" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Contacts</a>
                    <span>/</span>
                    <span class="text-amber-600 dark:text-amber-400">Bulk Import</span>
                </nav>
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 dark:bg-amber-400 shadow-2xs"></span>
                    <h2 class="font-extrabold text-2xl text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <i class="fas fa-file-import text-amber-500"></i>
                        <span>{{ __('Import Subscribers via CSV') }}</span>
                    </h2>
                </div>
            </div>
            <div>
                <a href="/contacts" class="inline-flex items-center px-3.5 py-2 bg-white dark:bg-[#111114] text-zinc-700 dark:text-zinc-200 text-xs font-semibold rounded-xl border border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all shadow-2xs gap-1.5">
                    <i class="fas fa-arrow-left text-3xs"></i>
                    <span>Back to Contacts</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10 pt-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form name="import_contacts" enctype="multipart/form-data" action="/import-contacts" method="post">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                    <!-- LEFT COLUMN: File Upload & Guidelines -->
                    <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between space-y-6">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                        Step 1
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-upload text-amber-500 text-xs"></i>
                                            Select CSV File
                                        </h3>
                                        <p class="text-3xs text-zinc-500 dark:text-zinc-400">Upload your formatted contacts spreadsheet.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Styled Dropzone Card -->
                            <div id="uploadDropzone" class="relative border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-8 text-center hover:border-amber-400 dark:hover:border-amber-400 transition-colors bg-zinc-50/70 dark:bg-[#09090B] group">
                                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-500/20 flex items-center justify-center text-2xl mx-auto mb-3 group-hover:scale-105 transition-transform shadow-2xs">
                                    <i class="fas fa-cloud-arrow-up"></i>
                                </div>
                                <h4 class="font-bold text-sm text-zinc-900 dark:text-white">Choose a CSV file to upload</h4>
                                <p class="text-3xs text-zinc-400 dark:text-zinc-500 mt-1 mb-4">Supported formats: .csv, .txt (up to 10MB)</p>
                                
                                <input type="file" id="contacts" name="contacts" accept=".csv, .txt" required 
                                       class="block w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-zinc-900 file:text-white dark:file:bg-white dark:file:text-zinc-950 hover:file:bg-zinc-800 cursor-pointer">

                                <div class="mt-3.5 hidden">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-3xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800">
                                        <i class="fas fa-circle-check mr-1.5 text-3xs"></i>
                                        <span id="selectedFileName">File selected</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Format Specifications & Sample Download -->
                            <div class="p-4 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-3xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-table text-amber-500 text-3xs"></i>
                                        Expected CSV Header Columns
                                    </span>
                                    <a href="/i/sample-import.csv" download class="inline-flex items-center gap-1 text-3xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                                        <i class="fas fa-download text-4xs"></i> Download Sample CSV
                                    </a>
                                </div>
                                <div class="flex flex-wrap gap-1.5 font-mono text-3xs text-zinc-600 dark:text-zinc-300">
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800">salutation</span>
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-amber-300 dark:border-amber-500/40 text-amber-700 dark:text-amber-300 font-bold">firstname</span>
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800">lastname</span>
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-amber-300 dark:border-amber-500/40 text-amber-700 dark:text-amber-300 font-bold">email</span>
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800">company</span>
                                    <span class="px-2 py-1 bg-white dark:bg-[#111114] rounded-lg border border-zinc-200/80 dark:border-zinc-800">mobile</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-end space-x-3 pt-5 mt-6 border-t border-zinc-100 dark:border-zinc-800/80">
                            <a href="/contacts" class="px-5 py-2.5 bg-white dark:bg-[#111114] hover:bg-zinc-50 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-semibold text-xs rounded-xl border border-zinc-200/80 dark:border-zinc-800 transition-colors shadow-2xs">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 font-bold text-xs rounded-xl shadow-md shadow-amber-500/15 hover:shadow-amber-500/25 hover:-translate-y-0.5 transition-all gap-2">
                                <i class="fas fa-file-import text-xs"></i>
                                <span>Import Contacts</span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: Batch Tag Assignment -->
                    <div class="bg-white dark:bg-[#111114] rounded-2xl p-6 shadow-xs border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col justify-between space-y-6">
                        
                        <div class="space-y-5">
                            <div class="border-b border-zinc-100 dark:border-zinc-800/80 pb-3.5 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-extrabold text-3xs uppercase tracking-wider border border-amber-200/60 dark:border-amber-500/30">
                                        Step 2
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                            <i class="fas fa-tags text-amber-500 text-xs"></i>
                                            Batch Tag Assignment
                                        </h3>
                                        <p class="text-3xs text-zinc-500 dark:text-zinc-400">Automatically attach selected tags to all imported subscribers.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tag Selector Grid -->
                            <div>
                                <label class="block font-semibold text-3xs text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2">
                                    Choose Tags for this Import Batch
                                </label>

                                @if(count($tags) > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-72 overflow-y-auto p-1 scrollbar-none">
                                        @foreach($tags as $t)
                                            <label class="cursor-pointer relative flex items-center p-2.5 rounded-xl border transition-all text-xs font-semibold select-none group bg-zinc-50/70 dark:bg-[#09090B] border-zinc-200/80 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 has-[:checked]:bg-amber-50/80 has-[:checked]:border-amber-300 dark:has-[:checked]:bg-amber-950/40 dark:has-[:checked]:border-amber-500/40 dark:has-[:checked]:text-amber-200 has-[:checked]:text-amber-900 shadow-2xs">
                                                <input type="checkbox" name="tags[]" value="{{ $t->id }}" class="rounded border-zinc-300 dark:border-zinc-700 text-amber-500 focus:ring-amber-400 mr-2">
                                                <span class="truncate">{{ $t->label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-5 bg-zinc-50/70 dark:bg-[#09090B] rounded-xl border border-zinc-200/80 dark:border-zinc-800 text-center">
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">No tags available for bulk assignment.</p>
                                        <a href="/tag-form/new" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1">
                                            <i class="fas fa-plus text-3xs"></i> Create a tag
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Guidance Note -->
                            <div class="p-4 bg-amber-50/40 dark:bg-amber-950/20 rounded-xl border border-amber-200/50 dark:border-amber-500/20 text-3xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                <i class="fas fa-circle-info text-amber-500 mr-1"></i>
                                <strong>Duplicate Check:</strong> Existing email addresses already present in your database will not be duplicated. Tags selected above will be appended to newly imported contacts.
                            </div>
                        </div>

                        <!-- Symmetrical Bottom Status Note -->
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-3xs text-zinc-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fas fa-shield-halved text-emerald-500"></i>
                                Secure batch processing
                            </span>
                            <span class="font-mono text-zinc-400">CSV Parser</span>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
