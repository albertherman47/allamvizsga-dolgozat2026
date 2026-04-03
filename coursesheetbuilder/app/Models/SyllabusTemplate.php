<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyllabusTemplate extends Model
{
    use HasFactory;

    protected $table = 'syllabus_templates';

    protected $fillable = [
        'academic_year_id',
        'name',
        'docx_template_path',
        'placeholders_config',
        'form_config',
        'is_active',
    ];

    protected $casts = [
        'placeholders_config' => 'array',
        'form_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the academic year this template belongs to
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all syllabus content using this template
     */
    public function courseSyllabusContents(): HasMany
    {
        return $this->hasMany(CourseSyllabusContent::class, 'template_id');
    }

    /**
     * Get all generated syllabi using this template
     */
    public function generatedSyllabi(): HasMany
    {
        return $this->hasMany(GeneratedSyllabus::class, 'template_id');
    }

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get templates by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Get all placeholders as a flat array (backward compatibility)
     */
    public function getPlaceholders(): array
    {
        $placeholders = [];

        if (isset($this->placeholders_config['sections'])) {
            foreach ($this->placeholders_config['sections'] as $section) {
                if (isset($section['placeholders'])) {
                    foreach ($section['placeholders'] as $placeholder) {
                        // Add section info back for backward compatibility
                        $placeholder['section'] = $section['section_key'];
                        $placeholder['section_title'] = $section['title'];
                        $placeholders[] = $placeholder;
                    }
                }
            }
        }

        return $placeholders;
    }

    /**
     * Get the new grouped section structure
     */
    public function getSections(): array
    {
        return $this->placeholders_config['sections'] ?? [];
    }

    /**
     * Get placeholders for a specific section
     */
    public function getPlaceholdersBySection(string $sectionKey): array
    {
        $sections = $this->getSections();

        foreach ($sections as $section) {
            if ($section['section_key'] === $sectionKey) {
                return $section['placeholders'] ?? [];
            }
        }

        return [];
    }

    /**
     * Get a specific placeholder by name
     */
    public function getPlaceholder(string $name): ?array
    {
        $placeholders = $this->getPlaceholders();

        foreach ($placeholders as $placeholder) {
            if ($placeholder['name'] === $name) {
                return $placeholder;
            }
        }

        return null;
    }
}
