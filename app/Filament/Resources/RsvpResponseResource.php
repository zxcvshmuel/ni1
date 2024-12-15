<?php

namespace App\Filament\Resources;

use App\Models\RsvpResponse;
use App\Filament\Resources\RsvpResponseResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RsvpResponseResource extends Resource
{
    protected static ?string $model = RsvpResponse::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    
    protected static ?string $navigationGroup = 'אירועים';
    
    protected static ?int $navigationSort = 8;
    
    public static function getModelLabel(): string
    {
        return 'אישור הגעה';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'אישורי הגעה';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('invitation_id')
                            ->label('הזמנה')
                            ->relationship('invitation', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('שם האורח')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('דוא"ל')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('טלפון')
                                    ->tel()
                                    ->maxLength(20),

                                Forms\Components\Select::make('status')
                                    ->label('סטטוס')
                                    ->required()
                                    ->options([
                                        'attending' => 'מגיע',
                                        'not_attending' => 'לא מגיע',
                                        'maybe' => 'אולי',
                                    ])
                                    ->default('attending'),

                                Forms\Components\TextInput::make('guests_count')
                                    ->label('מספר אורחים')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->required(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('הערות')
                                    ->maxLength(1000)
                                    ->columnSpanFull(),

                                Forms\Components\KeyValue::make('preferences')
                                    ->label('העדפות')
                                    ->keyLabel('סוג')
                                    ->valueLabel('העדפה')
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invitation.title')
                    ->label('אירוע')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('שם האורח')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attending' => 'מגיע',
                        'not_attending' => 'לא מגיע',
                        'maybe' => 'אולי',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'attending' => 'success',
                        'not_attending' => 'danger',
                        'maybe' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('guests_count')
                    ->label('אורחים')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('טלפון')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('תאריך אישור')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('invitation_id')
                    ->label('אירוע')
                    ->relationship('invitation', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('סטטוס')
                    ->options([
                        'attending' => 'מגיע',
                        'not_attending' => 'לא מגיע',
                        'maybe' => 'אולי',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('צפייה'),
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListRsvpResponses::route('/'),
            'create' => Pages\CreateRsvpResponse::route('/create'),
            'view' => Pages\ViewRsvpResponse::route('/{record}'),
            'edit' => Pages\EditRsvpResponse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('invitation');
    }
}