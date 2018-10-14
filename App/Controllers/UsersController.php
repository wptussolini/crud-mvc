<?php

namespace App\Controllers;

use App\Models\Phone;
use App\Models\User;
use App\src\Request;

class UsersController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    public function index()
    {
        $users = User::all();
        return view('index', compact('users'));
    }

    /**
     * @param $id
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('show', compact('user'));
    }

    public function create()
    {
        return view('create');
    }

    /**
     * @return false|string
     */
    public function store()
    {
        $data = $this->request->all();
        $user = new User();
        $phones = $data['phones'];
        unset($data['phones']);
        foreach ($data as $key => $value) {
            if($key == 'cep') {
                $value = clearMask($value);
            }
            $user->$key = $value;
        }
        $user->save();
        foreach ($phones as $number) {
            $phone = new Phone();
            $phone->number =  clearMask($number);
            $phone->user_id = $user->id;
            $phone->save();
        }
        return json_encode('success');
    }

    /**
     * @param $id
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('edit', compact('user'));
    }

    /**
     * @param  $id
     * @return false|string
     */
    public function update($id)
    {
        $user = User::find($id);
        $data = $this->request->all();
        $phones = $data['phones'];
        unset($data['phones']);
        foreach ($data as $key => $value) {
            if($key == 'cep') {
                $value = clearMask($value);
            }
            $user->$key = $value;
        }
        $user->save();
        if (count($phones) == 1 && $phones[0] = '') {
            if ($user->phones()) {
                foreach ($user->phones() as $phone) {
                    $phone->delete();
                }
            }
        } else {
            foreach ($phones as $key => $value) {
                $phones[$key] = clearMask($value);
            }
        }
        $user->syncPhones($phones);
        return json_encode('success');
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        header('location: /');
    }
}