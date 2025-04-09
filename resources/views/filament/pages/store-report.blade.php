<x-filament-panels::page>
    <x-filament::widget>
        <x-filament::card>
            <div class="space-y-6">
                @foreach ($stores as $store)
                    @php
                        $summary = $store->hawlaOverallSummary();
                    @endphp

                    <div class="border-b pb-4">
                        <h2 class="text-xl font-bold text-primary">{{ $store->name }} ({{ $store->uuid }})</h2>

                        @if (count($summary))
                            <table class="w-full text-sm mt-2 table-auto border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-1 text-left">Currency</th>
                                        <th class="px-2 py-1 text-right">Total Given</th>
                                        <th class="px-2 py-1 text-right">Total Received</th>
                                        <th class="px-2 py-1 text-right">Net Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary as $currency => $data)
                                        <tr class="border-t">
                                            <td class="px-2 py-1">{{ $currency }}</td>
                                            <td class="px-2 py-1 text-right text-blue-600">{{ $data['total_given'] }}</td>
                                            <td class="px-2 py-1 text-right text-green-600">{{ $data['total_received'] }}</td>
                                            <td class="px-2 py-1 text-right font-semibold">{{ $data['net_balance'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500 text-sm">No transactions found.</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::card>
    </x-filament::widget>

</x-filament-panels::page>
