<?php
//App/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Exemple simple d'implementation d'une classe modele utilisateur.
 * Class User
 * @package App\Models
 */
class User extends Model
{

    protected $table = 'users';
    protected $fillable = [
        'email', 'name', 'password'
    ];

    public function setPassword($password)
    {
        $this->update(array(
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ));
    }

}