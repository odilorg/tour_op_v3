<?php

namespace App\Filament\Resources\TourResource\Pages;

use App\Filament\Resources\TourResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTour extends EditRecord
{
    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview Itinerary')
                ->icon('heroicon-o-eye')
                ->modalHeading('Itinerary Preview')
                ->modalContent(fn () => view('filament.tours.preview', [
                    'tour' => $this->record->load('days'),
                ])),
            Actions\Action::make('duplicate')
                ->label('Duplicate Tour')
                ->icon('heroicon-o-document-duplicate')
                ->requiresConfirmation()
                ->action(function () {
                    $newTour = $this->record->replicate();
                    $newTour->title = $this->record->title . ' Copy';
                    $newTour->slug = $this->record->slug . '-' . now()->format('His');
                    $newTour->push();

                    foreach ($this->record->days as $day) {
                        $newTour->days()->create($day->replicate(['tour_id'])->toArray());
                    }

                    Notification::make()->title('Tour duplicated')->success()->send();

                    return redirect(TourResource::getUrl('edit', ['record' => $newTour]));
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
