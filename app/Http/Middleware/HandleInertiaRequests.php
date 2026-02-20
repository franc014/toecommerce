<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Settings\CompanySettings;
use App\Settings\StorefrontSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Middleware;

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
        $storeFront = app(StorefrontSettings::class)->toArray();

        $discountsDisplayConfig = [
            'show_message' => $storeFront['show_discount_campaign_message'],
            'message' => $storeFront['discount_campaign_message'],
        ];

        return [
            ...parent::share($request),
            'mainMenu' => Inertia::once(fn () => $mainMenu),
            'footerMenu' => Inertia::once(fn () => $footerMenu),
            'legalMenu' => Inertia::once(fn () => $legalMenu),
            'company' => Inertia::once(fn () => $company),
            'name' => Inertia::once(fn () => config('app.name')),
            'shoppingCart' => $request->hasCookie('cart') ? $request->cookie('cart') : null,
            'auth' => [
                'user' => $request->user(),
            ],
            'discountsDisplayConfig' => $discountsDisplayConfig,
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }
}
