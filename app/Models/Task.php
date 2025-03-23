<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'status', 'due_date', 'creator_id', 'assignee_id'];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
