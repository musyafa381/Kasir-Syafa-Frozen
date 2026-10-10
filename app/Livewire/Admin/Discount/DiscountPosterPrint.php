<?php

namespace App\Livewire\Admin\Discount;

use App\Models\Discount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Cetak Poster & Story Promo - Syafa Frozen Food')]
class DiscountPosterPrint extends Component
{
    public Discount $discount;

    public function mount(int $id): void
    {
        $this->discount = Discount::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.discount.discount-poster-print');
    }
}
