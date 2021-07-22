<!DOCTYPE html>
@php
    Illuminate\Support\Facades\Cache::remember('language', 3600 , function() {
        return Modules\Localization\Entities\Language::where('code', session()->get('locale', Config::get('app.locale')))->first();
    });
@endphp
<html @if(Illuminate\Support\Facades\Cache::get('language')->rtl == 1) dir="rtl" class="rtl" @endif >

@php
    if (empty($color_theme)) {
     $css = "background: url('".asset('/public/backEnd/img/body-bg.jpg')."')  no-repeat center; background-size: cover; ";
 } else {
     if (!empty($color_theme->background_type == 'image')) {
         $css = "background: url('" . url($color_theme->background_image) . "')  no-repeat center; background-size: cover; background-attachment: fixed; background-position: top; ";
     } else {
         $css = "background:" . $color_theme->background_color;
     }
 }

@endphp

