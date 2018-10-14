<?php

namespace App\Models;

class User extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['name', 'cep', 'road', 'number', 'complement', 'neigh', 'city', 'state'];

    /**
     * @return array|bool
     */
    public function phones()
    {
        return $this->hasOne(Phone::class, 'user_id');
    }

    /**
     * @param array $phones
     */
    public function syncPhones(array $phones)
    {
        $userPhones = $this->arrayPhones();
        $add = array_diff($phones, $userPhones); //Create Result
        $rem = array_diff($userPhones, $phones); //Delete Result
        foreach ($add as $number) {
            $phone = new Phone();
            $phone->number = $number;
            $phone->user_id = $this->id;
            $phone->save();
        }
        foreach ($rem as $number) {
            $phone = Phone::select(['number' => $number]);
            $phone->delete();
        }
    }

    /**
     * @return array
     */
    public function arrayPhones()
    {
        $userPhones = $this->phones();
        $phoneArray = [];
        foreach ($userPhones as $userPhone) {
            $phoneArray[] = $userPhone->number;
        }
        return $phoneArray;
    }
}