<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AISettings extends Model
{
    use HasFactory;
    
    protected $table = 'ai_settings';

    protected $fillable = [
        'school_id',
        'ai_name',
        'ai_icon',
        'avatar',
        'primary_color',
        'secondary_color',
        'welcome_message',
        'tone',
        'mode',
        'enabled',
        'human_handoff_enabled',
        'escalation_phrases',
        'api_key',
        'openrouter_api_key',
        'embed_enabled',
        'embed_script',
        'allowed_domains',
        'default_language',
        'ai_provider',
        'ai_model',
        'rate_limit_per_minute',
        'max_tokens',
        'temperature',
        'landing_ai_name',
        'landing_mode',
        'landing_enabled',
        'landing_welcome_message',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'human_handoff_enabled' => 'boolean',
        'embed_enabled' => 'boolean',
        'escalation_phrases' => 'array',
        'allowed_domains' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function knowledge()
    {
        return $this->hasMany(AIKnowledge::class, 'school_id', 'school_id');
    }

    public static function getGlobalSettings()
    {
        return static::whereNull('school_id')->first() ?? static::createDefault();
    }

    public static function getForSchool($schoolId)
    {
        return static::where('school_id', $schoolId)->first() 
            ?? static::createForSchool($schoolId);
    }

    private static function createDefault()
    {
        return static::create([
            'ai_name' => 'Eliana D',
            'welcome_message' => "Hello! I'm Eliana D, your admission assistant. How can I help you today?",
            'tone' => 'professional',
            'mode' => 'hybrid',
            'enabled' => true,
            'human_handoff_enabled' => true,
            'escalation_phrases' => ['human', 'speak to person', 'talk to admin', 'manager', 'help me personally', 'customer service', 'real person'],
            'default_language' => 'en',
            'allowed_domains' => [],
            'ai_provider' => 'openrouter',
            'ai_model' => 'mistralai/mistral-7b-instruct',
            'rate_limit_per_minute' => 10,
            'max_tokens' => 500,
            'temperature' => 0.7,
        ]);
    }

    private static function createForSchool($schoolId)
    {
        $global = static::getGlobalSettings();
        
        return static::create([
            'school_id' => $schoolId,
            'ai_name' => $global->ai_name,
            'primary_color' => $global->primary_color,
            'secondary_color' => $global->secondary_color,
            'welcome_message' => $global->welcome_message,
            'tone' => $global->tone,
            'mode' => $global->mode,
            'enabled' => true,
            'human_handoff_enabled' => $global->human_handoff_enabled,
            'escalation_phrases' => $global->escalation_phrases,
            'default_language' => $global->default_language ?? 'en',
            'allowed_domains' => $global->allowed_domains ?? [],
            'ai_provider' => $global->ai_provider ?? 'openrouter',
            'ai_model' => $global->ai_model ?? 'mistralai/mistral-7b-instruct',
            'rate_limit_per_minute' => $global->rate_limit_per_minute ?? 10,
            'max_tokens' => $global->max_tokens ?? 500,
            'temperature' => $global->temperature ?? 0.7,
        ]);
    }

    public function generateApiKey()
    {
        $this->api_key = 'ai_' . bin2hex(random_bytes(24));
        $this->save();
        return $this->api_key;
    }

    public function generateEmbedScript()
    {
        $language = $this->default_language ?? 'en';
        
        $script = "<script>
(function() {
    window.ElianaD = {
        schoolId: '{$this->school_id}',
        apiKey: '{$this->api_key}',
        config: {
            name: '{$this->ai_name}',
            primaryColor: '{$this->primary_color}',
            secondaryColor: '{$this->secondary_color}',
            avatar: " . ($this->avatar ? "'" . asset('storage/' . $this->avatar) . "'" : "null") . ",
            position: 'bottom-right',
            language: '{$language}'
        }
    };
    var d=document, s=d.createElement('script');
    s.src = '" . config('app.url') . "/js/eliana-widget.js';
    s.async = true;
    d.head.appendChild(s);
})();
</script>";
        
        $this->embed_script = $script;
        $this->embed_enabled = true;
        $this->save();
        
        return $script;
    }

    public function isDomainAllowed(string $domain): bool
    {
        $allowedDomains = $this->allowed_domains ?? [];
        
        if (empty($allowedDomains)) {
            return true;
        }
        
        foreach ($allowedDomains as $allowed) {
            if (str_ends_with($domain, $allowed)) {
                return true;
            }
        }
        
        return false;
    }
}
