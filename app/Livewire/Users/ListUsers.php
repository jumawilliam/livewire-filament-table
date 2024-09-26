<?php

namespace App\Livewire\Users;

use App\Models\User;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;


class ListUsers extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_verified')
                ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
            ])
            ->actions([

                Action::make('edit')
                ->form([
                    TextInput::make('name'),
                    TextInput::make('email'),
                ])
                ->action(function (array $data, User $record): void {
                    $record->update($data);
                    Notification::make()
                    ->title('Record updated successfully')
                    ->success()
                    ->send();
                })->mountUsing(function (Form $form, User $record) {
                    $form->fill([
                        'name' => $record->name,
                        'email' => $record->email,
                    ]);

                    // ...
                }),
            Action::make('delete')
                ->action(function (User $record){
                    $record->delete();

                    Notification::make()
                    ->title('Record Deleted successfully')
                    ->success()
                    ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Delete User')
                ->modalDescription('Are you sure you\'d like to delete this post? This cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete '),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.users.list-users');
    }
}
