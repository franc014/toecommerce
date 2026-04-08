<?php

namespace App\Filament\Resources\UserInfoEntries;

use App\Filament\Resources\UserInfoEntries\Pages\CreateUserInfoEntry;
use App\Filament\Resources\UserInfoEntries\Pages\EditUserInfoEntry;
use App\Filament\Resources\UserInfoEntries\Pages\ListUserInfoEntries;
use App\Filament\Resources\UserInfoEntries\Schemas\UserInfoEntryForm;
use App\Filament\Resources\UserInfoEntries\Tables\UserInfoEntriesTable;
use App\Models\UserInfoEntry;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserInfoEntryResource extends Resource
{
    protected static ?string $model = UserInfoEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getModelLabel(): string
    {
        return __('firesources.user_info_entry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('firesources.user_info_entries');
    }

    public static function getNavigationGroup(): UnitEnum|string|null
    {
        return __('firesources.purchases');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'icon-user-pen';
    }

    /*  public static function getNavigationBadge(): ?string
     {

         if (Filament::getCurrentPanel()->getId() === 'customer') {
             return static::getModel()::query()->where('user_id', auth()->user()->id)->count();
         }

         return static::getModel()::query()->count();
     } */

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (Filament::getCurrentPanel()->getId() === 'customer') {
            $query = parent::getEloquentQuery()->where('user_id', auth()->user()->id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return UserInfoEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserInfoEntriesTable::configure($table);
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
            'index' => ListUserInfoEntries::route('/'),
            /* 'create' => CreateUserInfoEntry::route('/create'),
            'edit' => EditUserInfoEntry::route('/{record}/edit'), */
        ];
    }
}
