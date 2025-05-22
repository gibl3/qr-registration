<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorCredentials extends Notification
{
    use Queueable;

    protected $credentials;

    /**
     * Create a new notification instance.
     */
    public function __construct($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Instructor Account Credentials')
            ->greeting('Welcome to QR Attendance System!')
            ->line('Your instructor account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('Email: ' . $this->credentials['email'])
            ->line('Password: ' . $this->credentials['password'])
            ->line('Please change your password after your first login.')
            ->action('Login Now', route('login'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
