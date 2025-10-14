<?php

namespace App\Utils;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;

class PerformsAddsToCart
{

    protected array $data = [];
    protected ?Model $model; //or purchasable


    /**
     * Create a new class instance.
     */
    public function __construct(private Cart $cart, private ResolvesPurchasable $resolver, private int $quantity)
    {

    }

    private function setNumbersData(): void
    {

        $this->data['quantity'] = $this->quantity;
        $this->data['total'] = $this->model->price * $this->quantity;
        $this->data['total_with_taxes'] = $this->model->priceWithTaxes() * $this->quantity;
        $this->data['computed_taxes'] = $this->model->computedTaxes() * $this->quantity;

    }

    private function setPurchasableData()
    {
        $this->data = $this->model->dataforCart();
    }

    private function getData(): array
    {
        return $this->data;
    }

    public function handle(): CartItem
    {
        try {
            $this->model = $this->resolver->resolve();
            $this->setPurchasableData();
            $this->setNumbersData();
            return $this->cart->addOrUpdateItem($this->getData());
        } catch (BindingResolutionException $e) {
            throw $e;
        }
    }




}
