<ul class="space-y-2">
    @foreach($items as $item)
    <li class="flex justify-between">
        <span>{{ $item['name'] }}</span>
        <span class="font-semibold">{{ $item['amount'] }}</span>
    </li>
    @endforeach
</ul>
