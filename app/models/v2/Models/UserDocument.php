<?php

namespace v2\Models;

use  v2\Models\AdminComment;
use v2\Filters\Traits\Filterable;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;


class UserDocument extends Eloquent
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'document_type',
        'path',
        'data',
        'status',
    ];


    protected $table = 'users_documents';


    public static $statuses = [1 => 'In Review', 2 => 'Approved', 3 => 'Declined'];
    public static $document_types = [


        1 => [
            'name' => 'Selfie Photo',
            'instruction' => 'Please upload a clear selfie photo of yourself holding a piece of paper written "your username" 
            <a href="">See example.</a> ',
            'type' => 'document',
        ],

        2 => [
            'name' => 'ID Card',
            'instruction' => 'Please upload a clear government issued ID card e.g Driver License, Int\'l Passport, Voters card, etc.
                <br>suported format are: .jpg, .png',
            'type' => 'document',
        ],

        3 => [
            'name' => 'Links',
            'instruction' => 'Please note that the names on each profile must be exact match to be verified.',
            'type' => 'link',
        ],

        /*
        3 => [
                'name'=> 'Address ',
                'instruction'=> 'Address',
            ],
*/
    ];

    public function getDataAttribute($value)
    {
        if ($value == null) {
            return [];
        }
        return json_decode($value, true);
    }

    public function getLinksAttribute()
    {

        return $this->data;
    }
    public function isDocument()
    {
        return $this->Type['type'] == 'document';
    }

    public static function get_status($status)
    {
        $order = new self();
        $order->status = $status;

        return $order->DisplayStatus;
    }


    public function adminComments()
    {
        $comments =   AdminComment::where('model_id', $this->id)->where('model', 'user_document')->get();
        return $comments;
    }


    public function getTypeAttribute()
    {
        return self::$document_types[$this->document_type];
    }


    public function is_status($status)
    {
        return $this->status == $status;
    }
    public function is_approved()
    {
        return $this->status == 2;
    }


    public function scopeApproved($query)
    {
        return $query->where('status', 2);
    }


    public function getDisplayStatusAttribute()
    {
        switch ((string)($this->status)) {
            case 2:
                $status = '<span class="badge badge-success"> Approved</span>';
                break;

            case 1:
                $status = '<span class="badge badge-warning"> In review</span>';
                break;

            case 3:
                $status = '<span class="badge badge-danger"> Declined</span>';
                break;

            default:
                $status = '<span class="badge badge-warning"> Unknown</span>';
                break;
        }

        return $status;
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}
