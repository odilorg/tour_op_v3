<x-filament::section heading="Current Week">
    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="border rounded p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold">{{ $booking->reference_code }} - {{ $booking->customer_name }}</h3>
                        <p class="text-xs text-gray-500">{{ $booking->start_date->format('M d') }} → {{ $booking->end_date->format('M d') }} · {{ ucfirst($booking->status->value) }}</p>
                    </div>
                    <x-filament::badge color="{{ $booking->progress_percent >= 70 ? 'success' : 'warning' }}">
                        {{ $booking->progress_percent }}%
                    </x-filament::badge>
                </div>
                <ul class="mt-3 space-y-1 text-sm">
                    @foreach($booking->days as $day)
                        <li class="flex justify-between">
                            <span>{{ $day->date->format('D d') }} · {{ $day->title }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="text-sm text-gray-500">No bookings scheduled for this week.</p>
        @endforelse
    </div>
</x-filament::section>
