<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingResource;
use App\Jobs\RecomputeBookingFinancialsJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('requestConfirmation')
                ->label('Request Confirmation')
                ->icon('heroicon-o-envelope')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->statusLogs()->create([
                        'old_status' => $this->record->status->value,
                        'new_status' => $this->record->status->value,
                        'changed_at' => now(),
                        'user_id' => auth()->id(),
                        'note' => 'Confirmation request sent.',
                    ]);

                    Notification::make()->success()->title('Confirmation request logged')->send();
                }),
            Actions\Action::make('recomputeTotals')
                ->label('Recompute Totals')
                ->icon('heroicon-o-calculator')
                ->action(function () {
                    RecomputeBookingFinancialsJob::dispatch($this->record);
                    Notification::make()->info()->title('Totals recalculation queued')->send();
                }),
            Actions\Action::make('markConfirmed')
                ->label('Mark Confirmed')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status !== BookingStatus::CONFIRMED)
                ->action(function () {
                    $this->record->update([
                        'status' => BookingStatus::CONFIRMED,
                        'manual_status_override' => true,
                    ]);

                    Notification::make()->success()->title('Booking marked as confirmed')->send();
                }),
            Actions\Action::make('cancelBooking')
                ->label('Cancel Booking')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => BookingStatus::CANCELLED,
                        'manual_status_override' => true,
                    ]);

                    Notification::make()->success()->title('Booking cancelled')->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
