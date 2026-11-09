<?php

namespace App\Filament\Admin\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ClientAccessActions
{
    /** @return array<Action> */
    public static function all(): array{
        return [self::activate(), self::resetPassword(), self::toggleAccess()];
    }

    private static function isSuperAdmin(): bool{
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    private static function showCredentials(string $email, string $password): void{
        Notification::make()
            ->title('Identifiants du client')
            ->body(new HtmlString(
                'Email : <strong>'.e($email).'</strong><br>'
                .'Mot de passe : <strong>'.e($password).'</strong><br>'
                .'Notez-le maintenant : il ne sera plus affiché.'
            ))
            ->success()
            ->persistent()
            ->send();
    }

    public static function activate(): Action{
        return Action::make('activateClientSpace')
            ->label('Activer l’espace client')
            ->icon('heroicon-o-key')
            ->color('success')
            ->visible(fn ($record): bool => self::isSuperAdmin() && $record->user_id === null)
            ->modalHeading('Créer le compte de connexion du client')
            ->modalDescription('Le mot de passe est affiché une seule fois après la création.')
            ->fillForm(fn ($record): array => [
                'email' => $record->email,
                'password' => Str::password(12, symbols: false),
            ])
            ->schema([
                TextInput::make('email')
                    ->label('Email de connexion')
                    ->email()
                    ->required()
                    ->unique('users', 'email'),
                TextInput::make('password')
                    ->label('Mot de passe provisoire')
                    ->required()
                    ->minLength(8),
            ])
            ->action(function (array $data, $record): void {
                abort_unless(self::isSuperAdmin(), 403);

                DB::transaction(function () use ($data, $record) {
                    $user = new User([
                        'name' => $record->display_name,
                        'email' => $data['email'],
                        'phone' => $record->phone,
                        'password' => $data['password'],
                        'is_active' => true,
                    ]);
                    $user->email_verified_at = now();
                    $user->save();
                    $user->assignRole('client');

                    $record->forceFill([
                        'user_id' => $user->id,
                        'email' => $record->email ?? $data['email'],
                    ])->save();
                });

                self::showCredentials($data['email'], $data['password']);
            });
    }

    public static function resetPassword(): Action{
        return Action::make('resetClientPassword')
            ->label('Réinitialiser le mot de passe')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->visible(fn ($record): bool => self::isSuperAdmin() && $record->user_id !== null)
            ->requiresConfirmation()
            ->modalDescription('Un nouveau mot de passe provisoire sera généré et affiché une seule fois.')
            ->action(function ($record): void {
                abort_unless(self::isSuperAdmin(), 403);

                $password = Str::password(12, symbols: false);
                $record->user->update(['password' => $password]); // haché par le cast du modèle

                self::showCredentials($record->user->email, $password);
            });
    }

    public static function toggleAccess(): Action{
        return Action::make('toggleClientAccess')
            ->label(fn ($record): string => $record->user?->is_active ? 'Suspendre l’accès' : 'Rétablir l’accès')
            ->icon('heroicon-o-lock-closed')
            ->color(fn ($record): string => $record->user?->is_active ? 'danger' : 'success')
            ->visible(fn ($record): bool => self::isSuperAdmin() && $record->user_id !== null)
            ->requiresConfirmation()
            ->action(function ($record): void {
                abort_unless(self::isSuperAdmin(), 403);

                $record->user->update(['is_active' => ! $record->user->is_active]);

                Notification::make()
                    ->title($record->user->is_active ? 'Accès rétabli' : 'Accès suspendu')
                    ->success()
                    ->send();
            });
    }
}