<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_section_id',
        'name',
        'key',
        'type',
        'label',
        'placeholder',
        'help_text',
        'options',
        'validation',
        'order',
        'is_active',
        'is_required',
        'css_class',
        'depends_on',
        'depends_value',
        'file_types',
        'max_file_size',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'order' => 'integer',
        'options' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'form_section_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getValidationRules(): array
    {
        $rules = [];
        
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        if ($this->validation) {
            $rules = array_merge($rules, explode('|', $this->validation));
        }

        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'tel':
                $rules[] = 'string';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'file':
                $rules[] = 'file';
                if ($this->max_file_size) {
                    $rules[] = 'max:' . $this->max_file_size;
                }
                if ($this->file_types) {
                    $rules[] = 'mimes:' . str_replace(',', ',', $this->file_types);
                }
                break;
        }

        return $rules;
    }

    public function render(?string $value = null, ?string $oldValue = null): string
    {
        $inputValue = $oldValue ?? $value ?? old($this->key);
        $required = $this->is_required ? 'required' : '';
        $placeholder = $this->placeholder ? "placeholder=\"{$this->placeholder}\"" : '';
        $helpText = $this->help_text ? "<p class=\"mt-1 text-xs text-gray-500\">{$this->help_text}</p>" : '';
        $errorClass = $errors = $this->css_class ?? '';
        
        $optionsRaw = $this->options ?? '';
        $options = is_string($optionsRaw) ? json_decode($optionsRaw, true) ?? [] : ($optionsRaw ?? []);
        $fileTypes = $this->file_types ? "accept=\"{$this->file_types}\"" : '';

        $html = "<div class=\"mb-4 {$errorClass}\" x-data=\"{ visible: true }\" ";
        if ($this->depends_on) {
            $html .= "x-show=\"getFieldValue('{$this->depends_on}') === '{$this->depends_value}'\"";
        }
        $html .= ">";

        switch ($this->type) {
            case 'text':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"text\" name=\"{$this->key}\" value=\"{$inputValue}\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">";
                break;

            case 'email':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"email\" name=\"{$this->key}\" value=\"{$inputValue}\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">";
                break;

            case 'tel':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"tel\" name=\"{$this->key}\" value=\"{$inputValue}\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">";
                break;

            case 'number':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"number\" name=\"{$this->key}\" value=\"{$inputValue}\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">";
                break;

            case 'date':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"date\" name=\"{$this->key}\" value=\"{$inputValue}\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">";
                break;

            case 'textarea':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <textarea name=\"{$this->key}\" rows=\"4\" {$placeholder} {$required}
                    class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">{$inputValue}</textarea>";
                break;

            case 'select':
                $optionsHtml = '';
                if (is_array($options)) {
                    foreach ($options as $optValue => $optLabel) {
                        $selected = $inputValue == $optValue ? 'selected' : '';
                        $optionsHtml .= "<option value=\"{$optValue}\" {$selected}>{$optLabel}</option>";
                    }
                }
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <select name=\"{$this->key}\" {$required} class=\"w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent\">
                    <option value=\"\">Select...</option>{$optionsHtml}</select>";
                break;

            case 'radio':
                $optionsHtml = '';
                if (is_array($options)) {
                    foreach ($options as $optValue => $optLabel) {
                        $checked = $inputValue == $optValue ? 'checked' : '';
                        $optionsHtml .= "<label class=\"flex items-center mr-4\"><input type=\"radio\" name=\"{$this->key}\" value=\"{$optValue}\" {$checked} {$required} class=\"mr-2\">{$optLabel}</label>";
                    }
                }
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <div class=\"flex flex-wrap\">{$optionsHtml}</div>";
                break;

            case 'checkbox':
                $checked = $inputValue ? 'checked' : '';
                $html .= "<label class=\"flex items-center\"><input type=\"checkbox\" name=\"{$this->key}\" value=\"1\" {$checked} class=\"w-4 h-4 mr-2\">{$this->label}</label>";
                break;

            case 'file':
                $html .= "<label class=\"block text-sm font-medium text-gray-700 mb-1\">{$this->label}</label>
                    <input type=\"file\" name=\"{$this->key}\" {$fileTypes} {$required}
                    class=\"block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100\">";
                break;
        }

        $html .= $helpText . "</div>";

        return $html;
    }
}
