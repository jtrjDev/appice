<?php

namespace App\Livewire\Traits;

trait WithToast
{
    /**
     * Dispara um toast de sucesso
     */
    public function toastSuccess(string $message)
    {
        $this->dispatch('toast', type: 'success', message: $message);
    }

    /**
     * Dispara um toast de erro
     */
    public function toastError(string $message)
    {
        $this->dispatch('toast', type: 'error', message: $message);
    }

    /**
     * Dispara um toast de aviso
     */
    public function toastWarning(string $message)
    {
        $this->dispatch('toast', type: 'warning', message: $message);
    }

    /**
     * Dispara um toast de informação
     */
    public function toastInfo(string $message)
    {
        $this->dispatch('toast', type: 'info', message: $message);
    }

    /**
     * Dispara toast genérico
     */
    public function toast(string $type, string $message)
    {
        $this->dispatch('toast', type: $type, message: $message);
    }
}