<?php
namespace App\Http\ViewComposers;

use App\Models\CompanySetting;
use App\Models\SimpinRule;
use Illuminate\View\View;

class Navigation
{
    public function compose(View $view)
    {
        $settings = CompanySetting::get()->pluck('value','id');
        $view->with('settings', $settings);
    }
}
