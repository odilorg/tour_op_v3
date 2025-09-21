<div class="space-y-4">
    <h2 class="text-lg font-semibold">{{ $tour->title }}</h2>
    <p class="text-sm text-gray-600">{{ $tour->description }}</p>
    <ol class="space-y-2 list-decimal list-inside">
        @foreach($tour->days as $day)
            <li>
                <span class="font-medium">Day {{ $day->day_index }}:</span>
                {{ $day->title }}
                @if($day->description)
                    <div class="text-sm text-gray-500">{{ $day->description }}</div>
                @endif
            </li>
        @endforeach
    </ol>
</div>
