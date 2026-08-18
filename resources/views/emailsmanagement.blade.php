@push('js')
<link rel="stylesheet" href="/css/all.min.css" />
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {

        const table = $('#emails').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                url: "{{ route('emails.data') }}",
                type: "GET",
                data: function (d) {
                    d.status = $('#email-status').val();
                }
            },

            pageLength: 10,

            lengthMenu: [10, 25, 50, 100],

            order: [[4, 'desc']],

            columns: [
                {
                    data: 'subject',
                    name: 'subject'
                },
                {
                    data: 'campaign_name',
                    name: 'campaign_name'
                },
                {
                    data: 'recipient',
                    name: 'recipient'
                },
                {
                    data: 'sender_mail_account',
                    name: 'sender_mail_account'
                },
                {
                    data: 'timestamp',
                    name: 'timestamp'
                }
            ],

            scrollX: true
        });

        $('#email-status').on('change', function () {
            table.ajax.reload();
        });

    });
</script>
@endpush


<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Emails') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                    <div class="mt-6 text-gray-900">

                        <div class="table-responsive">

                            <div class="mb-4">
                                <label for="email-status" class="font-semibold text-gray-700">
                                    Email Status
                                </label>

                                <select id="email-status" class="border-gray-300 rounded-md shadow-sm ml-2">
                                    <option value="all">All</option>
                                    <option value="sent">Sent</option>
                                    <option value="queued">Queued</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>

                            <table
                                id="emails"
                                class="table table-bordered display stripe hover"
                                style="width:100%"
                            >

                                <thead class="text-primary">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Campaign</th>
                                        <th>Recipient</th>
                                        <th>Sender Mail Account</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>