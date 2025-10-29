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
use Illuminate\Database\Eloquent\Builder;

class UserInfoEntryResource extends Resource
{
    protected static ?string $model = UserInfoEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'first_name';

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
            'create' => CreateUserInfoEntry::route('/create'),
            'edit' => EditUserInfoEntry::route('/{record}/edit'),
        ];
    }
}
