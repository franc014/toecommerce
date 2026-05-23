<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Schemas\Schema;
use JFA\ToecommerceCore\Filament\Forms\Components\SharedFields;

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
