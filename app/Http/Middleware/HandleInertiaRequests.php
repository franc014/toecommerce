<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use App\Models\Menu;
use App\Settings\CompanySettings;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function setUpCart(Request $request)
    {

        if ($request->hasCookie('cart')) {
            $uiCartId =   $request->cookie('cart');
            $cart = Cart::byUICartId($uiCartId)->first();
        } else {
            $cart = Cart::create([
                'ui_cart_id' => Str::uuid7(),
            ]);
            Cookie::queue('cart', $cart->ui_cart_id, 60 * 24 * 30);
        }

        return $cart->toArray();
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $mainMenu = Menu::byName('main');
        $footerMenu = Menu::byName('footer');
        $legalMenu = Menu::byName('legal');

        $company = app(CompanySettings::class)->toArray();

        return [
            ...parent::share($request),
            'mainMenu' => $mainMenu,
            'footerMenu' => $footerMenu,
            'legalMenu' => $legalMenu,
            'company' => $company,
            'name' => config('app.name'),
            'shoppingCart' => $this->setUpCart($request),
            'auth' => [
                'user' => $request->user(),
            ],
        ];
    }
}
