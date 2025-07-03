<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PermohonanDiterimaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $jenis;
    public $status;
    public $alasan;

    public function __construct($user, $jenis, $status, $alasan)
    {
        $this->user = $user;
        $this->jenis = $jenis;
        $this->status = $status;
        $this->alasan = $alasan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Permohonan ' . $this->jenis . ' Anda: ' . $this->status,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.permohonan_diterima');
    }

    public function attachments(): array
    {
        return [];
    }
}
