<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @property string username
 * @property string password
 * @property string first_name
 * @property string last_name
 */
class User extends Model
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'username',
        'password',
        'first_name',
        'last_name',
    ];
}
