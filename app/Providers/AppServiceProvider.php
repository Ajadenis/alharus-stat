<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // CSS proyek menata pagination berformat Bootstrap (ul.pagination > li > a/span).
    // Tanpa ini Laravel memakai tampilan Tailwind yang tidak dimuat di proyek ini.
    Paginator::useBootstrapFour();
  }
}
