<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\SiteSettingsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.seo-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav.seo');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.seo_settings.title');
    }

    public function getTitle(): string
    {
        return __('admin.seo_settings.title');
    }

    public function mount(): void
    {
        $formData = [];

        $settings = SiteSetting::all();

        foreach ($settings as $setting) {
            $fieldName = $setting->group . '__' . $setting->key;
            $formData[$fieldName] = $setting->value;
        }

        $this->form->fill($formData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('seo_settings')
                    ->tabs([
                        $this->generalTab(),
                        $this->metaTab(),
                        $this->ogTab(),
                        $this->twitterTab(),
                        $this->analyticsTab(),
                        $this->schemaTab(),
                        $this->advancedTab(),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    protected function generalTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.general'))
            ->schema([
                Forms\Components\TextInput::make('general__site_name')
                    ->label(__('admin.seo_settings.site_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('general__tagline')
                    ->label(__('admin.seo_settings.tagline'))
                    ->maxLength(255),

                Forms\Components\Textarea::make('general__header_subtitle')
                    ->label(__('admin.seo_settings.header_subtitle'))
                    ->helperText(__('admin.seo_settings.header_subtitle_help'))
                    ->rows(2)
                    ->maxLength(500),

                Forms\Components\FileUpload::make('general__site_logo')
                    ->label(__('admin.seo_settings.site_logo'))
                    ->image()
                    ->directory('seo')
                    ->disk('public'),

                Forms\Components\FileUpload::make('general__favicon')
                    ->label(__('admin.seo_settings.favicon'))
                    ->helperText(__('admin.seo_settings.favicon_help'))
                    ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml', 'image/vnd.microsoft.icon'])
                    ->directory('seo')
                    ->disk('public'),

                Forms\Components\FileUpload::make('general__apple_touch_icon')
                    ->label(__('admin.seo_settings.apple_touch_icon'))
                    ->image()
                    ->directory('seo')
                    ->disk('public'),
            ]);
    }

    protected function metaTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.meta'))
            ->schema([
                Forms\Components\TextInput::make('meta__title_template')
                    ->label(__('admin.seo_settings.title_template'))
                    ->helperText(__('admin.seo_settings.title_template_help'))
                    ->maxLength(255),

                Forms\Components\Textarea::make('meta__default_description')
                    ->label(__('admin.seo_settings.default_description'))
                    ->rows(3)
                    ->maxLength(500),

                Forms\Components\Textarea::make('meta__default_keywords')
                    ->label(__('admin.seo_settings.default_keywords'))
                    ->rows(2)
                    ->maxLength(500),

                Forms\Components\TextInput::make('meta__meta_author')
                    ->label(__('admin.seo_settings.meta_author'))
                    ->maxLength(255),

                Forms\Components\Toggle::make('meta__robots_index')
                    ->label(__('admin.seo_settings.robots_index'))
                    ->default(true),

                Forms\Components\Toggle::make('meta__robots_follow')
                    ->label(__('admin.seo_settings.robots_follow'))
                    ->default(true),
            ]);
    }

    protected function ogTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.og'))
            ->schema([
                Forms\Components\FileUpload::make('og__default_image')
                    ->label(__('admin.seo_settings.og_default_image'))
                    ->helperText(__('admin.seo_settings.og_default_image_help'))
                    ->image()
                    ->directory('seo')
                    ->disk('public'),

                Forms\Components\TextInput::make('og__site_name')
                    ->label(__('admin.seo_settings.og_site_name'))
                    ->helperText(__('admin.seo_settings.og_site_name_help'))
                    ->maxLength(255),

                Forms\Components\Select::make('og__type')
                    ->label(__('admin.seo_settings.og_type'))
                    ->options([
                        'website' => 'website',
                        'article' => 'article',
                    ]),

                Forms\Components\TextInput::make('og__locale')
                    ->label(__('admin.seo_settings.og_locale'))
                    ->maxLength(10),
            ]);
    }

    protected function twitterTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.twitter'))
            ->schema([
                Forms\Components\Select::make('twitter__card_type')
                    ->label(__('admin.seo_settings.twitter_card_type'))
                    ->options([
                        'summary' => 'summary',
                        'summary_large_image' => 'summary_large_image',
                    ]),

                Forms\Components\TextInput::make('twitter__site_handle')
                    ->label(__('admin.seo_settings.twitter_site_handle'))
                    ->prefix('@')
                    ->maxLength(50),

                Forms\Components\FileUpload::make('twitter__default_image')
                    ->label(__('admin.seo_settings.twitter_default_image'))
                    ->image()
                    ->directory('seo')
                    ->disk('public'),
            ]);
    }

    protected function analyticsTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.analytics'))
            ->schema([
                Forms\Components\Section::make('Verification')
                    ->schema([
                        Forms\Components\TextInput::make('analytics__google_search_console')
                            ->label(__('admin.seo_settings.google_search_console'))
                            ->helperText(__('admin.seo_settings.google_search_console_help'))
                            ->maxLength(255),

                        Forms\Components\TextInput::make('analytics__bing_webmaster')
                            ->label(__('admin.seo_settings.bing_webmaster'))
                            ->helperText(__('admin.seo_settings.bing_webmaster_help'))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Analytics')
                    ->schema([
                        Forms\Components\TextInput::make('analytics__ga4_id')
                            ->label(__('admin.seo_settings.ga4_id'))
                            ->helperText(__('admin.seo_settings.ga4_id_help'))
                            ->maxLength(20),

                        Forms\Components\TextInput::make('analytics__gtm_id')
                            ->label(__('admin.seo_settings.gtm_id'))
                            ->helperText(__('admin.seo_settings.gtm_id_help'))
                            ->maxLength(20),

                        Forms\Components\TextInput::make('analytics__facebook_pixel_id')
                            ->label(__('admin.seo_settings.facebook_pixel_id'))
                            ->maxLength(30),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Custom Scripts')
                    ->schema([
                        Forms\Components\Textarea::make('analytics__custom_head_scripts')
                            ->label(__('admin.seo_settings.custom_head_scripts'))
                            ->helperText(__('admin.seo_settings.custom_head_scripts_help'))
                            ->rows(5),

                        Forms\Components\Textarea::make('analytics__custom_body_scripts')
                            ->label(__('admin.seo_settings.custom_body_scripts'))
                            ->helperText(__('admin.seo_settings.custom_body_scripts_help'))
                            ->rows(5),

                        Forms\Components\Textarea::make('analytics__custom_footer_scripts')
                            ->label(__('admin.seo_settings.custom_footer_scripts'))
                            ->helperText(__('admin.seo_settings.custom_footer_scripts_help'))
                            ->rows(5),
                    ]),
            ]);
    }

    protected function schemaTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.schema'))
            ->schema([
                Forms\Components\TextInput::make('schema__org_name')
                    ->label(__('admin.seo_settings.org_name'))
                    ->maxLength(255),

                Forms\Components\FileUpload::make('schema__org_logo')
                    ->label(__('admin.seo_settings.org_logo'))
                    ->image()
                    ->directory('seo')
                    ->disk('public'),

                Forms\Components\TextInput::make('schema__org_url')
                    ->label(__('admin.seo_settings.org_url'))
                    ->url()
                    ->maxLength(255),

                Forms\Components\TextInput::make('schema__contact_email')
                    ->label(__('admin.seo_settings.contact_email'))
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('schema__contact_phone')
                    ->label(__('admin.seo_settings.contact_phone'))
                    ->tel()
                    ->maxLength(20),
            ]);
    }

    protected function advancedTab(): Forms\Components\Tabs\Tab
    {
        return Forms\Components\Tabs\Tab::make(__('admin.seo_settings.tabs.advanced'))
            ->schema([
                Forms\Components\Textarea::make('advanced__robots_txt')
                    ->label(__('admin.seo_settings.robots_txt'))
                    ->rows(8),

                Forms\Components\TextInput::make('advanced__canonical_prefix')
                    ->label(__('admin.seo_settings.canonical_prefix'))
                    ->helperText(__('admin.seo_settings.canonical_prefix_help'))
                    ->url()
                    ->maxLength(255),

                Forms\Components\Toggle::make('advanced__trailing_slash')
                    ->label(__('admin.seo_settings.trailing_slash')),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $fieldName => $value) {
            if (!str_contains($fieldName, '__')) {
                continue;
            }

            [$group, $key] = explode('__', $fieldName, 2);

            // Convert toggle booleans to string '1'/'0'
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }

            SiteSetting::setValue($group, $key, $value);
        }

        Notification::make()
            ->title(__('admin.seo_settings.saved'))
            ->success()
            ->send();
    }

    public function clearCache(): void
    {
        app(SiteSettingsService::class)->clearCache();

        Notification::make()
            ->title(__('admin.seo_settings.cache_cleared'))
            ->success()
            ->send();
    }
}
