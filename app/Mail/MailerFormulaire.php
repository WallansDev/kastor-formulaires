<?php

// ************************
// *    Timothé VAQUIÉ    *
// *    Version : 1.0     *
// ************************

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class MailerFormulaire extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $email = $this->subject('Nouveau IPBX - ' . $this->data['customer_name'] . ' | https://' . $this->data['urlPbx'] . '.wildixin.com')
            ->view('emails.contact')
            ->with([
                'reseller_name' => $this->data['reseller_name'],
                'reseller_email' => $this->data['reseller_email'],
                'customer_name' => $this->data['customer_name'],
                'urlPbx' => $this->data['urlPbx'],
                'portes' => $this->data['portes'],
                'extensions' => $this->data['extensions'],
                'callGroups' => $this->data['callGroups'],
                'timetable_ho' => $this->data['timetable_ho'],
                'svi_options' => $this->data['svi_options'],
                'dialplan' => $this->data['dialplan'],
                'infos_remarques' => $this->data['infos_remarques'],
                'devices' => $this->data['devices'],
            ]);

        if (isset($this->data['fichier']) && file_exists($this->data['fichier'])) {
            $email->attach($this->data['fichier'], [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        if (isset($this->data['pdf'])) {
            $email->attachData($this->data['pdf'], 'dossier_parametrage_'.$this->data['customer_name'].'.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        
        return $email;
    }
}
