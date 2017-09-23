<?php

namespace App\Notifications;

use App\Model\UserToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class SignUp
 *
 * @package App\Notifications
 */
class SignUp extends Notification {
	use Queueable;
	/**
	 * @var UserToken
	 */
	public $UserToken;

	/**
	 * Create a new notification instance.
	 *
	 * @param UserToken $UserToken
	 */
	public function __construct(UserToken $UserToken) {
		$this->UserToken = $UserToken;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable) {
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable) {
		return (new MailMessage)
			->subject('Activate Your Account')
			->greeting('Hey ' . $notifiable->firstName . ',')
			->line('Please click below to activate your account.')
			->action('Activate My Account', route('activation', urlencode($this->UserToken->token)))
			->salutation(null)
			->replyTo('do-not-reply@cudget.com', 'Do Not Reply');
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray($notifiable) {
		return [//
		];
	}
}
