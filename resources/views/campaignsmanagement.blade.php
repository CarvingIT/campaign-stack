@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="/css/jquery-ui.css" />
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script>
    $(document).ready(function() {
     $("#campaigns").DataTable(
        {
        stateSave:true,
        "scrollX": true,
        columnDefs: [
                        { width: '30%', targets: 0 },
                        { "orderable": false, targets: 3 }
                ],
        "lengthMenu": [10,50,100, 500, 1000 ],
        "pageLength": 10,
        //fixedColumns: true
        fixedColumns:{ left:1, right:1}

        }
    );
// New code to retain search value
// Restore state
    var table = $('#campaigns').val();
    if(table){
    var state = table.state.loaded();
    if ( state ) {
      table.columns().eq( 0 ).each( function ( colIdx ) {
        var colSearch = state.columns[colIdx].search;

        if ( colSearch.search ) {
          $( 'input', table.column( colIdx ).footer() ).val( colSearch.search );
        }
      } );

      table.draw();
    }
 // Apply the search
    table.columns().eq( 0 ).each( function ( colIdx ) {
        $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
            table
                .column( colIdx )
                .search( this.value )
                .draw();
        } );
    } );
    }
//
});

    function showDeleteDialog(campaign_id){
        //alert(campaign_id);
        $('#delete_campaign_id').val(campaign_id);
        $("#deletedialog").dialog({
            title:'Are you sure?',
            dialogClass: "alert"
        });
    }
</script>

@endpush
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Campaigns') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                   @if(Session::has('alert-' . $msg))
                    <div class="mt-6 text-gray-900 leading-7 font-semibold ">
                                <span @if($msg == 'danger') style="color:red" @else style="color:green"  @endif>{{ Session::get('alert-' . $msg) }}</span>
                    </div>
                   @endif
               @endforeach

            <div class="text-right">
                <a class="m-5" title="New Book" href="/campaign-form/new"><span class="fas fa-plus"></span></a>
                <!--a class="m-5" title="Export" href="/export/campaigns"><span class="fas fa-file-export"></span></a-->
            </div>
                <div class="mt-6 text-gray-900">
            <div class="table-responsive">
                    <table id="campaigns" class="table table-bordered  display stripe hover" style="width:100%">
                        <thead class="text-primary">
                            <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Total no.of Newsltters</th>
                            <th>Updated at</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            @foreach ($campaigns as $c)
                @php $attributes = json_decode($c->other_attributes);@endphp
                <tr>
            <td>{{ @$c->name }}{{ $attributes->type }}</td>
            <td>{{ $attributes->type }}</td>
            <td>{{ @$c->campaign->newsletters }}</td>
            <td>{{ $c->updated_at }}</td>
            <td>
                <!--a href="/campaign/{{ $c->id }}" title="View Details"><span class="fas fa-eye" style="padding:5%;"></span></a-->
                <a href="/campaign-form/{{ $c->id }}" title="Edit"><span class="fas fa-pencil-alt" style="padding:5%;"></span></a>
                <button id="opener" onClick="showDeleteDialog({{ $c->id }});" title="Delete"><span class="fas fa-trash-alt"></span></button>

                <div id="deletedialog" style="display:none;" class="bg-grey">
                <form name="deletedoc" method="post" action="/campaign/delete">
                @csrf
                <input type="hidden" id="delete_campaign_id" name="campaign_id" value="{{ $c->id }}" />
            This action can not be undone.
            <div class="flex items-center justify-end px-4 py-3 sm:px-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 m-1" wire:loading.attr="disabled">Delete</button>
                <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 m-1 do-not-delete" wire:loading.attr="disabled" onClick="document.location='/campaigns';">Cancel</button>
            </div>
                </form>
            </div>
            </td>
            </tr>
                    @endforeach
            </tbody>
                </table>
{{-- This renders the pagination HTML/CSS --}}
<div class="mt-4">
    {{-- $books->links() --}}
</div>
                        </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
