<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TagsColumn;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'ניהול משתמשים';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'משתמש';
    }

    public static function getPluralModelLabel(): string
    {
        return 'משתמשים';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('שם מלא')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('דוא"ל')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            TextInput::make('password')
                                ->label('סיסמה')
                                ->password()
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $context): bool => $context === 'create'),

                            TextInput::make('phone')
                                ->label('טלפון')
                                ->tel()
                                ->maxLength(20),

                            TextInput::make('credits')
                                ->label('קרדיטים')
                                ->numeric()
                                ->default(0),

                            Select::make('language')
                                ->label('שפה')
                                ->options([
                                    'he' => 'עברית',
                                    'en' => 'English',
                                ])
                                ->default('he')
                                ->required(),

                            Select::make('roles')
                                ->label('תפקידים')
                                ->multiple()
                                ->relationship('roles', 'name')
                                ->preload(),

                            Toggle::make('is_active')
                                ->label('משתמש פעיל')
                                ->default(true),

                            DateTimePicker::make('email_verified_at')
                                ->label('אימות דוא"ל')
                                ->nullable(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('מזהה')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('שם')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('דוא"ל')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('טלפון')
                    ->searchable(),

                TextColumn::make('credits')
                    ->label('קרדיטים')
                    ->sortable(),

                TextColumn::make('language')
                    ->label('שפה')
                    ->sortable(),

                TagsColumn::make('roles.name')
                    ->label('תפקידים'),

                ToggleColumn::make('is_active')
                    ->label('פעיל'),

                TextColumn::make('created_at')
                    ->label('נוצר ב')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->label('תפקידים'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('משתמש פעיל'),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('דוא"ל מאומת')
                    ->nullable(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Impersonate::make()
                        ->label('התחבר כמשתמש')
                        ->redirectTo('/admin')
                        ->hidden(fn (User $record) => ! auth()->user()->can('view_any_user')),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletes::class
            ]);
    }
}