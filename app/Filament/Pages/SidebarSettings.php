<?php

namespace App\Filament\Pages;

use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SidebarSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.sidebar-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.appearance');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.sidebar_settings.title');
    }

    public function getTitle(): string
    {
        return __('admin.sidebar_settings.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'north_provinces' => Province::where('region', 'north')
                ->where('show_in_left_sidebar', true)
                ->pluck('id')
                ->toArray(),
            'central_provinces' => Province::where('region', 'central')
                ->where('show_in_left_sidebar', true)
                ->pluck('id')
                ->toArray(),
            'south_provinces' => Province::where('region', 'south')
                ->where('show_in_left_sidebar', true)
                ->pluck('id')
                ->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.regions.north'))
                    ->description(__('admin.sidebar_settings.description'))
                    ->schema([
                        Forms\Components\CheckboxList::make('north_provinces')
                            ->label('')
                            ->options(
                                Province::where('region', 'north')
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                            )
                            ->columns(3),
                    ]),

                Forms\Components\Section::make(__('admin.regions.central'))
                    ->schema([
                        Forms\Components\CheckboxList::make('central_provinces')
                            ->label('')
                            ->options(
                                Province::where('region', 'central')
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                            )
                            ->columns(3),
                    ]),

                Forms\Components\Section::make(__('admin.regions.south'))
                    ->schema([
                        Forms\Components\CheckboxList::make('south_provinces')
                            ->label('')
                            ->options(
                                Province::where('region', 'south')
                                    ->where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('name', 'id')
                            )
                            ->columns(3),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Reset all provinces
        Province::query()->update(['show_in_left_sidebar' => false]);

        // Update selected provinces
        $allSelected = array_merge(
            $data['north_provinces'] ?? [],
            $data['central_provinces'] ?? [],
            $data['south_provinces'] ?? []
        );

        if (!empty($allSelected)) {
            Province::whereIn('id', $allSelected)
                ->update(['show_in_left_sidebar' => true]);
        }

        Notification::make()
            ->title(__('admin.sidebar_settings.saved'))
            ->success()
            ->send();
    }
}
