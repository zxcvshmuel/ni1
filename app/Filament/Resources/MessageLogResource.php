<?php

namespace App\Filament\Resources;

use App\Models\MessageLog;
use App\Filament\Resources\MessageLogResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageLogResource extends Resource
{
    protected static ?string $model = MessageLog::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    
    protected static ?string $navigationGroup = 'תקשורת';
    
    protected static ?int $navigationSort = 8;
    
    public static function getModelLabel(): string
    {
        return 'תיעוד הודעה';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'תיעוד הודעות';
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
                            ->preload(),

                        Forms\Components\Select::make('message_id')
                            ->label('תבנית הודעה')
                            ->relationship('automatedMessage', 'name->he')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('recipient')
                            ->label('נמען')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('סוג')
                            ->required()
                            ->options([
                                'email' => 'דוא״ל',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                            ]),

                        Forms\Components\Select::make('status')
                            ->label('סטטוס')
                            ->required()
                            ->options([
                                'pending' => 'ממתין',
                                'sent' => 'נשלח',
                                'failed' => 'נכשל',
                            ]),

                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('נשלח ב')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invitation.title')
                    ->label('הזמנה')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('automatedMessage.name.he')
                    ->label('תבנית')
                    ->sortable(),

                Tables\Columns\TextColumn::make('recipient')
                    ->label('נמען')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('סוג')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'email' => 'דוא״ל',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'email',
                        'success' => 'whatsapp',
                        'warning' => 'sms',
                    ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'ממתין',
                        'sent' => 'נשלח',
                        'failed' => 'נכשל',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('נשלח ב')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invitation')
                    ->label('הזמנה')
                    ->relationship('invitation', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('סוג')
                    ->options([
                        'email' => 'דוא״ל',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('סטטוס')
                    ->options([
                        'pending' => 'ממתין',
                        'sent' => 'נשלח',
                        'failed' => 'נכשל',
                    ]),

                Tables\Filters\Filter::make('sent_at')
                    ->label('תאריך שליחה')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('צפייה'),
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
            'index' => Pages\ListMessageLogs::route('/'),
            'view' => Pages\ViewMessageLog::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['invitation', 'automatedMessage']);
    }
}