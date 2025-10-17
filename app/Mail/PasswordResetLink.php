<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $student;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($token, Student $student = null)
    {
        $this->token = $token;
        $this->student = $student;
        $this->resetUrl = url('/student/reset-password?token=' . $token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Request - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student.password-reset',
            text: 'emails.student.password-reset-text',
            with: [
                'token' => $this->token,
                'student' => $this->student,
                'resetUrl' => $this->resetUrl,
                'appName' => config('app.name'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
