<?php

namespace App\Mail;

use App\Model\UserToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class Signup
 *
 * @package App\Mail
 */
class Signup extends Mailable {
	use Queueable, SerializesModels;

	/**
	 * @var UserToken
	 */
	public $activationUrl;

	/**
	 * Create a new message instance.
	 *
	 * @param UserToken $UserToken
	 */
	public function __construct(UserToken $UserToken, UrlGenerator $UrlGenerator) {
		$this->activationUrl = $UrlGenerator->route('activation', ['token' => urlencode($UserToken->token)]);
		$this->replyTo('do-not-reply@cudget.com', 'Do Not Reply');
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		return $this->view('emails.signup.email-validation');
	}
}
