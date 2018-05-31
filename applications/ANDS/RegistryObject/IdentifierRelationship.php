<?php


namespace ANDS\RegistryObject;


use Illuminate\Database\Eloquent\Model;

class IdentifierRelationship extends Model
{
    protected $table = "registry_object_identifier_relationships";
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['registry_object_id', 'related_object_identifier', 'related_info_type',
    'related_object_identifier_type', 'relation_type', 'related_title', 'related_url',
        'related_description', 'connections_preview_div', 'notes'];

    public function toCSV()
    {
        return [
            'identifier:ID' => $this->related_object_identifier,
            ':LABEL' => implode(';', ['RelatedInfo', $this->related_object_identifier_type, $this->related_info_type]),
            'type' => $this->related_info_type,
            'relatedInfoType' => $this->related_object_identifier_type
        ];
    }
}

