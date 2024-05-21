<?php

namespace App\Livewire;

use Livewire\Attributes\Validate;
use Livewire\Component;

class LoginForm extends Component
{
    #[Validate('required|min:3|max:255')]
    public string $email = '';

    #[Validate('required|min:3|max:255')]
    public string $password = '';

    public function mount()
    {
        if (auth()->check()) {
            $this->redirect(route('home'));
        }
    }

    public function authenticate()
    {
        $this->validate();

        if (auth()->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            $this->redirect(route('home'));
            return;
        }

        $this->addError('error', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.loginform')
            ->layout('components.layouts.app');
    }
}
