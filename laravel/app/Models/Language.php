<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string id
 * @property string name
 * @property string nativeName
 */
class Language extends Model
{
    const id = 'id';
    const name = 'name';
    const nativeName = 'nativeName';
    const ENG = 'eng';

    protected $primaryKey = self::id;
    public $timestamps = false;
    protected $fillable = [
        self::id,
        self::name,
        self::nativeName,
    ];

    protected function casts(): array
    {
        return [
            self::id => 'string',
        ];
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }

    public function getLabel(): string
    {
        return sprintf('%s (%s)', $this->name, $this->nativeName);
    }
}
