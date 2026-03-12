<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Filament\Forms\Components\SharedFields;
use Filament\Schemas\Schema;

class MenuForm
{
    use SharedFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(
                self::titleAndSlugFields()
            );
    }
}
