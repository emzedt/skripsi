<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PermohonanDiketahuiEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $boss;
    public $user;
    public $jenis;
    public $status;
    public $alasan;

    public function __construct($boss, $user, $jenis, $status, $alasan)
    {
        $this->boss = $boss;
        $this->user = $user;
        $this->jenis = $jenis;
        $this->status = $status;
        $this->alasan = $alasan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Status Permohonan ' . $this->jenis . ' ' . $this->user->nama . ': ' . $this->status);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.permohonan_diketahui');
    }

    public function attachments(): array
    {
        return [];
    }
}
