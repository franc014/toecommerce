<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Filament\Forms\Components\SharedFields;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('firesources.main_information'))
                    ->secondary()
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                        ->label(__('firesources.label'))
                        ->required()
                        ->maxLength(100)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $set('slug', Str::slug($state));
                        }),
                        TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100),
                            TextInput::make('url')
                            ->required(),
                        ]),

                Section::make(__('firesources.sub_menu_items'))
                    ->secondary()
                    ->columns(2)
                    ->schema([
                       Repeater::make('items')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->label(__('firesources.label'))
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                        $set('slug', Str::slug($state));
                                        // $set('url', $get('../../url').'/'.Str::slug($state));
                                    })->maxLength(255),
                                TextInput::make('slug')
                                    ->maxLength(255),
                                TextInput::make('url')
                                    ->required(),

                            ])
                            ->columnSpanFull()
                            ->defaultItems(0)

                    ]),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('label')
                    ->sortable()
                    ->label(__('firesources.label'))
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('firesources.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('firesources.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                //AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                //DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
