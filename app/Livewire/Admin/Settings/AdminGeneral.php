<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\Ai\AiProviderRegistry;
use Illuminate\Support\Str;
use App\Models\CategoryField;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AdminGeneral extends Component
{
    public bool $ai_enabled = true;

    public string $generation_mode = 'manual';

    public int $manual_timeframe_hours = 24;

    public int $min_word_count = 400;

    public int $max_word_count = 1200;

    /** @var array<int, array{id: string, provider: string, model: string, label: string, is_default: bool}> */
    public array $contentProviders = [];

    public string $ai_temperature = '0.7';

    public int $ai_max_tokens = 1200;

    public int $ai_timeout_seconds = 30;

    public int $ai_rate_limit_per_user_per_day = 20;

    // Tab state for the admin page: 'content' or 'fields'
    public string $activeTab = 'content';

    // Category fields management (moved from AdminCategoryFields)
    public bool $showAddModal = false;
    public bool $showEditModal = false;

    public ?int $editingFieldId = null;

    public string $field_key = '';
    public string $field_label = '';
    public string $field_data_type = CategoryField::TYPE_TEXT;
    public bool $field_is_required = false;
    public string $field_optionsLines = '';
    public string $field_validationJson = '';

    public function mount(AiProviderRegistry $registry): void
    {
        $this->generation_mode = (string) Setting::getValue('content.generation_mode', 'manual');
        if (! in_array($this->generation_mode, ['manual', 'ai'], true)) {
            $this->generation_mode = 'manual';
        }
        $this->ai_enabled = (bool) Setting::getValue('ai.enabled', true);
        $this->manual_timeframe_hours = (int) Setting::getValue('content.manual_timeframe_hours', 24);
        $this->min_word_count = (int) Setting::getValue('content.min_word_count', 400);
        $this->max_word_count = (int) Setting::getValue('content.max_word_count', 1200);
        $this->contentProviders = $registry->contentProviders();
        if ($this->contentProviders === []) {
            $this->contentProviders = [$this->blankProviderRow('openai')];
        }
        $this->ai_temperature = (string) Setting::getValue('ai.temperature', '0.7');
        $this->ai_max_tokens = (int) Setting::getValue('ai.max_tokens', 1200);
        $this->ai_timeout_seconds = (int) Setting::getValue('ai.timeout_seconds', 30);
        $this->ai_rate_limit_per_user_per_day = (int) Setting::getValue('ai.rate_limit_per_user_per_day', 20);
    }

    public function openFieldsTab(): void
    {
        $this->activeTab = 'fields';
    }

    public function openAddField(): void
    {
        $this->reset(['field_key','field_label','field_data_type','field_is_required','field_optionsLines','field_validationJson','editingFieldId']);
        $this->showAddModal = true;
        $this->activeTab = 'fields';
    }

    public function saveNewField(): void
    {
        $this->validate([
            'field_key' => ['required','string','max:120','regex:/^[a-z][a-z0-9_\-]*$/', Rule::unique('category_fields','key')],
            'field_label' => ['required','string','max:255'],
            'field_data_type' => ['required', Rule::in($this->dataTypeOptions())],
        ]);

        $options = $this->parseOptionsLines($this->field_optionsLines);

        CategoryField::query()->create([
            'key' => $this->field_key,
            'label' => $this->field_label,
            'data_type' => $this->field_data_type,
            'is_required' => $this->field_is_required,
            'options' => $options,
            'validation' => null,
        ]);

        $this->showAddModal = false;
        session()->flash('status', 'Field created.');
    }

    public function openEditField(int $fieldId): void
    {
        $field = CategoryField::query()->findOrFail($fieldId);
        $this->editingFieldId = $field->id;
        $this->field_key = $field->key;
        $this->field_label = $field->label;
        $this->field_data_type = $field->data_type;
        $this->field_is_required = (bool) $field->is_required;
        $this->field_optionsLines = $field->options ? implode("\n", $field->options) : '';
        $this->field_validationJson = $field->validation ? json_encode($field->validation, JSON_PRETTY_PRINT) : '';
        $this->showEditModal = true;
        $this->activeTab = 'fields';
    }

    public function saveEditField(): void
    {
        $this->validate([
            'editingFieldId' => ['required','integer'],
            'field_label' => ['required','string','max:255'],
            'field_data_type' => ['required', Rule::in($this->dataTypeOptions())],
        ]);

        $field = CategoryField::query()->findOrFail($this->editingFieldId);
        $options = $this->parseOptionsLines($this->field_optionsLines);

        $field->update([
            'label' => $this->field_label,
            'data_type' => $this->field_data_type,
            'is_required' => $this->field_is_required,
            'options' => $options,
            'validation' => null,
        ]);

        $this->showEditModal = false;
        session()->flash('status', 'Field updated.');
    }

    public function deleteField(int $fieldId): void
    {
        $field = CategoryField::query()->findOrFail($fieldId);
        $field->delete();
        session()->flash('status', 'Field deleted.');
    }

    protected function dataTypeOptions(): array
    {
        return [
            CategoryField::TYPE_SINGLE_SELECT,
            CategoryField::TYPE_MULTI_SELECT,
            CategoryField::TYPE_NUMBER,
            CategoryField::TYPE_TEXT,
            CategoryField::TYPE_TEXTAREA,
            CategoryField::TYPE_BOOLEAN,
            CategoryField::TYPE_DATE,
        ];
    }

    protected function parseOptionsLines(string $raw): ?array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: [])));
        return $lines === [] ? null : $lines;
    }

    #[Computed]
    public function fields()
    {
        return CategoryField::query()->orderBy('key')->get();
    }

    #[Computed]
    public function providerRegistry(): array
    {
        return app(AiProviderRegistry::class)->all();
    }

    protected function rules(): array
    {
        $providerKeys = implode(',', array_keys(config('ai.providers', [])));

        return [
            'ai_enabled' => ['required', 'boolean'],
            'generation_mode' => ['required', 'in:manual,ai'],
            'manual_timeframe_hours' => ['required', 'integer', 'min:1', 'max:8760'],
            'min_word_count' => ['required', 'integer', 'min:50', 'max:50000'],
            'max_word_count' => ['required', 'integer', 'min:50', 'max:50000', 'gte:min_word_count'],
            'contentProviders' => ['array'],
            'contentProviders.*.provider' => ['required', 'string', "in:{$providerKeys}"],
            'contentProviders.*.model' => ['required', 'string', 'max:150'],
            'contentProviders.*.label' => ['nullable', 'string', 'max:120'],
            'contentProviders.*.is_default' => ['boolean'],
            'ai_temperature' => ['required', 'numeric', 'between:0,2'],
            'ai_max_tokens' => ['required', 'integer', 'min:50', 'max:16000'],
            'ai_timeout_seconds' => ['required', 'integer', 'min:5', 'max:180'],
            'ai_rate_limit_per_user_per_day' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }

    public function addContentProvider(): void
    {
        $firstConfigured = collect($this->providerRegistry)
            ->filter(fn (array $provider) => $provider['configured'])
            ->keys()
            ->first() ?? array_key_first($this->providerRegistry) ?? 'openai';

        $this->contentProviders[] = $this->blankProviderRow($firstConfigured);
    }

    public function removeContentProvider(int $index): void
    {
        if (! isset($this->contentProviders[$index])) {
            return;
        }

        $wasDefault = (bool) ($this->contentProviders[$index]['is_default'] ?? false);
        unset($this->contentProviders[$index]);
        $this->contentProviders = array_values($this->contentProviders);

        if ($this->contentProviders === []) {
            $this->contentProviders = [$this->blankProviderRow('openai')];

            return;
        }

        if ($wasDefault) {
            $this->contentProviders[0]['is_default'] = true;
        }
    }

    public function setDefaultContentProvider(int $index): void
    {
        foreach ($this->contentProviders as $i => $row) {
            $this->contentProviders[$i]['is_default'] = $i === $index;
        }
    }

    public function updatedContentProviders($value, string $name): void
    {
        if (! str_ends_with($name, '.provider')) {
            return;
        }

        preg_match('/contentProviders\.(\d+)\.provider/', $name, $matches);
        $index = (int) ($matches[1] ?? -1);

        if (! isset($this->contentProviders[$index])) {
            return;
        }

        $provider = $this->contentProviders[$index]['provider'];
        $suggested = app(AiProviderRegistry::class)->suggestedModels($provider);

        if ($suggested !== [] && ($this->contentProviders[$index]['model'] ?? '') === '') {
            $this->contentProviders[$index]['model'] = $suggested[0];
        }
    }

    public function save(AiProviderRegistry $registry): void
    {
        $this->validate();
        $normalized = $registry->normalizeContentProviders($this->contentProviders);

        if ($this->ai_enabled && $this->generation_mode === 'ai') {
            if ($normalized === []) {
                $this->addError('contentProviders', __('Add at least one AI provider and model.'));

                return;
            }

            $defaultCount = collect($normalized)->where('is_default', true)->count();
            if ($defaultCount !== 1) {
                $this->addError('contentProviders', __('Select exactly one default provider.'));

                return;
            }

            $default = collect($normalized)->firstWhere('is_default', true);
            if (! $registry->isConfigured((string) $default['provider'])) {
                $this->addError('contentProviders', __('The default provider must have an API key in .env.'));

                return;
            }
        }

        Setting::setValue('ai.enabled', $this->ai_enabled, 'boolean');
        Setting::setValue('content.generation_mode', $this->generation_mode, 'string');
        Setting::setValue('content.manual_timeframe_hours', $this->manual_timeframe_hours, 'integer');
        Setting::setValue('content.min_word_count', $this->min_word_count, 'integer');
        Setting::setValue('content.max_word_count', $this->max_word_count, 'integer');
        Setting::setValue('ai.content_providers', $normalized, 'json');
        Setting::setValue('ai.temperature', $this->ai_temperature, 'string');
        Setting::setValue('ai.max_tokens', $this->ai_max_tokens, 'integer');
        Setting::setValue('ai.timeout_seconds', $this->ai_timeout_seconds, 'integer');
        Setting::setValue('ai.rate_limit_per_user_per_day', $this->ai_rate_limit_per_user_per_day, 'integer');

        $defaultRow = collect($normalized)->firstWhere('is_default', true);
        if ($defaultRow) {
            Setting::setValue('ai.provider', $defaultRow['provider'], 'string');
            Setting::setValue('ai.model', $defaultRow['model'], 'string');
        }

        $this->contentProviders = $normalized;

        session()->flash('status', __('Content settings saved.'));
    }

    /**
     * @return array{id: string, provider: string, model: string, label: string, is_default: bool}
     */
    protected function blankProviderRow(string $provider): array
    {
        $suggested = app(AiProviderRegistry::class)->suggestedModels($provider);

        return [
            'id' => (string) Str::uuid(),
            'provider' => $provider,
            'model' => $suggested[0] ?? '',
            'label' => '',
            'is_default' => $this->contentProviders === [],
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.admin-general');
    }
}
