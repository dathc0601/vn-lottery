<?php

namespace App\Filament\Pages;

use App\Models\NavigationItem;
use App\Services\NavigationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class NavigationManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.navigation-manager';

    public ?array $data = [];
    public ?int $editingItemId = null;
    public bool $showPreview = false;
    public Collection $items;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.title');
    }

    public function getTitle(): string
    {
        return __('admin.navigation.title');
    }

    public function mount(): void
    {
        $this->loadItems();
        $this->resetForm();
    }

    public function loadItems(): void
    {
        $this->items = NavigationItem::whereNull('parent_id')
            ->with(['allChildren'])
            ->orderBy('sort_order')
            ->get();
    }

    public function resetForm(): void
    {
        $this->editingItemId = null;
        $this->form->fill([
            'title' => '',
            'title_short' => '',
            'type' => 'route',
            'route_name' => '',
            'url' => '',
            'active_pattern' => '',
            'dropdown_type' => 'simple',
            'icon' => '',
            'parent_id' => null,
            'is_active' => true,
            'open_in_new_tab' => false,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.navigation.fields.title'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('title_short')
                    ->label(__('admin.navigation.fields.title_short'))
                    ->maxLength(255)
                    ->helperText(__('admin.navigation.fields.title_short_help')),

                Forms\Components\Select::make('type')
                    ->label(__('admin.navigation.fields.type'))
                    ->options(NavigationItem::getTypeOptions())
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('dropdown_type', 'none')),

                Forms\Components\TextInput::make('route_name')
                    ->label(__('admin.navigation.fields.route_name'))
                    ->visible(fn (callable $get) => $get('type') === 'route')
                    ->helperText('e.g., home, xsmb, results.book'),

                Forms\Components\TextInput::make('url')
                    ->label(__('admin.navigation.fields.url'))
                    ->visible(fn (callable $get) => $get('type') === 'static_link')
                    ->url(),

                Forms\Components\TextInput::make('active_pattern')
                    ->label(__('admin.navigation.fields.active_pattern'))
                    ->visible(fn (callable $get) => in_array($get('type'), ['route', 'static_link']))
                    ->helperText('e.g., xsmb* or home'),

                Forms\Components\Select::make('dropdown_type')
                    ->label(__('admin.navigation.fields.dropdown_type'))
                    ->options(NavigationItem::getDropdownTypeOptions())
                    ->visible(fn (callable $get) => !in_array($get('type'), ['xsmb_days', 'xsmt_days', 'xsmn_days', 'divider'])),

                Forms\Components\TextInput::make('icon')
                    ->label(__('admin.navigation.fields.icon'))
                    ->helperText('Heroicon name without prefix, e.g., home, chart-bar'),

                Forms\Components\Select::make('parent_id')
                    ->label(__('admin.navigation.fields.parent'))
                    ->options(function () {
                        // Only route/static_link items can have children
                        return NavigationItem::whereNull('parent_id')
                            ->whereIn('type', ['route', 'static_link'])
                            ->pluck('title', 'id');
                    })
                    ->searchable()
                    ->placeholder(__('admin.navigation.fields.no_parent')),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.navigation.fields.is_active'))
                    ->default(true),

                Forms\Components\Toggle::make('open_in_new_tab')
                    ->label(__('admin.navigation.fields.open_in_new_tab'))
                    ->default(false),
            ])
            ->statePath('data');
    }

    public function editItem(int $id): void
    {
        $item = NavigationItem::find($id);

        if (!$item) {
            Notification::make()
                ->title(__('admin.navigation.not_found'))
                ->danger()
                ->send();
            return;
        }

        $this->editingItemId = $id;
        $this->form->fill([
            'title' => $item->title,
            'title_short' => $item->title_short,
            'type' => $item->type,
            'route_name' => $item->route_name,
            'url' => $item->url,
            'active_pattern' => $item->active_pattern,
            'dropdown_type' => $item->dropdown_type,
            'icon' => $item->icon,
            'parent_id' => $item->parent_id,
            'is_active' => $item->is_active,
            'open_in_new_tab' => $item->open_in_new_tab,
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Set sort_order for new items
        if (!$this->editingItemId) {
            $data['sort_order'] = NavigationItem::where('parent_id', $data['parent_id'])->max('sort_order') + 1;
        }

        if ($this->editingItemId) {
            $item = NavigationItem::find($this->editingItemId);
            $item->update($data);

            Notification::make()
                ->title(__('admin.navigation.updated'))
                ->success()
                ->send();
        } else {
            NavigationItem::create($data);

            Notification::make()
                ->title(__('admin.navigation.created'))
                ->success()
                ->send();
        }

        $this->loadItems();
        $this->resetForm();
    }

    public function deleteItem(int $id): void
    {
        $item = NavigationItem::find($id);

        if ($item) {
            $item->delete();

            Notification::make()
                ->title(__('admin.navigation.deleted'))
                ->success()
                ->send();

            $this->loadItems();
        }
    }

    public function toggleActive(int $id): void
    {
        $item = NavigationItem::find($id);

        if ($item) {
            $item->update(['is_active' => !$item->is_active]);
            $this->loadItems();

            Notification::make()
                ->title($item->is_active ? __('admin.navigation.activated') : __('admin.navigation.deactivated'))
                ->success()
                ->send();
        }
    }

    public function moveUp(int $id): void
    {
        $item = NavigationItem::find($id);

        if (!$item) return;

        $previousItem = NavigationItem::where('parent_id', $item->parent_id)
            ->where('sort_order', '<', $item->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousItem) {
            $tempOrder = $item->sort_order;
            $item->sort_order = $previousItem->sort_order;
            $previousItem->sort_order = $tempOrder;

            $item->save();
            $previousItem->save();

            $this->loadItems();
        }
    }

    public function moveDown(int $id): void
    {
        $item = NavigationItem::find($id);

        if (!$item) return;

        $nextItem = NavigationItem::where('parent_id', $item->parent_id)
            ->where('sort_order', '>', $item->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextItem) {
            $tempOrder = $item->sort_order;
            $item->sort_order = $nextItem->sort_order;
            $nextItem->sort_order = $tempOrder;

            $item->save();
            $nextItem->save();

            $this->loadItems();
        }
    }

    public function clearCache(): void
    {
        app(NavigationService::class)->clearAllCaches();

        Notification::make()
            ->title(__('admin.navigation.cache_cleared'))
            ->success()
            ->send();
    }

    public function togglePreview(): void
    {
        $this->showPreview = !$this->showPreview;
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function addChildTo(int $parentId): void
    {
        $this->resetForm();
        $this->form->fill([
            'title' => '',
            'title_short' => '',
            'type' => 'route',
            'route_name' => '',
            'url' => '',
            'active_pattern' => '',
            'dropdown_type' => 'simple',
            'icon' => '',
            'parent_id' => $parentId,
            'is_active' => true,
            'open_in_new_tab' => false,
        ]);
    }
}
