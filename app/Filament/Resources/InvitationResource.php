<?php

namespace App\Filament\Resources;

use App\Models\Invitation;
use App\Filament\Resources\InvitationResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'אירועים';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'הזמנה';
    }

    public static function getPluralModelLabel(): string
    {
        return 'הזמנות';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Basic Information
                                Forms\Components\TextInput::make('title')
                                    ->label('כותרת האירוע')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                        if (! $set->get('slug')) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['regex:/^[a-z0-9\-]+$/'])
                                    ->helperText('משמש לכתובת URL. רק אותיות באנגלית, מספרים ומקפים'),

                                Forms\Components\Select::make('event_type')
                                    ->label('סוג האירוע')
                                    ->required()
                                    ->options([
                                        'wedding' => 'חתונה',
                                        'bar_mitzvah' => 'בר מצווה',
                                        'bat_mitzvah' => 'בת מצווה',
                                        'birthday' => 'יום הולדת',
                                        'engagement' => 'אירוסין',
                                        'other' => 'אחר',
                                    ]),

                                Forms\Components\DateTimePicker::make('event_date')
                                    ->label('תאריך ושעת האירוע')
                                    ->required()
                                    ->timezone('Asia/Jerusalem'),

                                // Venue Information
                                Forms\Components\TextInput::make('venue_name')
                                    ->label('שם האולם/מקום')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('venue_address')
                                    ->label('כתובת')
                                    ->required()
                                    ->rows(2),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('venue_latitude')
                                            ->label('קו רוחב')
                                            ->numeric()
                                            ->rules(['regex:/^[-]?((([0-8]?[0-9])\.(\d+))|(90(\.0+)?))$/'])
                                            ->placeholder('31.7767'),

                                        Forms\Components\TextInput::make('venue_longitude')
                                            ->label('קו אורך')
                                            ->numeric()
                                            ->rules(['regex:/^[-]?((([0-9]?[0-9]|1[0-7][0-9])\.(\d+))|(180(\.0+)?))$/'])
                                            ->placeholder('35.2345'),
                                    ]),

                                // Template Selection
                                Forms\Components\Select::make('template_id')
                                    ->label('תבנית')
                                    ->required()
                                    ->relationship(
                                        'template',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->select(['id', 'name'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name['he'] ?? '')
                                    ->preload()
                                    ->searchable(),

                                Forms\Components\Select::make('effects')
                                    ->label('אפקטים')
                                    ->multiple()
                                    ->relationship(
                                        'effects',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->select(['id', 'name'])
                                    )
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name['he'] ?? '')
                                    ->preload(),

                                Forms\Components\Select::make('songs')
                                    ->label('שירים')
                                    ->multiple()
                                    ->relationship('songs', 'title')
                                    ->preload()
                                    ->columnSpanFull(),

                                // Content and Settings
                                Forms\Components\KeyValue::make('content')
                                    ->label('תוכן')
                                    ->keyLabel('שדה')
                                    ->valueLabel('תוכן')
                                    ->reorderable()
                                    ->columnSpanFull(),

                                Forms\Components\KeyValue::make('settings')
                                    ->label('הגדרות')
                                    ->keyLabel('מפתח')
                                    ->valueLabel('ערך')
                                    ->reorderable()
                                    ->columnSpanFull(),

                                // Status and Expiration
                                Forms\Components\Toggle::make('is_active')
                                    ->label('פעיל')
                                    ->default(true),

                                Forms\Components\DateTimePicker::make('expires_at')
                                    ->label('תאריך תפוגה')
                                    ->nullable()
                                    ->timezone('Asia/Jerusalem'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('כותרת')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event_type')
                    ->label('סוג')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'wedding' => 'חתונה',
                        'bar_mitzvah' => 'בר מצווה',
                        'bat_mitzvah' => 'בת מצווה',
                        'birthday' => 'יום הולדת',
                        'engagement' => 'אירוסין',
                        'other' => 'אחר',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'wedding',
                        'danger' => 'bar_mitzvah',
                        'warning' => 'bat_mitzvah',
                        'primary' => 'birthday',
                        'secondary' => 'engagement',
                        'gray' => 'other',
                    ]),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('תאריך')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('venue_name')
                    ->label('מקום')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rsvp_responses_count')
                    ->label('אישורי הגעה')
                    ->counts('rsvpResponses')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('צפיות')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('פעיל'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('נוצר ב')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->label('סוג אירוע')
                    ->options([
                        'wedding' => 'חתונה',
                        'bar_mitzvah' => 'בר מצווה',
                        'bat_mitzvah' => 'בת מצווה',
                        'birthday' => 'יום הולדת',
                        'engagement' => 'אירוסין',
                        'other' => 'אחר',
                    ]),

                Tables\Filters\Filter::make('event_date')
                    ->label('תאריך אירוע')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('מתאריך'),
                        Forms\Components\DatePicker::make('until')
                            ->label('עד תאריך'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('event_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('סטטוס')
                    ->boolean()
                    ->trueLabel('פעיל')
                    ->falseLabel('לא פעיל')
                    ->placeholder('הכל'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('צפייה'),
                    Tables\Actions\EditAction::make()
                        ->label('עריכה'),
                    Tables\Actions\Action::make('preview')
                        ->label('תצוגה מקדימה')
                        ->url(fn(Invitation $record): string => route('invitations.preview', $record))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\Action::make('copy_link')
                        ->label('העתק קישור')
                        ->icon('heroicon-o-link')
                        ->action(fn(Invitation $record) => "navigator.clipboard.writeText('" . route('invitations.show', $record->slug) . "')"),
                    Tables\Actions\DeleteAction::make()
                        ->label('מחיקה'),
                ]),
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'view' => Pages\ViewInvitation::route('/{record}'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['rsvpResponses']);
    }
}
