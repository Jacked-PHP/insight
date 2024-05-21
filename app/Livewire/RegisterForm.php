<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RegisterForm extends Component
{
    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:3|max:255')]
    public string $email = '';

    #[Validate('required|min:3|max:255')]
    public string $password = '';

    #[Validate('required|accepted')]
    public bool $acceptTerms = false;

    public bool $userRegistered = false;

    public function register()
    {
        $this->validate();

        if (User::where('email', $this->email)->exists()) {
            $this->addError('error', 'The provided email is already registered.');
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if (null !== $user) {
            $this->userRegistered = true;
            return;
        }

        $this->addError('error', 'There was an error while registering, try again later.');
    }

    public function render()
    {
        return view('livewire.registerform')
            ->layout('components.layouts.app');
    }
}
