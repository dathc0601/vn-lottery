<?php

namespace App\Filament\Pages;

use App\Models\FooterColumn;
use App\Models\FooterLink;
use App\Models\SiteSetting;
use App\Services\FooterService;
use App\Services\SiteSettingsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class FooterManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-right';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.footer-manager';

    // Column/link form
    public ?array $data = [];
    public ?int $editingColumnId = null;
    public ?int $editingLinkId = null;
    public ?int $addingLinkToColumnId = null;
    public string $formMode = 'column'; // 'column' or 'link'

    // Text settings form
    public ?array $textData = [];

    // UI state
    public bool $showPreview = false;
    public Collection $columns;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.footer_manager.title');
    }

    public function getTitle(): string
    {
        return __('admin.footer_manager.title');
    }

    public function mount(): void
    {
        $this->loadColumns();
        $this->resetForm();
        $this->loadTextSettings();
    }

    public function loadColumns(): void
    {
        $this->columns = FooterColumn::with(['links' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function loadTextSettings(): void
    {
        $this->textData = [
            'footer__about_text' => SiteSetting::getValue('footer', 'about_text', ''),
            'footer__copyright_template' => SiteSetting::getValue('footer', 'copyright_template', '© {year} {site_name}'),
            'footer__disclaimer_text' => SiteSetting::getValue('footer', 'disclaimer_text', ''),
        ];
    }

    public function resetForm(): void
    {
        $this->editingColumnId = null;
        $this->editingLinkId = null;
        $this->addingLinkToColumnId = null;
        $this->formMode = 'column';
        $this->form->fill([
            'title' => '',
            'type' => 'links',
            'is_active' => true,
            // Link fields
            'label' => '',
            'link_type' => 'route',
            'route_name' => '',
            'url' => '',
            'open_in_new_tab' => false,
            'footer_column_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Column fields
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.footer_manager.fields.title'))
                    ->required()
                    ->maxLength(255)
                    ->visible(fn () => $this->formMode === 'column'),

                Forms\Components\Select::make('type')
                    ->label(__('admin.footer_manager.fields.type'))
                    ->options([
                        'links' => __('admin.footer_manager.fields.column_type_links'),
                        'about' => __('admin.footer_manager.fields.column_type_about'),
                        'info' => __('admin.footer_manager.fields.column_type_info'),
                    ])
                    ->required()
                    ->visible(fn () => $this->formMode === 'column'),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.footer_manager.fields.is_active'))
                    ->default(true)
                    ->visible(fn () => $this->formMode === 'column'),

                // Link fields
                Forms\Components\TextInput::make('label')
                    ->label(__('admin.footer_manager.fields.label'))
                    ->required()
                    ->maxLength(255)
                    ->visible(fn () => $this->formMode === 'link'),

                Forms\Components\Select::make('link_type')
                    ->label(__('admin.footer_manager.fields.link_type'))
                    ->options([
                        'route' => __('admin.footer_manager.fields.link_type_route'),
                        'url' => __('admin.footer_manager.fields.link_type_url'),
                    ])
                    ->required()
                    ->reactive()
                    ->visible(fn () => $this->formMode === 'link'),

                Forms\Components\TextInput::make('route_name')
                    ->label(__('admin.footer_manager.fields.route_name'))
                    ->visible(fn (callable $get) => $this->formMode === 'link' && $get('link_type') === 'route')
                    ->helperText('e.g., home, xsmb, results.book'),

                Forms\Components\TextInput::make('url')
                    ->label(__('admin.footer_manager.fields.url'))
                    ->visible(fn (callable $get) => $this->formMode === 'link' && $get('link_type') === 'url')
                    ->url(),

                Forms\Components\Toggle::make('open_in_new_tab')
                    ->label(__('admin.footer_manager.fields.open_in_new_tab'))
                    ->default(false)
                    ->visible(fn () => $this->formMode === 'link'),

                Forms\Components\Toggle::make('link_is_active')
                    ->label(__('admin.footer_manager.fields.is_active'))
                    ->default(true)
                    ->visible(fn () => $this->formMode === 'link'),
            ])
            ->statePath('data');
    }

    // --- Column CRUD ---

    public function editColumn(int $id): void
    {
        $column = FooterColumn::find($id);
        if (!$column) {
            Notification::make()->title(__('admin.footer_manager.not_found'))->danger()->send();
            return;
        }

        $this->editingColumnId = $id;
        $this->editingLinkId = null;
        $this->addingLinkToColumnId = null;
        $this->formMode = 'column';
        $this->form->fill([
            'title' => $column->title,
            'type' => $column->type,
            'is_active' => $column->is_active,
        ]);
    }

    public function deleteColumn(int $id): void
    {
        $column = FooterColumn::find($id);
        if ($column) {
            $column->delete();
            Notification::make()->title(__('admin.footer_manager.deleted_column'))->success()->send();
            $this->loadColumns();
            $this->resetForm();
        }
    }

    public function toggleColumnActive(int $id): void
    {
        $column = FooterColumn::find($id);
        if ($column) {
            $column->update(['is_active' => !$column->is_active]);
            $this->loadColumns();
            Notification::make()
                ->title($column->is_active ? __('admin.footer_manager.activated') : __('admin.footer_manager.deactivated'))
                ->success()
                ->send();
        }
    }

    public function moveColumnUp(int $id): void
    {
        $column = FooterColumn::find($id);
        if (!$column) return;

        $prev = FooterColumn::where('sort_order', '<', $column->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $tmp = $column->sort_order;
            $column->sort_order = $prev->sort_order;
            $prev->sort_order = $tmp;
            $column->save();
            $prev->save();
            $this->loadColumns();
        }
    }

    public function moveColumnDown(int $id): void
    {
        $column = FooterColumn::find($id);
        if (!$column) return;

        $next = FooterColumn::where('sort_order', '>', $column->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $tmp = $column->sort_order;
            $column->sort_order = $next->sort_order;
            $next->sort_order = $tmp;
            $column->save();
            $next->save();
            $this->loadColumns();
        }
    }

    // --- Link CRUD ---

    public function addLinkToColumn(int $columnId): void
    {
        $this->editingColumnId = null;
        $this->editingLinkId = null;
        $this->addingLinkToColumnId = $columnId;
        $this->formMode = 'link';
        $this->form->fill([
            'label' => '',
            'link_type' => 'route',
            'route_name' => '',
            'url' => '',
            'open_in_new_tab' => false,
            'link_is_active' => true,
        ]);
    }

    public function editLink(int $id): void
    {
        $link = FooterLink::find($id);
        if (!$link) {
            Notification::make()->title(__('admin.footer_manager.not_found'))->danger()->send();
            return;
        }

        $this->editingColumnId = null;
        $this->editingLinkId = $id;
        $this->addingLinkToColumnId = $link->footer_column_id;
        $this->formMode = 'link';
        $this->form->fill([
            'label' => $link->label,
            'link_type' => $link->type,
            'route_name' => $link->route_name,
            'url' => $link->url,
            'open_in_new_tab' => $link->open_in_new_tab,
            'link_is_active' => $link->is_active,
        ]);
    }

    public function deleteLink(int $id): void
    {
        $link = FooterLink::find($id);
        if ($link) {
            $link->delete();
            Notification::make()->title(__('admin.footer_manager.deleted_link'))->success()->send();
            $this->loadColumns();
            $this->resetForm();
        }
    }

    public function toggleLinkActive(int $id): void
    {
        $link = FooterLink::find($id);
        if ($link) {
            $link->update(['is_active' => !$link->is_active]);
            $this->loadColumns();
            Notification::make()
                ->title($link->is_active ? __('admin.footer_manager.activated') : __('admin.footer_manager.deactivated'))
                ->success()
                ->send();
        }
    }

    public function moveLinkUp(int $id): void
    {
        $link = FooterLink::find($id);
        if (!$link) return;

        $prev = FooterLink::where('footer_column_id', $link->footer_column_id)
            ->where('sort_order', '<', $link->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $tmp = $link->sort_order;
            $link->sort_order = $prev->sort_order;
            $prev->sort_order = $tmp;
            $link->save();
            $prev->save();
            $this->loadColumns();
        }
    }

    public function moveLinkDown(int $id): void
    {
        $link = FooterLink::find($id);
        if (!$link) return;

        $next = FooterLink::where('footer_column_id', $link->footer_column_id)
            ->where('sort_order', '>', $link->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $tmp = $link->sort_order;
            $link->sort_order = $next->sort_order;
            $next->sort_order = $tmp;
            $link->save();
            $next->save();
            $this->loadColumns();
        }
    }

    // --- Save ---

    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->formMode === 'column') {
            $this->saveColumn($data);
        } else {
            $this->saveLink($data);
        }

        $this->loadColumns();
        $this->resetForm();
    }

    protected function saveColumn(array $data): void
    {
        $columnData = [
            'title' => $data['title'],
            'type' => $data['type'],
            'is_active' => $data['is_active'],
        ];

        if ($this->editingColumnId) {
            $column = FooterColumn::find($this->editingColumnId);
            $column->update($columnData);
            Notification::make()->title(__('admin.footer_manager.updated_column'))->success()->send();
        } else {
            $columnData['sort_order'] = (FooterColumn::max('sort_order') ?? 0) + 1;
            FooterColumn::create($columnData);
            Notification::make()->title(__('admin.footer_manager.created_column'))->success()->send();
        }
    }

    protected function saveLink(array $data): void
    {
        $linkData = [
            'footer_column_id' => $this->addingLinkToColumnId,
            'label' => $data['label'],
            'type' => $data['link_type'],
            'route_name' => $data['link_type'] === 'route' ? $data['route_name'] : null,
            'url' => $data['link_type'] === 'url' ? $data['url'] : null,
            'open_in_new_tab' => $data['open_in_new_tab'],
            'is_active' => $data['link_is_active'],
        ];

        if ($this->editingLinkId) {
            $link = FooterLink::find($this->editingLinkId);
            $link->update($linkData);
            Notification::make()->title(__('admin.footer_manager.updated_link'))->success()->send();
        } else {
            $linkData['sort_order'] = (FooterLink::where('footer_column_id', $this->addingLinkToColumnId)->max('sort_order') ?? 0) + 1;
            FooterLink::create($linkData);
            Notification::make()->title(__('admin.footer_manager.created_link'))->success()->send();
        }
    }

    // --- Text Settings ---

    public function saveTextSettings(): void
    {
        foreach ($this->textData as $fieldName => $value) {
            if (!str_contains($fieldName, '__')) {
                continue;
            }

            [$group, $key] = explode('__', $fieldName, 2);
            SiteSetting::setValue($group, $key, $value);
        }

        Notification::make()
            ->title(__('admin.footer_manager.text_settings_saved'))
            ->success()
            ->send();
    }

    // --- UI Actions ---

    public function togglePreview(): void
    {
        $this->showPreview = !$this->showPreview;
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function clearCache(): void
    {
        app(FooterService::class)->clearCache();
        app(SiteSettingsService::class)->clearCache();

        Notification::make()
            ->title(__('admin.footer_manager.cache_cleared'))
            ->success()
            ->send();
    }
}
