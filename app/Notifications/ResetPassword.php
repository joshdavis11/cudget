<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class ResetPassword
 *
 * @package App\Notifications
 */
class ResetPassword extends Notification {
	/**
	 * The password reset token.
	 *
	 * @var string
	 */
	public $token;

	/**
	 * Create a notification instance.
	 *
	 * @param  string $token
	 */
	public function __construct($token) {
		$this->token = $token;
	}

	/**
	 * Get the notification's channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array|string
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Build the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		return (new MailMessage)
			->subject('Reset Password')
			->greeting('Hey ' . $notifiable->firstName . ',')
			->line('You are receiving this email because we received a password reset request for your account.')
			->action('Reset Password', route('password.reset', $this->token))
			->line('If you did not request a password reset, no further action is required.');
	}
}
