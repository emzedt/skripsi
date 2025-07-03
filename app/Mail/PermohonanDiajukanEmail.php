<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermohonanDiajukanEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $boss;
    public $user;
    public $jenis;
    public $tanggalMulai;
    public $tanggalSelesai;

    public function __construct($boss, $user, $jenis, $tanggalMulai, $tanggalSelesai)
    {
        $this->boss = $boss;
        $this->user = $user;
        $this->jenis = $jenis;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Permohonan ' . $this->jenis . ' Diajukan');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.permohonan_diajukan');
    }

    public function attachments(): array
    {
        return [];
    }
}
